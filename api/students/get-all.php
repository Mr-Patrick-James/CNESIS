<?php
/**
 * Get All Students API
 * Returns all students with program information
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once __DIR__ . '/../config/database.php';

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
                s.date_enrolled,
                s.gender,
                s.address,
                s.department,
                s.section_id,
                sec.section_name,
                sec.section_code,
                s.yearlevel,
                s.status,
                s.remarks,
                s.avatar,
                s.created_at,
                s.updated_at
              FROM students s
              LEFT JOIN sections sec ON s.section_id = sec.id
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
            'date_enrolled' => $row['date_enrolled'],
            'gender' => $row['gender'],
            'address' => $row['address'],
            'department' => $row['department'],
            'section_id' => $row['section_id'],
            'section_name' => $row['section_name'],
            'section_code' => $row['section_code'],
            'year_level' => $row['yearlevel'],
            'status' => $row['status'],
            'remarks' => $row['remarks'],
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
