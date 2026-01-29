<?php
/**
 * Test Debug API Directly
 */

$url = 'http://localhost/CNESIS/api/email/send-debug.php';
$data = [
    'email_type' => 'custom',
    'recipient_email' => 'belugaw6@gmail.com',
    'custom_subject' => 'Test with Attachment',
    'custom_message' => 'Test message',
    'attachments' => [
        ['path' => 'assets/documents/application-form.pdf', 'name' => 'Application Form']
    ]
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data)
    ]
]);

$response = file_get_contents($url, false, $context);

echo "HTTP Response Headers:\n";
print_r($http_response_header);

echo "\nRaw Response:\n";
echo "================================\n";
echo $response;
echo "\n================================\n";

// Check if response is valid JSON
$jsonData = json_decode($response, true);
if ($jsonData === null) {
    echo "\n❌ Invalid JSON!\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
    
    // Check for HTML
    if (strpos($response, '<') !== false) {
        echo "❌ Contains HTML - this is the problem!\n";
        
        // Show first few lines
        $lines = explode("\n", $response);
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            if (!empty(trim($lines[$i]))) {
                echo "Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
            }
        }
    }
} else {
    echo "\n✅ Valid JSON!\n";
    print_r($jsonData);
}
?>
