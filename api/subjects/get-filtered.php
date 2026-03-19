<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$program_code = isset($_GET['program_code']) ? $_GET['program_code'] : null;
$year_level = isset($_GET['year_level']) ? $_GET['year_level'] : null;
$semester = isset($_GET['semester']) ? $_GET['semester'] : null;

try {
    if ($program_code && $year_level && $semester) {
        // Get subjects linked to this program, year level, and semester in the prospectus
        $query = "SELECT s.* FROM subjects s
                  JOIN prospectus p ON s.id = p.subject_id
                  WHERE p.program_code = ? AND p.year_level = ? AND p.semester = ?
                  ORDER BY s.subject_code ASC";
        $stmt = $db->prepare($query);
        $stmt->execute([$program_code, $year_level, $semester]);
    } else if ($program_code && $year_level) {
        // Fallback for when semester is not provided but program and year are
        $query = "SELECT s.* FROM subjects s
                  JOIN prospectus p ON s.id = p.subject_id
                  WHERE p.program_code = ? AND p.year_level = ?
                  ORDER BY s.subject_code ASC";
        $stmt = $db->prepare($query);
        $stmt->execute([$program_code, $year_level]);
    } else if ($program_code) {
        // Filter only by department (for irregular students or general view)
        $query = "SELECT * FROM subjects WHERE department_code = ? ORDER BY subject_code ASC";
        $stmt = $db->prepare($query);
        $stmt->execute([$program_code]);
    } else {
        // Fallback to all subjects if no filters provided
        $query = "SELECT * FROM subjects ORDER BY subject_code ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
    }
    
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "subjects" => $subjects]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>