<?php
/**
 * Test Minimal API
 * Test the stripped down API to isolate the issue
 */

header("Content-Type: text/html; charset=UTF-8");

echo "<h1>Test Minimal Admission API</h1>";

// Test data
$testData = [
    'email' => 'test' . time() . '@example.com',
    'first_name' => 'Test',
    'last_name' => 'User'
];

echo "<h2>Test Data:</h2>";
echo "<pre>" . htmlspecialchars(json_encode($testData, JSON_PRETTY_PRINT)) . "</pre>";

// Test with cURL
$ch = curl_init('http://localhost/CNESIS/api/admissions/create_minimal.php');
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

echo "<h2>cURL Test Results:</h2>";
echo "<p><strong>HTTP Status:</strong> $httpCode</p>";

if ($curlError) {
    echo "<p><strong>cURL Error:</strong> $curlError</p>";
}

echo "<p><strong>Raw Response:</strong></p>";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlspecialchars($response) . "</textarea>";

// Test with file_get_contents as alternative
echo "<h2>file_get_contents Test:</h2>";

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($testData),
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);
$result = file_get_contents('http://localhost/CNESIS/api/admissions/create_minimal.php', false, $context);

echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

// Analyze both responses
echo "<h2>Analysis:</h2>";

function analyzeResponse($response, $method) {
    echo "<h3>$method Analysis:</h3>";
    
    if ($response === false) {
        echo "<p style='color: red;'>❌ Failed to get response</p>";
        return;
    }
    
    if (strpos(trim($response), '<') === 0) {
        echo "<p style='color: red;'>❌ Response starts with HTML</p>";
        
        // Extract potential error
        if (preg_match('/<br\s*\/?>\s*(.+?)(?:<br|<div|<\/)/i', $response, $matches)) {
            echo "<p><strong>Possible Error:</strong> " . htmlspecialchars($matches[1]) . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Response does not start with HTML</p>";
    }
    
    $jsonResult = json_decode($response, true);
    if ($jsonResult !== null) {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
        echo "<pre>" . htmlspecialchars(json_encode($jsonResult, JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠️ Not valid JSON: " . json_last_error_msg() . "</p>";
    }
}

analyzeResponse($response, "cURL");
analyzeResponse($result, "file_get_contents");

echo "<h2>Troubleshooting Steps:</h2>";
echo "<ol>";
echo "<li>If minimal API works, the issue is in the full API</li>";
echo "<li>If minimal API fails, there's a server configuration issue</li>";
echo "<li>Check PHP error logs for detailed errors</li>";
echo "<li>Verify .htaccess is not interfering</li>";
echo "<li>Check if there are any PHP warnings being output</li>";
echo "</ol>";

// Test direct file access
echo "<h2>Direct File Access:</h2>";
$direct = file_get_contents('http://localhost/CNESIS/api/admissions/create_minimal.php');
if ($direct !== false) {
    echo "<p>✅ File is accessible via HTTP</p>";
    $preview = substr($direct, 0, 300);
    echo "<p>Preview:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($preview) . "</pre>";
} else {
    echo "<p>❌ File not accessible via HTTP</p>";
}
?>
