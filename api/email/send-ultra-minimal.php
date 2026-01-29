<?php
/**
 * Ultra Minimal Email Test
 * Bypasses everything to just return JSON
 */

// Disable ALL error reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// Set headers FIRST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Content-Length: 0");
    exit;
}

// Get input safely
$input = file_get_contents('php://input');
if ($input === false) {
    echo json_encode(['success' => false, 'message' => 'No input']);
    exit;
}

// Parse JSON safely
$data = json_decode($input);
if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Bad JSON', 'input' => $input]);
    exit;
}

// Return success with debug info
echo json_encode([
    'success' => true,
    'message' => 'Ultra minimal test working',
    'debug' => [
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'none',
        'input_length' => strlen($input),
        'data_keys' => $data ? array_keys((array)$data) : [],
        'has_attachments' => isset($data->attachments) && is_array($data->attachments),
        'attachment_count' => isset($data->attachments) && is_array($data->attachments) ? count($data->attachments) : 0
    ]
]);
?>
