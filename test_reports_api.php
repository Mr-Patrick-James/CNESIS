<?php
// Test the reports API directly
echo "Testing reports API...\n";

// Test summary report
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/CNESIS/api/reports/generate-report.php?type=summary");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: " . $http_code . "\n";
echo "Response: " . $response . "\n";

// Test student enrollment report
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/CNESIS/api/reports/generate-report.php?type=student-enrollment");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\n=== Student Enrollment Report ===\n";
echo "HTTP Status: " . $http_code . "\n";
echo "Response: " . $response . "\n";
?>
