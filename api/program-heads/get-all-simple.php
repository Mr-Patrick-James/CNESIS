<?php
/**
 * Get All Program Heads API
 * Returns all program heads for dropdown selection
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
    $query = "SELECT 
                id, 
                employee_id, 
                CONCAT(first_name, ' ', last_name) as full_name,
                email,
                department,
                specialization,
                status
              FROM program_heads 
              WHERE status = 'active'
              ORDER BY full_name";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $programHeads = [];
    
    while ($row = $stmt->fetch()) {
        $programHeads[] = [
            'id' => $row['id'],
            'employee_id' => $row['employee_id'],
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'department' => $row['department'],
            'specialization' => $row['specialization'],
            'status' => $row['status']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "count" => count($programHeads),
        "program_heads" => $programHeads
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
