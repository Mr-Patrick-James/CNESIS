<?php
/**
 * Ultra Minimal Email API
 * Stripped down to absolute basics
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

// Get input
$input = file_get_contents('php://input');
if ($input === false) {
    echo json_encode(['success' => false, 'message' => 'No input']);
    exit;
}

$data = json_decode($input);
if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Bad JSON']);
    exit;
}

// Validate email
if (!isset($data->recipient_email)) {
    echo json_encode(['success' => false, 'message' => 'No email']);
    exit;
}

$toEmail = $data->recipient_email;
$subject = $data->custom_subject ?? 'Test Email';
$message = $data->custom_message ?? 'Test message';

// Try to send email using mail() function as fallback
$sent = false;
$error = '';

try {
    // Try PHPMailer first
    if (file_exists('../../vendor/autoload.php')) {
        require_once '../../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Basic SMTP config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'belugaw6@gmail.com';
        $mail->Password = 'klotmfurniohmmjo';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('belugaw6@gmail.com', 'Colegio De Naujan');
        $mail->addAddress($toEmail);
        
        $mail->Subject = $subject;
        $mail->Body = '<html><body>' . $message . '</body></html>';
        $mail->AltBody = strip_tags($message);
        
        $sent = $mail->send();
        if (!$sent) {
            $error = $mail->ErrorInfo;
        }
    } else {
        $error = 'PHPMailer not found';
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

// If PHPMailer failed, try basic mail()
if (!$sent && empty($error)) {
    $headers = "From: Colegio De Naujan <belugaw6@gmail.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $htmlMessage = '<html><body>' . $message . '</body></html>';
    
    $sent = mail($toEmail, $subject, $htmlMessage, $headers);
    if (!$sent) {
        $error = 'mail() function failed';
    }
}

// Return response
echo json_encode([
    'success' => $sent,
    'message' => $sent ? 'Email sent!' : 'Failed: ' . $error,
    'debug' => [
        'to' => $toEmail,
        'subject' => $subject,
        'phpmailer_available' => file_exists('../../vendor/autoload.php'),
        'error' => $error
    ]
]);
?>
