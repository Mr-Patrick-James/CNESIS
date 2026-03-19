<?php
/**
 * Migration Script
 * Updates users table role enum to include 'student'
 */

require_once 'api/config/database.php';

echo "Updating database...<br>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    // Update role enum to include 'student'
    $db->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'staff', 'faculty', 'program_head', 'student') NOT NULL DEFAULT 'staff'");
    echo "<b>Success:</b> Updated users table role enum.<br>";
    
    // Optional: Sync existing students to users table
    $stmt = $db->query("SELECT first_name, last_name, email FROM students");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $userInsert = $db->prepare("INSERT IGNORE INTO users (username, email, password, full_name, role, status) 
                               VALUES (:username, :email, :password, :full_name, 'student', 'active')");
    
    $passwordHash = password_hash('password123', PASSWORD_DEFAULT);
    $count = 0;
    foreach ($students as $student) {
        $userInsert->execute([
            ':username' => $student['email'],
            ':email' => $student['email'],
            ':password' => $passwordHash,
            ':full_name' => $student['first_name'] . ' ' . $student['last_name']
        ]);
        if ($userInsert->rowCount() > 0) $count++;
    }
    echo "<b>Success:</b> Created $count user accounts for existing students.<br>";
    
    echo "<br><b>Database setup complete!</b> You can now delete this file.";

} catch (Exception $e) {
    echo "<b>Error:</b> " . $e->getMessage();
}
?>
