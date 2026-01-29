<?php
/**
 * Get All Students API
 * Returns all students with program information
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

try {
    // Build query with program join
    $query = "SELECT 
                s.id,
                s.student_id,
                s.first_name,
                s.middle_name,
                s.last_name,
                s.email,
                s.phone,
                s.birth_date,
                s.gender,
                s.address,
                s.department,
                s.section_id,
                s.yearlevel,
                s.status,
                s.avatar,
                s.created_at,
                s.updated_at
              FROM students s
              ORDER BY s.student_id";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $students = [];
    
    while ($row = $stmt->fetch()) {
        $students[] = [
            'id' => $row['id'],
            'student_id' => $row['student_id'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'birth_date' => $row['birth_date'],
            'gender' => $row['gender'],
            'address' => $row['address'],
            'department' => $row['department'],
            'section_id' => $row['section_id'],
            'year_level' => $row['yearlevel'],
            'status' => $row['status'],
            'avatar' => $row['avatar'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "count" => count($students),
        "students" => $students
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
