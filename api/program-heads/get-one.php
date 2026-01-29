<?php
/**
 * Get Single Program Head API
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
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Program head ID is required"
        ]);
        exit;
    }
    
    $query = "SELECT * FROM program_heads WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "program_head" => [
                'id' => $result['id'],
                'employee_id' => $result['employee_id'],
                'first_name' => $result['first_name'],
                'middle_name' => $result['middle_name'],
                'last_name' => $result['last_name'],
                'email' => $result['email'],
                'phone' => $result['phone'],
                'department' => $result['department'],
                'specialization' => $result['specialization'],
                'hire_date' => $result['hire_date'],
                'status' => $result['status'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Program head not found"
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