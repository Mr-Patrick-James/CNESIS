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
            
        case 'admission-statistics':
            generateAdmissionStatisticsReport($db);
            break;
            
            
            
        case 'prospectus-downloads':
            generateProspectusDownloadsReport($db);
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



function generateAdmissionStatisticsReport($db) {
    // Check for status filter
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    
    // Admissions by status and type
    $query = "SELECT 
                a.status,
                a.admission_type,
                COUNT(*) as count,
                DATE_FORMAT(a.submitted_at, '%Y-%m') as month
              FROM admissions a";
              
    if ($status) {
        $query .= " WHERE a.status = :status";
    } else {
        $query .= " WHERE a.status != 'enrolled'";
    }
    
    $query .= " GROUP BY a.status, a.admission_type, DATE_FORMAT(a.submitted_at, '%Y-%m')
              ORDER BY month DESC, a.status";
    
    $stmt = $db->prepare($query);
    
    if ($status) {
        $stmt->bindParam(':status', $status);
    }
    
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
                    COUNT(CASE WHEN status = 'scheduled' THEN 1 END) as scheduled,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
                  FROM admissions";
                  
    if ($status) {
        $totalQuery .= " WHERE status = :status";
    } else {
        $totalQuery .= " WHERE status != 'enrolled'";
    }
    
    $totalStmt = $db->prepare($totalQuery);
    
    if ($status) {
        $totalStmt->bindParam(':status', $status);
    }
    
    $totalStmt->execute();
    $totals = $totalStmt->fetch(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "admission-statistics",
        "generated_at" => date('Y-m-d H:i:s'),
        "requested_status" => $status,
        "summary" => [
            'total_applications' => (int)$totals['total_applications'],
            'approved' => (int)$totals['approved'],
            'scheduled' => (int)$totals['scheduled'],
            'pending' => (int)$totals['pending'],
            'rejected' => (int)$totals['rejected']
        ],
        "details" => $admissionData
    ]);
}





function generateProspectusDownloadsReport($db) {
    // Check if table exists, create if not
    try {
        $db->query("SELECT 1 FROM prospectus_downloads LIMIT 1");
    } catch (PDOException $e) {
        // Table doesn't exist, create it
        $sql = "CREATE TABLE IF NOT EXISTS prospectus_downloads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            program_id INT NOT NULL,
            download_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            ip_address VARCHAR(45),
            user_agent TEXT,
            FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $db->exec($sql);
    }

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
    $totalDownloads = 0;
    $programsWithDownloads = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count = (int)$row['download_count'];
        $downloadData[] = [
            'program_id' => (int)$row['id'],
            'program_code' => $row['short_title'],
            'program_title' => $row['title'],
            'download_count' => $count,
            'last_download' => $row['last_download']
        ];
        
        $totalDownloads += $count;
        if ($count > 0) {
            $programsWithDownloads++;
        }
    }
    
    // Monthly trends
    $trendsQuery = "SELECT 
                      DATE_FORMAT(download_date, '%Y-%m') as month,
                      COUNT(*) as download_count
                    FROM prospectus_downloads
                    GROUP BY DATE_FORMAT(download_date, '%Y-%m')
                    ORDER BY month DESC
                    LIMIT 12";
                    
    $trendsStmt = $db->prepare($trendsQuery);
    $trendsStmt->execute();
    
    $monthlyTrends = [];
    while ($row = $trendsStmt->fetch(PDO::FETCH_ASSOC)) {
        $monthlyTrends[] = [
            'month' => $row['month'],
            'downloads' => (int)$row['download_count']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "prospectus-downloads",
        "generated_at" => date('Y-m-d H:i:s'),
        "summary" => [
            'total_downloads' => $totalDownloads,
            'programs_with_downloads' => $programsWithDownloads
        ],
        "by_program" => $downloadData,
        "monthly_trends" => $monthlyTrends
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
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM admissions WHERE status != 'enrolled'");
    $stmt->execute();
    $stats['admissions'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count downloads
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM prospectus_downloads");
        $stmt->execute();
        $stats['downloads'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (PDOException $e) {
        // Table likely doesn't exist, default to 0
        $stats['downloads'] = 0;
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "report_type" => "summary",
        "generated_at" => date('Y-m-d H:i:s'),
        "statistics" => $stats
    ]);
}
?>
