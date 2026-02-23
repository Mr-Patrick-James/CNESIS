<?php
require_once '../api/config/database.php';

header('Content-Type: text/plain');

echo "CNESIS Admin User Setup\n";
echo "=======================\n";

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Database connection failed. Check your config/database.php settings.");
    }

    echo "Connected to database.\n";

    // Default Admin Credentials
    $username = 'admin_demo@colegio.edu'; // This is the username field in the DB
    $email = 'admin@colegio.edu';
    $password = 'password123';
    $fullName = 'System Administrator';

    // Check if admin exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if ($existingUser) {
        // Update existing admin
        $updateStmt = $db->prepare("UPDATE users SET password = ?, full_name = ?, status = 'active' WHERE id = ?");
        $updateStmt->execute([$passwordHash, $fullName, $existingUser['id']]);
        echo "Updated existing admin user (ID: " . $existingUser['id'] . ").\n";
    } else {
        // Create new admin
        $insertStmt = $db->prepare("INSERT INTO users (username, email, password, full_name, role, status, created_at) VALUES (?, ?, ?, ?, 'admin', 'active', NOW())");
        $insertStmt->execute([$username, $email, $passwordHash, $fullName]);
        echo "Created new admin user.\n";
    }

    echo "\n--------------------------------------------------\n";
    echo "Admin Setup Complete!\n";
    echo "Username: $username\n";
    echo "Email:    $email\n";
    echo "Password: $password\n";
    echo "--------------------------------------------------\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
