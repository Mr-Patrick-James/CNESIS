<?php
/**
 * Get Student History API
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Student ID is required"]);
    exit;
}

try {
    // Auto-create table if missing to prevent SQL errors shown in screenshot
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

    $query = "SELECT h.*, u.full_name as changed_by_name, s.section_name as old_section, s2.section_name as new_section,
                     d.department_name as old_dept, d2.department_name as new_dept
              FROM student_history h
              LEFT JOIN users u ON h.changed_by = u.id
              LEFT JOIN sections s ON h.field_name = 'section_id' AND h.old_value = s.id
              LEFT JOIN sections s2 ON h.field_name = 'section_id' AND h.new_value = s2.id
              LEFT JOIN departments d ON h.field_name = 'department' AND h.old_value = d.department_code
              LEFT JOIN departments d2 ON h.field_name = 'department' AND h.new_value = d2.department_code
              WHERE h.student_id = ?
              ORDER BY h.changed_at DESC";
              
    $stmt = $db->prepare($query);
    $stmt->execute([$studentId]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "history" => $history
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
