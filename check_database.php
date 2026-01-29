<?php
/**
 * Check Database Status
 * Verify if the database and tables exist
 */

echo "<h1>Database Status Check</h1>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Connected to MySQL server</p>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'cnesis_db'");
    $dbExists = $stmt->rowCount() > 0;
    
    if ($dbExists) {
        echo "<p style='color: green;'>✅ Database 'cnesis_db' exists</p>";
        
        // Connect to the database
        $pdo = new PDO("mysql:host=localhost;dbname=cnesis_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if admissions table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'admissions'");
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            echo "<p style='color: green;'>✅ Table 'admissions' exists</p>";
            
            // Check table structure
            $stmt = $pdo->query("DESCRIBE admissions");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Admissions Table Structure:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Check if programs table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'programs'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✅ Table 'programs' exists</p>";
                
                // Count programs
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM programs");
                $count = $stmt->fetch()['count'];
                echo "<p>Programs in database: $count</p>";
                
                if ($count > 0) {
                    echo "<p style='color: green;'>✅ Database has sample data</p>";
                } else {
                    echo "<p style='color: orange;'>⚠️ Programs table is empty</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Table 'programs' missing</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Table 'admissions' missing</p>";
            echo "<p>You need to run the setup.sql file to create the tables.</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database 'cnesis_db' does not exist</p>";
        echo "<p>You need to create the database first. Run the setup.sql file in phpMyAdmin.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Check if MySQL is running and if the credentials are correct.</p>";
}

echo "<h2>Quick Fixes:</h2>";
echo "<ol>";
echo "<li>If database doesn't exist: Import setup.sql in phpMyAdmin</li>";
echo "<li>If tables are missing: Run the setup.sql file</li>";
echo "<li>If connection fails: Check MySQL service</li>";
echo "<li>After fixing, test the API again</li>";
echo "</ol>";

echo "<p><a href='quick_test.php'>Test API Again</a> | <a href='../database/setup.sql'>View Setup SQL</a></p>";
?>
