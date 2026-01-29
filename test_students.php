<?php
include_once 'api/config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Database connected successfully\n";
    
    // Check total students
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM students");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total students: " . $count['count'] . "\n";
    
    // Check students with yearlevel
    $stmt = $db->prepare("SELECT yearlevel, status, COUNT(*) as count FROM students WHERE yearlevel IS NOT NULL GROUP BY yearlevel, status");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Students with yearlevel:\n";
    foreach ($students as $student) {
        echo "Year: " . $student['yearlevel'] . ", Status: " . $student['status'] . ", Count: " . $student['count'] . "\n";
    }
    
    // Check sample student data
    $stmt = $db->prepare("SELECT id, first_name, last_name, yearlevel, status FROM students LIMIT 3");
    $stmt->execute();
    $sample = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nSample student records:\n";
    print_r($sample);
    
} else {
    echo "Database connection failed\n";
}
?>
