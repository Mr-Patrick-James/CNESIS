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

header("Access-Control-Allow-Origin: *");
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
?>