<?php
/**
 * Quick API Test
 * Test the admission API directly
 */

echo "<h1>Quick API Test</h1>";

// Test data
$data = [
    'email' => 'test' . time() . '@example.com',
    'first_name' => 'Test'
];

// Use file_get_contents to test
$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);

// Test minimal API first
echo "<h2>Testing Minimal API:</h2>";
$result = file_get_contents('http://localhost/CNESIS/api/admissions/create_minimal.php', false, $context);

echo "<p><strong>Raw Response:</strong></p>";
echo "<textarea style='width: 100%; height: 150px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

// Check if it's valid JSON
$json = json_decode($result, true);
if ($json) {
    echo "<p style='color: green;'>✅ Minimal API returns valid JSON</p>";
    echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Minimal API does not return valid JSON</p>";
    echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
    
    // Show first 200 chars
    $preview = substr($result, 0, 200);
    echo "<p><strong>First 200 characters:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($preview) . "</pre>";
}

// Test full API
echo "<h2>Testing Full API:</h2>";
$fullData = [
    'application_id' => 'APP-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT),
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

$options['content'] = json_encode($fullData);
$context = stream_context_create($options);

$result = file_get_contents('http://localhost/CNESIS/api/admissions/create.php', false, $context);

echo "<p><strong>Raw Response:</strong></p>";
echo "<textarea style='width: 100%; height: 150px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

// Check if it's valid JSON
$json = json_decode($result, true);
if ($json) {
    echo "<p style='color: green;'>✅ Full API returns valid JSON</p>";
    echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Full API does not return valid JSON</p>";
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

echo "<h2>Quick Fix Options:</h2>";
echo "<ol>";
echo "<li>If minimal API works but full doesn't, the issue is in the full API logic</li>";
echo "<li>If both fail, there's a server configuration issue</li>";
echo "<li>Check if the database 'cnesis_db' exists</li>";
echo "<li>Verify all required files exist</li>";
echo "</ol>";
?>
