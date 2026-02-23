<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/api/config/database.php';

echo "Starting database setup...<br>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        die("Connection failed. Check error logs.");
    }
    
    // Create exam_schedules table
    $sql = "CREATE TABLE IF NOT EXISTS exam_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        batch_name VARCHAR(100) NOT NULL,
        exam_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        venue VARCHAR(255) NOT NULL,
        max_slots INT NOT NULL,
        current_slots INT DEFAULT 0,
        status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    echo "Table 'exam_schedules' created or already exists.<br>";
    
    // Check if exam_schedule_id exists in admissions table
    $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'exam_schedule_id'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$column) {
        $sql = "ALTER TABLE admissions ADD COLUMN exam_schedule_id INT NULL DEFAULT NULL AFTER status";
        $db->exec($sql);
        echo "Column 'exam_schedule_id' added to 'admissions' table.<br>";
        
        // Add foreign key constraint
        try {
            $sql = "ALTER TABLE admissions ADD CONSTRAINT fk_exam_schedule 
                    FOREIGN KEY (exam_schedule_id) REFERENCES exam_schedules(id) ON DELETE SET NULL";
            $db->exec($sql);
            echo "Foreign key constraint added.<br>";
        } catch (PDOException $e) {
            echo "Foreign key constraint might already exist or failed: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Column 'exam_schedule_id' already exists in 'admissions' table.<br>";
    }

    echo "Database setup completed successfully.<br>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
