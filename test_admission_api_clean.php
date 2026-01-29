<?php
/**
 * Test Clean Admission API
 * Test the fixed admission API with output buffering
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<h1>Test Clean Admission API</h1>";

// Test with unique email to avoid conflicts
$uniqueEmail = 'test' . time() . '@example.com';
$testData = [
    'application_id' => 'APP-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT),
    'student_id' => null,
    'program_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => $uniqueEmail,
    'phone' => '09123456789',
    'birthdate' => '2000-01-01',
    'gender' => 'other',
    'address' => 'Test Address',
    'high_school' => 'Test High School',
    'admission_type' => 'freshman',
    'status' => 'pending'
];

echo "<h2>Test Data:</h2>";
echo "<pre>" . htmlspecialchars(json_encode($testData, JSON_PRETTY_PRINT)) . "</pre>";

// Call the API
$ch = curl_init('http://localhost/CNESIS/api/admissions/create.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
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

echo "<p><strong>Raw Response:</strong></p>";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlspecialchars($response) . "</textarea>";

// Check if response is valid JSON
if (strpos($response, '<') === 0) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ Still returning HTML instead of JSON</strong><br>";
    echo "The output buffering fix may not be working. Check for:";
    echo "<ul>";
    echo "<li>Syntax errors in PHP files</li>";
    echo "<li>Fatal errors before ob_start()</li>";
    echo "<li>PHP configuration issues</li>";
    echo "</ul>";
    echo "</div>";
} else {
    $result = json_decode($response, true);
    if ($result) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✅ Valid JSON Response!</strong>";
        echo "</div>";
        
        echo "<h3>Parsed Response:</h3>";
        echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
        
        if ($result['success']) {
            echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>🎉 Application Created Successfully!</strong><br>";
            echo "Application ID: " . ($result['application_id'] ?? 'N/A') . "<br>";
            echo "Database ID: " . ($result['id'] ?? 'N/A') . "<br>";
            echo "Email Sent: " . ($result['email_sent'] ? 'Yes' : 'No') . "<br>";
            if (isset($result['email_error'])) {
                echo "Email Error: " . $result['email_error'];
            }
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>❌ Application Failed</strong><br>";
            echo "Error: " . ($result['message'] ?? 'Unknown error');
            echo "</div>";
        }
    } else {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>⚠️ Invalid JSON Response</strong><br>";
        echo "JSON Error: " . json_last_error_msg();
        echo "</div>";
    }
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If successful, try the admission form in the admin panel</li>";
echo "<li>If still failing, check PHP error logs</li>";
echo "<li>Verify all API files exist and are readable</li>";
echo "<li>Test with different data combinations</li>";
echo "</ol>";

echo "<p><a href='debug_admission_api.php'>Run Full Debug</a> | <a href='../test_email_working.php'>Test Email System</a></p>";
?>
