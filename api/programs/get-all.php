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
    
    if ($category !== null && in_array($category, ['undergraduate', 'technical'])) {
        $query .= " AND category = :category";
    }
    
    $query .= " ORDER BY category, short_title";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    
    if ($category !== null && in_array($category, ['undergraduate', 'technical'])) {
        $stmt->bindParam(':category', $category);
    }
    
    $stmt->execute();
    
    $programs = [];
    
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
