<?php
/**
 * Get All Inquiries API
 * Retrieves all admission inquiries for admin panel
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
    // Get all inquiries with ordering
    $query = "SELECT 
                id,
                inquiry_id,
                full_name,
                email,
                phone,
                program_id,
                program_name,
                question,
                inquiry_type,
                status,
                notes,
                created_at
              FROM inquiries 
              WHERE inquiry_type = 'admission'
              ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Inquiries retrieved successfully",
        "inquiries" => $inquiries,
        "count" => count($inquiries)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error retrieving inquiries: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
