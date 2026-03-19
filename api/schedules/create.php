<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data || (!isset($data->subject_id) && (!isset($data->subject_code) || !isset($data->subject_title))) || !isset($data->section_id) || !isset($data->day_of_week) || !isset($data->start_time) || !isset($data->end_time)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

try {
    $db->beginTransaction();

    $subject_id = $data->subject_id ?? null;

    if (!$subject_id) {
        // Find or create subject by code
        $stmt = $db->prepare("SELECT id FROM subjects WHERE subject_code = ?");
        $stmt->execute([$data->subject_code]);
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($subject) {
            $subject_id = $subject['id'];
        } else {
            // Create new subject
            $stmt = $db->prepare("INSERT INTO subjects (subject_code, subject_title) VALUES (?, ?)");
            $stmt->execute([$data->subject_code, $data->subject_title]);
            $subject_id = $db->lastInsertId();
        }
    }

    // Check if student_id column exists
    $stmt = $db->query("SHOW COLUMNS FROM class_schedules LIKE 'student_id'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE class_schedules ADD COLUMN student_id INT DEFAULT NULL AFTER section_id");
    }

    $query = "INSERT INTO class_schedules (subject_id, section_id, student_id, semester, instructor_name, day_of_week, start_time, end_time, room) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $subject_id,
        $data->section_id,
        $data->student_id ?? null,
        $data->semester ?? 1,
        $data->instructor_name ?? null,
        $data->day_of_week,
        $data->start_time,
        $data->end_time,
        $data->room ?? null
    ]);

    $db->commit();
    echo json_encode(["success" => true, "message" => "Schedule created successfully"]);
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>