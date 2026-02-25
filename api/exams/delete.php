<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
    try {
        $db->beginTransaction();
        
        // 1. Update students status back to 'verified' before deleting the schedule
        $updateStudentsQuery = "UPDATE admissions SET status = 'verified', exam_schedule_id = NULL WHERE exam_schedule_id = :id";
        $updateStmt = $db->prepare($updateStudentsQuery);
        $updateStmt->bindParam(":id", $data->id);
        $updateStmt->execute();
        
        // 2. Delete the exam schedule
        $query = "DELETE FROM exam_schedules WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $data->id);
        
        if ($stmt->execute()) {
            $db->commit();
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Exam schedule deleted successfully"]);
        } else {
            $db->rollBack();
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "Unable to delete exam schedule"]);
        }
    } catch (PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Incomplete data"]);
}
?>
