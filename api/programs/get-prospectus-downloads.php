<?php
/**
 * Get Prospectus Downloads API
 * Retrieves download statistics for admin dashboard
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
    // Check if program_id is provided (optional - if not provided, get all programs)
    $programId = isset($_GET['program_id']) ? $_GET['program_id'] : null;
    
    if ($programId !== null) {
        // Get download count for specific program
        $query = "CALL sp_get_prospectus_download_count(:program_id)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':program_id', $programId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $downloadCount = $result['download_count'];
        
        // Also get program info
        $programQuery = "SELECT code, title, short_title FROM programs WHERE id = :program_id";
        $programStmt = $db->prepare($programQuery);
        $programStmt->bindParam(':program_id', $programId);
        $programStmt->execute();
        
        $program = $programStmt->fetch(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "program" => $program,
            "download_count" => (int)$downloadCount
        ]);
    } else {
        // Get download counts for all programs
        $query = "SELECT 
                    p.id,
                    p.code,
                    p.title,
                    p.short_title,
                    p.category,
                    p.status,
                    COALESCE(downloads.download_count, 0) as download_count
                  FROM programs p
                  LEFT JOIN (
                      SELECT 
                          program_id,
                          COUNT(*) as download_count
                      FROM prospectus_downloads
                      GROUP BY program_id
                  ) downloads ON p.id = downloads.program_id
                  ORDER BY p.category, p.short_title";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = [
                'id' => $row['id'],
                'code' => $row['code'],
                'title' => $row['title'],
                'short_title' => $row['short_title'],
                'category' => $row['category'],
                'status' => $row['status'],
                'download_count' => (int)$row['download_count']
            ];
        }
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "download_stats" => $results
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>