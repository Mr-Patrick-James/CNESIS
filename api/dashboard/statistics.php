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

    // Get Batch Summary Statistics
    $batchSummaryQuery = "SELECT 
                            status, 
                            COUNT(*) as count 
                          FROM exam_schedules 
                          GROUP BY status";
    $batchSummaryStmt = $db->prepare($batchSummaryQuery);
    $batchSummaryStmt->execute();
    
    $batchSummary = [
        'total' => 0,
        'active' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    while ($row = $batchSummaryStmt->fetch(PDO::FETCH_ASSOC)) {
        $batchSummary[$row['status']] = (int)$row['count'];
        $batchSummary['total'] += (int)$row['count'];
    }

    // Get Active Batches Details
    $activeBatchesDetailsQuery = "SELECT 
                                    id, 
                                    batch_name, 
                                    exam_date, 
                                    start_time, 
                                    end_time, 
                                    venue, 
                                    current_slots, 
                                    max_slots 
                                  FROM exam_schedules 
                                  WHERE status = 'active' 
                                  ORDER BY exam_date ASC, start_time ASC";
    $activeBatchesDetailsStmt = $db->prepare($activeBatchesDetailsQuery);
    $activeBatchesDetailsStmt->execute();
    $activeBatchesDetails = $activeBatchesDetailsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get Admissions Trend (Last 6 months)
    $admissionsTrendQuery = "SELECT 
                                DATE_FORMAT(submitted_at, '%b %Y') as month_year,
                                COUNT(*) as count,
                                DATE_FORMAT(submitted_at, '%Y-%m') as sort_key
                              FROM admissions 
                              WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                              GROUP BY month_year, sort_key
                              ORDER BY sort_key ASC";
    $admissionsTrendStmt = $db->prepare($admissionsTrendQuery);
    $admissionsTrendStmt->execute();
    $admissionsTrend = $admissionsTrendStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get Inquiry Trend (Last 6 months)
    $inquiryTrendQuery = "SELECT 
                            DATE_FORMAT(created_at, '%b %Y') as month_year,
                            COUNT(*) as count,
                            DATE_FORMAT(created_at, '%Y-%m') as sort_key
                          FROM inquiries 
                          WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                          GROUP BY month_year, sort_key
                          ORDER BY sort_key ASC";
    $inquiryTrendStmt = $db->prepare($inquiryTrendQuery);
    $inquiryTrendStmt->execute();
    $inquiryTrend = $inquiryTrendStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get Admissions by Program (Popularity)
    $programPopularityQuery = "SELECT 
                                 p.short_title as program,
                                 COUNT(*) as count
                               FROM admissions a
                               JOIN programs p ON a.program_id = p.id
                               GROUP BY p.id, p.short_title
                               ORDER BY count DESC";
    $programPopularityStmt = $db->prepare($programPopularityQuery);
    $programPopularityStmt->execute();
    $programPopularity = $programPopularityStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get Admissions by Type
    $admissionTypesQuery = "SELECT 
                              admission_type,
                              COUNT(*) as count
                            FROM admissions
                            GROUP BY admission_type";
    $admissionTypesStmt = $db->prepare($admissionTypesQuery);
    $admissionTypesStmt->execute();
    $admissionTypes = $admissionTypesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get Student Gender Distribution
    $genderDistQuery = "SELECT 
                          gender,
                          COUNT(*) as count
                        FROM students
                        WHERE gender IS NOT NULL
                        GROUP BY gender";
    $genderDistStmt = $db->prepare($genderDistQuery);
    $genderDistStmt->execute();
    $genderDist = $genderDistStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "statistics" => [
            "total_students" => $totalStudents,
            "active_students" => $activeStudents,
            "total_program_heads" => $totalProgramHeads,
            "pending_admissions" => $pendingAdmissions,
            "active_programs" => $activePrograms,
            "students_by_year" => $studentsByYear,
            "active_batches_count" => count($examBatchesList),
            "batch_summary" => $batchSummary,
            "active_batches_details" => $activeBatchesDetails,
            "admissions_trend" => $admissionsTrend,
            "inquiry_trend" => $inquiryTrend,
            "program_popularity" => $programPopularity,
            "admission_types" => $admissionTypes,
            "gender_distribution" => $genderDist
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
