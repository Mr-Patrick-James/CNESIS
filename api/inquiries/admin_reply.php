<?php
/**
 * Admin Reply API
 * Sends a message from admin to student
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';
include_once 'db_init.php';
include_once '../email/EmailService.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Ensure tables exist
initializeInquiryTables($db);

try {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || empty($data->inquiry_id) || empty($data->message)) {
        throw new Exception("Missing required fields");
    }

    $db->beginTransaction();

    // 1. Get student info for email notification
    $inquiryQuery = "SELECT full_name, email FROM inquiries WHERE id = :id";
    $inquiryStmt = $db->prepare($inquiryQuery);
    $inquiryStmt->execute(['id' => $data->inquiry_id]);
    $inquiry = $inquiryStmt->fetch(PDO::FETCH_ASSOC);

    if (!$inquiry) {
        throw new Exception("Inquiry not found");
    }

    // 2. Add admin message to the conversation
    $msgQuery = "INSERT INTO inquiry_messages (inquiry_id, sender_type, message, is_read) 
                 VALUES (:inquiry_id, 'admin', :message, 1)";
    $msgStmt = $db->prepare($msgQuery);
    $msgStmt->execute([
        'inquiry_id' => $data->inquiry_id,
        'message' => $data->message
    ]);

    // 3. Update inquiry status to 'responded'
    $updateQuery = "UPDATE inquiries SET status = 'responded' WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute(['id' => $data->inquiry_id]);

    $db->commit();

    // 4. Send email notification to student
    $emailSent = false;
    try {
        $emailService = new EmailService($db);
        $emailData = new stdClass();
        $emailData->recipient_email = $inquiry['email'];
        $emailData->custom_subject = "Reply to your Admission Inquiry - Colegio De Naujan";
        $emailData->custom_message = "Dear " . $inquiry['full_name'] . ",\n\n" .
                                   "An administrator has responded to your inquiry:\n\n" .
                                   "\"" . $data->message . "\"\n\n" .
                                   "You can also view the full conversation and reply back on our website:\n" .
                                   "Go to the Admission page and click 'Check for replies'.\n\n" .
                                   "Best regards,\n" .
                                   "Admission Office\n" .
                                   "Colegio De Naujan";
        $emailData->email_type = 'inquiry_reply';
        
        $emailService->sendEmail($emailData);
        $emailSent = true;
    } catch (Exception $e) {
        error_log("Failed to send inquiry reply email: " . $e->getMessage());
    }

    echo json_encode([
        "success" => true,
        "message" => "Reply sent successfully" . ($emailSent ? " and email notification sent" : " (email notification failed)"),
        "email_sent" => $emailSent
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
