<?php
require_once '../api/config/database.php';

header('Content-Type: text/plain');

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Database connection failed");
    }

    echo "Connected to database.\n";

    // Check if email_verifications table exists
    $checkTable = $db->query("SHOW TABLES LIKE 'email_verifications'");
    if ($checkTable->rowCount() == 0) {
        // Create the table if it doesn't exist (inferring structure from usage)
        $sql = "CREATE TABLE email_verifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            otp_code VARCHAR(6) NOT NULL,
            expires_at DATETIME NOT NULL,
            status ENUM('pending', 'verified', 'expired') NOT NULL DEFAULT 'pending',
            portal_token VARCHAR(64) DEFAULT NULL,
            token_expires_at DATETIME DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        echo "Created email_verifications table.\n";
    } else {
        echo "email_verifications table exists.\n";
    }

    // Check if password_hash column exists
    $checkColumn = $db->query("SHOW COLUMNS FROM email_verifications LIKE 'password_hash'");
    if ($checkColumn->rowCount() == 0) {
        $sql = "ALTER TABLE email_verifications ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL AFTER email";
        $db->exec($sql);
        echo "Added password_hash column to email_verifications table.\n";
    } else {
        echo "password_hash column already exists.\n";
    }

    echo "Migration completed successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
