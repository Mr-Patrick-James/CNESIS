<?php
/**
 * Combined Document API
 * Handles both upload and listing with proper error handling
 */

// Disable error display but log errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering
ob_start();

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Content-Length: 0");
    ob_end_clean();
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        handleUpload();
        break;
    case 'GET':
        handleList();
        break;
    default:
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "Method not allowed"]);
        break;
}

function handleUpload() {
    // Check if file was uploaded
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        ob_end_clean();
        echo json_encode([
            "success" => false,
            "message" => "No file uploaded or upload error"
        ]);
        exit;
    }
    
    $file = $_FILES['document'];
    $description = $_POST['description'] ?? '';
    
    // Validate file
    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
        'text/plain'
    ];
    
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        ob_end_clean();
        echo json_encode([
            "success" => false,
            "message" => "File type not allowed"
        ]);
        exit;
    }
    
    if ($file['size'] > $maxSize) {
        ob_end_clean();
        echo json_encode([
            "success" => false,
            "message" => "File size exceeds 10MB limit"
        ]);
        exit;
    }
    
    // Create upload directory
    $uploadDir = '../assets/uploads/email-attachments/' . date('Y-m');
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            ob_end_clean();
            echo json_encode([
                "success" => false,
                "message" => "Failed to create upload directory"
            ]);
            exit;
        }
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueFilename = uniqid() . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . '/' . $uniqueFilename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        ob_end_clean();
        echo json_encode([
            "success" => false,
            "message" => "Failed to save uploaded file"
        ]);
        exit;
    }
    
    // Auto-categorize
    $category = 'general';
    $filename = strtolower($file['name']);
    $descriptionLower = strtolower($description);
    
    if (strpos($filename, 'application') !== false || strpos($descriptionLower, 'application') !== false) {
        $category = 'application-form';
    } elseif (strpos($filename, 'requirement') !== false || strpos($descriptionLower, 'requirement') !== false) {
        $category = 'requirements';
    } elseif (strpos($filename, 'policy') !== false || strpos($descriptionLower, 'policy') !== false) {
        $category = 'policies';
    } elseif (strpos($filename, 'template') !== false || strpos($descriptionLower, 'template') !== false) {
        $category = 'templates';
    }
    
    // Store in session for listing (simple approach)
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (!isset($_SESSION['uploaded_documents'])) {
        $_SESSION['uploaded_documents'] = [];
    }
    
    $_SESSION['uploaded_documents'][] = [
        'id' => uniqid(),
        'document_name' => $file['name'],
        'file_path' => str_replace('../', '', $filePath),
        'file_size' => $file['size'],
        'file_type' => $file['type'],
        'category' => $category,
        'description' => $description,
        'upload_date' => date('Y-m-d H:i:s'),
        'is_active' => 1
    ];
    
    ob_end_clean();
    echo json_encode([
        "success" => true,
        "message" => "Document uploaded successfully",
        "document" => [
            "id" => uniqid(),
            "name" => $file['name'],
            "size" => $file['size'],
            "type" => $file['type'],
            "category" => $category,
            "upload_date" => date('Y-m-d H:i:s'),
            "file_path" => str_replace('../', '', $filePath)
        ]
    ]);
}

function handleList() {
    // Get documents from session
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $documents = $_SESSION['uploaded_documents'] ?? [];
    
    // Format file size for display
    foreach ($documents as &$doc) {
        $doc['file_size_formatted'] = formatFileSize($doc['file_size']);
        $doc['full_url'] = '/CNESIS/' . $doc['file_path'];
    }
    
    ob_end_clean();
    echo json_encode([
        "success" => true,
        "documents" => $documents
    ]);
}

function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
