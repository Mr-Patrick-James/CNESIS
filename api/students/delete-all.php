<?php
/**
 * Delete All Students API
 * Deletes all students and their associated user accounts
 */

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

try {
    $db->beginTransaction();

    // 0. Ensure archive table exists
    $db->exec("CREATE TABLE IF NOT EXISTS `archive_students` LIKE `students`") ;
    // Add archive columns if they don't exist
    $colsStmt = $db->query("SHOW COLUMNS FROM archive_students");
    $existingCols = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('deleted_at', $existingCols)) $db->exec("ALTER TABLE archive_students ADD COLUMN deleted_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    if (!in_array('deleted_by', $existingCols)) $db->exec("ALTER TABLE archive_students ADD COLUMN deleted_by VARCHAR(100)");
    if (!in_array('delete_reason', $existingCols)) $db->exec("ALTER TABLE archive_students ADD COLUMN delete_reason TEXT");
    if (!in_array('original_id', $existingCols)) $db->exec("ALTER TABLE archive_students ADD COLUMN original_id INT");

    // 1. Delete all user accounts with role 'student'
    $deleteUsersQuery = "DELETE FROM users WHERE role = 'student'";
    $db->exec($deleteUsersQuery);

    // 2. Delete all student records
    // Archive them first
    $archiveQuery = "INSERT INTO archive_students (
        original_id, student_id, first_name, middle_name, last_name, email, phone,
        birth_date, gender, address, department, section_id, yearlevel,
        status, avatar, created_at, updated_at, deleted_at, deleted_by, delete_reason
    ) SELECT 
        id, student_id, first_name, middle_name, last_name, email, phone,
        birth_date, gender, address, department, section_id, yearlevel,
        status, avatar, created_at, updated_at, NOW(), 'Administrator', 'Bulk Deletion'
    FROM students";
    $db->exec($archiveQuery);

    $deleteStudentsQuery = "DELETE FROM students";
    $count = $db->exec($deleteStudentsQuery);

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "All $count students and their portal accounts have been deleted.",
        "count" => $count
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