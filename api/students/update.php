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

function normalizeNullableInt($value) {
    if ($value === null || $value === '') {
        return null;
    }
    return (int) $value;
}

function normalizeNullableDate($value) {
    if ($value === null || $value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return null;
    }
    return $value;
}

function normalizeNullableString($value) {
    if ($value === null) {
        return null;
    }
    $value = trim((string) $value);
    return $value === '' ? null : $value;
}

try {
    try {
        $colStmt = $db->query("SHOW COLUMNS FROM students LIKE 'enrollment_type'");
        if ($colStmt->rowCount() === 0) {
            $db->exec("ALTER TABLE students ADD COLUMN enrollment_type ENUM('regular','irregular') NOT NULL DEFAULT 'regular' AFTER yearlevel");
        }

        $statusCol = $db->query("SHOW COLUMNS FROM students LIKE 'status'")->fetch(PDO::FETCH_ASSOC);
        if ($statusCol && stripos($statusCol['Type'], 'graduated') === false) {
            $db->exec("ALTER TABLE students MODIFY COLUMN status ENUM('active','inactive','graduated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active'");
        }
    } catch (PDOException $schemaEx) {
        error_log('students/update schema migration skipped: ' . $schemaEx->getMessage());
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

    $namePattern = "/^[\p{L}\s\.\-\(\)]+$/u"; // Support Unicode letters and parentheses
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
    if (!validateInput($data->first_name, $namePattern, 2, 100)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid First Name. Only letters, spaces, dots, hyphens, and parentheses are allowed."]);
        exit;
    }
    // Middle name is optional and can be more flexible
    if (!empty($data->middle_name) && !validateInput($data->middle_name, $namePattern, 1, 100)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid Middle Name. Only letters, spaces, dots, hyphens, and parentheses are allowed."]);
        exit;
    }
    if (!validateInput($data->last_name, $namePattern, 2, 100)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid Last Name. Only letters, spaces, dots, hyphens, and parentheses are allowed."]);
        exit;
    }

    $enrollmentType = isset($data->enrollment_type) && $data->enrollment_type ? strtolower(trim($data->enrollment_type)) : 'regular';
    if (!in_array($enrollmentType, ['regular', 'irregular'], true)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid enrollment type"]);
        exit;
    }

    $status = isset($data->status) && $data->status !== '' ? strtolower(trim((string) $data->status)) : 'active';
    if (!in_array($status, ['active', 'inactive', 'graduated'], true)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid status value"]);
        exit;
    }

    $sectionId = normalizeNullableInt($data->section_id ?? null);
    $yearLevel = normalizeNullableInt($data->year_level ?? null);
    $birthDate = normalizeNullableDate($data->birth_date ?? null);
    $phone = normalizeNullableString($data->phone ?? null);
    $middleName = normalizeNullableString($data->middle_name ?? null);
    $gender = normalizeNullableString($data->gender ?? null);
    $address = normalizeNullableString($data->address ?? null);
    $department = normalizeNullableString($data->department ?? null);

    if ($phone !== null && !validateInput($phone, $phonePattern, 11, 11)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid Phone Number. Must be 11 digits starting with 09."]);
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
        'section_id' => $sectionId,
        'yearlevel' => $yearLevel,
        'status' => $status,
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
    $stmt->bindValue(':id', (int) $data->id, PDO::PARAM_INT);
    $stmt->bindValue(':student_id', $data->student_id);
    $stmt->bindValue(':first_name', $data->first_name);
    $stmt->bindValue(':middle_name', $middleName);
    $stmt->bindValue(':last_name', $data->last_name);
    $stmt->bindValue(':email', $data->email);
    $stmt->bindValue(':phone', $phone);
    $stmt->bindValue(':birth_date', $birthDate);
    $stmt->bindValue(':gender', $gender);
    $stmt->bindValue(':address', $address);
    $stmt->bindValue(':department', $department);
    $stmt->bindValue(':section_id', $sectionId, $sectionId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':year_level', $yearLevel, $yearLevel === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':enrollment_type', $enrollmentType);
    $stmt->bindValue(':status', $status);
    
    if ($stmt->execute()) {
        // Log changes in history (non-fatal — do not fail graduate if history logging fails)
        if (!empty($changes)) {
            try {
                if (session_status() === PHP_SESSION_NONE) {
                    @session_start();
                }
                $adminId = $_SESSION['user_id'] ?? null;
                $historyStmt = $db->prepare("INSERT INTO student_history (student_id, field_name, old_value, new_value, changed_by) VALUES (?, ?, ?, ?, ?)");
                foreach ($changes as $change) {
                    $historyStmt->execute([$data->id, $change['field'], $change['old'], $change['new'], $adminId]);
                }
            } catch (Throwable $historyEx) {
                error_log('students/update history log failed: ' . $historyEx->getMessage());
            }
        }

        // Update student user account
        $userUpdateQuery = "UPDATE users SET 
                            username = :new_email_1,
                            email = :new_email_2,
                            full_name = :full_name
                            WHERE email = :old_email AND role = 'student'";
        $userUpdateStmt = $db->prepare($userUpdateQuery);
        $userUpdateStmt->bindParam(':new_email_1', $data->email);
        $userUpdateStmt->bindParam(':new_email_2', $data->email);
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
        $err = $stmt->errorInfo();
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to update student",
            "detail" => $err[2] ?? null
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
