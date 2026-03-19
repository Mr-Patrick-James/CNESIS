<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->program_code)) {
    try {
        $query = "DELETE FROM prospectus WHERE program_code = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$data->program_code])) {
            echo json_encode(["success" => true, "message" => "Curriculum cleared successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Unable to clear curriculum"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Incomplete data. Provide program_code."]);
}
?>
