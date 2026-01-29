<?php
/**
 * Simple Email Test without Database
 * Test PHPMailer and SMTP directly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Email Test (No Database)</h1>";

// Test 1: Check PHPMailer
echo "<h2>1. Testing PHPMailer</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "✅ PHPMailer autoload found<br>";
    require_once 'vendor/autoload.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "✅ PHPMailer instance created successfully<br>";
        
        // Configure SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'belugaw6@gmail.com';
        $mail->Password = 'klotmfurniohmmjo';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Set sender
        $mail->setFrom('belugaw6@gmail.com', 'Colegio De Naujan');
        
        echo "✅ SMTP configuration set<br>";
        
        // Test connection
        echo "<h3>Testing SMTP Connection:</h3>";
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        
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

// Test 2: Test actual email sending
echo "<h2>2. Testing Email Sending</h2>";
if (isset($_GET['send_test']) && !empty($_GET['email'])) {
    $testEmail = $_GET['email'];
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configure SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'belugaw6@gmail.com';
        $mail->Password = 'klotmfurniohmmjo';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Set sender and recipient
        $mail->setFrom('belugaw6@gmail.com', 'Colegio De Naujan');
        $mail->addAddress($testEmail);
        $mail->addReplyTo('belugaw6@gmail.com', 'Colegio De Naujan');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from CNESIS - ' . date('Y-m-d H:i:s');
        
        $htmlBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Test Email</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                .header { background: #1a365d; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; background: #f9f9f9; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
                .footer { background: #2c5282; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Colegio De Naujan</h1>
                <p>Test Email</p>
            </div>
            <div class="content">
                <h2>Email System Test</h2>
                <p>This is a test email from the CNESIS admission system.</p>
                <p><strong>Sent at:</strong> ' . date('Y-m-d H:i:s') . '</p>
                <p><strong>To:</strong> ' . htmlspecialchars($testEmail) . '</p>
                <p>If you receive this email, the SMTP system is working correctly.</p>
            </div>
            <div class="footer">
                <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </body>
        </html>';
        
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);
        
        // Add attachment if specified
        if (isset($_GET['attachment']) && file_exists($_GET['attachment'])) {
            $mail->addAttachment($_GET['attachment'], basename($_GET['attachment']));
            echo "✅ Attachment added: " . basename($_GET['attachment']) . "<br>";
        }
        
        echo "<h3>Sending email...</h3>";
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        
        $result = $mail->send();
        
        if ($result) {
            echo "✅ Email sent successfully to $testEmail<br>";
            echo "✅ Message ID: " . $mail->getLastMessageID() . "<br>";
        } else {
            echo "❌ Failed to send email<br>";
            echo "❌ Error: " . $mail->ErrorInfo . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Email sending error: " . $e->getMessage() . "<br>";
    }
} else {
    echo '<p>To test email sending, add ?send_test=1&email=your@email.com to the URL</p>';
    echo '<p>To test with attachment, add &attachment=path/to/file.pdf</p>';
}

// Test 3: Check available files for attachment testing
echo "<h2>3. Available Files for Attachment Testing</h2>";
$testFiles = [
    'assets/documents/application-form.pdf',
    'assets/documents/requirements-list.pdf',
    'assets/documents/school-policies.pdf'
];

foreach ($testFiles as $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "✅ Found: $file (" . number_format($size/1024, 2) . " KB)<br>";
    } else {
        echo "❌ Not found: $file<br>";
    }
}

echo "<h2>Test Complete</h2>";
?>
