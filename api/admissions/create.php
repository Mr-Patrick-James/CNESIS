<?php
// Start output buffering immediately to catch any early output
ob_start();

// Suppress warnings and errors from being output
error_reporting(0);
ini_set('display_errors', 0);

/**
 * Create Admission API
 * Handles creating new admission applications
 */

// Start output buffering to prevent any HTML/warnings from interfering with JSON
// ob_start(); // Moved to top

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

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

try {
    // Get raw input for debugging
    $rawInput = file_get_contents("php://input");
    error_log("Admissions Create - Raw input: " . $rawInput);
    
    $data = json_decode($rawInput);
    error_log("Admissions Create - Decoded data: " . print_r($data, true));
    
    // Validate JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid JSON: " . json_last_error_msg()
        ]);
        exit;
    }
    
    // Validate required fields
    if (empty($data->application_id) || empty($data->first_name) || empty($data->last_name) || 
        empty($data->email) || empty($data->admission_type)) {
        
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields",
            "missing" => [
                "application_id" => empty($data->application_id),
                "first_name" => empty($data->first_name),
                "last_name" => empty($data->last_name),
                "email" => empty($data->email),
                "admission_type" => empty($data->admission_type)
            ]
        ]);
        exit;
    }
    
    // Check if application ID already exists
    $checkQuery = "SELECT id FROM admissions WHERE application_id = :application_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':application_id', $data->application_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Application ID already exists"
        ]);
        exit;
    }
    
    // Check if email already exists
    $checkEmailQuery = "SELECT id, status FROM admissions WHERE email = :email AND status != 'rejected'";
    $checkEmailStmt = $db->prepare($checkEmailQuery);
    $checkEmailStmt->bindParam(':email', $data->email);
    $checkEmailStmt->execute();
    
    if ($checkEmailStmt->rowCount() > 0) {
        $existing = $checkEmailStmt->fetch(PDO::FETCH_ASSOC);
        
        // If the existing application is just a draft, we can delete it and create the new one
        // We consider 'draft', 'new', empty, or NULL status as draft.
        if ($existing['status'] === 'draft' || $existing['status'] === 'new' || empty($existing['status'])) {
             $deleteQuery = "DELETE FROM admissions WHERE id = :id";
             $deleteStmt = $db->prepare($deleteQuery);
             $deleteStmt->execute([':id' => $existing['id']]);
        } else {
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "Email already has an active application"
            ]);
            exit;
        }
    }
    
    // Insert new admission
    $query = "INSERT INTO admissions (
                application_id,
                student_id,
                program_id,
                first_name,
                middle_name,
                last_name,
                email,
                phone,
                birthdate,
                gender,
                address,
                high_school,
                last_school,
                year_graduated,
                gwa,
                entrance_exam_score,
                admission_type,
                previous_program,
                status,
                notes,
                attachments,
                form_data
              ) VALUES (
                :application_id,
                :student_id,
                :program_id,
                :first_name,
                :middle_name,
                :last_name,
                :email,
                :phone,
                :birthdate,
                :gender,
                :address,
                :high_school,
                :last_school,
                :year_graduated,
                :gwa,
                :entrance_exam_score,
                :admission_type,
                :previous_program,
                :status,
                :notes,
                :attachments,
                :form_data
              )";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':application_id', $data->application_id);
    $stmt->bindParam(':student_id', $data->student_id);
    $stmt->bindParam(':program_id', $data->program_id);
    $stmt->bindParam(':first_name', $data->first_name);
    $stmt->bindParam(':middle_name', $data->middle_name);
    $stmt->bindParam(':last_name', $data->last_name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':birthdate', $data->birthdate);
    $stmt->bindParam(':gender', $data->gender);
    $stmt->bindParam(':address', $data->address);
    $stmt->bindParam(':high_school', $data->high_school);
    $stmt->bindParam(':last_school', $data->last_school);
    $stmt->bindParam(':year_graduated', $data->year_graduated);
    $stmt->bindParam(':gwa', $data->gwa);
    $stmt->bindParam(':entrance_exam_score', $data->entrance_exam_score);
    $stmt->bindParam(':admission_type', $data->admission_type);
    $stmt->bindParam(':previous_program', $data->previous_program);
    
    // Default status to pending if not provided
    $status = !empty($data->status) ? $data->status : 'pending';
    $stmt->bindParam(':status', $status);
    
    $stmt->bindParam(':notes', $data->notes);
    $stmt->bindParam(':attachments', $data->attachments);
    $stmt->bindParam(':form_data', $data->form_data);
    
    if ($stmt->execute()) {
        $newId = $db->lastInsertId();
        
        // Send confirmation email
        $emailSent = false;
        $emailError = null;
        
        try {
            // Buffer any output from email functions
            ob_start();
            
            include_once '../config/email_config.php';
            $emailConfig = new EmailConfig($db);
            
            // Prepare email content
            $subject = "Application Received - Colegio De Naujan";
            $htmlBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Application Received</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                    .header { background: #1a365d; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                    .header h1 { margin: 0; font-size: 28px; }
                    .content { padding: 30px; background: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
                    .content h2 { color: #1a365d; margin-top: 0; }
                    .footer { background: #2c5282; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
                    .btn { display: inline-block; padding: 12px 24px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                    .info-box { background: #e2e8f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Colegio De Naujan</h1>
                    <p>Admissions Office</p>
                </div>
                <div class="content">
                    <h2>Application Received!</h2>
                    <p>Dear <strong>' . htmlspecialchars($data->first_name . ' ' . $data->last_name) . '</strong>,</p>
                    <p>Thank you for your interest in Colegio De Naujan. We have successfully received your application.</p>
                    
                    <div class="info-box">
                        <h3>Application Details:</h3>
                        <p><strong>Application ID:</strong> ' . htmlspecialchars($data->application_id) . '</p>
                        <p><strong>Program:</strong> ' . htmlspecialchars($data->admission_type) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($data->email) . '</p>
                        <p><strong>Date Submitted:</strong> ' . date('F j, Y, g:i a') . '</p>
                    </div>
                    
                    <h3>Next Steps:</h3>
                    <ul>
                        <li>Our admissions team will review your application</li>
                        <li>You will receive updates via email</li>
                        <li>Please keep your Application ID for future reference</li>
                    </ul>
                    
                    <p>If you have any questions, please contact our admissions office.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </body>
            </html>';
            
            // Prepare attachments
            $attachments = [];
            $attachmentFiles = [
                'assets/documents/application-form.pdf' => 'Application Form.pdf',
                'assets/documents/requirements-list.pdf' => 'Admission Requirements.pdf',
                'assets/documents/school-policies.pdf' => 'School Policies.pdf'
            ];
            
            foreach ($attachmentFiles as $path => $name) {
                // Correct path: from api/admissions/ -> ../../assets/
                $fullPath = '../../' . $path;
                if (file_exists($fullPath)) {
                    $attachments[] = [
                        'path' => $fullPath,
                        'name' => $name
                    ];
                }
            }
            
            // Send email
            $emailSent = $emailConfig->sendEmail($data->email, $subject, $htmlBody, $attachments);
            
            if ($emailSent) {
                error_log("Confirmation email sent to: " . $data->email);
            } else {
                error_log("Failed to send confirmation email to: " . $data->email);
            }
            
        } catch (Throwable $emailError) {
            error_log("Email sending error: " . $emailError->getMessage());
            $emailError = $emailError->getMessage();
            // Continue with response even if email fails
        }
        
        // Clean email output buffer
        ob_end_clean();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Application submitted successfully",
            "id" => $newId,
            "application_id" => $data->application_id,
            "email_sent" => isset($emailSent) ? $emailSent : false,
            "email_error" => $emailError
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to submit application"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();

// Clean output buffer and send JSON
ob_end_flush();
?>
