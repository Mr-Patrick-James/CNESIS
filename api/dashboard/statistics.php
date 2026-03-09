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
    
    // Get all pending admissions for notification modal
    $pendingListQuery = "SELECT 
                            a.id,
                            a.first_name,
                            a.last_name,
                            a.email,
                            a.status,
                            a.submitted_at,
                            p.title as program_title
                          FROM admissions a
                          LEFT JOIN programs p ON a.program_id = p.id
                          WHERE a.status = 'pending'
                          ORDER BY a.submitted_at DESC";
    $pendingListStmt = $db->prepare($pendingListQuery);
    $pendingListStmt->execute();
    
    $pendingAdmissionsList = [];
    while ($row = $pendingListStmt->fetch(PDO::FETCH_ASSOC)) {
        $pendingAdmissionsList[] = [
            'type' => 'admission',
            'id' => $row['id'],
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'email' => $row['email'],
            'program' => $row['program_title'] ?? 'N/A',
            'status' => $row['status'],
            'date' => $row['submitted_at']
        ];
    }

    // Get active/starting exam batches for notification modal
    $batchesQuery = "SELECT 
                        id, 
                        batch_name, 
                        exam_date, 
                        start_time, 
                        end_time, 
                        venue, 
                        current_slots, 
                        max_slots,
                        created_at
                     FROM exam_schedules 
                     WHERE status = 'active' 
                     AND (exam_date >= CURDATE())
                     ORDER BY exam_date ASC, start_time ASC";
    $batchesStmt = $db->prepare($batchesQuery);
    $batchesStmt->execute();
    
    $examBatchesList = [];
    $today = date('Y-m-d');
    while ($row = $batchesStmt->fetch(PDO::FETCH_ASSOC)) {
        $examBatchesList[] = [
            'type' => 'exam_batch',
            'id' => $row['id'],
            'name' => $row['batch_name'],
            'date' => $row['exam_date'],
            'time' => $row['start_time'] . ' - ' . $row['end_time'],
            'venue' => $row['venue'],
            'slots' => $row['current_slots'] . '/' . $row['max_slots'],
            'is_today' => ($row['exam_date'] === $today),
            'created_at' => $row['created_at']
        ];
    }
    
    // Combine notifications and sort by date/relevance
    $allNotifications = array_merge($pendingAdmissionsList, $examBatchesList);
    
    // Sort logic: Exam batches for TODAY first, then latest admissions
    usort($allNotifications, function($a, $b) {
        if ($a['type'] === 'exam_batch' && $a['is_today'] && !($b['type'] === 'exam_batch' && $b['is_today'])) return -1;
        if (!($a['type'] === 'exam_batch' && $a['is_today']) && $b['type'] === 'exam_batch' && $b['is_today']) return 1;
        
        $dateA = $a['type'] === 'admission' ? $a['date'] : $a['created_at'];
        $dateB = $b['type'] === 'admission' ? $b['date'] : $b['created_at'];
        return strtotime($dateB) - strtotime($dateA);
    });

    // Get recent admissions (last 5) for dashboard table
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
                              WHERE a.status NOT IN ('draft', 'new')
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
    
    echo json_encode([
        "success" => true,
        "statistics" => [
            "total_students" => $totalStudents,
            "active_students" => $activeStudents,
            "total_program_heads" => $totalProgramHeads,
            "pending_admissions" => $pendingAdmissions,
            "active_programs" => $activePrograms,
            "students_by_year" => $studentsByYear,
            "active_batches_count" => count($examBatchesList)
        ],
        "recent_admissions" => $recentAdmissions,
        "notifications" => $allNotifications
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
