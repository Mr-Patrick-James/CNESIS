<?php
/**
 * Working Email API with Attachments
 * Combines working connectivity with real PHPMailer email sending
 */

// Disable error display but log errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Content-Length: 0");
    exit;
}

// Get and validate input
$input = file_get_contents('php://input');
$data = json_decode($input);

if (!$data || !isset($data->recipient_email)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Prepare email
$toEmail = filter_var($data->recipient_email, FILTER_SANITIZE_EMAIL);
$subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
$message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';

if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Add personalization
if (isset($data->admission_id)) {
    $message = "<p>Admission ID: " . htmlspecialchars($data->admission_id) . "</p><p>" . $message . "</p>";
}

// Process attachments
$attachments = [];
if (isset($data->attachments) && is_array($data->attachments)) {
    foreach ($data->attachments as $attachment) {
        $filePath = $attachment['path'];
        
        // Resolve path correctly
        if (strpos($filePath, 'assets/') === 0) {
            $fullPath = '../' . $filePath;
        } elseif (strpos($filePath, '../') === 0) {
            $fullPath = $filePath;
        } else {
            $fullPath = '../assets/' . $filePath;
        }
        
        if (file_exists($fullPath) && is_readable($fullPath)) {
            $attachments[] = [
                'path' => $fullPath,
                'name' => $attachment['name'] ?? basename($filePath)
            ];
        }
    }
}

// Try to send email
$emailSent = false;
$errorMessage = '';

try {
    // Check if PHPMailer is available
    if (file_exists('../../vendor/autoload.php')) {
        require_once '../../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP Configuration
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
        foreach ($attachments as $attachment) {
            try {
                $mail->addAttachment($attachment['path'], $attachment['name']);
            } catch (Exception $e) {
                error_log("Failed to attach {$attachment['path']}: " . $e->getMessage());
                // Continue without this attachment
            }
        }
        
        // Send email
        $emailSent = $mail->send();
        
        if (!$emailSent) {
            $errorMessage = $mail->ErrorInfo;
        }
        
    } else {
        $errorMessage = 'PHPMailer library not available';
    }
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    error_log("Email sending error: " . $errorMessage);
}

// Return response
echo json_encode([
    'success' => $emailSent,
    'message' => $emailSent ? 'Email sent successfully via Gmail SMTP' : 'Email sending failed: ' . $errorMessage,
    'data' => [
        'recipient' => $toEmail,
        'subject' => $subject,
        'attachments_count' => count($attachments),
        'sent_at' => date('Y-m-d H:i:s'),
        'method' => $emailSent ? 'Gmail SMTP' : 'Failed',
        'debug_info' => [
            'phpmailer_available' => file_exists('../../vendor/autoload.php'),
            'attachments_found' => count($attachments),
            'error_details' => $errorMessage
        ]
    ]
]);
?>
