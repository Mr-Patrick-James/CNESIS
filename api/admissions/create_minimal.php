<?php
/**
 * Minimal Admission API Test
 * Stripped down version to isolate the JSON issue
 */

// Start output buffering immediately
ob_start();

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Simple test response first
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

// Get input
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

if (!$data) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid JSON", "debug" => $rawInput]);
    exit;
}

// Test basic validation
if (empty($data->email) || empty($data->first_name)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Try database connection (minimal)
try {
    include_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }
    
    // Simple test query
    $stmt = $db->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    if (!$result || $result['test'] != 1) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database query failed"]);
        exit;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    exit;
}

// Success response
http_response_code(200);
echo json_encode([
    "success" => true,
    "message" => "Minimal API test successful",
    "data" => [
        "email" => $data->email,
        "first_name" => $data->first_name,
        "timestamp" => date('Y-m-d H:i:s')
    ]
]);

// Clean output buffer
ob_end_clean();
?>
