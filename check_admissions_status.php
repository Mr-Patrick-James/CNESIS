<?php
require_once 'api/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT status, COUNT(*) as count FROM admissions GROUP BY status");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results, JSON_PRETTY_PRINT);
?>