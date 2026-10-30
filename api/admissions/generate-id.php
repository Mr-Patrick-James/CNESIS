<?php
/**
 * Generate Application ID API
 * Returns the next sequential application ID: APP-YYYY-001
 * Requires an active verified-email session (OTP-verified applicant).
 */

require_once __DIR__ . '/../config/cors.php';
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST');

// Only accessible to OTP-verified applicants or logged-in users
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

$hasVerifiedEmail = !empty($_SESSION['verified_email']);
$hasLoggedInUser  = !empty($_SESSION['user_id']);

if (!$hasVerifiedEmail && !$hasLoggedInUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please verify your email first.']);
    exit;
}

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $year = (int) date('Y');
    $prefix = "APP-{$year}-";

    // Find the highest existing sequential number for this year
    $stmt = $db->prepare("
        SELECT application_id
        FROM admissions
        WHERE application_id LIKE :prefix
        ORDER BY application_id DESC
        LIMIT 1
    ");
    $stmt->execute([':prefix' => $prefix . '%']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $nextNum = 1;
    if ($row) {
        // Extract the numeric suffix and increment
        $parts = explode('-', $row['application_id']);
        $lastPart = end($parts);
        if (is_numeric($lastPart)) {
            $nextNum = (int)$lastPart + 1;
        }
    }

    // Zero-pad to 3 digits minimum (grows beyond 3 if needed, e.g., 001 → 999 → 1000)
    $seq = str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    $applicationId = "{$prefix}{$seq}";

    // Double-check uniqueness (race condition guard)
    $checkStmt = $db->prepare("SELECT id FROM admissions WHERE application_id = :application_id");
    $checkStmt->execute([':application_id' => $applicationId]);
    if ($checkStmt->rowCount() > 0) {
        // Increment further
        $seq = str_pad($nextNum + 1, 3, '0', STR_PAD_LEFT);
        $applicationId = "{$prefix}{$seq}";
    }

    echo json_encode([
        'success'        => true,
        'application_id' => $applicationId,
        'year'           => $year,
        'sequence'       => (int)ltrim($seq, '0') ?: 0
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
$database->closeConnection();
?>
