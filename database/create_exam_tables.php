<?php
require_once __DIR__ . '/../api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
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
    echo "Table 'exam_schedules' created successfully.\n";
    
    // Add exam_schedule_id to admissions table if it doesn't exist
    $columns = $db->query("SHOW COLUMNS FROM admissions LIKE 'exam_schedule_id'")->fetchAll();
    if (empty($columns)) {
        $sql = "ALTER TABLE admissions ADD COLUMN exam_schedule_id INT NULL DEFAULT NULL AFTER status;";
        $db->exec($sql);
        echo "Column 'exam_schedule_id' added to 'admissions' table.\n";
        
        // Add foreign key constraint
        $sql = "ALTER TABLE admissions ADD CONSTRAINT fk_exam_schedule 
                FOREIGN KEY (exam_schedule_id) REFERENCES exam_schedules(id) ON DELETE SET NULL;";
        $db->exec($sql);
        echo "Foreign key constraint added.\n";
    } else {
        echo "Column 'exam_schedule_id' already exists in 'admissions' table.\n";
    }

    // Add exam_status column to admissions table if needed (or just rely on status)
    // The user mentioned "students who are still verifying". 
    // Let's assume we might need a specific status for exam scheduling if not using the main status.
    // However, keeping it simple: main status 'pending' -> 'scheduled_exam' -> 'exam_passed'/'exam_failed' -> 'interview_scheduled' etc.
    // For now, let's stick to adding the schedule ID. We can update the main status to 'scheduled' if needed.
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
