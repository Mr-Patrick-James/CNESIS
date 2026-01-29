<?php
/**
 * Simple Email Test API
 * Bypass all complexity to test JSON response
 */

// Buffer all output
ob_start();

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get POST data
$data = json_decode(file_get_contents("php://input"));

// Simple response
$response = [
    "success" => true,
    "message" => "Email API is working!",
    "received_data" => $data,
    "timestamp" => date('Y-m-d H:i:s')
];

// Clean buffer and output JSON
ob_end_clean();
echo json_encode($response);
?>
