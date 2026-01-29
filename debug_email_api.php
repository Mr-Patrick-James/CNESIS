<?php
/**
 * Simplified Email API Test
 * Debug what's causing the 500 error
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Email Components...\n\n";

// Test 1: Check if files exist
echo "1. Checking required files:\n";
$files = [
    '../config/database.php',
    '../config/email_config.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file missing\n";
    }
}

// Test 2: Try to include database
echo "\n2. Testing database connection:\n";
try {
    include_once '../config/database.php';
    echo "✅ Database config included\n";
    
    $database = new Database();
    echo "✅ Database class instantiated\n";
    
    $db = $database->getConnection();
    if ($db) {
        echo "✅ Database connection successful\n";
    } else {
        echo "❌ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Try to include email config
echo "\n3. Testing email config:\n";
try {
    include_once '../config/email_config.php';
    echo "✅ Email config included\n";
    
    if (isset($db)) {
        $emailConfig = new EmailConfig($db);
        echo "✅ EmailConfig class instantiated\n";
    }
} catch (Exception $e) {
    echo "❌ Email config error: " . $e->getMessage() . "\n";
}

// Test 4: Check PHPMailer
echo "\n4. Testing PHPMailer:\n";
if (file_exists('../vendor/autoload.php')) {
    echo "✅ Vendor autoload exists\n";
    try {
        include_once '../vendor/autoload.php';
        echo "✅ Vendor autoload included\n";
        
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            echo "✅ PHPMailer class available\n";
        } else {
            echo "❌ PHPMailer class not found\n";
        }
    } catch (Exception $e) {
        echo "❌ PHPMailer error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Vendor autoload missing\n";
}

echo "\n✅ Component test completed!\n";
?>
