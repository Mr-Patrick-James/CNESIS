<?php
/**
 * Update Student API
 * Handles updating existing students
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, POST");
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
    $colStmt = $db->query("SHOW COLUMNS FROM students LIKE 'enrollment_type'");
    if ($colStmt->rowCount() === 0) {
        $db->exec("ALTER TABLE students ADD COLUMN enrollment_type ENUM('regular','irregular') NOT NULL DEFAULT 'regular' AFTER yearlevel");
    }

    // Get raw input
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput);
    
    // Validation helper
    function validateInput($value, $pattern, $minLength, $maxLength) {
        if (empty($value)) return true;
        if (strlen($value) < $minLength || strlen($value) > $maxLength) return false;
        if (!preg_match($pattern, $value)) return false;
        return true;
    }

    $namePattern = "/^[A-Za-z\s\.\-]+$/";
    $phonePattern = "/^09\d{9}$/";

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
    if (empty($data->id) || empty($data->student_id) || empty($data->first_name) || 
        empty($data->last_name) || empty($data->email)) {
        
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields"
        ]);
        exit;
    }

    // Advanced Validation
    if (!validateInput($data->first_name, $namePattern, 2, 63)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid First Name. Only letters, spaces, dots, and hyphens are allowed (2-63 characters)."]);
        exit;
    }
    if (!empty($data->middle_name) && !validateInput($data->middle_name, $namePattern, 1, 63)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid Middle Name. Only letters, spaces, dots, and hyphens are allowed (max 63 characters)."]);
        exit;
    }
    if (!validateInput($data->last_name, $namePattern, 2, 63)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid Last Name. Only letters, spaces, dots, and hyphens are allowed (2-63 characters)."]);
        exit;
    }
    if (!empty($data->phone) && !validateInput($data->phone, $phonePattern, 11, 11)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid Phone Number. Must be 11 digits starting with 09."]);
        exit;
    }

    $enrollmentType = isset($data->enrollment_type) && $data->enrollment_type ? strtolower(trim($data->enrollment_type)) : 'regular';
    if (!in_array($enrollmentType, ['regular', 'irregular'], true)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid enrollment type"]);
        exit;
    }
    
    // Check if student exists
    $checkQuery = "SELECT * FROM students WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->execute();
    $oldStudentData = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$oldStudentData) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Student not found"
        ]);
        exit;
    }
    
    // Ensure history table exists
    $db->exec("CREATE TABLE IF NOT EXISTS student_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        field_name VARCHAR(50) NOT NULL,
        old_value TEXT,
        new_value TEXT,
        changed_by INT,
        changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (student_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Track changes
    $changes = [];
    $fieldsToTrack = [
        'department' => $data->department ?? null,
        'section_id' => $data->section_id ?? null,
        'yearlevel' => $data->year_level ?? null,
        'status' => $data->status ?? null,
        'enrollment_type' => $enrollmentType
    ];

    foreach ($fieldsToTrack as $field => $newValue) {
        $oldValue = $oldStudentData[$field] ?? null;
        // Normalize for comparison
        $normOld = $oldValue === null ? '' : (string)$oldValue;
        $normNew = $newValue === null ? '' : (string)$newValue;
        
        if ($normOld !== $normNew) {
            $changes[] = [
                'field' => $field,
                'old' => $oldValue,
                'new' => $newValue
            ];
        }
    }

    // Validate new password if provided
    $newPassword = isset($data->new_password) ? trim($data->new_password) : '';
    if ($newPassword !== '') {
        if (strlen($newPassword) < 8) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
            exit;
        }
        if (!preg_match('/[A-Z]/', $newPassword)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Password must contain at least one uppercase letter."]);
            exit;
        }
        if (!preg_match('/[a-z]/', $newPassword)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Password must contain at least one lowercase letter."]);
            exit;
        }
        if (!preg_match('/[0-9]/', $newPassword)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Password must contain at least one number."]);
            exit;
        }
    }

    // Update student
    $query = "UPDATE students SET 
                student_id = :student_id,
                first_name = :first_name,
                middle_name = :middle_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                birth_date = :birth_date,
                gender = :gender,
                address = :address,
                department = :department,
                section_id = :section_id,
                yearlevel = :year_level,
                enrollment_type = :enrollment_type,
                status = :status
              WHERE id = :id";    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':student_id', $data->student_id);
    $stmt->bindParam(':first_name', $data->first_name);
    $stmt->bindParam(':middle_name', $data->middle_name);
    $stmt->bindParam(':last_name', $data->last_name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    $stmt->bindParam(':birth_date', $data->birth_date);
    $stmt->bindParam(':gender', $data->gender);
    $stmt->bindParam(':address', $data->address);
    $stmt->bindParam(':department', $data->department);
    $stmt->bindParam(':section_id', $data->section_id);
    $stmt->bindParam(':year_level', $data->year_level);
    $stmt->bindParam(':enrollment_type', $enrollmentType);
    $stmt->bindParam(':status', $data->status);
    
    if ($stmt->execute()) {
        // Log changes in history
        if (!empty($changes)) {
            session_start();
            $adminId = $_SESSION['user_id'] ?? null;
            $historyStmt = $db->prepare("INSERT INTO student_history (student_id, field_name, old_value, new_value, changed_by) VALUES (?, ?, ?, ?, ?)");
            foreach ($changes as $change) {
                $historyStmt->execute([$data->id, $change['field'], $change['old'], $change['new'], $adminId]);
            }
        }

        // Update student user account
        $userUpdateQuery = "UPDATE users SET 
                            username = :new_email,
                            email = :new_email,
                            full_name = :full_name
                            WHERE email = :old_email AND role = 'student'";
        $userUpdateStmt = $db->prepare($userUpdateQuery);
        $userUpdateStmt->bindParam(':new_email', $data->email);
        $fullName = trim($data->first_name . ' ' . ($data->middle_name ?? '') . ' ' . $data->last_name);
        $userUpdateStmt->bindParam(':full_name', $fullName);
        $userUpdateStmt->bindParam(':old_email', $oldStudentData['email']);
        $userUpdateStmt->execute();

        // Update password if provided
        if ($newPassword !== '') {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $pwStmt = $db->prepare("UPDATE users SET password = :pw WHERE email = :email AND role = 'student'");
            $pwStmt->bindParam(':pw', $hashedPassword);
            $pwStmt->bindParam(':email', $data->email);
            $pwStmt->execute();
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Student updated successfully",
            "id" => $data->id,
            "changes_logged" => count($changes)
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to update student"
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
?>
