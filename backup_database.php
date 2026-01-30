<?php
/**
 * Database Backup Script for CNESIS
 * Creates a full backup of the current database
 */

header("Content-Type: text/plain; charset=UTF-8");

echo "=== CNESIS Database Backup ===\n";
echo "This script will create a full backup of your current cnesis_db database\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=localhost", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL server\n";
    
    // Create backup directory if it doesn't exist
    $backupDir = __DIR__ . '/database/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
        echo "✅ Created backup directory: $backupDir\n";
    }
    
    // Generate backup filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . "/cnesis_db_backup_$timestamp.sql";
    
    echo "📁 Creating backup file: cnesis_db_backup_$timestamp.sql\n";
    
    // Use mysqldump to create backup
    $command = "mysqldump --user=root --password= --host=localhost --single-transaction --routines --triggers --databases cnesis_db > \"$backupFile\"";
    
    echo "🔄 Running: $command\n";
    
    $output = shell_exec($command);
    
    if (file_exists($backupFile) && filesize($backupFile) > 0) {
        $fileSize = filesize($backupFile);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        
        echo "✅ Backup completed successfully!\n";
        echo "📄 File: $backupFile\n";
        echo "📊 Size: {$fileSizeMB} MB\n";
        echo "📅 Date: " . date('Y-m-d H:i:s') . "\n";
        
        // Verify backup file
        $lines = count(file($backupFile));
        echo "📝 Lines: $lines\n";
        
        echo "\n=== Backup Summary ===\n";
        echo "✅ Database: cnesis_db\n";
        echo "✅ File: cnesis_db_backup_$timestamp.sql\n";
        echo "✅ Size: {$fileSizeMB} MB\n";
        echo "✅ Lines: $lines\n";
        echo "✅ Date: " . date('Y-m-d H:i:s') . "\n";
        
        echo "\n📋 To Restore:\n";
        echo "1. Open phpMyAdmin\n";
        echo "2. Select cnesis_db database\n";
        echo "3. Click 'Import' tab\n";
        echo "4. Choose backup file: cnesis_db_backup_$timestamp.sql\n";
        echo "5. Click 'Go'\n";
        
    } else {
        echo "❌ Backup failed! File not created or empty.\n";
        echo "Error output: $output\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n💡 Make sure:\n";
    echo "- MySQL server is running\n";
    echo "- You have proper permissions\n";
    echo "- mysqldump is in your PATH\n";
}
?>
