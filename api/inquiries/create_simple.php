<?php
/**
 * Student Inquiry Submission API
 * Handles initial inquiry and subsequent student messages
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

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

// Ensure inquiry tables exist before processing any request
initializeInquiryTables($db);

try {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || empty($data->fullName) || empty($data->email) || empty($data->question)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }

    $db->beginTransaction();

    // 1. Check if an active inquiry already exists for this email address
    $checkQuery = "SELECT id FROM inquiries WHERE email = :email AND status != 'closed' LIMIT 1";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute(['email' => $data->email]);
    $inquiry = $checkStmt->fetch(PDO::FETCH_ASSOC);

    $isNewInquiry = true;
    if ($inquiry) {
        $inquiryId = $inquiry['id'];
        $isNewInquiry = false;
        // Update inquiry timestamp for sorting
        $updateQuery = "UPDATE inquiries SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute(['id' => $inquiryId]);
    } else {
        // Generate unique inquiry ID (e.g., INQ-2026-1234)
        $externalInquiryId = 'INQ-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if inquiry ID already exists (very unlikely but just in case)
        $checkInqIdQuery = "SELECT id FROM inquiries WHERE inquiry_id = ? LIMIT 1";
        $checkInqIdStmt = $db->prepare($checkInqIdQuery);
        $checkInqIdStmt->execute([$externalInquiryId]);
        
        if ($checkInqIdStmt->fetch()) {
            // Generate another ID if collision occurs
            $externalInquiryId = 'INQ-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        
        // Create new inquiry record for new students
        $query = "INSERT INTO inquiries (inquiry_id, full_name, email, program_id, status) VALUES (:inquiry_id, :full_name, :email, :program_id, 'open')";
        $stmt = $db->prepare($query);
        $stmt->execute([
            'inquiry_id' => $externalInquiryId,
            'full_name' => $data->fullName,
            'email' => $data->email,
            'program_id' => $data->program ?? null
        ]);
        $inquiryId = $db->lastInsertId();
    }

    // 2. Add the student message to the conversation history
    $msgQuery = "INSERT INTO inquiry_messages (inquiry_id, sender_type, message, is_read) 
                 VALUES (:inquiry_id, 'student', :message, 0)";
    $msgStmt = $db->prepare($msgQuery);
    $msgStmt->execute([
        'inquiry_id' => $inquiryId,
        'message' => $data->question
    ]);

    $db->commit();

    // 3. Send confirmation email for NEW inquiries
    $emailSent = false;
    if ($isNewInquiry) {
        try {
            $emailService = new EmailService($db);
            $emailData = new stdClass();
            $emailData->recipient_email = $data->email;
            $emailData->custom_subject = "Admission Inquiry Received - Colegio De Naujan";
            $emailData->custom_message = "Dear " . $data->fullName . ",\n\n" .
                                       "Thank you for reaching out to us. We have received your inquiry and our team will get back to you shortly.\n\n" .
                                       "Your Question:\n" .
                                       "\"" . $data->question . "\"\n\n" .
                                       "You can check for replies on our website by visiting the Admission page and clicking 'Check for replies' using your email address.\n\n" .
                                       "Best regards,\n" .
                                       "Admission Office\n" .
                                       "Colegio De Naujan";
            $emailData->email_type = 'inquiry_received';
            
            $emailService->sendEmail($emailData);
            $emailSent = true;
        } catch (Exception $e) {
            error_log("Failed to send inquiry confirmation email: " . $e->getMessage());
        }
    }

    echo json_encode([
        "success" => true,
        "message" => "Inquiry submitted successfully",
        "inquiry_id" => $inquiryId,
        "email_sent" => $emailSent
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
