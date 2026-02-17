<?php
header("Content-Type: text/plain");
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "Database connected.\n";
        
        // Check attachments column
        $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'attachments'");
        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE admissions ADD COLUMN attachments TEXT DEFAULT NULL COMMENT 'JSON object of file paths' AFTER status";
            $db->exec($sql);
            echo "Column 'attachments' added successfully.\n";
        } else {
            echo "Column 'attachments' already exists.\n";
        }
        
        // Check form_data column
        $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'form_data'");
        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE admissions ADD COLUMN form_data LONGTEXT DEFAULT NULL COMMENT 'Full JSON dump of form data' AFTER attachments";
            $db->exec($sql);
            echo "Column 'form_data' added successfully.\n";
        } else {
            echo "Column 'form_data' already exists.\n";
        }
        
    } else {
        echo "Failed to connect to database.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
