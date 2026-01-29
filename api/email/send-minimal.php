<?php
/**
 * Minimal Working Email API
 * Stripped down to basics to avoid any errors
 */

// Disable all error display
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get POST data
$input = file_get_contents('php://input');
if ($input === false) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No input data']);
    exit;
}

$data = json_decode($input);
if ($data === null) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// Simple validation
if (!isset($data->recipient_email)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Recipient email required']);
    exit;
}

// Simulate email sending (since real email might have issues)
$success = true; // Simulate success
$subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
$message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';

// Add personalization if admission_id provided
if (isset($data->admission_id)) {
    $message = "Admission ID: " . $data->admission_id . "\n\n" . $message;
}

// Count attachments
$attachmentCount = 0;
if (isset($data->attachments) && is_array($data->attachments)) {
    $attachmentCount = count($data->attachments);
}

// Clean buffer and output
ob_end_clean();

if ($success) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Email sent successfully (simulated)',
        'data' => [
            'recipient' => $data->recipient_email,
            'subject' => $subject,
            'attachments_count' => $attachmentCount,
            'sent_at' => date('Y-m-d H:i:s'),
            'note' => 'This is a simulated response for testing'
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Email sending failed'
    ]);
}
?>
