<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->id)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Schedule ID is required"]);
    exit;
}

try {
    $query = "DELETE FROM class_schedules WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->id]);

    echo json_encode(["success" => true, "message" => "Schedule deleted successfully"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>