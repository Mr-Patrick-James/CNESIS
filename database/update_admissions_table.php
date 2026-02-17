<?php
require_once __DIR__ . '/../api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if attachments column exists
    $check = $db->query("SHOW COLUMNS FROM admissions LIKE 'attachments'");
    if ($check->rowCount() == 0) {
        $sql = "ALTER TABLE admissions ADD COLUMN attachments TEXT DEFAULT NULL COMMENT 'JSON object of file paths' AFTER status";
        $db->exec($sql);
        echo "Column 'attachments' added successfully.\n";
    } else {
        echo "Column 'attachments' already exists.\n";
    }
    
    // Also check for other missing columns that might be in the form but not in DB
    // The form has 'health_problem', 'first_male_college', 'shs_strand', 'gpa_rating' (mapped to gwa?), 'grade10_gpa', etc.
    // setup.sql has: gwa, entrance_exam_score, admission_type, previous_program
    // It seems we need to add more columns to store all the form data properly, or dump them into a 'form_data' JSON column.
    // Given the number of fields (parents, schools, grades, health info), a 'form_data' JSON column is a lifesaver here.
    
    $check2 = $db->query("SHOW COLUMNS FROM admissions LIKE 'form_data'");
    if ($check2->rowCount() == 0) {
        $sql2 = "ALTER TABLE admissions ADD COLUMN form_data LONGTEXT DEFAULT NULL COMMENT 'Full JSON dump of form data' AFTER attachments";
        $db->exec($sql2);
        echo "Column 'form_data' added successfully.\n";
    } else {
        echo "Column 'form_data' already exists.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
