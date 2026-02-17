<?php
/**
 * Upload Admission Attachment API
 * Handles uploading of admission documents (PDF, Images)
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Check if file was uploaded
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No file uploaded"]);
    exit;
}

$file = $_FILES['file'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "File upload error: " . $file['error']]);
    exit;
}

// Validate file size (10MB max)
$maxSize = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "File size exceeds 10MB limit"]);
    exit;
}

// Validate file type
$allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
$allowedMimeTypes = [
    'image/jpeg', 
    'image/png', 
    'application/pdf'
];

$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$fileMimeType = mime_content_type($file['tmp_name']);

if (!in_array($fileExtension, $allowedExtensions) || !in_array($fileMimeType, $allowedMimeTypes)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid file type. Allowed: JPG, PNG, PDF"
    ]);
    exit;
}

// Generate unique filename
$prefix = isset($_POST['type']) ? preg_replace('/[^a-zA-Z0-9-_]/', '', $_POST['type']) : 'attachment';
$timestamp = time();
$random = bin2hex(random_bytes(4));
$newFilename = $prefix . '-' . $timestamp . '-' . $random . '.' . $fileExtension;

// Set upload directory
$uploadDir = '../../assets/uploads/admissions/' . date('Y-m') . '/';

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to create upload directory"]);
        exit;
    }
}

$uploadPath = $uploadDir . $newFilename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Return relative path for database storage
    // We store the path relative to the api folder or project root?
    // Usually project root relative or absolute url.
    // Let's store relative to project root: assets/uploads/admissions/...
    $relativePath = 'assets/uploads/admissions/' . date('Y-m') . '/' . $newFilename;
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "File uploaded successfully",
        "filename" => $newFilename,
        "path" => $relativePath
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to move uploaded file"]);
}
?>