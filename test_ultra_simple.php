<?php
/**
 * Ultra Simple Test
 * Test if basic API calls work
 */

echo "<h1>Ultra Simple Test</h1>";

// Test the ultra-simple API
echo "<h2>Test Ultra-Simple API:</h2>";
$result = file_get_contents('http://localhost/CNESIS/api/inquiries/test.php');
echo "<textarea style='width: 100%; height: 100px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

$json = json_decode($result, true);
if ($json && $json['success']) {
    echo "<p style='color: green;'>✅ Ultra-simple API works</p>";
} else {
    echo "<p style='color: red;'>❌ Even ultra-simple API fails</p>";
    echo "<p>This means there's a server configuration issue</p>";
}

// Test with POST
echo "<h2>Test POST to Ultra-Simple:</h2>";
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'content' => '{"test": "data"}',
        'header' => "Content-Type: application/json\r\n",
        'ignore_errors' => true
    ]
]);

$result = file_get_contents('http://localhost/CNESIS/api/inquiries/test.php', false, $context);
echo "<textarea style='width: 100%; height: 100px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li>If ultra-simple works: The issue is in the complex API</li>";
echo "<li>If ultra-simple fails: Server/WAMP configuration issue</li>";
echo "<li>Check Apache error logs</li>";
echo "<li>Try restarting WAMP services</li>";
echo "</ul>";
?>
