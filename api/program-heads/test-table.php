<?php
/**
 * Test Program Heads Table
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    // Check if program_heads table exists
    $stmt = $db->query("SHOW TABLES LIKE 'program_heads'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo json_encode([
            "success" => false, 
            "message" => "program_heads table does not exist"
        ]);
        exit;
    }
    
    // Get table structure
    $stmt = $db->query("DESCRIBE program_heads");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get count
    $stmt = $db->query("SELECT COUNT(*) as count FROM program_heads");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "message" => "program_heads table exists",
        "columns" => $columns,
        "count" => $count['count']
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        "success" => false, 
        "message" => "Database error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
