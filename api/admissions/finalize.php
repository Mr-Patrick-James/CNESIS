<?php
/**
 * Finalize Admission API
 * Handles passing/failing students and creating student/user records
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../config/email_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->admission_id)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Admission ID is required"]);
    exit;
}

try {
    $db->beginTransaction();

    // 1. Get admission details
    $query = "SELECT a.*, p.title as program_title, p.code as program_code 
             FROM admissions a 
             LEFT JOIN programs p ON a.program_id = p.id 
             WHERE a.id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->admission_id]);
    $admission = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admission) {
        throw new Exception("Admission record not found");
    }

    // Use assigned info if available
    $department = $data->department ?? $admission['assigned_department'];
    $yearLevel = $data->year_level ?? $admission['assigned_year_level'];
    $sectionName = $data->section_name ?? ($admission['assigned_section_name'] ?? '');

    if (!$department || !$yearLevel) {
        throw new Exception("Enrollment details (Department, Year) are required for deployment");
    }

    // Find or create section if sectionName is provided
    $sectionId = $data->section_id ?? $admission['assigned_section_id'];
    if (!empty($sectionName)) {
        $secStmt = $db->prepare("SELECT id FROM sections WHERE section_name = ?");
        $secStmt->execute([$sectionName]);
        $existingSec = $secStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingSec) {
            $sectionId = $existingSec['id'];
        } else {
            // Create new section
            $createSecStmt = $db->prepare("INSERT INTO sections (section_code, section_name, department_code, year_level, status) VALUES (?, ?, ?, ?, 'active')");
            $sectionCode = str_replace(' ', '-', $sectionName);
            $createSecStmt->execute([$sectionCode, $sectionName, $department, $yearLevel]);
            $sectionId = $db->lastInsertId();
        }
    }

    if (!$sectionId) {
        throw new Exception("Section information is required for deployment");
    }

    // 2. Generate Student ID (Current Year + 4 digit incremental)
    $year = date('Y');
    $stmt = $db->query("SELECT student_id FROM students WHERE student_id LIKE '$year-%' ORDER BY student_id DESC LIMIT 1");
    $lastId = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lastId) {
        $lastNum = (int)substr($lastId['student_id'], 5);
        $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newNum = '0001';
    }
    $studentId = "$year-$newNum";

    // 3. Generate School Email
    $schoolEmail = strtolower($studentId . "@colegiodenaujan.edu.ph");

    // 4. Create Student Record
    $studentQuery = "INSERT INTO students (
                        student_id, first_name, middle_name, last_name, 
                        email, phone, address, birth_date, gender, 
                        department, section_id, yearlevel, status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
    
    $studentStmt = $db->prepare($studentQuery);
    $studentStmt->execute([
        $studentId,
        $admission['first_name'],
        $admission['middle_name'],
        $admission['last_name'],
        $schoolEmail, // Use dedicated school email
        $admission['phone'],
        $admission['address'],
        $admission['birthdate'],
        $admission['gender'],
        $department,
        $sectionId,
        $yearLevel
    ]);
    
    $newStudentId = $db->lastInsertId();

    // 5. Create User Account
    // Ensure 'student' exists in enum
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'role'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (strpos($row['Type'], "'student'") === false) {
        $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin','staff','faculty','program_head','student') DEFAULT 'staff'");
    }

    $password = "CN-" . str_replace('-', '', $studentId); // Default password e.g. CN-20260001
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $fullName = trim($admission['first_name'] . ' ' . ($admission['middle_name'] ? $admission['middle_name'] . ' ' : '') . $admission['last_name']);
    
    $userQuery = "INSERT INTO users (username, email, password, full_name, role, status) 
                  VALUES (?, ?, ?, ?, 'student', 'active')";
    $userStmt = $db->prepare($userQuery);
    $userStmt->execute([
        $schoolEmail, // Username is the school email
        $schoolEmail,
        $passwordHash,
        $fullName
    ]);

    // 6. Update Admission Status
    // Ensure 'passed' and 'enrolled' exist in enum
    $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'status'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (strpos($row['Type'], "'passed'") === false || strpos($row['Type'], "'enrolled'") === false) {
        $db->exec("ALTER TABLE admissions MODIFY COLUMN status ENUM('pending','approved','rejected','scheduled','examed','did not attend','reschedule','passed','enrolled') DEFAULT 'pending'");
    }

    $updateQuery = "UPDATE admissions SET 
                    status = 'enrolled', 
                    student_id = ?,
                    notes = CONCAT(COALESCE(notes, ''), ?),
                    reviewed_at = NOW()
                    WHERE id = ?";
    $notes = "\n\n" . date('Y-m-d H:i:s') . ": Deployed to official student list. Student ID: $studentId. School Email: $schoolEmail";
    if (!empty($data->notes)) {
        $notes .= "\nAdmin Notes: " . $data->notes;
    }
    
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$studentId, $notes, $data->admission_id]);

    // 7. Get Section Name for Redirection
    $sectionStmt = $db->prepare("SELECT section_name FROM sections WHERE id = ?");
    $sectionStmt->execute([$data->section_id]);
    $section = $sectionStmt->fetch(PDO::FETCH_ASSOC);
    $sectionName = $section ? $section['section_name'] : '';

    // 8. Log Action
    $logQuery = "INSERT INTO admission_status_log (admission_id, old_status, new_status, action_by, notes, created_at) 
                 VALUES (?, 'passed', 'enrolled', 1, ?, NOW())";
    $logStmt = $db->prepare($logQuery);
    $logStmt->execute([$data->admission_id, "Finalized deployment as $studentId"]);

    // 9. Send Email to student with credentials (Optional, but good practice)
    try {
        $emailConfig = new EmailConfig($db);
        $subject = "Welcome to Colegio de Naujan - Your Student Account Details";
        $htmlBody = "
            <h3>Congratulations, $fullName!</h3>
            <p>Your admission has been finalized and you are now officially enrolled.</p>
            <p><strong>Your Student Details:</strong></p>
            <ul>
                <li><strong>Student ID:</strong> $studentId</li>
                <li><strong>School Email:</strong> $schoolEmail</li>
                <li><strong>Default Password:</strong> $password</li>
            </ul>
            <p>You can now login to the student portal using your school email and the default password above.</p>
            <p>Please change your password after your first login.</p>
            <br>
            <p>Best regards,<br>Admissions Office<br>Colegio de Naujan</p>
        ";
        $emailConfig->sendEmail($admission['email'], $subject, $htmlBody);
    } catch (Exception $e) {
        // Log email error but don't fail the transaction
        error_log("Finalization email error: " . $e->getMessage());
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "Admission finalized successfully",
        "student_id" => $studentId,
        "school_email" => $schoolEmail,
        "section_name" => $sectionName
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error finalizing admission: " . $e->getMessage()
    ]);
}
?>