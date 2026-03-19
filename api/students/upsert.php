<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../config/database.php';

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

function normalizeEmailPart($value) {
    $s = is_null($value) ? '' : (string)$value;
    $s = trim($s);
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '.', $s);
    $s = preg_replace('/\.+/', '.', $s);
    $s = preg_replace('/^\./', '', $s);
    $s = preg_replace('/\.$/', '', $s);
    return $s;
}

function buildStudentEmailFromName($firstName, $middleName, $lastName, &$usedEmails) {
    $domain = 'colegiodenaujan.edu.ph';
    $first = normalizeEmailPart($firstName);
    $middle = normalizeEmailPart($middleName);
    $last = normalizeEmailPart($lastName);

    $base = implode('.', array_values(array_filter([$first, $last], function($v) { return $v !== ''; })));
    if ($base === '') {
        $base = 'student';
    }

    $local = $base;
    if (isset($usedEmails[$local]) && $middle !== '') {
        $alt = implode('.', array_values(array_filter([$first, substr($middle, 0, 1), $last], function($v) { return $v !== ''; })));
        if ($alt !== '' && !isset($usedEmails[$alt])) {
            $local = $alt;
        }
    }

    if (!isset($usedEmails[$local])) {
        $usedEmails[$local] = 1;
        return $local . '@' . $domain;
    }

    $usedEmails[$local] += 1;
    return $local . $usedEmails[$local] . '@' . $domain;
}

try {
    $rawInput = file_get_contents("php://input");
    $data = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        exit;
    }

    $students = isset($data['students']) && is_array($data['students']) ? $data['students'] : [];
    if (count($students) === 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "No students provided"]);
        exit;
    }

    // Ensure enrollment_type column exists
    $colStmt = $db->query("SHOW COLUMNS FROM students LIKE 'enrollment_type'");
    if ($colStmt->rowCount() === 0) {
        $db->exec("ALTER TABLE students ADD COLUMN enrollment_type ENUM('regular','irregular') NOT NULL DEFAULT 'regular' AFTER yearlevel");
    }

    $validDepartments = [];
    $deptStmt = $db->query("SELECT department_code FROM departments");
    while ($row = $deptStmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['department_code'])) {
            $validDepartments[strtoupper(trim((string)$row['department_code']))] = true;
        }
    }

    $validSectionIds = [];
    $secStmt = $db->query("SELECT id FROM sections");
    while ($row = $secStmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($row['id'])) {
            $validSectionIds[(int)$row['id']] = true;
        }
    }

    // Get existing students to handle updates
    $existingStudents = [];
    $stmt = $db->query("SELECT id, student_id, email FROM students");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existingStudents[$row['student_id']] = $row;
    }

    // Get used emails for generation
    $usedEmails = [];
    foreach ($existingStudents as $student) {
        $emailLocal = explode('@', $student['email'])[0];
        $usedEmails[$emailLocal] = true;
    }

    $db->beginTransaction();

    $insert = $db->prepare(
        "INSERT INTO students (
            student_id, first_name, middle_name, last_name, email, 
            phone, birth_date, gender, address, department, 
            section_id, yearlevel, enrollment_type, status
        ) VALUES (
            :student_id, :first_name, :middle_name, :last_name, :email, 
            :phone, :birth_date, :gender, :address, :department, 
            :section_id, :yearlevel, :enrollment_type, :status
        )"
    );

    $update = $db->prepare(
        "UPDATE students SET 
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
            yearlevel = :yearlevel,
            enrollment_type = :enrollment_type,
            status = :status,
            updated_at = CURRENT_TIMESTAMP
        WHERE student_id = :student_id"
    );

    // Prepare User account statements
    $userInsert = $db->prepare(
        "INSERT INTO users (username, email, password, full_name, role, status) 
         VALUES (:username, :email, :password, :full_name, 'student', 'active')
         ON DUPLICATE KEY UPDATE 
            password = VALUES(password),
            full_name = VALUES(full_name),
            status = 'active'"
    );
    
    $userUpdate = $db->prepare(
        "UPDATE users SET 
            username = :new_username,
            email = :new_email,
            full_name = :full_name,
            updated_at = CURRENT_TIMESTAMP
         WHERE email = :old_email AND role = 'student'"
    );

    $inserted = 0;
    $updated = 0;
    $defaultPasswordHash = password_hash('password123', PASSWORD_DEFAULT);

    foreach ($students as $s) {
        $studentId = isset($s['student_id']) ? trim((string)$s['student_id']) : '';
        $firstName = isset($s['first_name']) ? trim((string)$s['first_name']) : '';
        $middleName = isset($s['middle_name']) ? trim((string)$s['middle_name']) : '';
        $lastName = isset($s['last_name']) ? trim((string)$s['last_name']) : '';

        if ($studentId === '' || $firstName === '' || $lastName === '') {
            continue;
        }

        $enrollmentType = isset($s['enrollment_type']) ? strtolower(trim((string)$s['enrollment_type'])) : 'regular';
        if ($enrollmentType !== 'regular' && $enrollmentType !== 'irregular') {
            $enrollmentType = 'regular';
        }

        $status = isset($s['status']) ? strtolower(trim((string)$s['status'])) : 'active';
        if (!in_array($status, ['active', 'inactive', 'graduated', 'suspended', 'on_leave'], true)) {
            $status = 'active';
        }

        $yearlevel = isset($s['year_level']) ? $s['year_level'] : (isset($s['yearlevel']) ? $s['yearlevel'] : null);
        if ($yearlevel !== null && $yearlevel !== '') {
            $yearlevel = (int)$yearlevel;
        } else {
            $yearlevel = null;
        }

        $department = null;
        if (isset($s['department']) && trim((string)$s['department']) !== '') {
            $dep = strtoupper(trim((string)$s['department']));
            if (isset($validDepartments[$dep])) {
                $department = $dep;
            }
        }

        $sectionId = null;
        if (isset($s['section_id']) && $s['section_id'] !== '') {
            $sid = (int)$s['section_id'];
            if (isset($validSectionIds[$sid])) {
                $sectionId = $sid;
            }
        }

        // Generate the NEW email format for this student
        $newEmail = buildStudentEmailFromName($firstName, $middleName, $lastName, $usedEmails);

        $params = [
            ':student_id' => $studentId,
            ':first_name' => $firstName,
            ':middle_name' => $middleName !== '' ? $middleName : null,
            ':last_name' => $lastName,
            ':email' => $newEmail,
            ':phone' => isset($s['phone']) && trim((string)$s['phone']) !== '' ? trim((string)$s['phone']) : null,
            ':birth_date' => isset($s['birth_date']) && trim((string)$s['birth_date']) !== '' ? trim((string)$s['birth_date']) : null,
            ':gender' => isset($s['gender']) && trim((string)$s['gender']) !== '' ? trim((string)$s['gender']) : null,
            ':address' => isset($s['address']) && trim((string)$s['address']) !== '' ? trim((string)$s['address']) : null,
            ':department' => $department,
            ':section_id' => $sectionId,
            ':yearlevel' => $yearlevel,
            ':enrollment_type' => $enrollmentType,
            ':status' => $status
        ];

        if (isset($existingStudents[$studentId])) {
            $oldEmail = $existingStudents[$studentId]['email'];
            $update->execute($params);
            
            // Update user account (search by old email if it changed)
            $userUpdate->execute([
                ':new_username' => $newEmail,
                ':new_email' => $newEmail,
                ':full_name' => trim($firstName . ' ' . $lastName),
                ':old_email' => $oldEmail
            ]);
            
            // If the email didn't change but the user account was missing, ON DUPLICATE KEY handles it
            if ($userUpdate->rowCount() === 0) {
                $userInsert->execute([
                    ':username' => $newEmail,
                    ':email' => $newEmail,
                    ':password' => $defaultPasswordHash,
                    ':full_name' => trim($firstName . ' ' . $lastName)
                ]);
            }
            
            $updated++;
        } else {
            $insert->execute($params);
            
            // Create user account
            $userInsert->execute([
                ':username' => $newEmail,
                ':email' => $newEmail,
                ':password' => $defaultPasswordHash,
                ':full_name' => trim($firstName . ' ' . $lastName)
            ]);
            
            $inserted++;
        }
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "Students processed successfully",
        "inserted" => $inserted,
        "updated" => $updated
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
