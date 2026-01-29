<?php
/**
 * Test Inquiry API Fix
 * Test the fixed inquiry API
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<h1>Test Inquiry API Fix</h1>";

// Test data for inquiry
$inquiryData = [
    'fullName' => 'Test User',
    'email' => 'test' . time() . '@example.com',
    'program' => '1',
    'question' => 'This is a test inquiry.',
    'inquiryType' => 'admission'
];

echo "<h2>Test Data:</h2>";
echo "<pre>" . htmlspecialchars(json_encode($inquiryData, JSON_PRETTY_PRINT)) . "</pre>";

// Test the inquiry API
$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($inquiryData),
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);

echo "<h2>Testing Inquiry API:</h2>";
$result = file_get_contents('http://localhost/CNESIS/api/inquiries/create.php', false, $context);

echo "<p><strong>Raw Response:</strong></p>";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

// Check if it's valid JSON
$json = json_decode($result, true);
if ($json) {
    echo "<p style='color: green;'>✅ Inquiry API returns valid JSON!</p>";
    echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
    
    if ($json['success']) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>🎉 Inquiry Created Successfully!</strong><br>";
        echo "Inquiry ID: " . ($json['inquiry_id'] ?? 'N/A') . "<br>";
        echo "Program: " . ($json['program_name'] ?? 'N/A') . "<br>";
        echo "Email Sent: " . ($json['email_sent'] ? 'Yes' : 'No') . "<br>";
        if (isset($json['email_error'])) {
            echo "Email Error: " . $json['email_error'];
        }
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ Inquiry Failed</strong><br>";
        echo "Error: " . ($json['message'] ?? 'Unknown error');
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ Inquiry API does not return valid JSON</p>";
    echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
    
    // Show first 200 chars
    $preview = substr($result, 0, 200);
    echo "<p><strong>First 200 characters:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($preview) . "</pre>";
    
    // Try to extract error message
    if (preg_match('/<br\s*\/?>\s*(.+?)(?:<br|<div|<\/)/i', $result, $matches)) {
        echo "<p><strong>Possible Error:</strong> " . htmlspecialchars($matches[1]) . "</p>";
    }
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If this test works, the inquiry form should now work</li>";
echo "<li>Try the inquiry form on the admission page</li>";
echo "<li>If still failing, check database connection</li>";
echo "<li>Verify the inquiries table was created</li>";
echo "</ol>";

echo "<p><a href='../view/admission.php'>Test Inquiry Form</a> | <a href='check_database.php'>Check Database</a></p>";
?>
