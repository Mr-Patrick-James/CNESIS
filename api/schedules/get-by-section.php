<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;
$semester = isset($_GET['semester']) ? $_GET['semester'] : 1;

if (!$section_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Section ID is required"]);
    exit;
}

try {
    $query = "SELECT cs.*, sub.subject_code, sub.subject_title, sub.units 
              FROM class_schedules cs
              JOIN subjects sub ON cs.subject_id = sub.id
              WHERE cs.section_id = ? AND cs.semester = ? AND cs.student_id IS NULL
              ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time";
    $stmt = $db->prepare($query);
    $stmt->execute([$section_id, $semester]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "schedules" => $schedules]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>