<?php
/**
 * Professional Email API
 * Uses EmailService class for robust email sending
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Content-Length: 0");
    exit;
}

// Include dependencies
require_once '../../config/database.php';
require_once 'EmailService.php';

// Initialize services
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        throw new Exception("Database connection failed");
    }
    
    $emailService = new EmailService($db);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Service initialization failed: " . $e->getMessage(),
        "debug" => [
            "database_connected" => isset($db),
            "error" => $e->getMessage()
        ]
    ]);
    exit;
}

// Get and validate request
try {
    $input = file_get_contents('php://input');
    
    if ($input === false) {
        throw new Exception("No input data received");
    }
    
    $data = json_decode($input);
    
    if ($data === null) {
        throw new Exception("Invalid JSON data: " . json_last_error_msg());
    }
    
    // Send email
    $result = $emailService->sendEmail($data);
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Email sent successfully",
        "data" => [
            "recipient" => $data->recipient_email,
            "subject" => $data->custom_subject ?? 'Message from Colegio De Naujan',
            "attachments_count" => $result['attachments_sent'] ?? 0,
            "sent_at" => date('Y-m-d H:i:s'),
            "message_id" => $result['message_id'] ?? null,
            "professional" => true
        ]
    ]);
    
} catch (EmailServiceException $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "debug" => [
            "error_type" => "EmailServiceException",
            "timestamp" => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error: " . $e->getMessage(),
        "debug" => [
            "error_type" => "Exception",
            "timestamp" => date('Y-m-d H:i:s'),
            "trace" => $e->getTraceAsString()
        ]
    ]);
}
?>
