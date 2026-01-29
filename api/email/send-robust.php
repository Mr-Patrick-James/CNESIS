<?php
/**
 * Robust Real Email API
 * Handles browser requests properly
 */

// Enable error logging but disable display
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any unwanted output
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

// Set headers for POST requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get POST data
$input = file_get_contents('php://input');
if ($input === false) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'No input data received']);
    exit;
}

$data = json_decode($input);
if ($data === null) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data received']);
    exit;
}

// Validate required fields
if (!isset($data->recipient_email)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Recipient email is required']);
    exit;
}

// Prepare email content
$toEmail = filter_var($data->recipient_email, FILTER_SANITIZE_EMAIL);
$subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
$message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';

// Validate email
if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid recipient email address']);
    exit;
}

// Add personalization if admission_id provided
if (isset($data->admission_id)) {
    $message = "<p>Admission ID: " . htmlspecialchars($data->admission_id) . "</p><p>" . $message . "</p>";
}

// Try to send email using PHPMailer
$emailSent = false;
$errorMessage = '';
$attachmentCount = 0;

try {
    // Include PHPMailer
    if (file_exists('../../vendor/autoload.php')) {
        require_once '../../vendor/autoload.php';
        
        // Create PHPMailer instance
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'belugaw6@gmail.com';
        $mail->Password = 'klotmfurniohmmjo';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        // Set charset
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
        
        // Add attachments if provided
        if (isset($data->attachments) && is_array($data->attachments)) {
            foreach ($data->attachments as $attachment) {
                $filePath = '../' . $attachment['path'];
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath, $attachment['name'] ?? basename($filePath));
                    $attachmentCount++;
                }
            }
        }
        
        // Send email
        $emailSent = $mail->send();
        
        if (!$emailSent) {
            $errorMessage = $mail->ErrorInfo;
        }
        
    } else {
        $errorMessage = 'PHPMailer library not found';
    }
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    error_log("Email sending error: " . $e->getMessage());
}

// Clean any output buffer and send response
ob_end_clean();

if ($emailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Email sent successfully via Gmail SMTP',
        'data' => [
            'recipient' => $toEmail,
            'subject' => $subject,
            'attachments_count' => $attachmentCount,
            'sent_at' => date('Y-m-d H:i:s'),
            'method' => 'Gmail SMTP'
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Email sending failed: ' . $errorMessage,
        'debug_info' => [
            'phpmailer_available' => file_exists('../../vendor/autoload.php'),
            'recipient' => $toEmail,
            'subject' => $subject,
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set'
        ]
    ]);
}
?>
