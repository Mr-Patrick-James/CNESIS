<?php
require_once 'api/config/database.php';

try {
    $db = (new Database())->getConnection();
    
    echo "--- Distinct Statuses ---\n";
    $stmt = $db->query("SELECT DISTINCT status FROM admissions");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
    
    echo "\n--- Table Columns ---\n";
    $stmt = $db->query("DESCRIBE admissions");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
