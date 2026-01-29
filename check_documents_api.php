<?php
/**
 * Check What Documents API Returns
 */

echo "Testing documents API...\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/CNESIS/api/documents/upload-document.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n\n";

$jsonData = json_decode($response, true);
if ($jsonData && isset($jsonData['documents'])) {
    echo "Documents returned by API:\n";
    foreach ($jsonData['documents'] as $doc) {
        echo "- {$doc['document_name']}\n";
        echo "  Path: {$doc['file_path']}\n";
        echo "  Category: {$doc['category']}\n";
        echo "  Full URL: {$doc['full_url']}\n\n";
    }
} else {
    echo "No documents found or invalid JSON\n";
}
?>
