<?php
/**
 * Test Minimal Email API
 */

$url = 'http://localhost/CNESIS/api/email/send-minimal.php';
$data = [
    'email_type' => 'custom',
    'recipient_email' => 'test@example.com',
    'custom_subject' => 'Test Email',
    'custom_message' => '<h1>This is a test</h1>',
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
} else {
    echo "\n✅ Valid JSON!\n";
    print_r($jsonData);
}
?>
