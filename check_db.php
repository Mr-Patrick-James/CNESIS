<?php
header("Content-Type: text/plain");
require_once __DIR__ . '/api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "Database connected.\n";
        
        $stmt = $db->query("SHOW COLUMNS FROM admissions");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "Columns: " . implode(", ", $columns) . "\n";
        
        if (in_array('attachments', $columns)) {
            echo "Column 'attachments' exists.\n";
        } else {
            echo "Column 'attachments' MISSING.\n";
        }
        
        if (in_array('form_data', $columns)) {
            echo "Column 'form_data' exists.\n";
        } else {
            echo "Column 'form_data' MISSING.\n";
        }
        
    } else {
        echo "Failed to connect to database.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>