<?php
/**
 * Debug Attachment Email API
 * Shows what's happening with attachments
 */

// Enable error logging but disable display
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering
ob_start();

// Handle OPTIONS request for CORS
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

// Debug: Log what we received
error_log("Email API Debug - Received data: " . print_r($data, true));

if (!$data || !isset($data->recipient_email)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request data', 'debug' => ['received' => $data]]);
    exit;
}

// Prepare email content
$toEmail = filter_var($data->recipient_email, FILTER_SANITIZE_EMAIL);
$subject = $data->custom_subject ?? 'Test Email';
$message = $data->custom_message ?? 'Test message';

// Debug attachments
$attachmentDebug = [];
$attachmentCount = 0;

if (isset($data->attachments) && is_array($data->attachments)) {
    foreach ($data->attachments as $i => $attachment) {
        $filePath = '../' . $attachment['path'];
        $attachmentDebug[$i] = [
            'original_path' => $attachment['path'],
            'full_path' => $filePath,
            'file_exists' => file_exists($filePath),
            'is_readable' => file_exists($filePath) ? is_readable($filePath) : false,
            'file_size' => file_exists($filePath) ? filesize($filePath) : 'N/A'
        ];
        
        if (file_exists($filePath) && is_readable($filePath)) {
            $attachmentCount++;
        }
    }
}

// Try to send email without attachments first
$emailSent = false;
$errorMessage = '';

try {
    if (file_exists('../../vendor/autoload.php')) {
        require_once '../../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'belugaw6@gmail.com';
        $mail->Password = 'klotmfurniohmmjo';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Recipients
        $mail->setFrom('belugaw6@gmail.com', 'Colegio De Naujan');
        $mail->addAddress($toEmail);
        $mail->addReplyTo('belugaw6@gmail.com', 'Colegio De Naujan');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // Add attachments with error handling
        if (isset($data->attachments) && is_array($data->attachments)) {
            foreach ($data->attachments as $attachment) {
                $filePath = '../' . $attachment['path'];
                $fileName = $attachment['name'] ?? basename($filePath);
                
                try {
                    if (file_exists($filePath) && is_readable($filePath)) {
                        $mail->addAttachment($filePath, $fileName);
                        error_log("Successfully attached: $filePath");
                    } else {
                        error_log("Failed to attach - file not found or not readable: $filePath");
                    }
                } catch (Exception $e) {
                    error_log("Attachment error for $filePath: " . $e->getMessage());
                    // Continue without this attachment
                }
            }
        }
        
        // Send email
        $emailSent = $mail->send();
        
        if (!$emailSent) {
            $errorMessage = $mail->ErrorInfo;
            error_log("PHPMailer Error: " . $errorMessage);
        }
        
    } else {
        $errorMessage = 'PHPMailer not found';
    }
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    error_log("Email sending exception: " . $errorMessage);
}

// Clean buffer and send response
ob_end_clean();

echo json_encode([
    'success' => $emailSent,
    'message' => $emailSent ? 'Email sent successfully' : 'Email sending failed: ' . $errorMessage,
    'debug_info' => [
        'recipient' => $toEmail,
        'subject' => $subject,
        'attachment_count' => $attachmentCount,
        'attachment_details' => $attachmentDebug,
        'phpmailer_available' => file_exists('../../vendor/autoload.php'),
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'error_message' => $errorMessage
    ]
]);
?>
