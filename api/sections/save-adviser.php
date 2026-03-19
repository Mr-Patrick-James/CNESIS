<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->section_id)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing section ID"]);
    exit;
}

try {
    // 1. Ensure adviser column exists
    $stmt = $db->query("SHOW COLUMNS FROM sections LIKE 'adviser'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE sections ADD COLUMN adviser VARCHAR(255) DEFAULT NULL AFTER section_name");
    }

    // 2. Update adviser
    $query = "UPDATE sections SET adviser = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->adviser ?? null, $data->section_id]);

    echo json_encode(["success" => true, "message" => "Adviser updated successfully"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>