<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    $query = "SELECT * FROM exam_schedules";
    if ($status) {
        $query .= " WHERE status = :status";
    }
    $query .= " ORDER BY exam_date ASC, start_time ASC";
    
    $stmt = $db->prepare($query);
    if ($status) {
        $stmt->bindParam(":status", $status);
    }
    $stmt->execute();
    
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update current slots based on admissions
    // This is a dynamic check instead of relying on a column that might get out of sync
    // Although the table has current_slots, let's update it or just query it live
    // For performance, we can query counts here
    foreach ($schedules as &$schedule) {
        $countQuery = "SELECT COUNT(*) as count FROM admissions WHERE exam_schedule_id = :id";
        $countStmt = $db->prepare($countQuery);
        $countStmt->bindParam(":id", $schedule['id']);
        $countStmt->execute();
        $schedule['current_slots'] = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
    
    http_response_code(200);
    echo json_encode(["success" => true, "schedules" => $schedules]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
