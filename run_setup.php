<?php
/**
 * Database Setup Runner
 * Executes the setup.sql file to create all tables
 */

// Database connection
$host = 'localhost';
$dbname = 'cnesis_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n";
    
    // Read and execute setup.sql
    $setupFile = __DIR__ . '/database/setup.sql';
    if (file_exists($setupFile)) {
        $sql = file_get_contents($setupFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                    echo "✅ Executed: " . substr($statement, 0, 50) . "...\n";
                } catch (Exception $e) {
                    echo "❌ Error: " . $e->getMessage() . "\n";
                    echo "Statement: " . substr($statement, 0, 100) . "...\n";
                }
            }
        }
        
        echo "\n🎉 Database setup completed!\n";
        
        // Check if email_documents table exists
        $result = $pdo->query("SHOW TABLES LIKE 'email_documents'");
        if ($result->rowCount() > 0) {
            echo "✅ email_documents table created successfully!\n";
        } else {
            echo "❌ email_documents table was not created\n";
        }
        
    } else {
        echo "❌ setup.sql file not found\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
?>
