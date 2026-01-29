<?php
/**
 * Create New Program API
 * Adds a new program to the database
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
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Log incoming data for debugging
    error_log("Create API - Raw input: " . file_get_contents("php://input"));
    error_log("Create API - Decoded data: " . print_r($data, true));
    
    // Validate required fields
    if (
        empty($data->code) ||
        empty($data->title) ||
        empty($data->short_title) ||
        empty($data->category) ||
        empty($data->department) ||
        empty($data->description) ||
        empty($data->duration) ||
        empty($data->units)
    ) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }
    
    // Check if program code already exists
    $checkQuery = "SELECT id FROM programs WHERE code = :code";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':code', $data->code);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["success" => false, "message" => "Program code already exists"]);
        exit;
    }
    
    // Prepare insert query
    $query = "INSERT INTO programs (
                code, title, short_title, category, department, description,
                duration, units, image_path, prospectus_path, enrolled_students,
                status, highlights, career_opportunities, admission_requirements,
                program_head_id
              ) VALUES (
                :code, :title, :short_title, :category, :department, :description,
                :duration, :units, :image_path, :prospectus_path, :enrolled_students,
                :status, :highlights, :career_opportunities, :admission_requirements,
                :program_head_id
              )";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':code', $data->code);
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':short_title', $data->short_title);
    $stmt->bindParam(':category', $data->category);
    $stmt->bindParam(':department', $data->department);
    $stmt->bindParam(':description', $data->description);
    $stmt->bindParam(':duration', $data->duration);
    $stmt->bindParam(':units', $data->units);
    
    $imagePath = isset($data->image_path) ? $data->image_path : null;
    $prospectusPath = isset($data->prospectus_path) ? $data->prospectus_path : null;
    $enrolledStudents = isset($data->enrolled_students) ? $data->enrolled_students : 0;
    $status = isset($data->status) ? $data->status : 'active';
    $programHeadId = isset($data->program_head_id) ? $data->program_head_id : null;
    
    $stmt->bindParam(':image_path', $imagePath);
    $stmt->bindParam(':prospectus_path', $prospectusPath);
    $stmt->bindParam(':enrolled_students', $enrolledStudents);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':program_head_id', $programHeadId);
    
    // Convert arrays to JSON
    $highlights = isset($data->highlights) ? json_encode($data->highlights) : json_encode([]);
    $career_opportunities = isset($data->career_opportunities) ? json_encode($data->career_opportunities) : json_encode([]);
    $admission_requirements = isset($data->admission_requirements) ? json_encode($data->admission_requirements) : json_encode([]);
    
    $stmt->bindParam(':highlights', $highlights);
    $stmt->bindParam(':career_opportunities', $career_opportunities);
    $stmt->bindParam(':admission_requirements', $admission_requirements);
    
    // Execute query
    if ($stmt->execute()) {
        $programId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Program created successfully",
            "program_id" => $programId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to create program"]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$database->closeConnection();
?>
