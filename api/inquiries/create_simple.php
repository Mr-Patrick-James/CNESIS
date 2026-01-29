<?php
/**
 * Simple Inquiry API
 * Simplified version that works with existing database
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

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
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || empty($data->fullName) || empty($data->email) || empty($data->question)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields"
        ]);
        exit;
    }
    
    // Generate unique application ID
    $applicationId = 'APP-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Get program info
    $programName = 'Unknown Program';
    if (!empty($data->program)) {
        try {
            $stmt = $db->prepare("SELECT title FROM programs WHERE id = ? LIMIT 1");
            $stmt->execute([$data->program]);
            $result = $stmt->fetch();
            if ($result) {
                $programName = $result['title'];
            }
        } catch (Exception $e) {
            // Ignore program lookup errors
        }
    }
    
    // Create/Modify admissions table if needed
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS admissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            application_id VARCHAR(50) NOT NULL UNIQUE,
            student_id VARCHAR(20) DEFAULT NULL,
            program_id INT DEFAULT NULL,
            first_name VARCHAR(100) NOT NULL,
            middle_name VARCHAR(100) DEFAULT NULL,
            last_name VARCHAR(100) DEFAULT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            birthdate DATE DEFAULT NULL,
            gender ENUM('male', 'female', 'other') DEFAULT NULL,
            address TEXT DEFAULT NULL,
            high_school VARCHAR(255) DEFAULT NULL,
            last_school VARCHAR(255) DEFAULT NULL,
            year_graduated INT DEFAULT NULL,
            gwa DECIMAL(3,2) DEFAULT NULL,
            entrance_exam_score INT DEFAULT NULL,
            admission_type ENUM('freshman', 'transferee', 'returnee', 'shifter') DEFAULT 'freshman',
            previous_program VARCHAR(255) DEFAULT NULL,
            status ENUM('pending', 'approved', 'rejected', 'processing', 'enrolled') DEFAULT 'pending',
            notes TEXT DEFAULT NULL,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            reviewed_at TIMESTAMP NULL DEFAULT NULL,
            reviewed_by INT DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Modify existing table to allow NULL phone if needed
        $db->exec("ALTER TABLE admissions MODIFY COLUMN phone VARCHAR(20) DEFAULT NULL");
        
    } catch (Exception $e) {
        // Ignore table creation/modification errors
    }
    
    // Insert into admissions table as a simple inquiry
    $query = "INSERT INTO admissions (
                application_id, first_name, last_name, email, phone, 
                program_id, admission_type, status, notes
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $success = $stmt->execute([
        $applicationId,
        $data->fullName,
        '', // last_name empty since fullName includes both
        $data->email,
        $data->phone ?? '', // Empty string instead of null
        $data->program ?? null,
        'freshman',
        'pending',
        $data->question // Store question in notes field
    ]);
    
    if ($success) {
        $newId = $db->lastInsertId();
        
        // Try to send email (optional - don't fail if it doesn't work)
        $emailSent = false;
        try {
            // Simple email without attachments
            $subject = "Inquiry Received - Colegio De Naujan";
            $message = "Thank you for your inquiry. We will respond within 24-48 hours.\n\n";
            $message .= "Inquiry ID: $inquiryId\n";
            $message .= "Name: {$data->fullName}\n";
            $message .= "Email: {$data->email}\n";
            $message .= "Program: $programName\n";
            $message .= "Question: {$data->question}";
            
            $headers = "From: admissions@colegiodenaujan.edu.ph\r\n";
            $headers .= "Reply-To: {$data->email}\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            $emailSent = mail($data->email, $subject, $message, $headers);
        } catch (Exception $e) {
            // Ignore email errors
        }
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Inquiry submitted successfully",
            "id" => $newId,
            "application_id" => $applicationId,
            "program_name" => $programName,
            "email_sent" => $emailSent
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to save inquiry"
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
