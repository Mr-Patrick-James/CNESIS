<?php
include 'api/config/database.php';
try {
    $db = (new Database())->getConnection();
    $stmt = $db->query('DESCRIBE admissions');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>