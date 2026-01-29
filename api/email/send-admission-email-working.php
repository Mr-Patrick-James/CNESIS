<?php
/**
 * Working Email API
 * Simplified version that handles missing dependencies
 */

// Buffer all output
ob_start();

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid request data"
    ]);
    exit;
}

try {
    // Try to load dependencies
    $databaseLoaded = false;
    $emailConfigLoaded = false;
    
    if (file_exists('../../config/database.php')) {
        include_once '../../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        if ($db) {
            $databaseLoaded = true;
        }
    }
    
    if (file_exists('../../config/email_config.php') && $databaseLoaded) {
        include_once '../../config/email_config.php';
        $emailConfig = new EmailConfig($db);
        $emailConfigLoaded = true;
    }
    
    // Get admission details if provided
    $admissionDetails = null;
    if ($databaseLoaded && isset($data->admission_id)) {
        $query = "SELECT a.*, p.title as program_title 
                 FROM admissions a 
                 LEFT JOIN programs p ON a.program_id = p.id 
                 WHERE a.id = ? OR a.application_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$data->admission_id, $data->admission_id]);
        $admissionDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Prepare email content
    $subject = $data->custom_subject ?? 'Message from Colegio De Naujan';
    $htmlBody = $data->custom_message ?? 'This is a message from Colegio De Naujan.';
    
    // Add personalization if we have admission details
    if ($admissionDetails) {
        $htmlBody = "<p>Dear {$admissionDetails['first_name']} {$admissionDetails['last_name']},</p><p>" . $htmlBody . "</p>";
        $subject = str_replace('{{first_name}}', $admissionDetails['first_name'], $subject);
        $subject = str_replace('{{last_name}}', $admissionDetails['last_name'], $subject);
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
    
    // Try to send email if email config is loaded
    $emailSent = false;
    $errorMessage = null;
    
    if ($emailConfigLoaded) {
        try {
            $recipientEmail = $data->recipient_email ?? ($admissionDetails['email'] ?? null);
            if ($recipientEmail) {
                $emailSent = $emailConfig->sendEmail($recipientEmail, $subject, $htmlBody, $attachments);
            } else {
                $errorMessage = "No recipient email provided";
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    } else {
        $errorMessage = "Email configuration not available";
    }
    
    // Return response
    ob_end_clean();
    
    if ($emailSent) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Email sent successfully",
            "data" => [
                "recipient" => $data->recipient_email,
                "subject" => $subject,
                "attachments_count" => count($attachments),
                "sent_at" => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Email sending failed: " . ($errorMessage ?? "Unknown error"),
            "debug_info" => [
                "database_loaded" => $databaseLoaded,
                "email_config_loaded" => $emailConfigLoaded,
                "admission_found" => $admissionDetails !== null
            ]
        ]);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
