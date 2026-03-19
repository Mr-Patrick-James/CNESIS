<?php
require_once 'api/config/database.php';
$db = (new Database())->getConnection();
$query = "SELECT 
            a.id, a.status, p.title as program_title
          FROM admissions a
          LEFT JOIN programs p ON a.program_id = p.id
          WHERE a.status NOT IN ('draft', 'new')
          LIMIT 5";
$stmt = $db->query($query);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>