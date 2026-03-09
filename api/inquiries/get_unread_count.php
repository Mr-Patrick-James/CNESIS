<?php
/**
 * Unread Inquiry Count API
 * For Admin Notification Badge
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

// Initialize tables if they don't exist
initializeInquiryTables($db);

try {
    // Count unread messages from students across all inquiries
    $query = "SELECT COUNT(*) as count 
              FROM inquiry_messages 
              WHERE sender_type = 'student' AND is_read = 0";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count" => (int)$row['count']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
