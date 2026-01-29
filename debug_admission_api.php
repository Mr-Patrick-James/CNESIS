<?php
/**
 * Debug Admission API
 * Check what the API is actually returning
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<h1>Debug Admission API</h1>";

// Test data
$testData = [
    'application_id' => 'APP-2026-9999',
    'student_id' => null,
    'program_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test' . time() . '@example.com', // Unique email to avoid conflicts
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

// Check if response starts with HTML
if (strpos($response, '<') === 0) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ API returned HTML instead of JSON!</strong><br>";
    echo "This usually means there's a PHP error or warning being output.<br>";
    echo "Check the response above for HTML tags or error messages.";
    echo "</div>";
    
    // Try to extract error message
    if (preg_match('/<br\s*\/?>\s*<b>([^<]+)<\/b>/i', $response, $matches)) {
        echo "<p><strong>Possible Error:</strong> " . htmlspecialchars($matches[1]) . "</p>";
    }
    
    if (preg_match('/<br\s*\/?>\s*(.+?)(?:<br|$)/i', $response, $matches)) {
        echo "<p><strong>Details:</strong> " . htmlspecialchars($matches[1]) . "</p>";
    }
} else {
    // Try to parse as JSON
    $result = json_decode($response, true);
    if ($result) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✅ Valid JSON Response</strong>";
        echo "</div>";
        
        echo "<h3>Parsed JSON:</h3>";
        echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
        
        if ($result['success']) {
            echo "<p><strong>Application ID:</strong> " . ($result['application_id'] ?? 'N/A') . "</p>";
            echo "<p><strong>Email Sent:</strong> " . ($result['email_sent'] ? 'Yes' : 'No') . "</p>";
        }
    } else {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>⚠️ Response is not valid JSON</strong><br>";
        echo "JSON Error: " . json_last_error_msg();
        echo "</div>";
    }
}

echo "<h2>Troubleshooting Steps:</h2>";
echo "<ol>";
echo "<li>Check if all required PHP files exist in api/config/</li>";
echo "<li>Verify database connection is working</li>";
echo "<li>Check PHP error logs for detailed errors</li>";
echo "<li>Ensure no PHP warnings are being output before JSON</li>";
echo "<li>Check if email_config.php has any syntax errors</li>";
echo "</ol>";

echo "<h2>Common Causes:</h2>";
echo "<ul>";
echo "<li>Missing database connection file</li>";
echo "<li>Database connection failure</li>";
echo "<li>PHP syntax errors in included files</li>";
echo "<li>PHP warnings/notices being output</li>";
echo "<li>Email configuration errors</li>";
echo "</ul>";

// Test database connection directly
echo "<h2>Database Connection Test:</h2>";
try {
    include_once 'api/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
        echo "<strong>❌ Database connection failed</strong>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
        echo "<strong>✅ Database connection successful</strong>";
        echo "</div>";
        
        // Test if admissions table exists
        $stmt = $db->query("SHOW TABLES LIKE 'admissions'");
        if ($stmt->rowCount() > 0) {
            echo "<p>✅ Admissions table exists</p>";
        } else {
            echo "<p>❌ Admissions table missing</p>";
        }
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<strong>Database Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
