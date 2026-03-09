<?php
/**
 * Student Inquiry Retrieval API
 * Lets students check their conversation history using their email address
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once 'db_init.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Ensure tables exist
initializeInquiryTables($db);

try {
    $email = isset($_GET['email']) ? $_GET['email'] : null;
    
    if (!$email) {
        throw new Exception("Email is required");
    }

    // Get the latest open/responded inquiry for this specific email address
    $query = "SELECT id, full_name, status 
              FROM inquiries 
              WHERE email = :email AND status != 'closed' 
              ORDER BY updated_at DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute(['email' => $email]);
    $inquiry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inquiry) {
        echo json_encode(["success" => true, "found" => false]);
        exit;
    }

    // Mark admin responses as read when the student views the conversation
    $updateQuery = "UPDATE inquiry_messages 
                    SET is_read = 1 
                    WHERE inquiry_id = :id AND sender_type = 'admin'";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute(['id' => $inquiry['id']]);

    // Retrieve all messages for the student's conversation history
    $msgQuery = "SELECT * 
                 FROM inquiry_messages 
                 WHERE inquiry_id = :id 
                 ORDER BY created_at ASC";
    $msgStmt = $db->prepare($msgQuery);
    $msgStmt->execute(['id' => $inquiry['id']]);
    $messages = $msgStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "found" => true,
        "inquiry_id" => $inquiry['id'],
        "full_name" => $inquiry['full_name'],
        "status" => $inquiry['status'],
        "messages" => $messages
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
