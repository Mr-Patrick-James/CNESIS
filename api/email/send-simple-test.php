<?php
/**
 * Simple Attachment Test API
 * Tests attachment handling without PHPMailer
 */

// Disable error display
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Content-Length: 0");
    header("HTTP/1.1 200 OK");
    exit;
}

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input);

// Validate
if (!$data || !isset($data->recipient_email)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Test attachment handling
$attachmentResults = [];
$attachmentCount = 0;

if (isset($data->attachments) && is_array($data->attachments)) {
    foreach ($data->attachments as $i => $attachment) {
        // Handle both relative paths and full paths
        $filePath = $attachment['path'];
        
        // If path starts with 'assets/', it's already relative to web root
        // If path starts with '../', it's already relative to API location
        // Otherwise, assume it's relative to web root
        
        if (strpos($filePath, 'assets/') === 0) {
            $fullPath = '../' . $filePath; // API is in api/, so go up one level
        } elseif (strpos($filePath, '../') === 0) {
            $fullPath = $filePath; // Already relative to API location
        } else {
            $fullPath = '../assets/' . $filePath; // Assume it's in assets/
        }
        
        $fileName = $attachment['name'] ?? basename($filePath);
        
        $result = [
            'index' => $i,
            'original_path' => $attachment['path'],
            'resolved_path' => $fullPath,
            'file_name' => $fileName,
            'file_exists' => file_exists($fullPath),
            'is_readable' => false,
            'file_size' => 0,
            'mime_type' => 'unknown'
        ];
        
        if ($result['file_exists']) {
            $result['is_readable'] = is_readable($fullPath);
            $result['file_size'] = filesize($fullPath);
            $result['mime_type'] = mime_content_type($fullPath) ?: 'unknown';
            
            if ($result['is_readable']) {
                $attachmentCount++;
            }
        }
        
        $attachmentResults[] = $result;
    }
}

// Simulate email sending (no actual email)
$success = true; // Always succeed for testing
$subject = $data->custom_subject ?? 'Test Email';
$message = $data->custom_message ?? 'Test message';

// Clean buffer and respond
ob_end_clean();

echo json_encode([
    'success' => $success,
    'message' => 'Email test completed (no actual email sent)',
    'debug_info' => [
        'recipient' => $data->recipient_email,
        'subject' => $subject,
        'attachment_count' => $attachmentCount,
        'attachments' => $attachmentResults,
        'php_version' => PHP_VERSION,
        'server_method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set'
    ]
]);
?>
