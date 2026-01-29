<?php
/**
 * Test Email Configuration
 * Verifies that the email system is working properly
 */

include_once 'api/config/database.php';
include_once 'api/config/email_config.php';

echo "Testing Email Configuration...\n\n";

try {
    // Test database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        echo "❌ Database connection failed\n";
        exit;
    }
    
    echo "✅ Database connection successful\n";
    
    // Test email config loading
    $emailConfig = new EmailConfig($db);
    $config = $emailConfig->getConfig();
    
    echo "✅ Email configuration loaded\n";
    echo "   SMTP Host: " . $config['smtp_host'] . "\n";
    echo "   SMTP Port: " . $config['smtp_port'] . "\n";
    echo "   From Email: " . $config['from_email'] . "\n";
    echo "   From Name: " . $config['from_name'] . "\n";
    
    // Test PHPMailer instance creation
    try {
        $mailer = $emailConfig->getMailer();
        echo "✅ PHPMailer instance created successfully\n";
    } catch (Exception $e) {
        echo "❌ PHPMailer creation failed: " . $e->getMessage() . "\n";
        exit;
    }
    
    // Test email template loading
    $query = "SELECT COUNT(*) as count FROM email_templates WHERE is_active = TRUE";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $templateCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "✅ Email templates available: " . $templateCount . "\n";
    
    // Test sending a simple email (optional)
    $sendTest = false; // Set to true to actually send a test email
    
    if ($sendTest) {
        echo "\n📧 Sending test email...\n";
        
        $testEmail = [
            'recipient_email' => 'belugaw6@gmail.com',
            'template_name' => 'application_received',
            'variables' => [
                'first_name' => 'Test',
                'last_name' => 'Student',
                'program_title' => 'Bachelor of Science in Information Systems',
                'application_id' => 'TEST-001'
            ]
        ];
        
        $result = $emailConfig->sendEmail(
            $testEmail['recipient_email'],
            'Test Email - Colegio De Naujan',
            '<h1>This is a test email from Colegio De Naujan</h1><p>If you receive this, the email system is working!</p>',
            []
        );
        
        if ($result) {
            echo "✅ Test email sent successfully!\n";
        } else {
            echo "❌ Test email failed\n";
        }
    } else {
        echo "\n💡 To send a test email, set \$sendTest = true in this script\n";
    }
    
    echo "\n🎉 Email system is ready!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
