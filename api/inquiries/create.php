<?php
// Start output buffering immediately to catch any early output
ob_start();

// Suppress warnings and errors from being output
error_reporting(0);
ini_set('display_errors', 0);

/**
 * Create Inquiry API
 * Handles creating admission inquiries (not full applications)
 */

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
    error_log("Inquiry Create - Raw input: " . $rawInput);
    
    $data = json_decode($rawInput);
    error_log("Inquiry Create - Decoded data: " . print_r($data, true));
    
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
    if (empty($data->fullName) || empty($data->email) || empty($data->program) || empty($data->question)) {
        
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields",
            "missing" => [
                "fullName" => empty($data->fullName),
                "email" => empty($data->email),
                "program" => empty($data->program),
                "question" => empty($data->question)
            ]
        ]);
        exit;
    }
    
    // Validate email format
    if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format"
        ]);
        exit;
    }
    
    // Check if inquiries table exists, create if not
    $tableCheck = $db->query("SHOW TABLES LIKE 'inquiries'");
    if ($tableCheck->rowCount() === 0) {
        // Create inquiries table
        $createTableSQL = "CREATE TABLE inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            inquiry_id VARCHAR(50) NOT NULL UNIQUE,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            program_id INT NOT NULL,
            program_name VARCHAR(255) NOT NULL,
            question TEXT NOT NULL,
            inquiry_type ENUM('general', 'admission', 'program', 'requirements', 'other') DEFAULT 'general',
            status ENUM('new', 'responded', 'closed') DEFAULT 'new',
            notes TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            responded_at TIMESTAMP NULL,
            responded_by INT NULL,
            FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE RESTRICT,
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_inquiry_type (inquiry_type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($createTableSQL);
        error_log("Inquiries table created");
    }
    
    // Get program details
    $programQuery = "SELECT id, title FROM programs WHERE id = ? OR code = ?";
    $programStmt = $db->prepare($programQuery);
    $programStmt->execute([$data->program, $data->program]);
    $program = $programStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$program) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid program selection"
        ]);
        exit;
    }
    
    // Generate unique inquiry ID
    $inquiryId = 'INQ-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Check if inquiry ID already exists (very unlikely but just in case)
    $checkQuery = "SELECT id FROM inquiries WHERE inquiry_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$inquiryId]);
    
    if ($checkStmt->rowCount() > 0) {
        // Generate another ID if collision occurs
        $inquiryId = 'INQ-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    // Insert inquiry
    $query = "INSERT INTO inquiries (
                inquiry_id,
                full_name,
                email,
                phone,
                program_id,
                program_name,
                question,
                inquiry_type,
                status,
                notes
              ) VALUES (
                :inquiry_id,
                :full_name,
                :email,
                :phone,
                :program_id,
                :program_name,
                :question,
                :inquiry_type,
                :status,
                :notes
              )";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':inquiry_id', $inquiryId);
    $stmt->bindParam(':full_name', $data->fullName);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':program_id', $program['id']);
    $stmt->bindParam(':program_name', $program['title']);
    $stmt->bindParam(':question', $data->question);
    $stmt->bindParam(':inquiry_type', $inquiryType);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':notes', $notes);
    
    // Set default values
    $status = 'new';
    $phone = $data->phone ?? null;
    $notes = $data->notes ?? null;
    $inquiryType = $data->inquiryType ?? 'general';
    
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
            
            $subject = "Inquiry Received - Colegio De Naujan";
            $htmlBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Inquiry Received</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                    .header { background: #1a365d; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                    .header h1 { margin: 0; font-size: 28px; }
                    .content { padding: 30px; background: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
                    .content h2 { color: #1a365d; margin-top: 0; }
                    .footer { background: #2c5282; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
                    .info-box { background: #e2e8f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Colegio De Naujan</h1>
                    <p>Admissions Office</p>
                </div>
                <div class="content">
                    <h2>Inquiry Received!</h2>
                    <p>Dear <strong>' . htmlspecialchars($data->fullName) . '</strong>,</p>
                    <p>Thank you for your interest in <strong>' . htmlspecialchars($program['title']) . '</strong> at Colegio De Naujan. We have received your inquiry and will respond within 24-48 hours.</p>
                    
                    <div class="info-box">
                        <h3>Inquiry Details:</h3>
                        <p><strong>Inquiry ID:</strong> ' . htmlspecialchars($inquiryId) . '</p>
                        <p><strong>Program:</strong> ' . htmlspecialchars($program['title']) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($data->email) . '</p>
                        <p><strong>Date Submitted:</strong> ' . date('F j, Y, g:i a') . '</p>
                        <p><strong>Your Question:</strong> ' . htmlspecialchars($data->question) . '</p>
                    </div>
                    
                    <h3>Next Steps:</h3>
                    <ul>
                        <li>Our admissions team will review your inquiry</li>
                        <li>You will receive a response via email within 24-48 hours</li>
                        <li>Please keep your Inquiry ID for future reference</li>
                    </ul>
                    
                    <p>If you have any urgent questions, please contact our admissions office directly.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </body>
            </html>';
            
            $emailSent = $emailConfig->sendEmail($data->email, $subject, $htmlBody);
            
            if ($emailSent) {
                error_log("Inquiry confirmation email sent to: " . $data->email);
            } else {
                error_log("Failed to send inquiry confirmation email to: " . $data->email);
            }
            
        } catch (Exception $emailError) {
            error_log("Email sending error: " . $emailError->getMessage());
            $emailError = $emailError->getMessage();
        }
        
        // Clean email output buffer
        ob_end_clean();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Inquiry submitted successfully",
            "id" => $newId,
            "inquiry_id" => $inquiryId,
            "program_name" => $program['title'],
            "email_sent" => $emailSent,
            "email_error" => $emailError
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to submit inquiry"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();

// Clean output buffer and send JSON
ob_end_clean();
?>
