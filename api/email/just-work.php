<?php
/**
 * Working Email API with PHPMailer and Attachments
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering
ob_start();

/**
 * Generate email content based on email type
 */
function generateEmailContent($emailType, $subject, $message, $data) {
    $content = '<h2>' . htmlspecialchars($subject) . '</h2>';
    
    switch ($emailType) {
        case 'application_received':
            $content .= '
                <p>Dear Applicant,</p>
                <p>Thank you for your interest in Colegio De Naujan. We have successfully received your application.</p>
                <p><strong>Application Details:</strong></p>
                <ul>
                    <li>Application ID: ' . htmlspecialchars($data->admission_id ?? 'N/A') . '</li>
                    <li>Program: ' . htmlspecialchars($data->program_name ?? 'N/A') . '</li>
                    <li>Date Submitted: ' . date('F j, Y') . '</li>
                </ul>
                <p>We will review your application and contact you within 3-5 business days for the next steps.</p>
                <p>If you have any questions, please don\'t hesitate to contact our admissions office.</p>
            ';
            break;
            
        case 'admission_approved':
            $content .= '
                <p>Dear Applicant,</p>
                <p>Congratulations! We are pleased to inform you that your application has been <strong>approved</strong>.</p>
                <p><strong>Approval Details:</strong></p>
                <ul>
                    <li>Application ID: ' . htmlspecialchars($data->admission_id ?? 'N/A') . '</li>
                    <li>Program: ' . htmlspecialchars($data->program_name ?? 'N/A') . '</li>
                    <li>Approval Date: ' . date('F j, Y') . '</li>
                </ul>
                <p><strong>Next Steps:</strong></p>
                <ol>
                    <li>Complete your enrollment within 10 business days</li>
                    <li>Submit required documents</li>
                    <li>Pay the enrollment fee</li>
                    <li>Attend the orientation program</li>
                </ol>
                <p>Welcome to Colegio De Naujan!</p>
            ';
            break;
            
        case 'admission_rejected':
            $content .= '
                <p>Dear Applicant,</p>
                <p>Thank you for your interest in Colegio De Naujan. After careful consideration of your application, we regret to inform you that we are unable to offer you admission at this time.</p>
                <p><strong>Application Details:</strong></p>
                <ul>
                    <li>Application ID: ' . htmlspecialchars($data->admission_id ?? 'N/A') . '</li>
                    <li>Program: ' . htmlspecialchars($data->program_name ?? 'N/A') . '</li>
                    <li>Date Reviewed: ' . date('F j, Y') . '</li>
                </ul>
                <p>We encourage you to apply again in the future. If you would like feedback on your application, please contact our admissions office.</p>
                <p>Wishing you the best in your academic pursuits.</p>
            ';
            break;
            
        case 'document_request':
            $content .= '
                <p>Dear Applicant,</p>
                <p>We require additional documents to complete your application review.</p>
                <p><strong>Required Documents:</strong></p>
                <ul>
                    <li>Birth Certificate (NSO)</li>
                    <li>Form 138 (Report Card)</li>
                    <li>Certificate of Good Moral Character</li>
                    <li>2x2 ID Pictures (2 copies)</li>
                </ul>
                <p>Please submit these documents within 5 business days to avoid delays in processing your application.</p>
                <p>You may submit them personally at our admissions office or email scanned copies to admissions@colegiodenaujan.edu.ph</p>
            ';
            break;
            
        case 'interview_schedule':
            $content .= '
                <p>Dear Applicant,</p>
                <p>Your application has been shortlisted! We would like to schedule an interview with you.</p>
                <p><strong>Interview Details:</strong></p>
                <ul>
                    <li>Date: ' . date('F j, Y', strtotime('+3 days')) . '</li>
                    <li>Time: 10:00 AM</li>
                    <li>Venue: Admissions Office, Colegio De Naujan</li>
                    <li>Duration: Approximately 30 minutes</li>
                </ul>
                <p>Please bring the following documents:</p>
                <ul>
                    <li>Printed application form</li>
                    <li>Valid ID</li>
                    <li>Portfolio (if applicable)</li>
                </ul>
                <p>Please confirm your attendance by replying to this email or calling us at (123) 456-7890.</p>
            ';
            break;
            
        case 'custom':
        default:
            $content .= '<div style="white-space: pre-wrap;">' . nl2br(htmlspecialchars($message)) . '</div>';
            break;
    }
    
    return $content;
}

try {
    error_log("=== EMAIL API START ===");
    
    // Get data
    $input = file_get_contents('php://input');
    error_log("Raw input: " . $input);
    
    $data = json_decode($input);
    error_log("Decoded data: " . print_r($data, true));
    
    if (!$data || !isset($data->recipient_email)) {
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "No email provided"]);
        exit;
    }
    
    $to = $data->recipient_email;
    $emailType = $data->email_type ?? 'custom';
    $subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
    $message = $data->custom_message ?? 'This is a message from Colegio De Naujan.';
    
    error_log("Email details - To: $to, Type: $emailType, Subject: $subject");
    
    // Generate email content based on type
    $emailContent = generateEmailContent($emailType, $subject, $message, $data);
    
    // Try PHPMailer first (better for attachments)
    if (file_exists('../../vendor/autoload.php')) {
        error_log("Loading PHPMailer...");
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
        
        // Set sender
        $mail->setFrom('belugaw6@gmail.com', 'Colegio De Naujan');
        $mail->addReplyTo('belugaw6@gmail.com', 'Colegio De Naujan');
        $mail->addAddress($to);
        
        error_log("SMTP configured, adding attachments...");
        
        // Create HTML email with dynamic content
        $htmlMessage = '
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
                .email-type-badge { background: #1a365d; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; margin-bottom: 20px; display: inline-block; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Colegio De Naujan</h1>
                <p>Official Communication</p>
            </div>
            <div class="content">
                <div class="email-type-badge">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $emailType))) . '</div>
                ' . $emailContent . '
            </div>
            <div class="footer">
                <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>If you have questions, please contact the admissions office directly.</p>
            </div>
        </body>
        </html>';
        
        // Handle attachments
        $attachmentsAdded = 0;
        if (isset($data->attachments) && is_array($data->attachments)) {
            error_log("Processing " . count($data->attachments) . " attachments");
            $htmlMessage .= '<div class="attachment-info"><h3>Attachments:</h3>';
            
            foreach ($data->attachments as $attachment) {
                $filePath = $attachment->path;
                error_log("Processing attachment: " . print_r($attachment, true));
                
                // Handle different path formats
                if (strpos($filePath, '../assets/') === 0) {
                    // Path is relative to API directory, use as-is
                    $fullPath = $filePath;
                } elseif (strpos($filePath, 'assets/') === 0) {
                    // Path is relative to root, add ../ to go to API directory
                    $fullPath = '../' . $filePath;
                } elseif (strpos($filePath, '../') === 0) {
                    // Path already has ../ prefix, use as-is
                    $fullPath = $filePath;
                } else {
                    // Full path or relative path, use as-is
                    $fullPath = $filePath;
                }
                
                // Debug logging
                error_log("Attachment path: $filePath -> Resolved to: $fullPath");
                error_log("File exists: " . (file_exists($fullPath) ? 'YES' : 'NO'));
                error_log("File readable: " . (is_readable($fullPath) ? 'YES' : 'NO'));
                
                if (file_exists($fullPath)) {
                    try {
                        $mail->addAttachment($fullPath, $attachment->name ?? basename($fullPath));
                        $htmlMessage .= '<p>📎 ' . htmlspecialchars($attachment->name ?? basename($fullPath)) . '</p>';
                        $attachmentsAdded++;
                        error_log("Successfully added attachment: $fullPath");
                    } catch (Exception $attachError) {
                        error_log("Failed to add attachment $fullPath: " . $attachError->getMessage());
                    }
                } else {
                    error_log("Attachment file not found: $fullPath");
                }
            }
            
            $htmlMessage .= '</div>';
        } else {
            error_log("No attachments found in data");
        }
        
        $htmlMessage .= '
            </div>
            <div class="footer">
                <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </body>
        </html>';
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlMessage;
        $mail->AltBody = strip_tags($htmlMessage);
        
        error_log("Sending email...");
        $sent = $mail->send();
        error_log("Email send result: " . ($sent ? 'SUCCESS' : 'FAILED'));
        
        if (!$sent) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
        }
        
        $method = "PHPMailer with SMTP";
        
    } else {
        error_log("PHPMailer not found, using mail() function");
        // Fallback to basic mail() function (no attachments)
        $headers = "From: Colegio De Naujan <belugaw6@gmail.com>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $htmlMessage = "<html><body><h2>Colegio De Naujan</h2><p>$message</p><hr><small>Sent: " . date('Y-m-d H:i:s') . "</small></body></html>";
        
        $sent = mail($to, $subject, $htmlMessage, $headers);
        $method = "mail() function";
        $attachmentsAdded = 0;
    }
    
    ob_end_clean();
    
    if ($sent) {
        echo json_encode([
            "success" => true,
            "message" => "Email sent successfully!",
            "method" => $method,
            "attachments_sent" => $attachmentsAdded ?? 0,
            "debug" => [
                "to" => $to,
                "subject" => $subject,
                "message_length" => strlen($message),
                "timestamp" => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Email sending failed",
            "method" => $method,
            "timestamp" => date('Y-m-d H:i:s')
        ]);
    }
    
} catch (Exception $e) {
    error_log("EMAIL API EXCEPTION: " . $e->getMessage());
    error_log("Exception trace: " . $e->getTraceAsString());
    ob_end_clean();
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage(),
        "debug" => [
            "error_type" => get_class($e),
            "error_message" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "timestamp" => date('Y-m-d H:i:s')
        ]
    ]);
}

error_log("=== EMAIL API END ===");
?>
