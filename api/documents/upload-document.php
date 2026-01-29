<?php
/**
 * Document Upload API
 * Handles file uploads for email attachments
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE");
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

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        uploadDocument();
        break;
    case 'GET':
        getDocuments();
        break;
    case 'DELETE':
        deleteDocument();
        break;
    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed"]);
        break;
}

function uploadDocument() {
    global $db;
    
    // Check if file was uploaded
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "No file uploaded or upload error"
        ]);
        return;
    }
    
    $file = $_FILES['document'];
    $description = $_POST['description'] ?? '';
    
    // Auto-categorize based on filename or description
    $category = 'general'; // default
    $filename = strtolower($file['name']);
    $descriptionLower = strtolower($description);
    
    // Auto-detect category
    if (strpos($filename, 'application') !== false || strpos($descriptionLower, 'application') !== false) {
        $category = 'application-form';
    } elseif (strpos($filename, 'requirement') !== false || strpos($descriptionLower, 'requirement') !== false) {
        $category = 'requirements';
    } elseif (strpos($filename, 'policy') !== false || strpos($descriptionLower, 'policy') !== false) {
        $category = 'policies';
    } elseif (strpos($filename, 'template') !== false || strpos($descriptionLower, 'template') !== false) {
        $category = 'templates';
    }
    
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
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "File type not allowed"
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
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../assets/uploads/email-attachments/' . date('Y-m');
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueFilename = uniqid() . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . '/' . $uniqueFilename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to save uploaded file"
        ]);
        return;
    }
    
    // Check if table exists, if not, just return success without database
    try {
        $tableCheck = $db->query("SHOW TABLES LIKE 'email_documents'");
        if ($tableCheck->rowCount() === 0) {
            // Table doesn't exist, just return success
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Document uploaded successfully (stored in file system)",
                "document" => [
                    "id" => uniqid(),
                    "name" => $file['name'],
                    "size" => $file['size'],
                    "type" => $file['type'],
                    "category" => $category,
                    "upload_date" => date('Y-m-d H:i:s')
                ]
            ]);
            return;
        }
        
        // Save to database
        $query = "INSERT INTO email_documents 
                 (document_name, file_path, file_size, file_type, category, description, uploaded_by) 
                 VALUES (?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $file['name'],
            str_replace('../', '', $filePath), // Store relative path
            $file['size'],
            $file['type'],
            $category,
            $description
        ]);
        
        if ($result) {
            $documentId = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Document uploaded successfully",
                "document" => [
                    "id" => $documentId,
                    "name" => $file['name'],
                    "size" => $file['size'],
                    "type" => $file['type'],
                    "category" => $category,
                    "upload_date" => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            // Delete uploaded file if database insert failed
            unlink($filePath);
            
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to save document to database"
            ]);
        }
        
    } catch (Exception $e) {
        // Delete uploaded file if error occurred
        unlink($filePath);
        
        error_log("Document upload error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Error uploading document: " . $e->getMessage()
        ]);
    }
}

function getDocuments() {
    global $db;
    
    $category = $_GET['category'] ?? null;
    $activeOnly = $_GET['active_only'] ?? 'true';
    
    try {
        // Check if table exists
        $tableCheck = $db->query("SHOW TABLES LIKE 'email_documents'");
        if ($tableCheck->rowCount() === 0) {
            // Table doesn't exist, return empty list
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "documents" => []
            ]);
            return;
        }
        
        $query = "SELECT * FROM email_documents WHERE 1=1";
        $params = [];
        
        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($activeOnly === 'true') {
            $query .= " AND is_active = TRUE";
        }
        
        $query .= " ORDER BY upload_date DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $documents = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Format file size for display
        foreach ($documents as &$doc) {
            $doc['file_size_formatted'] = formatFileSize($doc['file_size']);
            $doc['full_url'] = '/CNESIS/' . $doc['file_path'];
        }
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "documents" => $documents
        ]);
        
    } catch (Exception $e) {
        error_log("Error getting documents: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Error retrieving documents: " . $e->getMessage()
        ]);
    }
}

function deleteDocument() {
    global $db;
    
    $documentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($documentId <= 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Valid document ID is required"
        ]);
        return;
    }
    
    try {
        // Get document info before deletion
        $query = "SELECT * FROM email_documents WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$documentId]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$document) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Document not found"
            ]);
            return;
        }
        
        // Delete from database
        $deleteQuery = "DELETE FROM email_documents WHERE id = ?";
        $deleteStmt = $db->prepare($deleteQuery);
        $result = $deleteStmt->execute([$documentId]);
        
        if ($result) {
            // Delete physical file
            $filePath = '../' . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Document deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete document from database"
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Error deleting document: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Error deleting document: " . $e->getMessage()
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
