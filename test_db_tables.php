<?php
/**
 * Test Database Tables
 * Check if required tables exist and create them if needed
 */

echo "Checking database tables...\n\n";

// Database connection using mysqli (since PDO has issues)
$host = 'localhost';
$dbname = 'cnesis_db';
$username = 'root';
$password = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ Connected to database\n";
    
    // Check if email_documents table exists
    $result = $conn->query("SHOW TABLES LIKE 'email_documents'");
    if ($result->num_rows > 0) {
        echo "✅ email_documents table exists\n";
    } else {
        echo "❌ email_documents table missing - creating...\n";
        
        $sql = "CREATE TABLE email_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            document_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INT NOT NULL,
            file_type VARCHAR(100) NOT NULL,
            category ENUM('application-form', 'requirements', 'policies', 'general', 'templates', 'other') DEFAULT 'general',
            description TEXT DEFAULT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            uploaded_by INT NOT NULL DEFAULT 1,
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            download_count INT DEFAULT 0,
            last_used TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($conn->query($sql)) {
            echo "✅ email_documents table created\n";
        } else {
            echo "❌ Error creating email_documents: " . $conn->error . "\n";
        }
    }
    
    // Check other tables
    $tables = ['email_logs', 'email_templates', 'email_configs', 'admission_status_log'];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "✅ $table table exists\n";
        } else {
            echo "❌ $table table missing\n";
        }
    }
    
    // Test document upload API
    echo "\n📧 Testing document API...\n";
    
    // Insert a test document
    $insertSql = "INSERT INTO email_documents (document_name, file_path, file_size, file_type, category, uploaded_by) 
                  VALUES ('test-document.pdf', 'assets/documents/test-document.pdf', 1024, 'application/pdf', 'general', 1)";
    
    if ($conn->query($insertSql)) {
        echo "✅ Test document inserted\n";
        
        // Test retrieval
        $result = $conn->query("SELECT * FROM email_documents LIMIT 1");
        if ($result->num_rows > 0) {
            $doc = $result->fetch_assoc();
            echo "✅ Document retrieval works: " . $doc['document_name'] . "\n";
        }
        
        // Clean up test document
        $conn->query("DELETE FROM email_documents WHERE document_name = 'test-document.pdf'");
        echo "✅ Test document cleaned up\n";
    } else {
        echo "❌ Error inserting test document: " . $conn->error . "\n";
    }
    
    echo "\n🎉 Database check completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

if (isset($conn)) {
    $conn->close();
}
?>
