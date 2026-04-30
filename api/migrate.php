<?php
header("Content-Type: text/plain");
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "Database connected.\n";
        
        // Check attachments column
        $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'attachments'");
        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE admissions ADD COLUMN attachments TEXT DEFAULT NULL COMMENT 'JSON object of file paths' AFTER status";
            $db->exec($sql);
            echo "Column 'attachments' added successfully.\n";
        } else {
            echo "Column 'attachments' already exists.\n";
        }
        
        // Check form_data column
        $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'form_data'");
        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE admissions ADD COLUMN form_data LONGTEXT DEFAULT NULL COMMENT 'Full JSON dump of form data' AFTER attachments";
            $db->exec($sql);
            echo "Column 'form_data' added successfully.\n";
        } else {
            echo "Column 'form_data' already exists.\n";
        }

        // Fix gender ENUM to include 'other' and ensure correct values
        $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'gender'");
        $col = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($col && strpos($col['Type'], 'other') === false) {
            $db->exec("ALTER TABLE admissions MODIFY COLUMN gender ENUM('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL");
            echo "Column 'gender' ENUM updated to include 'other'.\n";
        } else {
            echo "Column 'gender' ENUM already correct.\n";
        }

        // Fix gwa column to use DECIMAL(6,2) to safely store values up to 9999.99
        $stmt = $db->query("SHOW COLUMNS FROM admissions LIKE 'gwa'");
        $col = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($col) {
            if (strpos($col['Type'], '6,2') === false) {
                $db->exec("ALTER TABLE admissions MODIFY COLUMN gwa DECIMAL(6,2) DEFAULT NULL");
                echo "Column 'gwa' widened to DECIMAL(6,2).\n";
            } else {
                echo "Column 'gwa' already DECIMAL(6,2).\n";
            }
        } else {
            $db->exec("ALTER TABLE admissions ADD COLUMN gwa DECIMAL(6,2) DEFAULT NULL");
            echo "Column 'gwa' added as DECIMAL(6,2).\n";
        }

        // Add missing year-4 sections for BSIS and BPA
        $sectionsToAdd = [
            ['BSIS4', 'BSIS4', 'BSIS', 4],
            ['BPA4',  'BPA4',  'BPA',  4],
        ];
        foreach ($sectionsToAdd as [$code, $name, $dept, $year]) {
            $chk = $db->prepare("SELECT id FROM sections WHERE section_code = ?");
            $chk->execute([$code]);
            if ($chk->rowCount() === 0) {
                $db->prepare("INSERT INTO sections (section_code, section_name, department_code, year_level, status) VALUES (?, ?, ?, ?, 'active')")
                   ->execute([$code, $name, $dept, $year]);
                echo "Section '$code' added.\n";
            } else {
                echo "Section '$code' already exists.\n";
            }
        }
        
    } else {
        echo "Failed to connect to database.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
