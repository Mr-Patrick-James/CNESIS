<?php
/**
 * Create Student API
 * Handles creating new students
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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
    // Get raw input
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput);
    
    // Validate JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid JSON: " . json_last_error_msg()
        ]);
        exit;
    }
    
    // Validate required fields
    if (empty($data->student_id) || empty($data->first_name) || empty($data->last_name) || 
        empty($data->email)) {
        
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields"
        ]);
        exit;
    }
    
    // Check if student ID already exists
    $checkQuery = "SELECT id FROM students WHERE student_id = :student_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':student_id', $data->student_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Student ID already exists"
        ]);
        exit;
    }
    
    // Insert new student
    $query = "INSERT INTO students (
                student_id,
                first_name,
                middle_name,
                last_name,
                email,
                phone,
                birth_date,
                gender,
                address,
                department,
                section_id,
                yearlevel,
                status
              ) VALUES (
                :student_id,
                :first_name,
                :middle_name,
                :last_name,
                :email,
                :phone,
                :birth_date,
                :gender,
                :address,
                :department,
                :section_id,
                :yearlevel,
                :status
              )";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':student_id', $data->student_id);
    $stmt->bindParam(':first_name', $data->first_name);
    $stmt->bindParam(':middle_name', $data->middle_name);
    $stmt->bindParam(':last_name', $data->last_name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':birth_date', $data->birth_date);
    $stmt->bindParam(':gender', $data->gender);
    $stmt->bindParam(':address', $data->address);
    $stmt->bindParam(':department', $data->department);
    $stmt->bindParam(':section_id', $data->section_id);
    $stmt->bindParam(':yearlevel', $data->year_level);
    $stmt->bindParam(':status', $data->status);
    
    if ($stmt->execute()) {
        $newId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Student created successfully",
            "id" => $newId,
            "student_id" => $data->student_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to create student"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
