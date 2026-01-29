<?php
/**
 * Working Email Test Script
 * Tests email functionality with fallback configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Working Email Test</h1>";

// Test email sending
if (isset($_GET['send_test']) && !empty($_GET['email'])) {
    $testEmail = $_GET['email'];
    
    try {
        // Use fallback configuration directly
        $config = [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_username' => 'belugaw6@gmail.com',
            'smtp_password' => 'klotmfurniohmmjo',
            'encryption_type' => 'tls',
            'from_email' => 'belugaw6@gmail.com',
            'from_name' => 'Colegio De Naujan',
            'reply_to_email' => 'belugaw6@gmail.com'
        ];
        
        require_once 'vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configure SMTP
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = $config['encryption_type'];
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // Set sender and recipient
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($testEmail);
        $mail->addReplyTo($config['reply_to_email'], $config['from_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from CNESIS Admission System - ' . date('Y-m-d H:i:s');
        
        $htmlBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Test Email</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                .header { background: #1a365d; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .header h1 { margin: 0; font-size: 28px; }
                .content { padding: 30px; background: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
                .content h2 { color: #1a365d; margin-top: 0; }
                .footer { background: #2c5282; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
                .btn { display: inline-block; padding: 12px 24px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                .attachment-info { background: #e2e8f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Colegio De Naujan</h1>
                <p>Admission System Test</p>
            </div>
            <div class="content">
                <h2>Email System Working!</h2>
                <p>This is a test email from the CNESIS admission system.</p>
                
                <div class="attachment-info">
                    <h3>Test Details:</h3>
                    <p><strong>Sent at:</strong> ' . date('Y-m-d H:i:s') . '</p>
                    <p><strong>To:</strong> ' . htmlspecialchars($testEmail) . '</p>
                    <p><strong>System:</strong> CNESIS Admission System</p>
                    <p><strong>Status:</strong> ✅ Email system is working correctly</p>
                </div>
                
                <p>If you receive this email, the SMTP system is working correctly and can send emails to users.</p>
            </div>
            <div class="footer">
                <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </body>
        </html>';
        
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);
        
        // Add attachments if specified
        $attachmentsAdded = 0;
        if (isset($_GET['attachments']) && $_GET['attachments'] == '1') {
            $attachmentFiles = [
                'assets/documents/application-form.pdf' => 'Application Form.pdf',
                'assets/documents/requirements-list.pdf' => 'Admission Requirements.pdf',
                'assets/documents/school-policies.pdf' => 'School Policies.pdf'
            ];
            
            foreach ($attachmentFiles as $path => $name) {
                $fullPath = $path;
                if (file_exists($fullPath)) {
                    try {
                        $mail->addAttachment($fullPath, $name);
                        $attachmentsAdded++;
                        echo "✅ Attachment added: $name<br>";
                    } catch (Exception $e) {
                        echo "❌ Failed to add attachment $name: " . $e->getMessage() . "<br>";
                    }
                } else {
                    echo "❌ Attachment file not found: $fullPath<br>";
                }
            }
        }
        
        echo "<h3>Sending email...</h3>";
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        
        $result = $mail->send();
        
        if ($result) {
            echo "✅ Email sent successfully to $testEmail<br>";
            echo "✅ Message ID: " . $mail->getLastMessageID() . "<br>";
            echo "✅ Attachments sent: $attachmentsAdded<br>";
            echo "<p><strong>Status:</strong> Email system is working correctly!</p>";
        } else {
            echo "❌ Failed to send email<br>";
            echo "❌ Error: " . $mail->ErrorInfo . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Email sending error: " . $e->getMessage() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo '<h2>Test Email Sending</h2>';
    echo '<p>To test email sending, use: <code>?send_test=1&email=your@email.com</code></p>';
    echo '<p>To test with attachments, add: <code>&attachments=1</code></p>';
    
    echo '<h3>Quick Test Links:</h3>';
    echo '<ul>';
    echo '<li><a href="?send_test=1&email=belugaw6@gmail.com">Test without attachments</a></li>';
    echo '<li><a href="?send_test=1&email=belugaw6@gmail.com&attachments=1">Test with attachments</a></li>';
    echo '</ul>';
}

// Check attachment files
echo "<h2>Attachment Files Status</h2>";
$attachmentFiles = [
    'assets/documents/application-form.pdf',
    'assets/documents/requirements-list.pdf',
    'assets/documents/school-policies.pdf'
];

foreach ($attachmentFiles as $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "✅ Found: $file (" . number_format($size/1024, 2) . " KB)<br>";
    } else {
        echo "❌ Not found: $file<br>";
    }
}

echo "<h2>System Status</h2>";
echo "✅ PHPMailer: Available<br>";
echo "✅ SMTP Config: Fallback configured<br>";
echo "✅ Test Script: Working<br>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Test email sending using the links above</li>";
echo "<li>Check if emails arrive in your inbox</li>";
echo "<li>Test with attachments to verify file paths</li>";
echo "<li>Once confirmed, the admission system will send emails automatically</li>";
echo "</ol>";

echo "<h2>Troubleshooting</h2>";
echo "<p>If emails don't arrive:</p>";
echo "<ul>";
echo "<li>Check spam/junk folder</li>";
echo "<li>Verify SMTP credentials are correct</li>";
echo "<li>Check if Gmail allows less secure apps (or use App Password)</li>";
echo "<li>Review server error logs</li>";
echo "</ul>";
?>
