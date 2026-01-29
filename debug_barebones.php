<?php
/**
 * Barebones Debug
 * Strip everything down to find the exact issue
 */

echo "<h1>Barebones Debug</h1>";

// Test 1: Can we even access the API file?
echo "<h2>Test 1: Direct File Access</h2>";
$apiFile = 'http://localhost/CNESIS/api/inquiries/create_simple.php';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'ignore_errors' => true
    ]
]);

$result = file_get_contents($apiFile, false, $context);
echo "<textarea style='width: 100%; height: 100px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

// Test 2: Test with minimal data
echo "<h2>Test 2: Minimal POST Request</h2>";
$data = ['fullName' => 'Test', 'email' => 'test@test.com', 'question' => 'Test?'];

$context = stream_context_create([
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
]);

$result = file_get_contents($apiFile, false, $context);
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlspecialchars($result) . "</textarea>";

// Test 3: Check if it's JSON
$json = json_decode($result, true);
if ($json) {
    echo "<p style='color: green;'>✅ Valid JSON returned</p>";
    echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Not valid JSON</p>";
    
    // Show first 500 chars
    $preview = substr($result, 0, 500);
    echo "<p><strong>First 500 characters:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($preview) . "</pre>";
    
    // Look for common error patterns
    if (strpos($result, 'Warning') !== false) {
        echo "<p style='color: orange;'>⚠️ Contains PHP Warning</p>";
    }
    if (strpos($result, 'Fatal') !== false) {
        echo "<p style='color: red;'>❌ Contains Fatal Error</p>";
    }
    if (strpos($result, 'Parse') !== false) {
        echo "<p style='color: red;'>❌ Contains Parse Error</p>";
    }
}

// Test 4: Check PHP syntax
echo "<h2>Test 3: PHP Syntax Check</h2>";
$output = [];
$return_var = 0;
exec('php -l api/inquiries/create_simple.php 2>&1', $output, $return_var);

if ($return_var === 0) {
    echo "<p style='color: green;'>✅ PHP syntax is valid</p>";
} else {
    echo "<p style='color: red;'>❌ PHP syntax error:</p>";
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
}

// Test 5: Check database connection
echo "<h2>Test 4: Database Connection</h2>";
try {
    include_once 'api/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db === null) {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    } else {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        
        // Test simple query
        try {
            $stmt = $db->query("SELECT 1");
            echo "<p style='color: green;'>✅ Database query works</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Database query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Quick Fix Options:</h2>";
echo "<ol>";
echo "<li>If PHP syntax error: Fix the syntax issue</li>";
echo "<li>If database fails: Check MySQL service</li>";
echo "<li>If HTML in response: Check for PHP warnings</li>";
echo "<li>If file not found: Check file permissions</li>";
echo "</ol>";
?>
