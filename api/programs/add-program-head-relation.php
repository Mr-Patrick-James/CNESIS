<?php
/**
 * Add Program Head Relationship to Programs Table
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
    // Check if program_head_id column already exists in programs table
    $stmt = $db->query("SHOW COLUMNS FROM programs LIKE 'program_head_id'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        // Add program_head_id column to programs table
        $alterQuery = "ALTER TABLE programs ADD COLUMN program_head_id INT NULL";
        $db->exec($alterQuery);
        
        // Add foreign key constraint
        $fkQuery = "ALTER TABLE programs ADD CONSTRAINT fk_program_head 
                   FOREIGN KEY (program_head_id) REFERENCES program_heads(id) 
                   ON DELETE SET NULL ON UPDATE CASCADE";
        $db->exec($fkQuery);
        
        echo json_encode([
            "success" => true,
            "message" => "program_head_id column added to programs table with foreign key constraint"
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "message" => "program_head_id column already exists in programs table"
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        "success" => false, 
        "message" => "Database error: " . $e->getMessage()
    ]);
}

$database->closeConnection();
?>
