<?php
/**
 * Test the Fixed Email API
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<h1>Test Fixed Email API</h1>";

// Test the API directly
if (isset($_POST['test_email'])) {
    $testEmail = $_POST['test_email'];
    $subject = $_POST['subject'] ?? 'Test from CNESIS';
    $message = $_POST['message'] ?? 'This is a test message from the fixed email API.';
    
    // Prepare data for API
    $data = [
        'recipient_email' => $testEmail,
        'custom_subject' => $subject,
        'custom_message' => $message
    ];
    
    // Call the API
    $ch = curl_init('http://localhost/CNESIS/api/email/send-admission-email-fixed.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "<h2>API Response</h2>";
    echo "<p><strong>HTTP Status:</strong> $httpCode</p>";
    
    if ($curlError) {
        echo "<p><strong>cURL Error:</strong> $curlError</p>";
    }
    
    echo "<p><strong>Response:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($response);
    echo "</pre>";
    
    // Parse and display result
    $result = json_decode($response, true);
    if ($result && $result['success']) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✅ Email sent successfully!</strong><br>";
        echo "Message ID: " . ($result['data']['message_id'] ?? 'N/A') . "<br>";
        echo "Sent at: " . ($result['data']['sent_at'] ?? 'N/A');
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ Email failed to send</strong><br>";
        echo "Error: " . ($result['message'] ?? 'Unknown error');
        echo "</div>";
    }
}

// Show test form
echo "<h2>Test Email Sending</h2>";
echo "<form method='post' style='max-width: 500px;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='test_email' style='display: block; margin-bottom: 5px;'><strong>To Email:</strong></label>";
echo "<input type='email' id='test_email' name='test_email' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label for='subject' style='display: block; margin-bottom: 5px;'><strong>Subject:</strong></label>";
echo "<input type='text' id='subject' name='subject' value='Test from CNESIS Fixed API' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label for='message' style='display: block; margin-bottom: 5px;'><strong>Message:</strong></label>";
echo "<textarea id='message' name='message' rows='5' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>This is a test message from the fixed CNESIS email API.</textarea>";
echo "</div>";

echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Send Test Email</button>";
echo "</form>";

echo "<h2>Quick Test Links</h2>";
echo "<ul>";
echo "<li><a href='?test_direct=1'>Test API Directly (to belugaw6@gmail.com)</a></li>";
echo "<li><a href='test_email_working.php'>Test Original Working Script</a></li>";
echo "</ul>";

// Direct test
if (isset($_GET['test_direct'])) {
    echo "<h2>Direct API Test</h2>";
    
    $data = [
        'recipient_email' => 'belugaw6@gmail.com',
        'custom_subject' => 'Direct Test - ' . date('Y-m-d H:i:s'),
        'custom_message' => 'This is a direct test of the fixed email API.'
    ];
    
    $ch = curl_init('http://localhost/CNESIS/api/email/send-admission-email-fixed.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p><strong>HTTP Status:</strong> $httpCode</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($response);
    echo "</pre>";
}

echo "<h2>Troubleshooting</h2>";
echo "<p>If emails still don't work:</p>";
echo "<ol>";
echo "<li>Check if PHPMailer is installed: <code>vendor/autoload.php</code></li>";
echo "<li>Verify SMTP credentials are correct</li>";
echo "<li>Check if Gmail allows less secure apps or use App Password</li>";
echo "<li>Review server error logs</li>";
echo "<li>Test with the simple working script first</li>";
echo "</ol>";
?>
