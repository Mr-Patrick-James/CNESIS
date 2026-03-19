<?php
/**
 * Student Portal Migration Script
 * Creates tables for subjects and class schedules
 */

require_once 'api/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Student Portal Database Setup</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    // 1. Create subjects table
    $sql = "CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_code VARCHAR(20) NOT NULL UNIQUE,
        subject_title VARCHAR(200) NOT NULL,
        units INT NOT NULL DEFAULT 3,
        department_code VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $db->exec($sql);
    echo "✅ Table 'subjects' created.<br>";
    
    // 2. Create class_schedules table
    $sql = "CREATE TABLE IF NOT EXISTS class_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_id INT NOT NULL,
        section_id INT NOT NULL,
        instructor_name VARCHAR(255),
        day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        room VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
        FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $db->exec($sql);
    echo "✅ Table 'class_schedules' created.<br>";
    
    echo "<br><b>Setup complete!</b> You can now access the student portal features.";

} catch (Exception $e) {
    echo "<b style='color:red;'>Error:</b> " . $e->getMessage();
}
?>
