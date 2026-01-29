<?php
// Test with mysqli to check database connection
$mysqli = new mysqli("localhost", "root", "", "cnesis_db");

if ($mysqli->connect_error) {
    echo "MySQLi connection failed: " . $mysqli->connect_error . "\n";
} else {
    echo "MySQLi connected successfully\n";
    
    // Check if students table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'students'");
    if ($result->num_rows > 0) {
        echo "Students table exists\n";
        
        // Check total students
        $result = $mysqli->query("SELECT COUNT(*) as count FROM students");
        $row = $result->fetch_assoc();
        echo "Total students: " . $row['count'] . "\n";
        
        // Check students with yearlevel
        $result = $mysqli->query("SELECT yearlevel, status, COUNT(*) as count FROM students WHERE yearlevel IS NOT NULL GROUP BY yearlevel, status");
        echo "Students with yearlevel:\n";
        while ($row = $result->fetch_assoc()) {
            echo "Year: " . $row['yearlevel'] . ", Status: " . $row['status'] . ", Count: " . $row['count'] . "\n";
        }
        
        // Check sample data
        $result = $mysqli->query("SELECT id, first_name, last_name, yearlevel, status FROM students LIMIT 3");
        echo "\nSample student records:\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . ", Name: " . $row['first_name'] . " " . $row['last_name'] . ", Year: " . $row['yearlevel'] . ", Status: " . $row['status'] . "\n";
        }
    } else {
        echo "Students table does not exist\n";
        
        // Show all tables
        $result = $mysqli->query("SHOW TABLES");
        echo "Available tables:\n";
        while ($row = $result->fetch_row()) {
            echo "- " . $row[0] . "\n";
        }
    }
    
    $mysqli->close();
}
?>
