<?php
/**
 * Admin Inquiry List API
 * Retrieves all inquiries with latest message and unread count
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
    // Get all inquiries sorted by last activity, showing latest message and unread count
    $query = "SELECT i.*, 
              (SELECT message FROM inquiry_messages WHERE inquiry_id = i.id ORDER BY created_at DESC LIMIT 1) as latest_message,
              (SELECT created_at FROM inquiry_messages WHERE inquiry_id = i.id ORDER BY created_at DESC LIMIT 1) as last_activity,
              (SELECT COUNT(*) FROM inquiry_messages WHERE inquiry_id = i.id AND sender_type = 'student' AND is_read = 0) as unread_count
              FROM inquiries i
              ORDER BY last_activity DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "inquiries" => $inquiries
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
