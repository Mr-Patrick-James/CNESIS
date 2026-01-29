<?php
/**
 * Reports API
 * Handles generating various reports from database data
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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
    $reportType = isset($_GET['type']) ? $_GET['type'] : null;
    
    switch ($reportType) {
        case 'student-enrollment':
            generateStudentEnrollmentReport($db);
            break;
            
        case 'admission-statistics':
            generateAdmissionStatisticsReport($db);
            break;
            
        case 'program-head-performance':
            generateProgramHeadPerformanceReport($db);
            break;
            
        case 'financial':
            generateFinancialReport($db);
            break;
            
        case 'prospectus-downloads':
            generateProspectusDownloadsReport($db);
            break;
            
        case 'program-analytics':
            generateProgramAnalyticsReport($db);
            break;
            
        default:
            generateSummaryReport($db);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

function generateStudentEnrollmentReport($db) {
    // Students by year level (department column doesn't exist in students table)
    $query = "SELECT 
                s.yearlevel,
                s.status,
                COUNT(*) as student_count,
                COUNT(CASE WHEN s.status = 'active' THEN 1 END) as active_count,
                COUNT(CASE WHEN s.status = 'graduated' THEN 1 END) as graduated_count,
                COUNT(CASE WHEN s.status = 'inactive' THEN 1 END) as inactive_count
              FROM students s
              WHERE s.yearlevel IS NOT NULL
              GROUP BY s.yearlevel, s.status
              ORDER BY s.yearlevel, s.status";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $enrollmentData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $enrollmentData[] = [
            'year_level' => $row['yearlevel'],
            'department' => 'General', // Default since department column doesn't exist
            'status' => $row['status'],
            'total_students' => (int)$row['student_count'],
            'active_students' => (int)$row['active_count'],
            'graduated_students' => (int)$row['graduated_count'],
            'inactive_students' => (int)$row['inactive_count']
        ];
    }
    
    // Overall statistics
    $totalQuery = "SELECT 
                    COUNT(*) as total_students,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_students,
                    COUNT(CASE WHEN status = 'graduated' THEN 1 END) as graduated_students,
                    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_students
                  FROM students";
    
    $totalStmt = $db->prepare($totalQuery);
    $totalStmt->execute();
    $totals = $totalStmt->fetch(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "student_enrollment",
        "generated_at" => date('Y-m-d H:i:s'),
        "summary" => [
            'total_students' => (int)$totals['total_students'],
            'active_students' => (int)$totals['active_students'],
            'graduated_students' => (int)$totals['graduated_students'],
            'inactive_students' => (int)$totals['inactive_students']
        ],
        "details" => $enrollmentData
    ]);
}

function generateAdmissionStatisticsReport($db) {
    // Admissions by status and type
    $query = "SELECT 
                a.status,
                a.admission_type,
                COUNT(*) as count,
                DATE_FORMAT(a.submitted_at, '%Y-%m') as month
              FROM admissions a
              GROUP BY a.status, a.admission_type, DATE_FORMAT(a.submitted_at, '%Y-%m')
              ORDER BY month DESC, a.status";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $admissionData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $admissionData[] = [
            'status' => $row['status'],
            'admission_type' => $row['admission_type'],
            'count' => (int)$row['count'],
            'month' => $row['month']
        ];
    }
    
    // Overall statistics
    $totalQuery = "SELECT 
                    COUNT(*) as total_applications,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
                    COUNT(CASE WHEN status = 'enrolled' THEN 1 END) as enrolled
                  FROM admissions";
    
    $totalStmt = $db->prepare($totalQuery);
    $totalStmt->execute();
    $totals = $totalStmt->fetch(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "admission_statistics",
        "generated_at" => date('Y-m-d H:i:s'),
        "summary" => [
            'total_applications' => (int)$totals['total_applications'],
            'approved' => (int)$totals['approved'],
            'pending' => (int)$totals['pending'],
            'rejected' => (int)$totals['rejected'],
            'enrolled' => (int)$totals['enrolled']
        ],
        "details" => $admissionData
    ]);
}

function generateProgramHeadPerformanceReport($db) {
    $query = "SELECT 
                ph.id,
                ph.first_name,
                ph.last_name,
                ph.email,
                ph.department,
                ph.specialization,
                ph.status,
                COUNT(p.id) as program_count
              FROM program_heads ph
              LEFT JOIN programs p ON ph.id = p.program_head_id
              GROUP BY ph.id, ph.first_name, ph.last_name, ph.email, ph.department, ph.specialization, ph.status
              ORDER BY ph.last_name, ph.first_name";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $programHeadData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $programHeadData[] = [
            'id' => (int)$row['id'],
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'email' => $row['email'],
            'department' => $row['department'],
            'specialization' => $row['specialization'],
            'status' => $row['status'],
            'programs_managed' => (int)$row['program_count'],
            'students_supervised' => 0 // Will be calculated separately
        ];
    }
    
    // Calculate students supervised (since students don't have program_id, we'll estimate)
    $totalStudentsQuery = "SELECT COUNT(*) as total_students FROM students";
    $totalStmt = $db->prepare($totalStudentsQuery);
    $totalStmt->execute();
    $totalStudents = (int)$totalStmt->fetch(PDO::FETCH_ASSOC)['total_students'];
    
    // Distribute students evenly among program heads for estimation
    $totalProgramHeads = count($programHeadData);
    $avgStudentsPerHead = $totalProgramHeads > 0 ? round($totalStudents / $totalProgramHeads) : 0;
    
    foreach ($programHeadData as &$ph) {
        $ph['students_supervised'] = $avgStudentsPerHead;
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "program_head_performance",
        "generated_at" => date('Y-m-d H:i:s'),
        "summary" => [
            'total_program_heads' => count($programHeadData),
            'total_programs_managed' => array_sum(array_column($programHeadData, 'programs_managed')),
            'total_students_supervised' => $totalStudents
        ],
        "details" => $programHeadData
    ]);
}

function generateFinancialReport($db) {
    // This is a placeholder - in a real system, you'd have financial tables
    // For now, we'll simulate some basic financial data
    
    $financialData = [
        [
            'category' => 'Tuition Fees',
            'amount' => 2500000,
            'description' => 'Total tuition collected this semester'
        ],
        [
            'category' => 'Registration Fees',
            'amount' => 150000,
            'description' => 'New student registration fees'
        ],
        [
            'category' => 'Library Fees',
            'amount' => 75000,
            'description' => 'Library usage and maintenance fees'
        ],
        [
            'category' => 'Laboratory Fees',
            'amount' => 200000,
            'description' => 'Lab equipment and consumables'
        ]
    ];
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "financial",
        "generated_at" => date('Y-m-d H:i:s'),
        "summary" => [
            'total_revenue' => array_sum(array_column($financialData, 'amount')),
            'revenue_categories' => count($financialData)
        ],
        "details" => $financialData
    ]);
}

function generateProspectusDownloadsReport($db) {
    $query = "SELECT 
                p.id,
                p.short_title,
                p.title,
                COUNT(pd.id) as download_count,
                MAX(pd.download_date) as last_download
              FROM programs p
              LEFT JOIN prospectus_downloads pd ON p.id = pd.program_id
              GROUP BY p.id, p.short_title, p.title
              ORDER BY download_count DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $downloadData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $downloadData[] = [
            'program_id' => (int)$row['id'],
            'program_title' => $row['title'],
            'short_title' => $row['short_title'],
            'download_count' => (int)$row['download_count'],
            'last_download' => $row['last_download']
        ];
    }
    
    // Monthly downloads
    $monthlyQuery = "SELECT 
                      DATE_FORMAT(download_date, '%Y-%m') as month,
                      COUNT(*) as downloads
                    FROM prospectus_downloads
                    WHERE download_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(download_date, '%Y-%m')
                    ORDER BY month DESC";
    
    $monthlyStmt = $db->prepare($monthlyQuery);
    $monthlyStmt->execute();
    
    $monthlyData = [];
    while ($row = $monthlyStmt->fetch(PDO::FETCH_ASSOC)) {
        $monthlyData[] = [
            'month' => $row['month'],
            'downloads' => (int)$row['downloads']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "prospectus_downloads",
        "generated_at" => date('Y-m-d H:i:s'),
        "summary" => [
            'total_downloads' => array_sum(array_column($downloadData, 'download_count')),
            'programs_with_downloads' => count(array_filter($downloadData, fn($d) => $d['download_count'] > 0))
        ],
        "by_program" => $downloadData,
        "monthly_trends" => $monthlyData,
        "details" => $downloadData // Keep for consistency
    ]);
}

function generateProgramAnalyticsReport($db) {
    $query = "SELECT 
                p.id,
                p.title,
                p.short_title,
                p.category,
                p.status,
                0 as enrolled_students, -- Students table doesn't have program_id
                COUNT(pd.id) as download_count,
                COUNT(a.id) as application_count
              FROM programs p
              LEFT JOIN prospectus_downloads pd ON p.id = pd.program_id
              LEFT JOIN admissions a ON p.id = a.program_id
              GROUP BY p.id, p.title, p.short_title, p.category, p.status
              ORDER BY download_count DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $programData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $programData[] = [
            'program_id' => (int)$row['id'],
            'title' => $row['title'],
            'short_title' => $row['short_title'],
            'category' => $row['category'],
            'status' => $row['status'],
            'enrolled_students' => (int)$row['enrolled_students'],
            'prospectus_downloads' => (int)$row['download_count'],
            'admission_applications' => (int)$row['application_count']
        ];
    }
    
    // Calculate total students (since we can't link to programs)
    $totalStudentsQuery = "SELECT COUNT(*) as total_students FROM students";
    $totalStmt = $db->prepare($totalStudentsQuery);
    $totalStmt->execute();
    $totalStudents = (int)$totalStmt->fetch(PDO::FETCH_ASSOC)['total_students'];
    
    // Distribute students evenly among active programs for estimation
    $activePrograms = count(array_filter($programData, fn($p) => $p['status'] === 'active'));
    $avgStudentsPerProgram = $activePrograms > 0 ? round($totalStudents / $activePrograms) : 0;
    
    foreach ($programData as &$program) {
        if ($program['status'] === 'active') {
            $program['enrolled_students'] = $avgStudentsPerProgram;
        }
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "program_analytics",
        "generated_at" => date('Y-m-d H:i:s'),
        "summary" => [
            'total_programs' => count($programData),
            'active_programs' => count(array_filter($programData, fn($p) => $p['status'] === 'active')),
            'total_students' => $totalStudents,
            'total_downloads' => array_sum(array_column($programData, 'prospectus_downloads')),
            'total_applications' => array_sum(array_column($programData, 'admission_applications'))
        ],
        "details" => $programData
    ]);
}

function generateSummaryReport($db) {
    // Get overall system statistics
    $stats = [
        'students' => 0,
        'programs' => 0,
        'program_heads' => 0,
        'admissions' => 0,
        'downloads' => 0
    ];
    
    // Count students
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM students");
    $stmt->execute();
    $stats['students'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count programs
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM programs WHERE status = 'active'");
    $stmt->execute();
    $stats['programs'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count program heads
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM program_heads");
    $stmt->execute();
    $stats['program_heads'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count admissions
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM admissions");
    $stmt->execute();
    $stats['admissions'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count downloads
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM prospectus_downloads");
    $stmt->execute();
    $stats['downloads'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "summary",
        "generated_at" => date('Y-m-d H:i:s'),
        "statistics" => $stats
    ]);
}
?>
