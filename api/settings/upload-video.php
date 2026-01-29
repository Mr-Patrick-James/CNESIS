<?php
/**
 * Video Upload API
 * Handles video file uploads for the home page
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "No video file uploaded or upload error"
        ]);
        exit;
    }
    
    $file = $_FILES['video'];
    
    // Validate file type
    $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid file type. Only MP4, WebM, and OGG videos are allowed"
        ]);
        exit;
    }
    
    // Validate file size (max 50MB)
    $maxSize = 50 * 1024 * 1024; // 50MB
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "File too large. Maximum size is 50MB"
        ]);
        exit;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../assets/videos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'landing_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to move uploaded file"
        ]);
        exit;
    }
    
    // Update system settings with new video path
    $videoPath = 'assets/videos/' . $fileName;
    $updateQuery = "UPDATE system_settings SET setting_value = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = 'home_video'";
    $stmt = $db->prepare($updateQuery);
    $stmt->execute([$videoPath]);
    
    // Get old video path to delete it
    $oldVideoQuery = "SELECT setting_value FROM system_settings WHERE setting_key = 'home_video'";
    $oldStmt = $db->prepare($oldVideoQuery);
    $oldStmt->execute();
    $oldVideo = $oldStmt->fetch(PDO::FETCH_ASSOC)['setting_value'];
    
    // Delete old video file if it exists and is different
    if ($oldVideo && $oldVideo !== $videoPath && file_exists('../' . $oldVideo)) {
        unlink('../' . $oldVideo);
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Video uploaded successfully",
        "video_path" => $videoPath,
        "file_name" => $fileName,
        "file_size" => $file['size'],
        "mime_type" => $mimeType
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
