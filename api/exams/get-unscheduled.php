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
    // Select students who are 'approved' and have NO exam_schedule_id
    $query = "SELECT 
                a.id, 
                a.first_name, 
                a.last_name, 
                a.email, 
                a.submitted_at, 
                a.status,
                p.code as program_code
              FROM admissions a
              LEFT JOIN programs p ON a.program_id = p.id
              WHERE a.status IN ('approved', 'scheduled', 'reschedule', 'did not attend')
              AND a.exam_schedule_id IS NULL
              ORDER BY a.submitted_at ASC";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode(["success" => true, "students" => $students]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
