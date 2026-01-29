<?php
/**
 * Test Email API
 * Check if the API returns valid JSON
 */

echo "Testing Email API...\n\n";

// Test data
$testData = [
    'email_type' => 'custom',
    'template_name' => 'custom',
    'admission_id' => 1,
    'recipient_email' => 'test@example.com',
    'custom_subject' => 'Test Email',
    'custom_message' => '<h1>This is a test</h1><p>Testing email functionality.</p>',
    'attachments' => []
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://localhost/CNESIS/api/email/send-admission-email.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response Length: " . strlen($response) . " bytes\n\n";

if ($error) {
    echo "CURL Error: $error\n";
}

echo "Raw Response:\n";
echo "================================\n";
echo $response;
echo "\n================================\n\n";

// Try to parse JSON
$jsonData = json_decode($response, true);

if ($jsonData === null) {
    echo "❌ Invalid JSON detected!\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
    
    // Check if it contains HTML
    if (strpos($response, '<') !== false) {
        echo "❌ Response contains HTML - this is the problem!\n";
        
        // Extract first line of HTML
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            if (strpos($line, '<') !== false && strpos($line, '>') !== false) {
                echo "First HTML line: " . trim($line) . "\n";
                break;
            }
        }
    }
} else {
    echo "✅ Valid JSON response!\n";
    echo "Response Data:\n";
    print_r($jsonData);
}
?>
