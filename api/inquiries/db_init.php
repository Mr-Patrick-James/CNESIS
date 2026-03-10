<?php
/**
 * Inquiry Database Initialization
 * Ensures necessary tables exist for the inquiry system
 */
function initializeInquiryTables($db) {
    try {
        // 1. Inquiries main table
        $db->exec("CREATE TABLE IF NOT EXISTS inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            inquiry_id VARCHAR(50) NOT NULL UNIQUE,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            program_id INT DEFAULT NULL,
            program_name VARCHAR(255) DEFAULT NULL,
            question TEXT DEFAULT NULL,
            inquiry_type ENUM('general', 'admission', 'program', 'requirements', 'other') DEFAULT 'general',
            status ENUM('open', 'responded', 'closed', 'new') DEFAULT 'open',
            notes TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            responded_at TIMESTAMP NULL,
            responded_by INT NULL,
            INDEX (email),
            INDEX (status),
            INDEX (inquiry_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Ensure columns exist (Migration logic)
        $columnsToCheck = [
            'inquiry_id' => "VARCHAR(50) DEFAULT NULL AFTER id",
            'phone' => "VARCHAR(20) DEFAULT NULL AFTER email",
            'program_name' => "VARCHAR(255) DEFAULT NULL AFTER program_id",
            'question' => "TEXT DEFAULT NULL AFTER program_name",
            'inquiry_type' => "ENUM('general', 'admission', 'program', 'requirements', 'other') DEFAULT 'general' AFTER question",
            'notes' => "TEXT DEFAULT NULL AFTER status",
            'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at",
            'responded_at' => "TIMESTAMP NULL AFTER updated_at",
            'responded_by' => "INT NULL AFTER responded_at"
        ];

        foreach ($columnsToCheck as $column => $definition) {
            $checkColumn = $db->query("SHOW COLUMNS FROM inquiries LIKE '$column'");
            if ($checkColumn->rowCount() === 0) {
                $db->exec("ALTER TABLE inquiries ADD $column $definition");
                
                // If we just added inquiry_id, populate it for existing rows
                if ($column === 'inquiry_id') {
                    $stmt = $db->query("SELECT id FROM inquiries WHERE inquiry_id IS NULL OR inquiry_id = ''");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $newInqId = 'INQ-' . date('Y') . '-' . str_pad($row['id'], 4, '0', STR_PAD_LEFT);
                        $db->exec("UPDATE inquiries SET inquiry_id = '$newInqId' WHERE id = " . $row['id']);
                    }
                    // Now add NOT NULL and UNIQUE constraint
                    $db->exec("ALTER TABLE inquiries MODIFY COLUMN inquiry_id VARCHAR(50) NOT NULL UNIQUE");
                }
            } else {
                // Column exists, but ensure we populate any empty inquiry_id if it's currently nullable or has empty strings
                if ($column === 'inquiry_id') {
                    $stmt = $db->query("SELECT id FROM inquiries WHERE inquiry_id IS NULL OR inquiry_id = ''");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $newInqId = 'INQ-' . date('Y') . '-' . str_pad($row['id'], 4, '0', STR_PAD_LEFT);
                        $db->exec("UPDATE inquiries SET inquiry_id = '$newInqId' WHERE id = " . $row['id']);
                    }
                }
            }
        }

        // Update status ENUM if needed
        $db->exec("ALTER TABLE inquiries MODIFY COLUMN status ENUM('open', 'responded', 'closed', 'new') DEFAULT 'open'");

        // 2. Inquiry Messages table for conversation history
        $db->exec("CREATE TABLE IF NOT EXISTS inquiry_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            inquiry_id INT NOT NULL,
            sender_type ENUM('student', 'admin') NOT NULL,
            message TEXT NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (inquiry_id),
            INDEX (is_read),
            FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        return true;
    } catch (PDOException $e) {
        error_log("Inquiry table initialization failed: " . $e->getMessage());
        return false;
    }
}
?>
