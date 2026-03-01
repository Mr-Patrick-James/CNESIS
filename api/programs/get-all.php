<?php
/**
 * Get All Programs API
 * Fetches all active programs from database
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

// Create database connection
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
    // Get filter parameter if provided
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : 'active';
    
    // Build query
    $query = "SELECT 
                id,
                code,
                title,
                short_title,
                category,
                department,
                description,
                duration,
                units,
                image_path,
                prospectus_path,
                enrolled_students,
                status,
                highlights,
                career_opportunities,
                admission_requirements,
                program_head_name,
                created_at,
                updated_at
              FROM programs
              WHERE status = :status";
    
    if ($category !== null && in_array($category, ['4-years', 'technical'])) {
        $query .= " AND category = :category";
    }
    
    $query .= " ORDER BY category, short_title";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    
    if ($category !== null && in_array($category, ['4-years', 'technical'])) {
        $stmt->bindParam(':category', $category);
    }
    
    $stmt->execute();
    
    $programs = [];
    $countStmt = $db->prepare("SELECT COUNT(*) as cnt 
                                FROM students s 
                                LEFT JOIN sections sec ON s.section_id = sec.id 
                                WHERE (LOWER(TRIM(s.department)) = LOWER(TRIM(:code1)) 
                                   OR LOWER(TRIM(sec.department_code)) = LOWER(TRIM(:code2)) 
                                   OR LOWER(TRIM(sec.section_name)) LIKE CONCAT(LOWER(TRIM(:code3)), '%')) 
                                AND s.status = 'active'");
    
    while ($row = $stmt->fetch()) {
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
            'enrolled_students' => (int)$row['enrolled_students'],
            'status' => $row['status'],
            'highlights' => json_decode($row['highlights'], true) ?: [],
            'career_opportunities' => json_decode($row['career_opportunities'], true) ?: [],
            'admission_requirements' => json_decode($row['admission_requirements'], true) ?: [],
            'program_head_name' => $row['program_head_name'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
        
        $code = $row['code'];
        if (!empty($code)) {
            $countStmt->bindParam(':code1', $code);
            $countStmt->bindParam(':code2', $code);
            $countStmt->bindParam(':code3', $code);
            $countStmt->execute();
            $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
            if ($countRow && isset($countRow['cnt'])) {
                $program['enrolled_students'] = (int)$countRow['cnt'];
            }
        }
        
        $programs[] = $program;
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "count" => count($programs),
        "programs" => $programs
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error fetching programs: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
