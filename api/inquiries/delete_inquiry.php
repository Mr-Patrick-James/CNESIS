<?php
/**
 * Delete Inquiry API
 * Removes an inquiry and its conversation history
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, DELETE");

include_once '../config/database.php';
include_once 'db_init.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || empty($data->inquiry_id)) {
        throw new Exception("Missing required field: inquiry_id");
    }

    $db->beginTransaction();

    // Check if inquiry exists
    $checkQuery = "SELECT id FROM inquiries WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute(['id' => $data->inquiry_id]);
    
    if (!$checkStmt->fetch()) {
        throw new Exception("Inquiry not found");
    }

    // Delete inquiry - messages will be deleted automatically due to ON DELETE CASCADE
    $deleteQuery = "DELETE FROM inquiries WHERE id = :id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->execute(['id' => $data->inquiry_id]);

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "Inquiry deleted successfully"
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
