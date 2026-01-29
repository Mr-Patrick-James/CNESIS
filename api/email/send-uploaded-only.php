<?php
/**
 * Simple Email API - Only Uploaded Files
 * No predefined documents, only admin-uploaded files
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

// Prepare email
$subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
$message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';

// Add personalization
if (isset($data->admission_id)) {
    $message = "<p>Admission ID: " . htmlspecialchars($data->admission_id) . "</p><p>" . $message . "</p>";
}

// Process attachments (only uploaded files)
$attachments = [];
if (isset($data->attachments) && is_array($data->attachments)) {
    foreach ($data->attachments as $attachment) {
        // Only handle uploaded files (assets/uploads/ path)
        if (strpos($attachment['path'], 'assets/uploads/') === 0) {
            $fullPath = '../' . $attachment['path'];
            
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
        
        // Add attachments
        foreach ($attachments as $attachment) {
            try {
                $mail->addAttachment($attachment['path'], $attachment['name']);
            } catch (Exception $e) {
                error_log("Attachment error: " . $e->getMessage());
            }
        }
        
        // Send email
        $emailSent = $mail->send();
        
        if (!$emailSent) {
            $errorMessage = $mail->ErrorInfo;
        }
        
    } else {
        $errorMessage = 'PHPMailer not available';
    }
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}

// Return response
echo json_encode([
    'success' => $emailSent,
    'message' => $emailSent ? 'Email sent successfully!' : 'Failed: ' . $errorMessage,
    'data' => [
        'recipient' => $toEmail,
        'subject' => $subject,
        'attachments_count' => count($attachments),
        'sent_at' => date('Y-m-d H:i:s')
    ]
]);
?>
