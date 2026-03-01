<?php
/**
 * Get Single Program API
 * Fetches one program by ID
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid program ID"]);
        exit;
    }
    
    // Build query
    $query = "SELECT 
                p.id,
                p.code,
                p.title,
                p.short_title,
                p.category,
                p.department,
                p.description,
                p.duration,
                p.units,
                p.image_path,
                p.prospectus_path,
                p.enrolled_students,
                p.status,
                p.highlights,
                p.career_opportunities,
                p.admission_requirements,
                p.program_head_name,
                p.created_at,
                p.updated_at,
                COALESCE(downloads.download_count, 0) as download_count
              FROM programs p
              LEFT JOIN (
                  SELECT 
                      program_id,
                      COUNT(*) as download_count
                  FROM prospectus_downloads
                  GROUP BY program_id
              ) downloads ON p.id = downloads.program_id
              WHERE p.id = :id";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Program not found"]);
        exit;
    }
    
    $row = $stmt->fetch();
    
    // Count enrolled students for this program code
    $countQuery = "SELECT COUNT(*) as cnt 
                    FROM students s 
                    LEFT JOIN sections sec ON s.section_id = sec.id 
                    WHERE (LOWER(TRIM(s.department)) = LOWER(TRIM(:code1)) 
                       OR LOWER(TRIM(sec.department_code)) = LOWER(TRIM(:code2)) 
                       OR LOWER(TRIM(sec.section_name)) LIKE CONCAT(LOWER(TRIM(:code3)), '%')) 
                    AND s.status = 'active'";
    $countStmt = $db->prepare($countQuery);
    $countStmt->bindParam(':code1', $row['code']);
    $countStmt->bindParam(':code2', $row['code']);
    $countStmt->bindParam(':code3', $row['code']);
    $countStmt->execute();
    $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
    $enrolledCount = $countRow ? (int)$countRow['cnt'] : 0;
    
    // Parse JSON fields
    $program = [
        'id' => $row['id'],
        'code' => $row['code'],
        'title' => $row['title'],
        'short_title' => $row['short_title'],
        'category' => $row['category'],
        'department' => $row['department'],
        'description' => $row['description'],
        'duration' => $row['duration'],
        'units' => $row['units'],
        'image_path' => $row['image_path'],
        'prospectus_path' => $row['prospectus_path'],
        'enrolled_students' => $enrolledCount,
        'download_count' => (int)$row['download_count'],
        'status' => $row['status'],
        'highlights' => json_decode($row['highlights'], true) ?: [],
        'career_opportunities' => json_decode($row['career_opportunities'], true) ?: [],
        'admission_requirements' => json_decode($row['admission_requirements'], true) ?: [],
        'program_head_name' => $row['program_head_name'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ];

    http_response_code(200);
    echo json_encode(["success" => true, "program" => $program]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$database->closeConnection();
?>
