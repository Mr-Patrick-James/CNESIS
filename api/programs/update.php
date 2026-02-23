<?php
/**
 * Update Program API
 * Updates an existing program in the database
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
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Log incoming data for debugging
    error_log("Update API - Raw input: " . file_get_contents("php://input"));
    error_log("Update API - Decoded data: " . print_r($data, true));
    
    // Validate required fields
    if (empty($data->id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Program ID is required"]);
        exit;
    }
    
    // Check if program exists
    $checkQuery = "SELECT id FROM programs WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Program not found"]);
        exit;
    }
    
    // Build update query dynamically based on provided fields
    $updateFields = [];
    $params = [':id' => $data->id];
    
    if (isset($data->code)) {
        $updateFields[] = "code = :code";
        $params[':code'] = $data->code;
    }
    if (isset($data->title)) {
        $updateFields[] = "title = :title";
        $params[':title'] = $data->title;
    }
    if (isset($data->short_title)) {
        $updateFields[] = "short_title = :short_title";
        $params[':short_title'] = $data->short_title;
    }
    if (isset($data->category)) {
        $updateFields[] = "category = :category";
        $params[':category'] = $data->category;
    }
    if (isset($data->department)) {
        $updateFields[] = "department = :department";
        $params[':department'] = $data->department;
    }
    if (isset($data->description)) {
        $updateFields[] = "description = :description";
        $params[':description'] = $data->description;
    }
    if (isset($data->duration)) {
        $updateFields[] = "duration = :duration";
        $params[':duration'] = $data->duration;
    }
    if (isset($data->units)) {
        $updateFields[] = "units = :units";
        $params[':units'] = $data->units;
    }
    if (isset($data->image_path)) {
        $updateFields[] = "image_path = :image_path";
        $params[':image_path'] = $data->image_path;
    }
    if (isset($data->prospectus_path)) {
        $updateFields[] = "prospectus_path = :prospectus_path";
        $params[':prospectus_path'] = $data->prospectus_path;
    }
    if (isset($data->enrolled_students)) {
        $updateFields[] = "enrolled_students = :enrolled_students";
        $params[':enrolled_students'] = $data->enrolled_students;
    }
    if (isset($data->status)) {
        $updateFields[] = "status = :status";
        $params[':status'] = $data->status;
    }
    if (isset($data->highlights)) {
        $updateFields[] = "highlights = :highlights";
        $params[':highlights'] = json_encode($data->highlights);
    }
    if (isset($data->career_opportunities)) {
        $updateFields[] = "career_opportunities = :career_opportunities";
        $params[':career_opportunities'] = json_encode($data->career_opportunities);
    }
    if (isset($data->admission_requirements)) {
        $updateFields[] = "admission_requirements = :admission_requirements";
        $params[':admission_requirements'] = json_encode($data->admission_requirements);
    }
    if (isset($data->program_head_name)) {
        $updateFields[] = "program_head_name = :program_head_name";
        $params[':program_head_name'] = $data->program_head_name;
    }
    
    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "No fields to update"]);
        exit;
    }
    
    $query = "UPDATE programs SET " . implode(', ', $updateFields) . " WHERE id = :id";
    $stmt = $db->prepare($query);
    
    // Execute with all parameters
    if ($stmt->execute($params)) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Program updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update program"]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$database->closeConnection();
?>
