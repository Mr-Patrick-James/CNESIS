<?php
/**
 * Create Admission API
 * Handles creating new admission applications
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
    // Get raw input for debugging
    $rawInput = file_get_contents("php://input");
    error_log("Admissions Create - Raw input: " . $rawInput);
    
    $data = json_decode($rawInput);
    error_log("Admissions Create - Decoded data: " . print_r($data, true));
    
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
    if (empty($data->application_id) || empty($data->first_name) || empty($data->last_name) || 
        empty($data->email) || empty($data->admission_type)) {
        
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields",
            "missing" => [
                "application_id" => empty($data->application_id),
                "first_name" => empty($data->first_name),
                "last_name" => empty($data->last_name),
                "email" => empty($data->email),
                "admission_type" => empty($data->admission_type)
            ]
        ]);
        exit;
    }
    
    // Check if application ID already exists
    $checkQuery = "SELECT id FROM admissions WHERE application_id = :application_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':application_id', $data->application_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Application ID already exists"
        ]);
        exit;
    }
    
    // Check if email already exists
    $checkEmailQuery = "SELECT id FROM admissions WHERE email = :email AND status != 'rejected'";
    $checkEmailStmt = $db->prepare($checkEmailQuery);
    $checkEmailStmt->bindParam(':email', $data->email);
    $checkEmailStmt->execute();
    
    if ($checkEmailStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Email already has an active application"
        ]);
        exit;
    }
    
    // Insert new admission
    $query = "INSERT INTO admissions (
                application_id,
                student_id,
                program_id,
                first_name,
                middle_name,
                last_name,
                email,
                phone,
                birthdate,
                gender,
                address,
                high_school,
                last_school,
                year_graduated,
                gwa,
                entrance_exam_score,
                admission_type,
                previous_program,
                status,
                notes
              ) VALUES (
                :application_id,
                :student_id,
                :program_id,
                :first_name,
                :middle_name,
                :last_name,
                :email,
                :phone,
                :birthdate,
                :gender,
                :address,
                :high_school,
                :last_school,
                :year_graduated,
                :gwa,
                :entrance_exam_score,
                :admission_type,
                :previous_program,
                :status,
                :notes
              )";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':application_id', $data->application_id);
    $stmt->bindParam(':student_id', $data->student_id);
    $stmt->bindParam(':program_id', $data->program_id);
    $stmt->bindParam(':first_name', $data->first_name);
    $stmt->bindParam(':middle_name', $data->middle_name);
    $stmt->bindParam(':last_name', $data->last_name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':birthdate', $data->birthdate);
    $stmt->bindParam(':gender', $data->gender);
    $stmt->bindParam(':address', $data->address);
    $stmt->bindParam(':high_school', $data->high_school);
    $stmt->bindParam(':last_school', $data->last_school);
    $stmt->bindParam(':year_graduated', $data->year_graduated);
    $stmt->bindParam(':gwa', $data->gwa);
    $stmt->bindParam(':entrance_exam_score', $data->entrance_exam_score);
    $stmt->bindParam(':admission_type', $data->admission_type);
    $stmt->bindParam(':previous_program', $data->previous_program);
    $stmt->bindParam(':status', $data->status);
    $stmt->bindParam(':notes', $data->notes);
    
    if ($stmt->execute()) {
        $newId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Application submitted successfully",
            "id" => $newId,
            "application_id" => $data->application_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to submit application"
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
