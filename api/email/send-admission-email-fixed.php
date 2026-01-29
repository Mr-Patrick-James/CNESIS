<?php
/**
 * Fixed Email Sending API for Admissions
 * Uses PHPMailer with proper error handling
 */

// Buffer output to prevent HTML in JSON response
ob_start();

// Disable error display for JSON responses
ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Log any errors instead of displaying them
function errorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Email API Error: [$errno] $errstr in $errfile on line $errline");
    return true;
}
set_error_handler('errorHandler');

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"));

    if (!$data || !isset($data->recipient_email)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Recipient email is required"
        ]);
        exit;
    }

    // Validate email
    if (!filter_var($data->recipient_email, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format"
        ]);
        exit;
    }

    // Use fallback configuration directly (no database dependency)
    $config = [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_username' => 'belugaw6@gmail.com',
        'smtp_password' => 'klotmfurniohmmjo',
        'encryption_type' => 'tls',
        'from_email' => 'belugaw6@gmail.com',
        'from_name' => 'Colegio De Naujan',
        'reply_to_email' => 'belugaw6@gmail.com'
    ];

    // Load PHPMailer
    if (!file_exists('../../vendor/autoload.php')) {
        throw new Exception("PHPMailer library not found");
    }
    
    require_once '../../vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    // Configure SMTP
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = $config['encryption_type'];
    $mail->Port = $config['smtp_port'];
    $mail->CharSet = 'UTF-8';
    
    // Set sender
    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addReplyTo($config['reply_to_email'], $config['from_name']);
    
    // Add recipient
    $mail->addAddress($data->recipient_email);
    
    // Add CC/BCC if provided
    if (isset($data->cc_emails) && is_array($data->cc_emails)) {
        foreach ($data->cc_emails as $cc) {
            if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                $mail->addCC($cc);
            }
        }
    }
    
    if (isset($data->bcc_emails) && is_array($data->bcc_emails)) {
        foreach ($data->bcc_emails as $bcc) {
            if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                $mail->addBCC($bcc);
            }
        }
    }
    
    // Prepare email content
    $subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
    $message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';
    
    // Create professional HTML email
    $htmlBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($subject) . '</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
            .header { background: #1a365d; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .header h1 { margin: 0; font-size: 28px; }
            .content { padding: 30px; background: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
            .content h2 { color: #1a365d; margin-top: 0; }
            .footer { background: #2c5282; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
            .attachment-info { background: #e2e8f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Colegio De Naujan</h1>
            <p>Official Communication</p>
        </div>
        <div class="content">
            <h2>' . htmlspecialchars($subject) . '</h2>
            <div style="white-space: pre-wrap;">' . nl2br(htmlspecialchars($message)) . '</div>';
    
    // Add attachment info if any
    $attachmentsAdded = 0;
    if (isset($data->attachments) && is_array($data->attachments)) {
        $htmlBody .= '<div class="attachment-info"><h3>Attachments:</h3>';
        
        foreach ($data->attachments as $attachment) {
            $filePath = $attachment['path'];
            
            // Resolve path
            if (strpos($filePath, 'assets/') === 0) {
                $fullPath = '../' . $filePath;
            } else {
                $fullPath = $filePath;
            }
            
            if (file_exists($fullPath)) {
                try {
                    $mail->addAttachment($fullPath, $attachment['name'] ?? basename($fullPath));
                    $htmlBody .= '<p>📎 ' . htmlspecialchars($attachment['name'] ?? basename($fullPath)) . '</p>';
                    $attachmentsAdded++;
                } catch (Exception $attachError) {
                    error_log("Failed to add attachment $fullPath: " . $attachError->getMessage());
                }
            } else {
                error_log("Attachment file not found: $fullPath");
            }
        }
        
        $htmlBody .= '</div>';
    }
    
    $htmlBody .= '
        </div>
        <div class="footer">
            <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have questions, please contact the admissions office directly.</p>
        </div>
    </body>
    </html>';
    
    // Set content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody);
    
    // Send email
    $result = $mail->send();
    
    ob_end_clean();
    
    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Email sent successfully",
            "data" => [
                "recipient" => $data->recipient_email,
                "subject" => $subject,
                "attachments_sent" => $attachmentsAdded,
                "message_id" => $mail->getLastMessageID(),
                "sent_at" => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception("SMTP Error: " . $mail->ErrorInfo);
    }
    
} catch (Exception $e) {
    error_log("Email sending error: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error sending email: " . $e->getMessage(),
        "debug" => [
            "error_type" => get_class($e),
            "error_message" => $e->getMessage(),
            "timestamp" => date('Y-m-d H:i:s'),
            "smtp_config" => [
                "host" => $config['smtp_host'] ?? 'not set',
                "port" => $config['smtp_port'] ?? 'not set',
                "username" => $config['smtp_username'] ?? 'not set',
                "encryption" => $config['encryption_type'] ?? 'not set'
            ]
        ]
    ]);
}

// Clean output buffer and send response
ob_end_clean();
?>
