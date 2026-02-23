<?php
/**
 * Get Single Student API
 * Returns a single student by ID
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
    // Get student ID from URL
    $studentId = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (empty($studentId)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Student ID is required"
        ]);
        exit;
    }
    
    // Build query
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
                sec.section_name,
                sec.section_code,
                s.yearlevel,
                s.status,
                s.avatar,
                s.created_at,
                s.updated_at
              FROM students s
              LEFT JOIN sections sec ON s.section_id = sec.id
              WHERE s.id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $studentId);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Student not found"
        ]);
        exit;
    }
    
    $row = $stmt->fetch();
    
    $student = [
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
        'section_name' => $row['section_name'],
        'section_code' => $row['section_code'],
        'year_level' => $row['yearlevel'],
        'status' => $row['status'],
        'avatar' => $row['avatar'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ];
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "student" => $student
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
