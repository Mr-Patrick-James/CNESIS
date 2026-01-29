<?php
/**
 * Create Program Head API
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    // Get raw input for debugging
    $rawInput = file_get_contents("php://input");
    error_log("Program Heads Create - Raw input: " . $rawInput);
    
    $data = json_decode($rawInput);
    error_log("Program Heads Create - Decoded data: " . print_r($data, true));
    
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
    if (empty($data->employee_id) || empty($data->first_name) || empty($data->last_name) || 
        empty($data->email) || empty($data->phone) || empty($data->hire_date) || empty($data->department)) {
        
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields",
            "missing" => [
                "employee_id" => empty($data->employee_id),
                "first_name" => empty($data->first_name),
                "last_name" => empty($data->last_name),
                "email" => empty($data->email),
                "phone" => empty($data->phone),
                "hire_date" => empty($data->hire_date),
                "department" => empty($data->department)
            ]
        ]);
        exit;
    }
    
    // Check if employee ID already exists
    $checkQuery = "SELECT id FROM program_heads WHERE employee_id = :employee_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':employee_id', $data->employee_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Employee ID already exists"
        ]);
        exit;
    }
    
    // Check if email already exists
    $checkEmailQuery = "SELECT id FROM program_heads WHERE email = :email";
    $checkEmailStmt = $db->prepare($checkEmailQuery);
    $checkEmailStmt->bindParam(':email', $data->email);
    $checkEmailStmt->execute();
    
    if ($checkEmailStmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Email already exists"
        ]);
        exit;
    }
    
    // Insert new program head
    $query = "INSERT INTO program_heads (
                employee_id, 
                first_name, 
                middle_name, 
                last_name, 
                email, 
                phone, 
                department, 
                specialization, 
                hire_date, 
                status
              ) VALUES (
                :employee_id, 
                :first_name, 
                :middle_name, 
                :last_name, 
                :email, 
                :phone, 
                :department, 
                :specialization, 
                :hire_date, 
                :status
              )";
    
    $middleName = $data->middle_name ?? null;
    $specialization = $data->specialization ?? null;
    $status = $data->status ?? 'active';
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':employee_id', $data->employee_id);
    $stmt->bindParam(':first_name', $data->first_name);
    $stmt->bindParam(':middle_name', $middleName);
    $stmt->bindParam(':last_name', $data->last_name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':department', $data->department);
    $stmt->bindParam(':specialization', $specialization);
    $stmt->bindParam(':hire_date', $data->hire_date);
    $stmt->bindParam(':status', $status);
    
    if ($stmt->execute()) {
        $newId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Program head created successfully",
            "id" => $newId
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to create program head"
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