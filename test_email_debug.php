<?php
/**
 * Email Debug Test Script
 * Diagnose SMTP and attachment issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Email System Debug Test</h1>";

try {
    // Test 1: Database Connection
    echo "<h2>1. Testing Database Connection</h2>";
    include_once 'api/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        throw new Exception("Database connection failed");
    }
    echo "✅ Database connection successful<br>";
    
    // Test 2: Check email_configs table
    echo "<h2>2. Checking Email Configuration Table</h2>";
    $tableCheck = $db->query("SHOW TABLES LIKE 'email_configs'");
    if ($tableCheck->rowCount() === 0) {
        echo "❌ email_configs table not found<br>";
        echo "Creating table...<br>";
        
        $createTableSQL = "CREATE TABLE IF NOT EXISTS email_configs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            smtp_host VARCHAR(255) NOT NULL,
            smtp_port INT NOT NULL DEFAULT 587,
            smtp_username VARCHAR(255) NOT NULL,
            smtp_password VARCHAR(255) NOT NULL,
            encryption_type ENUM('tls', 'ssl') NOT NULL DEFAULT 'tls',
            from_email VARCHAR(255) NOT NULL,
            from_name VARCHAR(255) NOT NULL,
            reply_to_email VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($createTableSQL);
        echo "✅ email_configs table created<br>";
        
        // Insert default config
        $insertSQL = "INSERT INTO email_configs (smtp_host, smtp_port, smtp_username, smtp_password, encryption_type, from_email, from_name, reply_to_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($insertSQL);
        $result = $stmt->execute([
            'smtp.gmail.com',
            587,
            'belugaw6@gmail.com',
            'klotmfurniohmmjo',
            'tls',
            'belugaw6@gmail.com',
            'Colegio De Naujan',
            'belugaw6@gmail.com'
        ]);
        
        if ($result) {
            echo "✅ Default email configuration inserted<br>";
        } else {
            echo "❌ Failed to insert default configuration<br>";
        }
    } else {
        echo "✅ email_configs table exists<br>";
        
        // Check if there's an active config
        $configCheck = $db->query("SELECT * FROM email_configs WHERE is_active = TRUE LIMIT 1");
        if ($configCheck->rowCount() === 0) {
            echo "❌ No active email configuration found<br>";
        } else {
            $config = $configCheck->fetch(PDO::FETCH_ASSOC);
            echo "✅ Active email configuration found:<br>";
            echo "<pre>" . print_r($config, true) . "</pre>";
        }
    }
    
    // Test 3: Test EmailConfig class
    echo "<h2>3. Testing EmailConfig Class</h2>";
    include_once 'api/config/email_config.php';
    
    $emailConfig = new EmailConfig($db);
    $config = $emailConfig->getConfig();
    
    if ($config) {
        echo "✅ EmailConfig loaded successfully<br>";
        echo "<pre>" . print_r($config, true) . "</pre>";
    } else {
        echo "❌ Failed to load EmailConfig<br>";
    }
    
    // Test 4: Test PHPMailer
    echo "<h2>4. Testing PHPMailer</h2>";
    if (file_exists('vendor/autoload.php')) {
        echo "✅ PHPMailer autoload found<br>";
        require_once 'vendor/autoload.php';
        
        try {
            $mail = $emailConfig->getMailer();
            echo "✅ PHPMailer instance created successfully<br>";
            
            // Test SMTP connection without sending
            $mail->SMTPDebug = 2; // Enable debug
            $mail->Debugoutput = 'html';
            
            echo "<h3>SMTP Connection Test:</h3>";
            if ($mail->smtpConnect()) {
                echo "✅ SMTP connection successful<br>";
                $mail->smtpClose();
            } else {
                echo "❌ SMTP connection failed: " . $mail->ErrorInfo . "<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ PHPMailer error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ PHPMailer not found<br>";
    }
    
    // Test 5: Check attachment directories
    echo "<h2>5. Checking Attachment Directories</h2>";
    $attachmentDirs = [
        'api/assets/uploads/email-attachments',
        'assets/uploads/email-attachments'
    ];
    
    foreach ($attachmentDirs as $dir) {
        if (is_dir($dir)) {
            echo "✅ Directory exists: $dir<br>";
            if (is_writable($dir)) {
                echo "✅ Directory is writable: $dir<br>";
            } else {
                echo "❌ Directory is not writable: $dir<br>";
            }
        } else {
            echo "❌ Directory not found: $dir<br>";
            // Try to create it
            if (mkdir($dir, 0755, true)) {
                echo "✅ Created directory: $dir<br>";
            } else {
                echo "❌ Failed to create directory: $dir<br>";
            }
        }
    }
    
    // Test 6: Test actual email sending
    echo "<h2>6. Testing Email Sending</h2>";
    if (isset($_GET['test_email']) && !empty($_GET['test_email'])) {
        $testEmail = $_GET['test_email'];
        
        try {
            $attachments = [];
            if (isset($_GET['test_attachment']) && file_exists($_GET['test_attachment'])) {
                $attachments[] = [
                    'path' => $_GET['test_attachment'],
                    'name' => basename($_GET['test_attachment'])
                ];
            }
            
            $subject = "Test Email from CNESIS - " . date('Y-m-d H:i:s');
            $body = "<h2>Test Email</h2><p>This is a test email from the CNESIS admission system.</p><p>Sent at: " . date('Y-m-d H:i:s') . "</p>";
            
            $result = $emailConfig->sendEmail($testEmail, $subject, $body, $attachments);
            
            if ($result) {
                echo "✅ Test email sent successfully to $testEmail<br>";
            } else {
                echo "❌ Failed to send test email<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Email sending error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo '<p>To test email sending, add ?test_email=your@email.com to the URL</p>';
        echo '<p>To test with attachment, add &test_attachment=path/to/file.pdf</p>';
    }
    
    echo "<h2>7. Recent Email Logs</h2>";
    $logCheck = $db->query("SHOW TABLES LIKE 'email_logs'");
    if ($logCheck->rowCount() > 0) {
        $logs = $db->query("SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 10");
        echo "<table border='1'><tr><th>Time</th><th>Recipient</th><th>Subject</th><th>Success</th><th>Error</th></tr>";
        while ($log = $logs->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $log['sent_at'] . "</td>";
            echo "<td>" . $log['recipient_email'] . "</td>";
            echo "<td>" . $log['subject'] . "</td>";
            echo "<td>" . ($log['sent_successfully'] ? '✅' : '❌') . "</td>";
            echo "<td>" . ($log['error_message'] ?: 'None') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ email_logs table not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Test Complete</h2>";
?>
