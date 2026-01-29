<?php
// Test what data exists in all tables
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/CNESIS/api/reports/generate-report.php?type=summary");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Summary Report:\n";
echo $response . "\n\n";

// Test each report type
$reports = ['student-enrollment', 'admission-statistics', 'program-head-performance', 'prospectus-downloads', 'program-analytics'];

foreach ($reports as $report) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/CNESIS/api/reports/generate-report.php?type=" . $report);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo "=== " . strtoupper(str_replace('-', ' ', $report)) . " ===\n";
    $data = json_decode($response, true);
    if (isset($data['details'])) {
        echo "Details count: " . count($data['details']) . "\n";
        if (count($data['details']) > 0) {
            echo "First item: " . json_encode($data['details'][0], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "No details found\n";
    }
    echo "\n";
}
?>
