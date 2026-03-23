<?php
/**
 * Get All Admissions API
 * Returns all admission applications with program details
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

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
    // AUTO-MIGRATION: Ensure deployment columns exist
    $colsStmt = $db->query("SHOW COLUMNS FROM admissions");
    $existingCols = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('assigned_department', $existingCols)) {
        $db->exec("ALTER TABLE admissions ADD COLUMN assigned_department VARCHAR(100) DEFAULT NULL AFTER notes");
    }
    if (!in_array('assigned_section_id', $existingCols)) {
        $db->exec("ALTER TABLE admissions ADD COLUMN assigned_section_id INT DEFAULT NULL AFTER assigned_department");
    }
    if (!in_array('assigned_year_level', $existingCols)) {
        $db->exec("ALTER TABLE admissions ADD COLUMN assigned_year_level INT DEFAULT NULL AFTER assigned_section_id");
    }
    if (!in_array('assigned_section_name', $existingCols)) {
        $db->exec("ALTER TABLE admissions ADD COLUMN assigned_section_name VARCHAR(100) DEFAULT NULL AFTER assigned_year_level");
    }

    // Debug: Get raw status counts
    $debugStmt = $db->query("SELECT status, COUNT(*) as count FROM admissions GROUP BY status");
    $statusCounts = $debugStmt->fetchAll(PDO::FETCH_ASSOC);

    // Build query with program join - removed draft/new filter to ensure all data is fetched
    $query = "SELECT 
                a.id,
                a.application_id,
                a.student_id,
                a.first_name,
                a.middle_name,
                a.last_name,
                a.email,
                a.phone,
                a.birthdate,
                a.gender,
                a.address,
                a.high_school,
                a.last_school,
                a.year_graduated,
                a.gwa,
                a.entrance_exam_score,
                a.admission_type,
                a.previous_program,
                a.status,
                a.exam_schedule_id,
                a.submitted_at,
                a.reviewed_at,
                a.reviewed_by,
                a.notes,
                a.assigned_department,
                a.assigned_section_id,
                a.assigned_year_level,
                a.assigned_section_name,
                p.title as program_title,
                p.short_title as program_short_title,
                p.code as program_code,
                p.category as program_category,
                es.batch_name as batch_name,
                s.section_name as joined_section_name
              FROM admissions a
              LEFT JOIN programs p ON a.program_id = p.id
              LEFT JOIN exam_schedules es ON a.exam_schedule_id = es.id
              LEFT JOIN sections s ON a.assigned_section_id = s.id
              ORDER BY a.submitted_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $admissions = [];
    
    while ($row = $stmt->fetch()) {
        $admissions[] = [
            'id' => $row['id'],
            'application_id' => $row['application_id'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'birthdate' => $row['birthdate'],
            'gender' => $row['gender'],
            'address' => $row['address'],
            'high_school' => $row['high_school'],
            'last_school' => $row['last_school'],
            'year_graduated' => $row['year_graduated'],
            'gwa' => $row['gwa'],
            'entrance_exam_score' => $row['entrance_exam_score'],
            'admission_type' => $row['admission_type'],
            'previous_program' => $row['previous_program'],
            'status' => $row['status'],
            'exam_schedule_id' => $row['exam_schedule_id'],
            'submitted_at' => $row['submitted_at'],
            'reviewed_at' => $row['reviewed_at'],
            'reviewed_by' => $row['reviewed_by'],
            'notes' => $row['notes'],
            'assigned_department' => $row['assigned_department'],
            'assigned_section_id' => $row['assigned_section_id'],
            'assigned_year_level' => $row['assigned_year_level'],
            'assigned_section_name' => $row['assigned_section_name'] ?? $row['joined_section_name'],
            'program_title' => $row['program_title'],
            'program_short_title' => $row['program_short_title'],
            'program_code' => $row['program_code'],
            'program_category' => $row['program_category'],
            'batch_name' => $row['batch_name']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "count" => count($admissions),
        "status_counts" => $statusCounts,
        "admissions" => $admissions
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
