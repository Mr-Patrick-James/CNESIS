<?php
/**
 * Dashboard Statistics API
 * Returns real-time statistics for the admin dashboard
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
    // Get total students count
    $studentsQuery = "SELECT COUNT(*) as total_students FROM students";
    $studentsStmt = $db->prepare($studentsQuery);
    $studentsStmt->execute();
    $totalStudents = $studentsStmt->fetch(PDO::FETCH_ASSOC)['total_students'];
    
    // Get active students count
    $activeStudentsQuery = "SELECT COUNT(*) as active_students FROM students WHERE status = 'active'";
    $activeStudentsStmt = $db->prepare($activeStudentsQuery);
    $activeStudentsStmt->execute();
    $activeStudents = $activeStudentsStmt->fetch(PDO::FETCH_ASSOC)['active_students'];
    
    // Get program heads count
    $programHeadsQuery = "SELECT COUNT(*) as total_program_heads FROM program_heads";
    $programHeadsStmt = $db->prepare($programHeadsQuery);
    $programHeadsStmt->execute();
    $totalProgramHeads = $programHeadsStmt->fetch(PDO::FETCH_ASSOC)['total_program_heads'];
    
    // Get pending admissions count
    $pendingAdmissionsQuery = "SELECT COUNT(*) as pending_admissions FROM admissions WHERE status = 'pending'";
    $pendingAdmissionsStmt = $db->prepare($pendingAdmissionsQuery);
    $pendingAdmissionsStmt->execute();
    $pendingAdmissions = $pendingAdmissionsStmt->fetch(PDO::FETCH_ASSOC)['pending_admissions'];
    
    // Get active programs count
    $activeProgramsQuery = "SELECT COUNT(*) as active_programs FROM programs WHERE status = 'active'";
    $activeProgramsStmt = $db->prepare($activeProgramsQuery);
    $activeProgramsStmt->execute();
    $activePrograms = $activeProgramsStmt->fetch(PDO::FETCH_ASSOC)['active_programs'];
    
    // Get recent admissions (last 5)
    $recentAdmissionsQuery = "SELECT 
                                a.id,
                                a.first_name,
                                a.last_name,
                                a.email,
                                a.status,
                                a.submitted_at,
                                p.title as program_title
                              FROM admissions a
                              LEFT JOIN programs p ON a.program_id = p.id
                              ORDER BY a.submitted_at DESC
                              LIMIT 5";
    $recentAdmissionsStmt = $db->prepare($recentAdmissionsQuery);
    $recentAdmissionsStmt->execute();
    
    $recentAdmissions = [];
    while ($row = $recentAdmissionsStmt->fetch(PDO::FETCH_ASSOC)) {
        $recentAdmissions[] = [
            'id' => $row['id'],
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'email' => $row['email'],
            'program' => $row['program_title'] ?? 'N/A',
            'status' => $row['status'],
            'date' => $row['submitted_at']
        ];
    }
    
    // Get students by year level for statistics
    $studentsByYearQuery = "SELECT 
                               yearlevel,
                               COUNT(*) as count
                             FROM students 
                             WHERE yearlevel IS NOT NULL
                             GROUP BY yearlevel
                             ORDER BY yearlevel";
    $studentsByYearStmt = $db->prepare($studentsByYearQuery);
    $studentsByYearStmt->execute();
    
    $studentsByYear = [];
    while ($row = $studentsByYearStmt->fetch(PDO::FETCH_ASSOC)) {
        $studentsByYear[] = [
            'year_level' => $row['yearlevel'],
            'count' => $row['count']
        ];
    }
    
    // Get admissions by status
    $admissionsByStatusQuery = "SELECT 
                                 status,
                                 COUNT(*) as count
                               FROM admissions
                               GROUP BY status";
    $admissionsByStatusStmt = $db->prepare($admissionsByStatusQuery);
    $admissionsByStatusStmt->execute();
    
    $admissionsByStatus = [];
    while ($row = $admissionsByStatusStmt->fetch(PDO::FETCH_ASSOC)) {
        $admissionsByStatus[] = [
            'status' => $row['status'],
            'count' => $row['count']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "statistics" => [
            "total_students" => (int)$totalStudents,
            "active_students" => (int)$activeStudents,
            "total_program_heads" => (int)$totalProgramHeads,
            "pending_admissions" => (int)$pendingAdmissions,
            "active_programs" => (int)$activePrograms
        ],
        "recent_admissions" => $recentAdmissions,
        "students_by_year" => $studentsByYear,
        "admissions_by_status" => $admissionsByStatus,
        "last_updated" => date('Y-m-d H:i:s')
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
