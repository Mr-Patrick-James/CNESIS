<?php
/**
 * Update Admission Status API
 * Handles updating admission status and sending emails
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../config/email_config.php';

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

if (!$data || !isset($data->admission_id) || !isset($data->status)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Admission ID and status are required"
    ]);
    exit;
}

try {
    // Get admission details before updating
    $query = "SELECT a.*, p.title as program_title 
             FROM admissions a 
             LEFT JOIN programs p ON a.program_id = p.id 
             WHERE a.id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->admission_id]);
    $admission = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admission) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Admission not found"
        ]);
        exit;
    }
    
    // Update admission status
    $updateQuery = "UPDATE admissions SET 
                    status = ?, 
                    reviewed_at = NOW(),
                    reviewed_by = 1, -- Assuming admin user ID 1
                    notes = COALESCE(notes, '') || ? 
                    WHERE id = ?";
    
    $notes = isset($data->notes) ? "\n\n" . date('Y-m-d H:i:s') . ": " . $data->notes : "";
    $updateStmt = $db->prepare($updateQuery);
    $updateResult = $updateStmt->execute([$data->status, $notes, $data->admission_id]);
    
    if (!$updateResult) {
        throw new Exception("Failed to update admission status");
    }
    
    // Send email if requested
    $emailSent = false;
    $emailError = null;
    
    if (isset($data->send_email) && $data->send_email) {
        try {
            $emailConfig = new EmailConfig($db);
            
            // Get email template based on status
            $templateName = $data->status === 'approved' ? 'admission_approved' : 
                           ($data->status === 'rejected' ? 'admission_rejected' : null);
            
            if ($templateName) {
                $template = getEmailTemplate($db, $templateName);
                
                if ($template) {
                    // Prepare email variables
                    $variables = [
                        'first_name' => $admission['first_name'],
                        'last_name' => $admission['last_name'],
                        'program_title' => $admission['program_title'],
                        'application_id' => $admission['application_id'],
                        'email' => $admission['email']
                    ];
                    
                    // Replace template variables
                    $subject = replaceTemplateVariables($template['subject'], $variables);
                    $htmlBody = replaceTemplateVariables($template['html_body'], $variables);
                    $textBody = replaceTemplateVariables($template['text_body'] ?? '', $variables);
                    
                    // Send email
                    $emailSent = $emailConfig->sendEmail($admission['email'], $subject, $htmlBody);
                    
                    if (!$emailSent) {
                        $emailError = "Email sending failed";
                    }
                }
            }
        } catch (Exception $e) {
            $emailError = $e->getMessage();
        }
    }
    
    // Log the action
    $logQuery = "INSERT INTO admission_status_log (admission_id, old_status, new_status, action_by, notes, email_sent, created_at) 
                 VALUES (?, ?, ?, 1, ?, ?, NOW())";
    $logStmt = $db->prepare($logQuery);
    $logStmt->execute([
        $data->admission_id,
        $admission['status'],
        $data->status,
        $data->notes ?? '',
        $emailSent ? 1 : 0
    ]);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Admission status updated successfully",
        "data" => [
            "admission_id" => $data->admission_id,
            "old_status" => $admission['status'],
            "new_status" => $data->status,
            "email_sent" => $emailSent,
            "email_error" => $emailError,
            "updated_at" => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Update admission status error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error updating admission status: " . $e->getMessage()
    ]);
}

/**
 * Get email template from database
 */
function getEmailTemplate($db, $templateName) {
    $query = "SELECT * FROM email_templates WHERE template_name = ? AND is_active = TRUE LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$templateName]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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
?>
