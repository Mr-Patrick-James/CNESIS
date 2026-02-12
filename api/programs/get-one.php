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
    
    $query = "SELECT 
                p.*,
                CONCAT(ph.first_name, ' ', ph.last_name) as program_head_name,
                ph.email as program_head_email,
                ph.phone as program_head_phone
              FROM programs p
              LEFT JOIN program_heads ph ON p.program_head_id = ph.id
              WHERE p.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    $row = $stmt->fetch();
    
    if ($row) {
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
            'program_head_id' => $row['program_head_id'],
            'program_head_name' => $row['program_head_name'],
            'program_head_email' => $row['program_head_email'],
            'program_head_phone' => $row['program_head_phone'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
        
        http_response_code(200);
        echo json_encode(["success" => true, "program" => $program]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Program not found"]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$database->closeConnection();
?>
