<?php
/**
 * Test Raw API Output
 * See exactly what the API is returning
 */

// Test the API with file_get_contents to see raw output
$url = 'http://localhost/CNESIS/api/admissions/create.php';
$data = [
    'application_id' => 'APP-TEST-001',
    'student_id' => null,
    'program_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test' . time() . '@example.com',
    'phone' => '09123456789',
    'birthdate' => '2000-01-01',
    'gender' => 'other',
    'address' => 'Test Address',
    'high_school' => 'Test High School',
    'admission_type' => 'freshman',
    'status' => 'pending'
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true  // Don't throw on HTTP errors
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "<h1>Raw API Output Test</h1>";
echo "<h2>Request Data:</h2>";
echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";

echo "<h2>Raw Response:</h2>";
echo "<textarea style='width: 100%; height: 300px; font-family: monospace; white-space: pre-wrap;'>" . htmlspecialchars($result) . "</textarea>";

// Analyze the response
echo "<h2>Response Analysis:</h2>";

if ($result === false) {
    echo "<p style='color: red;'>❌ Failed to get response from API</p>";
} else {
    // Check first characters
    $firstChars = substr($result, 0, 100);
    echo "<p><strong>First 100 characters:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($firstChars) . "</pre>";
    
    // Check if it starts with HTML
    if (strpos(trim($result), '<') === 0) {
        echo "<p style='color: red;'>❌ Response starts with HTML tag</p>";
        
        // Try to extract error messages
        if (preg_match('/<br\s*\/?>\s*(.+?)(?:<br|<div|<\/)/i', $result, $matches)) {
            echo "<p><strong>Possible Error:</strong> " . htmlspecialchars($matches[1]) . "</p>";
        }
        
        if (preg_match('/<b>([^<]+)<\/b>/i', $result, $matches)) {
            echo "<p><strong>Error Type:</strong> " . htmlspecialchars($matches[1]) . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Response does not start with HTML</p>";
    }
    
    // Try to parse as JSON
    $jsonResult = json_decode($result, true);
    if ($jsonResult !== null) {
        echo "<p style='color: green;'>✅ Valid JSON</p>";
        echo "<pre>" . htmlspecialchars(json_encode($jsonResult, JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠️ Not valid JSON</p>";
        echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
    }
}

echo "<h2>Debug Information:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Allow URL fopen:</strong> " . (ini_get('allow_url_fopen') ? 'Yes' : 'No') . "</p>";

// Test direct file include to see if there are any errors
echo "<h2>Direct File Test:</h2>";
try {
    // Capture any output from including the file
    ob_start();
    include_once 'api/config/database.php';
    $output = ob_get_clean();
    
    if ($output) {
        echo "<p style='color: orange;'>⚠️ Database config produced output:</p>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    } else {
        echo "<p style='color: green;'>✅ Database config included cleanly</p>";
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    } else {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check if the API file can be accessed directly
echo "<h2>Direct API Access Test:</h2>";
$apiContent = file_get_contents('http://localhost/CNESIS/api/admissions/create.php');
if ($apiContent !== false) {
    echo "<p>API file is accessible via HTTP</p>";
    $firstChars = substr($apiContent, 0, 200);
    echo "<p>First 200 characters of direct access:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($firstChars) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Cannot access API file directly</p>";
}
?>
