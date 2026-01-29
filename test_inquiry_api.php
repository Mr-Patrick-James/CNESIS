<?php
/**
 * Test Inquiry API
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<h1>Test Inquiry API</h1>";

// Test the API directly
if (isset($_POST['test_inquiry'])) {
    $fullName = $_POST['full_name'] ?? 'Test User';
    $email = $_POST['email'] ?? 'test@example.com';
    $program = $_POST['program'] ?? '1';
    $question = $_POST['question'] ?? 'This is a test inquiry.';
    
    // Prepare data for API
    $data = [
        'fullName' => $fullName,
        'email' => $email,
        'program' => $program,
        'question' => $question,
        'inquiryType' => 'admission'
    ];
    
    // Call the API
    $ch = curl_init('http://localhost/CNESIS/api/inquiries/create.php');
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
        echo "<strong>✅ Inquiry submitted successfully!</strong><br>";
        echo "Inquiry ID: " . ($result['inquiry_id'] ?? 'N/A') . "<br>";
        echo "Program: " . ($result['program_name'] ?? 'N/A') . "<br>";
        echo "Email Sent: " . ($result['email_sent'] ? 'Yes' : 'No') . "<br>";
        if (isset($result['email_error'])) {
            echo "Email Error: " . $result['email_error'];
        }
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ Inquiry failed to submit</strong><br>";
        echo "Error: " . ($result['message'] ?? 'Unknown error');
        echo "</div>";
    }
}

// Show test form
echo "<h2>Test Inquiry Submission</h2>";
echo "<form method='post' style='max-width: 500px;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='full_name' style='display: block; margin-bottom: 5px;'><strong>Full Name:</strong></label>";
echo "<input type='text' id='full_name' name='full_name' value='Test User' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label for='email' style='display: block; margin-bottom: 5px;'><strong>Email:</strong></label>";
echo "<input type='email' id='email' name='email' value='test@example.com' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label for='program' style='display: block; margin-bottom: 5px;'><strong>Program ID:</strong></label>";
echo "<input type='text' id='program' name='program' value='1' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";

echo "<div style='margin-bottom: 15px;'>";
echo "<label for='question' style='display: block; margin-bottom: 5px;'><strong>Question:</strong></label>";
echo "<textarea id='question' name='question' rows='4' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>This is a test inquiry from the CNESIS system.</textarea>";
echo "</div>";

echo "<button type='submit' name='test_inquiry' value='1' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Submit Test Inquiry</button>";
echo "</form>";

echo "<h2>Quick Test Links</h2>";
echo "<ul>";
echo "<li><a href='?test_direct=1'>Test API Directly</a></li>";
echo "<li><a href='../test_email_working.php'>Test Email System</a></li>";
echo "</ul>";

// Direct test
if (isset($_GET['test_direct'])) {
    echo "<h2>Direct API Test</h2>";
    
    $data = [
        'fullName' => 'Direct Test User',
        'email' => 'belugaw6@gmail.com',
        'program' => '1',
        'question' => 'This is a direct test of the inquiry API.',
        'inquiryType' => 'admission'
    ];
    
    $ch = curl_init('http://localhost/CNESIS/api/inquiries/create.php');
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
echo "<p>If inquiries still don't work:</p>";
echo "<ol>";
echo "<li>Check if the inquiries table was created in the database</li>";
echo "<li>Verify the API endpoint is accessible</li>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Review server error logs</li>";
echo "<li>Test the email system separately</li>";
echo "</ol>";

echo "<h2>Database Check</h2>";
echo "<p>To check if the inquiries table was created, run this SQL query:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
echo "SHOW TABLES LIKE 'inquiries';";
echo "</pre>";
?>
