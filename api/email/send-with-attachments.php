<?php
/**
 * Email API with Working Attachments
 * Fixes attachment handling while keeping it simple
 */

// Disable error display
error_reporting(0);
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

// Get input
$input = file_get_contents('php://input');
$data = json_decode($input);

if (!$data || !isset($data->recipient_email)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Validate email
$toEmail = filter_var($data->recipient_email, FILTER_SANITIZE_EMAIL);
if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}

$subject = $data->custom_subject ?? 'Test Email';
$message = $data->custom_message ?? 'Test message';

// Add personalization
if (isset($data->admission_id)) {
    $message = "<p>Admission ID: " . htmlspecialchars($data->admission_id) . "</p><p>" . $message . "</p>";
}

// Process attachments
$attachments = [];
if (isset($data->attachments) && is_array($data->attachments)) {
    foreach ($data->attachments as $attachment) {
        $filePath = $attachment['path'];
        
        // Resolve path - only handle uploaded files
        if (strpos($filePath, 'assets/uploads/') === 0) {
            $fullPath = '../' . $filePath;
            
            if (file_exists($fullPath) && is_readable($fullPath)) {
                $attachments[] = [
                    'path' => $fullPath,
                    'name' => $attachment['name'] ?? basename($fullPath)
                ];
            }
        }
    }
}

// Try to send email
$sent = false;
$error = '';
$attachmentCount = count($attachments);

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
        $mail->Body = '<html><body>' . $message . '</body></html>';
        $mail->AltBody = strip_tags($message);
        
        // Add attachments with error handling
        foreach ($attachments as $attachment) {
            try {
                $mail->addAttachment($attachment['path'], $attachment['name']);
                error_log("Successfully attached: " . $attachment['path']);
            } catch (Exception $e) {
                error_log("Failed to attach " . $attachment['path'] . ": " . $e->getMessage());
                // Continue without this attachment
            }
        }
        
        // Send email
        $sent = $mail->send();
        
        if (!$sent) {
            $error = $mail->ErrorInfo;
        }
        
    } else {
        $error = 'PHPMailer not available';
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Email sending error: " . $error);
}

// Return response
echo json_encode([
    'success' => $sent,
    'message' => $sent ? 'Email sent with attachments!' : 'Failed: ' . $error,
    'debug' => [
        'to' => $toEmail,
        'subject' => $subject,
        'attachments_found' => $attachmentCount,
        'attachment_details' => $attachments,
        'phpmailer_available' => file_exists('../../vendor/autoload.php'),
        'error' => $error
    ]
]);
?>
