<?php
require_once 'api/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT status, COUNT(*) as count FROM admissions GROUP BY status");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
file_put_contents('debug_output.txt', print_r($results, true));
?>