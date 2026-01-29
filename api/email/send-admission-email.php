<?php
/**
 * Email Sending API for Admissions
 * Handles sending various admission-related emails
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
    include_once '../../config/database.php';
    include_once '../../config/email_config.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to load required files: " . $e->getMessage()
    ]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request data"
    ]);
    exit;
}

try {
    $emailConfig = new EmailConfig($db);
    
    // Determine email type and get admission details
    $emailType = $data->email_type ?? 'general';
    $admissionId = $data->admission_id ?? null;
    
    // Get admission details if admission_id is provided
    $admissionDetails = null;
    if ($admissionId) {
        $query = "SELECT a.*, p.title as program_title 
                 FROM admissions a 
                 LEFT JOIN programs p ON a.program_id = p.id 
                 WHERE a.id = ? OR a.application_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$admissionId, $admissionId]);
        $admissionDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get email template
    $templateName = $data->template_name ?? $emailType;
    $template = getEmailTemplate($db, $templateName);
    
    if (!$template) {
        // If no template found, create a simple email
        $subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
        $htmlBody = $data->custom_message ?? 'This is a message from Colegio De Naujan.';
        $textBody = strip_tags($htmlBody);
    } else {
        // Prepare email variables
        $variables = [];
        if ($admissionDetails) {
            $variables = [
                'first_name' => $admissionDetails['first_name'],
                'last_name' => $admissionDetails['last_name'],
                'program_title' => $admissionDetails['program_title'],
                'application_id' => $admissionDetails['application_id'],
                'email' => $admissionDetails['email']
            ];
        }
        
        // Merge with any additional variables provided
        if (isset($data->variables) && is_array($data->variables)) {
            $variables = array_merge($variables, $data->variables);
        }
        
        // Replace template variables
        $subject = replaceTemplateVariables($template['subject'], $variables);
        $htmlBody = replaceTemplateVariables($template['html_body'], $variables);
        $textBody = replaceTemplateVariables($template['text_body'] ?? '', $variables);
    }
    
    // Use custom subject and message if provided
    if (isset($data->custom_subject) && !empty($data->custom_subject)) {
        $subject = $data->custom_subject;
    }
    if (isset($data->custom_message) && !empty($data->custom_message)) {
        $htmlBody = $data->custom_message;
        $textBody = strip_tags($data->custom_message);
    }
    
    // Prepare attachments
    $attachments = [];
    if (isset($data->attachments) && is_array($data->attachments)) {
        foreach ($data->attachments as $attachment) {
            $filePath = '../' . $attachment['path'];
            if (file_exists($filePath)) {
                $attachments[] = [
                    'path' => $filePath,
                    'name' => $attachment['name'] ?? basename($attachment['path'])
                ];
            }
        }
    }
    
    // Send email
    $recipientEmail = $data->recipient_email ?? ($admissionDetails['email'] ?? null);
    $ccEmails = $data->cc_emails ?? [];
    $bccEmails = $data->bcc_emails ?? [];
    
    if (!$recipientEmail) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Recipient email is required"
        ]);
        exit;
    }
    
    $result = $emailConfig->sendEmail($recipientEmail, $subject, $htmlBody, $attachments, $ccEmails, $bccEmails);
    
    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Email sent successfully",
            "data" => [
                "recipient" => $recipientEmail,
                "subject" => $subject,
                "email_type" => $emailType,
                "template_used" => $templateName,
                "sent_at" => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to send email"
        ]);
    }
    
} catch (Exception $e) {
    error_log("Email sending error: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error sending email: " . $e->getMessage()
    ]);
}

/**
 * Get email template from database
 */
function getEmailTemplate($db, $templateName) {
    try {
        // Check if table exists
        $tableCheck = $db->query("SHOW TABLES LIKE 'email_templates'");
        if ($tableCheck->rowCount() === 0) {
            return null; // Table doesn't exist
        }
        
        $query = "SELECT * FROM email_templates WHERE template_name = ? AND is_active = TRUE LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$templateName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null; // Error accessing table
    }
}

/**
 * Replace template variables with actual values
 */
function replaceTemplateVariables($content, $variables) {
    foreach ($variables as $key => $value) {
        $content = str_replace('{{' . $key . '}}', $value, $content);
    }
    return $content;
}

// Clean output buffer and send response
ob_end_clean();
?>
