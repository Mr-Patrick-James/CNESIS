<?php
/**
 * Delete Student API with Archive Support
 * Archives student records before deletion for recovery
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../includes/ArchiveManager.php';

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

// Get student ID and delete reason
$studentId = null;
$deleteReason = 'Manual deletion by administrator';
$deletedBy = 'Administrator';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $studentId = isset($_GET['id']) ? $_GET['id'] : null;
    $deleteReason = isset($_GET['reason']) ? $_GET['reason'] : $deleteReason;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $studentId = isset($data->id) ? $data->id : null;
    $deleteReason = isset($data->reason) ? $data->reason : $deleteReason;
    $deletedBy = isset($data->deleted_by) ? $data->deleted_by : $deletedBy;
}

if (!$studentId) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Student ID is required"
    ]);
    exit;
}

// Validate student ID
if (!is_numeric($studentId)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid student ID"
    ]);
    exit;
}

try {
    // Check if student exists
    $checkQuery = "SELECT id, student_id, first_name, last_name FROM students WHERE id = :id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $studentId);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Student not found"
        ]);
        exit;
    }
    
    $student = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    // Ensure archive table exists and has archive metadata columns (schema may differ per environment)
    $db->exec("CREATE TABLE IF NOT EXISTS `archive_students` LIKE `students`");
    $colsStmt = $db->query("SHOW COLUMNS FROM archive_students");
    $existingCols = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('deleted_at', $existingCols, true)) {
        $db->exec("ALTER TABLE archive_students ADD COLUMN deleted_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    }
    if (!in_array('deleted_by', $existingCols, true)) {
        $db->exec("ALTER TABLE archive_students ADD COLUMN deleted_by VARCHAR(100)");
    }
    if (!in_array('delete_reason', $existingCols, true)) {
        $db->exec("ALTER TABLE archive_students ADD COLUMN delete_reason TEXT");
    }
    if (!in_array('original_id', $existingCols, true)) {
        $db->exec("ALTER TABLE archive_students ADD COLUMN original_id INT");
    }
    $colStmt = $db->query("SHOW COLUMNS FROM archive_students LIKE 'batch'");
    if ($colStmt->rowCount() === 0) {
        $db->exec("ALTER TABLE archive_students ADD COLUMN batch VARCHAR(50) DEFAULT '' AFTER delete_reason");
    }

    // Get student details to check for graduated status and calculate batch
    $studentDetailsStmt = $db->prepare("SELECT status, student_id FROM students WHERE id = :id");
    $studentDetailsStmt->bindParam(':id', $studentId);
    $studentDetailsStmt->execute();
    $studentData = $studentDetailsStmt->fetch(PDO::FETCH_ASSOC);
    
    $batch = '';
    if ($studentData && $studentData['status'] === 'graduated') {
        $studentIdStr = (string)$studentData['student_id'];
        if (preg_match('/^(\d{4})/', $studentIdStr, $matches)) {
            $startYear = intval($matches[1]);
            $gradYear = $startYear + 4;
            $batch = "$startYear-$gradYear Batch";
        } else {
            $gradYear = date('Y');
            $startYear = $gradYear - 4;
            $batch = "$startYear-$gradYear Batch";
        }
    }

    // Start transaction
    $db->beginTransaction();
    
    // Insert into archive table
    $archiveQuery = "INSERT INTO archive_students (
        original_id, student_id, first_name, middle_name, last_name, email, phone,
        birth_date, gender, address, department, section_id, yearlevel,
        status, avatar, created_at, updated_at, deleted_at, deleted_by, delete_reason, batch
    ) SELECT 
        id, student_id, first_name, middle_name, last_name, email, phone,
        birth_date, gender, address, department, section_id, yearlevel,
        status, avatar, created_at, updated_at, NOW(), :deleted_by, :delete_reason, :batch
    FROM students WHERE id = :id";
    
    $archiveStmt = $db->prepare($archiveQuery);
    $archiveStmt->bindParam(':id', $studentId);
    $archiveStmt->bindParam(':deleted_by', $deletedBy);
    $archiveStmt->bindParam(':delete_reason', $deleteReason);
    $archiveStmt->bindParam(':batch', $batch);
    
    if ($archiveStmt->execute()) {
        // Delete the original student
        $deleteQuery = "DELETE FROM students WHERE id = :id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $studentId);
        
        if ($deleteStmt->execute()) {
            $db->commit();
            
            echo json_encode([
                "success" => true,
                "message" => "Student archived successfully",
                "archived" => true,
                "data" => [
                    "archived_id" => $studentId,
                    "student_id" => $student['student_id'],
                    "name" => $student['first_name'] . ' ' . $student['last_name'],
                    "reason" => $deleteReason,
                    "deleted_by" => $deletedBy
                ]
            ]);
        } else {
            $db->rollBack();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete original student"
            ]);
        }
    } else {
        $db->rollBack();
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Failed to archive student"
        ]);
    }
    
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Error archiving student: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error archiving student: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("General error archiving student: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error archiving student: " . $e->getMessage()
    ]);
}
?>
