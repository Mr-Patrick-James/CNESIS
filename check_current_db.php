<?php
/**
 * Check Current Database Status
 * See what tables actually exist
 */

echo "<h1>Current Database Status</h1>";

try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'cnesis_db'");
    $dbExists = $stmt->rowCount() > 0;
    
    if ($dbExists) {
        echo "<p style='color: green;'>✅ Database 'cnesis_db' exists</p>";
        
        // Connect to the database
        $pdo = new PDO("mysql:host=localhost;dbname=cnesis_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // List all tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h2>Tables in database:</h2>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Check specific tables we need
        $requiredTables = ['programs', 'admissions', 'users', 'inquiries'];
        echo "<h2>Required Tables Status:</h2>";
        
        foreach ($requiredTables as $table) {
            if (in_array($table, $tables)) {
                echo "<p style='color: green;'>✅ $table exists</p>";
                
                // Count records
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $count = $stmt->fetch()['count'];
                echo "<p>   Records: $count</p>";
            } else {
                echo "<p style='color: red;'>❌ $table missing</p>";
            }
        }
        
        // Check if inquiries table needs to be created
        if (!in_array('inquiries', $tables)) {
            echo "<h3>Creating inquiries table...</h3>";
            try {
                $createSQL = "CREATE TABLE inquiries (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    inquiry_id VARCHAR(50) NOT NULL UNIQUE,
                    full_name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(20) DEFAULT NULL,
                    program_id INT NOT NULL,
                    program_name VARCHAR(255) NOT NULL,
                    question TEXT NOT NULL,
                    inquiry_type ENUM('general', 'admission', 'program', 'requirements', 'other') DEFAULT 'general',
                    status ENUM('new', 'responded', 'closed') DEFAULT 'new',
                    notes TEXT DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    responded_at TIMESTAMP NULL,
                    responded_by INT NULL,
                    INDEX idx_email (email),
                    INDEX idx_status (status),
                    INDEX idx_inquiry_type (inquiry_type),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                $pdo->exec($createSQL);
                echo "<p style='color: green;'>✅ Inquiries table created successfully</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Failed to create inquiries table: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database 'cnesis_db' does not exist</p>";
        echo "<p>You need to create the database first by importing setup.sql</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Test Inquiry API:</h2>";
echo "<p><a href='test_inquiry_fix.php'>Test Inquiry API Now</a></p>";
?>
