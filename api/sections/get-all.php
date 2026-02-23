<?php
/**
 * Get All Sections API
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once __DIR__ . '/../config/database.php';

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
    $query = "SELECT * FROM sections ORDER BY section_name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $sections = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sections[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "sections" => $sections
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>