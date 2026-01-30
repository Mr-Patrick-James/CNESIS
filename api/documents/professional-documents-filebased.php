<?php
/**
 * Professional Document Management API (File-based)
 * Handles document operations with file-based storage for reliability
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Max-Age: 86400");
    header("Content-Length: 0");
    exit;
}

// Start session for document storage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];

// Add debug logging
error_log("=== DOCUMENT API DEBUG ===");
error_log("Method: " . $method);
error_log("Session ID: " . session_id());
error_log("Session data exists: " . (isset($_SESSION['professional_documents']) ? 'YES' : 'NO'));

switch ($method) {
    case 'POST':
        handleUpload();
        break;
    case 'GET':
        handleList();
        break;
    case 'DELETE':
        handleDelete();
        break;
    default:
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Method not allowed"
        ]);
        break;
}

function handleUpload() {
    // Debug logging
    error_log("=== FILE UPLOAD DEBUG ===");
    error_log("FILES data: " . print_r($_FILES, true));
    error_log("POST data: " . print_r($_POST, true));
    
    // Check if file was uploaded
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'No file uploaded or upload error: ' . ($_FILES['document']['error'] ?? 'unknown');
        error_log("Upload error: $errorMsg");
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => $errorMsg
        ]);
        return;
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
        'image/gif',
        'text/plain'
    ];
    
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "File type not allowed. Allowed types: PDF, Word, Excel, Images, Text"
        ]);
        return;
    }
    
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "File size exceeds 10MB limit"
        ]);
        return;
    }
    
    // Create upload directory with proper permissions
    $uploadDir = '../assets/uploads/email-attachments/' . date('Y-m');
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to create upload directory: $uploadDir"
            ]);
            return;
        }
        // Set proper permissions
        chmod($uploadDir, 0777);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueFilename = uniqid() . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . '/' . $uniqueFilename;
    
    // Move uploaded file
    error_log("Moving file from {$file['tmp_name']} to $filePath");
    error_log("Upload dir exists: " . (file_exists($uploadDir) ? 'YES' : 'NO'));
    error_log("Upload dir writable: " . (is_writable($uploadDir) ? 'YES' : 'NO'));
    error_log("Source file exists: " . (file_exists($file['tmp_name']) ? 'YES' : 'NO'));
    
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        error_log("Failed to move uploaded file");
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to save uploaded file"
        ]);
        return;
    }
    
    error_log("File successfully moved to: $filePath");
    error_log("File exists after move: " . (file_exists($filePath) ? 'YES' : 'NO'));
    
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
    
    // Store in session
    if (!isset($_SESSION['professional_documents'])) {
        $_SESSION['professional_documents'] = [];
    }
    
    $document = [
        'id' => uniqid(),
        'document_name' => $file['name'],
        'file_path' => $filePath, // Store full path
        'file_size' => $file['size'],
        'file_type' => $file['type'],
        'category' => $category,
        'description' => $description,
        'is_active' => 1,
        'upload_date' => date('Y-m-d H:i:s'),
        'download_count' => 0,
        'last_used' => null
    ];
    
    $_SESSION['professional_documents'][] = $document;
    
    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Document uploaded successfully",
        "document" => $document
    ]);
}

function handleList() {
    error_log("=== HANDLE LIST DEBUG ===");
    
    $category = $_GET['category'] ?? null;
    $activeOnly = $_GET['active_only'] ?? 'true';
    
    // Initialize session documents array if it doesn't exist
    if (!isset($_SESSION['professional_documents'])) {
        $_SESSION['professional_documents'] = [];
        error_log("Initialized empty documents array in session");
    }
    
    $documents = $_SESSION['professional_documents'];
    
    error_log("Total documents before filtering: " . count($documents));
    
    // Filter by category
    if ($category) {
        $documents = array_filter($documents, function($doc) use ($category) {
            return $doc['category'] === $category;
        });
        error_log("Documents after category filter: " . count($documents));
    }
    
    // Filter by active status
    if ($activeOnly === 'true') {
        $documents = array_filter($documents, function($doc) {
            return $doc['is_active'] == 1;
        });
        error_log("Documents after active filter: " . count($documents));
    }
    
    // Format file size for display
    foreach ($documents as &$doc) {
        $doc['file_size_formatted'] = formatFileSize($doc['file_size']);
        $doc['full_url'] = '/CNESIS/' . $doc['file_path'];
    }
    
    // Sort by upload date
    usort($documents, function($a, $b) {
        return strtotime($b['upload_date']) - strtotime($a['upload_date']);
    });
    
    error_log("Final documents count: " . count($documents));
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "documents" => array_values($documents),
        "debug" => [
            "session_id" => session_id(),
            "total_before_filter" => count($_SESSION['professional_documents']),
            "total_after_filter" => count($documents)
        ]
    ]);
}

function handleDelete() {
    $documentId = $_GET['id'] ?? '';
    
    if (empty($documentId)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Document ID is required"
        ]);
        return;
    }
    
    if (!isset($_SESSION['professional_documents'])) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "No documents found"
        ]);
        return;
    }
    
    $deleted = false;
    $filePathToDelete = '';
    
    // Find and remove document
    foreach ($_SESSION['professional_documents'] as $key => $doc) {
        if ($doc['id'] === $documentId) {
            $filePathToDelete = '../' . $doc['file_path'];
            unset($_SESSION['professional_documents'][$key]);
            $deleted = true;
            break;
        }
    }
    
    if ($deleted) {
        // Delete physical file
        if ($filePathToDelete && file_exists($filePathToDelete)) {
            unlink($filePathToDelete);
        }
        
        // Reindex array
        $_SESSION['professional_documents'] = array_values($_SESSION['professional_documents']);
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Document deleted successfully"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Document not found"
        ]);
    }
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
