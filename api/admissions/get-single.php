<?php
/**
 * Get Single Admission API
 * Retrieves details of a specific admission application
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

// Get admission ID from query parameter (can be numeric ID or application_id string)
$identifier = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($identifier)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Admission ID is required"
    ]);
    exit;
}

try {
    // Check if identifier is numeric (ID) or string (application_id)
    if (is_numeric($identifier)) {
        $query = "SELECT a.*, p.title as program_title 
                 FROM admissions a 
                 LEFT JOIN programs p ON a.program_id = p.id 
                 WHERE a.id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$identifier]);
    } else {
        $query = "SELECT a.*, p.title as program_title 
                 FROM admissions a 
                 LEFT JOIN programs p ON a.program_id = p.id 
                 WHERE a.application_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$identifier]);
    }
    
    $admission = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admission) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "admission" => $admission
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Admission not found"
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error getting admission: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error retrieving admission: " . $e->getMessage()
    ]);
}
?>
