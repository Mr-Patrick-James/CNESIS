<?php
/**
 * Inquiry Conversation Messages API
 * Retrieves full message history for a specific inquiry
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
    $inquiry_id = isset($_GET['inquiry_id']) ? $_GET['inquiry_id'] : null;
    
    if (!$inquiry_id) {
        throw new Exception("Inquiry ID is required");
    }

    // Mark messages from student as read for the admin viewing it
    $updateQuery = "UPDATE inquiry_messages SET is_read = 1 WHERE inquiry_id = :id AND sender_type = 'student'";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute(['id' => $inquiry_id]);

    // Get all messages for the conversation history
    $query = "SELECT * FROM inquiry_messages WHERE inquiry_id = :id ORDER BY created_at ASC";
    $stmt = $db->prepare($query);
    $stmt->execute(['id' => $inquiry_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "messages" => $messages
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
