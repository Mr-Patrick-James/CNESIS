<?php
require_once 'api/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT * FROM admissions LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Keys in admissions table: " . implode(', ', array_keys($row)) . "\n";

$stmt = $db->query("SELECT status, COUNT(*) as count FROM admissions GROUP BY status");
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Status: [" . $r['status'] . "], Count: " . $r['count'] . "\n";
}
?>