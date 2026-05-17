<?php
/**
 * Verify local DB is ready to export for AWS hosting.
 * Open in browser: http://localhost/CNESIS/api/verify-db-ready.php
 * Or CLI: php api/verify-db-ready.php
 */
header('Content-Type: text/plain; charset=UTF-8');
require_once __DIR__ . '/config/database.php';

$db = (new Database())->getConnection();
if (!$db) {
    echo "FAIL: Cannot connect to database. Start WAMP MySQL first.\n";
    exit(1);
}

echo "=== CNESIS database readiness check ===\n\n";
$ok = true;

function check($label, $pass, $detail = '') {
    global $ok;
    echo ($pass ? '[OK] ' : '[FAIL] ') . $label;
    if ($detail !== '') {
        echo ' — ' . $detail;
    }
    echo "\n";
    if (!$pass) {
        $ok = false;
    }
}

$statusCol = $db->query("SHOW COLUMNS FROM students LIKE 'status'")->fetch(PDO::FETCH_ASSOC);
check(
    'students.status includes graduated',
    $statusCol && stripos($statusCol['Type'], 'graduated') !== false,
    $statusCol['Type'] ?? 'missing'
);

$enrollCol = $db->query("SHOW COLUMNS FROM students LIKE 'enrollment_type'")->fetch(PDO::FETCH_COLUMN);
check('students.enrollment_type exists', (bool) $enrollCol);

$archiveCols = $db->query("SHOW COLUMNS FROM archive_students")->fetchAll(PDO::FETCH_COLUMN);
$requiredArchive = ['original_id', 'deleted_at', 'deleted_by', 'delete_reason', 'batch'];
foreach ($requiredArchive as $col) {
    check("archive_students.$col exists", in_array($col, $archiveCols, true));
}

$history = $db->query("SHOW TABLES LIKE 'student_history'")->rowCount();
check('student_history table exists', $history > 0);

$year4 = (int) $db->query("SELECT COUNT(*) FROM students WHERE yearlevel = 4 AND status = 'active'")->fetchColumn();
echo "\nData: $year4 active 4th-year student(s) ready to test graduate.\n";

$total = (int) $db->query('SELECT COUNT(*) FROM students')->fetchColumn();
echo "Data: $total total student(s) in database.\n\n";

if ($ok) {
    echo "RESULT: Database is ready to export for AWS.\n";
    echo "Next steps:\n";
    echo "  1. Run http://localhost/CNESIS/api/migrate.php (if you have not yet)\n";
    echo "  2. Export cnesis_db from phpMyAdmin\n";
    echo "  3. Deploy updated project files + import dump on AWS\n";
    exit(0);
}

echo "RESULT: Run http://localhost/CNESIS/api/migrate.php first, then run this check again.\n";
exit(1);
