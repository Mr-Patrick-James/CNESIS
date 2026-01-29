<?php
/**
 * Simple Working Email API - With Proper Debugging
 * Shows exactly what's happening instead of generic errors
 */

// Disable error display but enable output buffering
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Get data safely
$input = file_get_contents('php://input');
$data = json_decode($input);

// Debug: Log what we received
error_log("=== EMAIL API DEBUG ===");
error_log("Raw input length: " . strlen($input));
error_log("JSON decode result: " . ($data ? "SUCCESS" : "FAILED"));
error_log("JSON error: " . json_last_error_msg());

if (!$data || !isset($data->recipient_email)) {
    ob_end_clean();
    echo json_encode([
        "success" => false,
        "message" => "No email provided",
        "debug" => [
            "input_received" => strlen($input) > 0,
            "json_parsed" => $data !== null,
            "data_keys" => $data ? array_keys((array)$data) : [],
            "json_error" => json_last_error_msg()
        ]
    ]);
    exit;
}

$toEmail = $data->recipient_email;
$subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
$message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';

// Debug: Log email details
error_log("Email details:");
error_log("To: " . $toEmail);
error_log("Subject: " . $subject);
error_log("Message length: " . strlen($message));

// Add HTML wrapper
$htmlMessage = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($subject) . '</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #1a365d; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1>Colegio De Naujan</h1>
        <p>Official Communication</p>
    </div>
    <div style="padding: 20px; background: #f9f9f9; border: 1px solid #ddd;">
        ' . $message . '
    </div>
    <div style="background: #2c5282; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px;">
        <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
    </div>
</body>
</html>';

// Process attachments
$attachments = [];
if (isset($data->attachments) && is_array($data->attachments)) {
    error_log("Processing " . count($data->attachments) . " attachments");
    
    foreach ($data->attachments as $attachment) {
        $filePath = $attachment['path'];
        
        // Fix path
        if (strpos($filePath, 'assets/') === 0) {
            $fullPath = '../' . $filePath;
        } else {
            $fullPath = $filePath;
        }
        
        $attachmentInfo = [
            'original_path' => $attachment['path'],
            'resolved_path' => $fullPath,
            'file_exists' => file_exists($fullPath),
            'is_readable' => file_exists($fullPath) ? is_readable($fullPath) : false,
            'name' => $attachment['name'] ?? basename($fullPath)
        ];
        
        error_log("Attachment debug: " . print_r($attachmentInfo, true));
        
        if ($attachmentInfo['file_exists'] && $attachmentInfo['is_readable']) {
            $attachments[] = [
                'path' => $fullPath,
                'name' => $attachmentInfo['name']
            ];
        }
    }
    
    error_log("Valid attachments: " . count($attachments));
} else {
    error_log("No attachments found");
}

// Send email with proper error handling
$result = false;
$error = '';
$debugInfo = [
    "step" => "initial_setup",
    "email" => $toEmail,
    "subject" => $subject,
    "attachments_count" => count($attachments),
    "php_version" => PHP_VERSION,
    "server_method" => $_SERVER['REQUEST_METHOD'],
    "content_type" => $_SERVER['CONTENT_TYPE'] ?? 'not set'
];

try {
    // Check if PHPMailer exists
    $phpmailerPath = '../../vendor/autoload.php';
    $debugInfo["phpmailer_exists"] = file_exists($phpmailerPath);
    $debugInfo["phpmailer_path"] = $phpmailerPath;
    
    error_log("PHPMailer check: " . ($debugInfo["phpmailer_exists"] ? "EXISTS" : "NOT FOUND"));
    
    if (!$debugInfo["phpmailer_exists"]) {
        throw new Exception('PHPMailer not found at ' . $phpmailerPath);
    }
    
    require_once $phpmailerPath;
    $debugInfo["step"] = "phpmailer_loaded";
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $debugInfo["step"] = "phpmailer_created";
    
    error_log("PHPMailer instance created");
    
    // SMTP setup
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'belugaw6@gmail.com';
    $mail->Password = 'klotmfurniohmmjo';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    $debugInfo["step"] = "smtp_configured";
    error_log("SMTP configured");
    
    // From/To
    $mail->setFrom('belugaw6@gmail.com', 'Colegio De Naujan');
    $mail->addAddress($toEmail);
    
    $debugInfo["step"] = "recipients_set";
    error_log("Recipients set");
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlMessage;
    
    $debugInfo["step"] = "content_set";
    error_log("Content set");
    
    // Add attachments with error handling
    foreach ($attachments as $i => $attachment) {
        try {
            $mail->addAttachment($attachment['path'], $attachment['name']);
            error_log("Attachment $i added successfully: " . $attachment['name']);
        } catch (Exception $e) {
            error_log("Attachment $i failed: " . $e->getMessage());
            $debugInfo["attachment_" . $i . "_error"] = $e->getMessage();
        }
    }
    
    $debugInfo["step"] = "attachments_processed";
    error_log("About to send email...");
    
    // Send email
    $result = $mail->send();
    $debugInfo["step"] = "email_sent";
    
    if (!$result) {
        $error = $mail->ErrorInfo;
        $debugInfo["phpmailer_error"] = $error;
        error_log("Email send failed: " . $error);
    } else {
        error_log("Email sent successfully!");
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $debugInfo["exception"] = $error;
    $debugInfo["exception_trace"] = $e->getTraceAsString();
    error_log("Email sending exception: " . $error);
}

// Clean output buffer and send response
ob_end_clean();

$debugInfo["final_result"] = $result;
$debugInfo["final_error"] = $error;

if ($result) {
    echo json_encode([
        "success" => true,
        "message" => "Email sent successfully!",
        "attachments_sent" => count($attachments),
        "debug" => $debugInfo
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Email sending failed: " . $error,
        "debug" => $debugInfo
    ]);
}
?>
