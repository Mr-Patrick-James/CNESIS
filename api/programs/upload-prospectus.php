<?php
/**
 * Upload Prospectus File API
 * Handles prospectus file uploads (PDF, Excel, Word)
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Check if file was uploaded
if (!isset($_FILES['prospectus'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No file uploaded"]);
    exit;
}

$file = $_FILES['prospectus'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "File upload error: " . $file['error']]);
    exit;
}

// Validate file size (10MB max)
$maxSize = 10 * 1024 * 1024; // 10MB in bytes
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "File size exceeds 10MB limit"]);
    exit;
}

// Validate file type
$allowedExtensions = ['pdf', 'xlsx', 'xls', 'docx', 'doc'];
$allowedMimeTypes = [
    'application/pdf',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$fileMimeType = mime_content_type($file['tmp_name']);

if (!in_array($fileExtension, $allowedExtensions) || !in_array($fileMimeType, $allowedMimeTypes)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid file type. Allowed: PDF, Excel, Word"
    ]);
    exit;
}

// Generate unique filename
$programCode = isset($_POST['program_code']) ? preg_replace('/[^a-zA-Z0-9-_]/', '', $_POST['program_code']) : 'program';
$timestamp = time();
$newFilename = $programCode . '-prospectus-' . $timestamp . '.' . $fileExtension;

// Set upload directory - use the proper programs directory
$uploadDir = '../../assets/img/programs/prospectus/';

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadPath = $uploadDir . $newFilename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Return relative path for database storage
    $relativePath = '../../assets/img/programs/prospectus/' . $newFilename;
    
    // Update database if program_id is provided
    if (isset($_POST['program_id']) && !empty($_POST['program_id'])) {
        include_once '../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db !== null) {
            try {
                $updateQuery = "UPDATE programs SET prospectus_path = :prospectus_path WHERE id = :program_id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':prospectus_path', $relativePath);
                $updateStmt->bindParam(':program_id', $_POST['program_id']);
                $updateStmt->execute();
            } catch (PDOException $e) {
                // Log error but don't fail the upload
                error_log("Failed to update database with prospectus path: " . $e->getMessage());
            }
            $database->closeConnection();
        }
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "File uploaded successfully",
        "filename" => $newFilename,
        "path" => $relativePath,
        "size" => $file['size']
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to move uploaded file"]);
}
?>
