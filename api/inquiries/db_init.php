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
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            program_id INT DEFAULT NULL,
            status ENUM('open', 'responded', 'closed') DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (email),
            INDEX (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

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
