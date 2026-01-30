<?php
/**
 * Web-based Archive Tables Setup
 */

header("Content-Type: application/json; charset=UTF-8");

function setupArchiveTables() {
    try {
        // Connect to database
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "cnesis_db";
        
        // First connect without database to create it if needed
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Read SQL file
        $sqlFile = 'database/archive_tables.sql';
        if (!file_exists($sqlFile)) {
            return [
                'success' => false,
                'message' => 'SQL file not found',
                'error' => 'Missing file: ' . $sqlFile
            ];
        }
        
        $sql = file_get_contents($sqlFile);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $results = [];
        $errors = [];
        
        foreach ($statements as $i => $statement) {
            if (empty($statement)) continue;
            
            try {
                $pdo->exec($statement);
                $results[] = "Statement " . ($i + 1) . ": SUCCESS - " . substr($statement, 0, 50) . "...";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    $results[] = "Statement " . ($i + 1) . ": SKIPPED (already exists) - " . substr($statement, 0, 50) . "...";
                } else {
                    $errors[] = "Statement " . ($i + 1) . ": ERROR - " . $e->getMessage();
                }
            }
        }
        
        // Verify tables
        $tables = ['archive_admissions', 'archive_programs', 'archive_program_heads', 'archive_students', 'archive_settings'];
        $tableStatus = [];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            $tableStatus[$table] = $stmt->rowCount() > 0;
        }
        
        // Check view
        $stmt = $pdo->query("SHOW TABLES LIKE 'all_archived_items'");
        $viewStatus = $stmt->rowCount() > 0;
        
        return [
            'success' => true,
            'message' => 'Archive tables setup completed successfully',
            'details' => [
                'statements_executed' => count($results),
                'results' => $results,
                'errors' => $errors,
                'tables_created' => $tableStatus,
                'view_created' => $viewStatus
            ]
        ];
        
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error occurred',
            'error' => $e->getMessage()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'An error occurred',
            'error' => $e->getMessage()
        ];
    }
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(setupArchiveTables());
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed'
    ]);
}
?>
