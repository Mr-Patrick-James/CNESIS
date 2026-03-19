<?php
$url = 'http://localhost/CNESIS/api/admissions/get-all.php';
$response = file_get_contents($url);
echo $response;
?>