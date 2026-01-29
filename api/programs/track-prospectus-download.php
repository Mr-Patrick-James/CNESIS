<?php
/**
 * Track Prospectus Download API
 * Records when a user downloads a prospectus file
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
    
    // Validate required fields
    if (empty($data->program_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing program ID"]);
        exit;
    }
    
    // Get user IP and user agent
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Check if program exists
    $checkQuery = "SELECT id FROM programs WHERE id = :program_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':program_id', $data->program_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() == 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Program not found"]);
        exit;
    }
    
    // Track the download using stored procedure
    $query = "CALL sp_track_prospectus_download(:program_id, :user_ip, :user_agent)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':program_id', $data->program_id);
    $stmt->bindParam(':user_ip', $user_ip);
    $stmt->bindParam(':user_agent', $user_agent);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Download tracked successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to track download"]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$database->closeConnection();
?>