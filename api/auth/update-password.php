<?php
header('Content-Type: application/json');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

// ── CSRF check ────────────────────────────────────────────────────────────────
// Only enforce when a token exists in session (allow first-login password change
// which may arrive before the token is regenerated).
$suppliedCsrf = $data->csrf_token ?? '';
$sessionCsrf  = $_SESSION['csrf_token'] ?? '';

if (!empty($sessionCsrf) && !hash_equals($sessionCsrf, $suppliedCsrf)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid request token. Please refresh the page.']);
    exit;
}

$newPassword     = $data->new_password ?? '';
$confirmPassword = $data->confirm_password ?? '';

if (empty($newPassword) || empty($confirmPassword)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Both password fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

if (strlen($newPassword) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
    exit;
}

// ── Stronger password rules ────────────────────────────────────────────────────
// Must contain at least one letter and one digit
if (!preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one letter and one number.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
    $stmt->execute([$passwordHash, $_SESSION['user_id']]);

    $_SESSION['must_change_password'] = 0;

    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
