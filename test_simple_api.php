<?php
/**
 * Test Simple Email API
 */

$testData = [
    'email_type' => 'custom',
    'recipient_email' => 'test@example.com',
    'custom_subject' => 'Test Email'
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://localhost/CNESIS/api/email/test-simple.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

$jsonData = json_decode($response, true);
if ($jsonData) {
    echo "✅ Valid JSON!\n";
} else {
    echo "❌ Invalid JSON!\n";
}
?>
