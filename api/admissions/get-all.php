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
    // Build query with program join
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
                a.submitted_at,
                a.reviewed_at,
                a.reviewed_by,
                a.notes,
                p.title as program_title,
                p.code as program_code,
                p.category as program_category
              FROM admissions a
              LEFT JOIN programs p ON a.program_id = p.id
              WHERE a.status NOT IN ('draft', 'new')
              ORDER BY a.submitted_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $admissions = [];
    
    while ($row = $stmt->fetch()) {
        $admissions[] = [
            'id' => $row['id'],
            'application_id' => $row['application_id'],
            'student_id' => $row['student_id'],
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
            'submitted_at' => $row['submitted_at'],
            'reviewed_at' => $row['reviewed_at'],
            'reviewed_by' => $row['reviewed_by'],
            'notes' => $row['notes'],
            'program_title' => $row['program_title'],
            'program_code' => $row['program_code'],
            'program_category' => $row['program_category']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "count" => count($admissions),
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
