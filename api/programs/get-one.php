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
              WHERE id = :id";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Program not found"]);
        exit;
    }
    
    $row = $stmt->fetch();
    
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

    http_response_code(200);
    echo json_encode(["success" => true, "program" => $program]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$database->closeConnection();
?>
