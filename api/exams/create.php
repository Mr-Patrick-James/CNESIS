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

if (
    !empty($data->batch_name) &&
    !empty($data->exam_date) &&
    !empty($data->start_time) &&
    !empty($data->end_time) &&
    !empty($data->venue) &&
    !empty($data->max_slots)
) {
    try {
        $query = "INSERT INTO exam_schedules 
                  (batch_name, exam_date, start_time, end_time, venue, max_slots, status) 
                  VALUES (:batch_name, :exam_date, :start_time, :end_time, :venue, :max_slots, 'active')";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":batch_name", $data->batch_name);
        $stmt->bindParam(":exam_date", $data->exam_date);
        $stmt->bindParam(":start_time", $data->start_time);
        $stmt->bindParam(":end_time", $data->end_time);
        $stmt->bindParam(":venue", $data->venue);
        $stmt->bindParam(":max_slots", $data->max_slots);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Exam schedule created successfully"]);
        } else {
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "Unable to create exam schedule"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Incomplete data"]);
}
?>
