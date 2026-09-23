<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

// CSRF protection: only process logout if a valid token is supplied.
// This prevents a malicious page from silently logging users out via a plain link/image.
$suppliedToken = $_GET['token'] ?? '';
$sessionToken  = $_SESSION['csrf_token'] ?? '';

$tokenValid = !empty($suppliedToken)
    && !empty($sessionToken)
    && hash_equals($sessionToken, $suppliedToken);

if (!$tokenValid) {
    // Token missing or wrong — bounce back without logging out.
    // This handles both unauthenticated hits and CSRF attempts.
    $basePath = str_replace('/api/auth/logout.php', '', $_SERVER['SCRIPT_NAME']);
    header('Location: ' . $basePath . '/index.php');
    exit;
}

// Token is valid — destroy the session fully
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

// Redirect to landing page
$basePath = str_replace('/api/auth/logout.php', '', $_SERVER['SCRIPT_NAME']);
header('Location: ' . $basePath . '/index.php');
exit;
