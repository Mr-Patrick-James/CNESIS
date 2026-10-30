<?php
require_once '../config/cors.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../config/database.php';

// ── Rate limiting: max 10 failed attempts per IP per 15-minute window ─────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

$clientIp      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitKey  = 'login_attempts_' . md5($clientIp);
$windowSeconds = 15 * 60; // 15 minutes
$maxAttempts   = 10;

$attemptData = $_SESSION[$rateLimitKey] ?? ['count' => 0, 'first_attempt' => time()];

// Reset window if it has expired
if (time() - $attemptData['first_attempt'] > $windowSeconds) {
    $attemptData = ['count' => 0, 'first_attempt' => time()];
}

if ($attemptData['count'] >= $maxAttempts) {
    $retryAfter = $windowSeconds - (time() - $attemptData['first_attempt']);
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Too many failed login attempts. Please wait ' . ceil($retryAfter / 60) . ' minute(s) before trying again.'
    ]);
    exit;
}

$_SESSION[$rateLimitKey] = $attemptData;

// Get posted data
$data = json_decode(file_get_contents("php://input"));

$identifier = $data->email ?? $data->username ?? '';
$password = $data->password ?? '';

if (empty($identifier) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username/Email and password are required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // 1. Check USERS table (Admin, Staff, Faculty)
    $stmt = $db->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND status = 'active' LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Login successful — clear rate-limit counter
            unset($_SESSION[$rateLimitKey]);
            if (session_status() === PHP_SESSION_NONE) {
                ini_set('session.cookie_httponly', '1');
                ini_set('session.cookie_samesite', 'Strict');
                ini_set('session.use_strict_mode', '1');
                ini_set('session.use_only_cookies', '1');
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['must_change_password'] = isset($user['must_change_password']) ? $user['must_change_password'] : 0;

            // Generate a CSRF token tied to this session (used to validate logout requests)
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Regenerate session ID after successful login to prevent session fixation attacks
            session_regenerate_id(true);

            // Determine redirect URL — detect base path for local vs production
            $basePath = str_replace('/api/auth/login.php', '', $_SERVER['SCRIPT_NAME']);
            $redirect = $basePath . '/index.php';
            if ($user['role'] === 'admin') {
                $redirect = $basePath . '/views/admin/features/dashboard.php';
            } elseif ($user['role'] === 'faculty') {
                $redirect = $basePath . '/index.php'; // Placeholder
            } elseif ($user['role'] === 'student') {
                $redirect = $basePath . '/views/student/dashboard.php';
            }

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'role' => $user['role'],
                'must_change_password' => $_SESSION['must_change_password'],
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ],
                'redirect' => $redirect
            ]);
            exit;
        }
    }

    // If we reach here, authentication failed — increment rate-limit counter
    $_SESSION[$rateLimitKey]['count']++;
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);

} catch (Exception $e) {
    http_response_code(500);
    // Log the real error but never expose it to the client
    error_log('Login error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A server error occurred. Please try again later.']);
}
?>
