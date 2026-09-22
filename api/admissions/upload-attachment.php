<?php
/**
 * Upload Admission Attachment API
 * Handles uploading of admission documents (PDF, Images)
 */

// Override PHP limits to allow large uploads
@ini_set('upload_max_filesize', '200M');
@ini_set('post_max_size', '210M');
@ini_set('max_execution_time', '300');
@ini_set('max_input_time', '300');
@ini_set('memory_limit', '256M');

require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Check if file was uploaded
if (empty($_FILES) || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "message" => "No file uploaded. This may be caused by the file exceeding the server's upload size limit (post_max_size: " . ini_get('post_max_size') . ", upload_max_filesize: " . ini_get('upload_max_filesize') . ")"
    ]);
    exit;
}

$file = $_FILES['file'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "File upload error: " . $file['error']]);
    exit;
}

// No file size limit enforced by application

// No file type restriction - accept any file
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (empty($fileExtension)) {
    $fileExtension = 'bin';
}

// Generate unique filename
$prefix = isset($_POST['type']) ? preg_replace('/[^a-zA-Z0-9-_]/', '', $_POST['type']) : 'attachment';
$timestamp = time();
$random = bin2hex(random_bytes(4));
$newFilename = $prefix . '-' . $timestamp . '-' . $random . '.' . $fileExtension;

// Set upload directory using absolute path (works on both local and AWS)
$projectRoot = realpath(__DIR__ . '/../../');
$uploadSubDir = 'assets/uploads/admissions/' . date('Y-m');
$uploadDir = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uploadSubDir) . DIRECTORY_SEPARATOR;

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            "success" => false, 
            "message" => "Failed to create upload directory",
            "debug_path" => $uploadDir
        ]);
        exit;
    }
}

$uploadPath = $uploadDir . $newFilename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {

    // --- Image compression (server-side fallback) ---
    // Only process actual image types; PDFs and other files are left as-is.
    $imageTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];
    if (in_array($fileExtension, $imageTypes) && function_exists('imagecreatefromjpeg')) {
        compressAdmissionImage($uploadPath, $fileExtension);
    }

    // Return relative path for database storage
    $relativePath = $uploadSubDir . '/' . $newFilename;
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "File uploaded successfully",
        "filename" => $newFilename,
        "path" => $relativePath
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Failed to move uploaded file",
        "debug_path" => $uploadPath
    ]);
}

/**
 * Compress/resize an uploaded image in-place using GD.
 * - Resizes to a max dimension of 1600 px (keeps aspect ratio).
 * - Re-encodes JPEGs at quality 75, PNGs at compression 7.
 * - Skips the file if GD cannot read it or if it is already small enough.
 *
 * @param string $filePath      Absolute path to the saved image.
 * @param string $ext           Lowercase file extension (jpg, jpeg, png, webp, gif, bmp).
 */
function compressAdmissionImage(string $filePath, string $ext): void
{
    $maxDimension = 1600; // px — max width or height after resize
    $jpegQuality  = 75;   // 0–100
    $pngLevel     = 7;    // 0–9 (9 = max compression)

    // Load image into a GD resource depending on extension
    $src = null;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $src = @imagecreatefromjpeg($filePath);
            break;
        case 'png':
            $src = @imagecreatefrompng($filePath);
            break;
        case 'webp':
            if (function_exists('imagecreatefromwebp')) {
                $src = @imagecreatefromwebp($filePath);
            }
            break;
        case 'gif':
            $src = @imagecreatefromgif($filePath);
            break;
        case 'bmp':
            if (function_exists('imagecreatefrombmp')) {
                $src = @imagecreatefrombmp($filePath);
            }
            break;
    }

    if (!$src) {
        return; // GD could not open the file — leave it untouched
    }

    $origW = imagesx($src);
    $origH = imagesy($src);

    // Calculate new dimensions (only downscale, never upscale)
    if ($origW <= $maxDimension && $origH <= $maxDimension) {
        $newW = $origW;
        $newH = $origH;
    } elseif ($origW >= $origH) {
        $newW = $maxDimension;
        $newH = (int) round($origH * ($maxDimension / $origW));
    } else {
        $newH = $maxDimension;
        $newW = (int) round($origW * ($maxDimension / $origH));
    }

    // Create destination canvas (truecolor for best quality)
    $dst = imagecreatetruecolor($newW, $newH);

    // Preserve transparency for PNG and GIF
    if ($ext === 'png' || $ext === 'gif') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
    imagedestroy($src);

    // Save back to the same file path
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($dst, $filePath, $jpegQuality);
            break;
        case 'png':
            imagepng($dst, $filePath, $pngLevel);
            break;
        case 'webp':
            if (function_exists('imagewebp')) {
                imagewebp($dst, $filePath, $jpegQuality);
            }
            break;
        case 'gif':
            imagegif($dst, $filePath);
            break;
        case 'bmp':
            if (function_exists('imagebmp')) {
                imagebmp($dst, $filePath);
            }
            break;
    }

    imagedestroy($dst);
}
?>