<?php
/**
 * Document Preview API
 * Handles document preview functionality
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

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

// Get document ID from query parameter
$documentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($documentId <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Valid document ID is required"
    ]);
    exit;
}

try {
    $query = "SELECT * FROM email_documents WHERE id = ? AND is_active = TRUE";
    $stmt = $db->prepare($query);
    $stmt->execute([$documentId]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($document) {
        $filePath = '../' . $document['file_path'];
        
        if (file_exists($filePath)) {
            // Get file info
            $fileInfo = pathinfo($filePath);
            $fileName = $document['document_name'];
            $fileSize = $document['file_size'];
            $mimeType = $document['file_type'];
            
            // Update download count
            $updateQuery = "UPDATE email_documents SET download_count = download_count + 1, last_used = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$documentId]);
            
            // Set appropriate headers for file display
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . $fileSize);
            header('Content-Disposition: inline; filename="' . $fileName . '"');
            header('Cache-Control: public, max-age=3600');
            
            // Output file content
            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "File not found on server"
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Document not found"
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error previewing document: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error retrieving document: " . $e->getMessage()
    ]);
}
?>
