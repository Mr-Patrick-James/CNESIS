<?php
/**
 * Update Student API
 * Handles updating existing students
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, POST");
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
    if (empty($data->id) || empty($data->student_id) || empty($data->first_name) || 
        empty($data->last_name) || empty($data->email)) {
        
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields"
        ]);
        exit;
    }
    
    // Check if student exists
    $checkQuery = "SELECT id FROM students WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Student not found"
        ]);
        exit;
    }
    
    // Check if student ID conflicts with another student
    if ($data->student_id) {
        $checkIdQuery = "SELECT id FROM students WHERE student_id = :student_id AND id != :id";
        $checkIdStmt = $db->prepare($checkIdQuery);
        $checkIdStmt->bindParam(':student_id', $data->student_id);
        $checkIdStmt->bindParam(':id', $data->id);
        $checkIdStmt->execute();
        
        if ($checkIdStmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "Student ID already exists"
            ]);
            exit;
        }
    }
    
    // Update student
    $query = "UPDATE students SET 
                student_id = :student_id,
                first_name = :first_name,
                middle_name = :middle_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                birth_date = :birth_date,
                gender = :gender,
                address = :address,
                department = :department,
                section_id = :section_id,
                yearlevel = :year_level,
                status = :status
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':id', $data->id);
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
    $stmt->bindParam(':year_level', $data->year_level);
    $stmt->bindParam(':status', $data->status);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Student updated successfully",
            "id" => $data->id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to update student"
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
