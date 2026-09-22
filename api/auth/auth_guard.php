<?php
/**
 * API Auth Guard
 * 
 * Include this file at the top of every admin-only API endpoint.
 * It starts the session and aborts with 401 if the caller is not
 * an authenticated admin (or staff) user.
 *
 * Usage:
 *   require_once __DIR__ . '/../auth/auth_guard.php';   // adjust path as needed
 *
 * The ALLOWED_ROLES constant can be overridden before the require if a
 * particular endpoint should accept a narrower or wider set of roles:
 *   define('ALLOWED_ROLES', ['admin']);
 *   require_once ...auth_guard.php;
 */

// Apply strict same-origin CORS policy (replaces wildcard * on all guarded endpoints)
require_once __DIR__ . '/../config/cors.php';

if (session_status() === PHP_SESSION_NONE) {
    // Harden session cookie settings in case php.ini defaults are weak
    ini_set('session.cookie_httponly', '1');   // Blocks JS from reading the session cookie
    ini_set('session.cookie_samesite', 'Strict'); // Blocks CSRF from cross-site requests
    ini_set('session.use_strict_mode', '1');   // Rejects unrecognised session IDs
    ini_set('session.use_only_cookies', '1');  // Prevents session ID in URL
    session_start();
}

// Roles that are permitted to reach admin API endpoints
$_authGuardAllowedRoles = defined('ALLOWED_ROLES')
    ? ALLOWED_ROLES
    : ['admin', 'staff'];

if (
    empty($_SESSION['user_id']) ||
    !in_array($_SESSION['role'] ?? '', $_authGuardAllowedRoles, true)
) {
    // Return a JSON 401 — do not redirect, this is an API endpoint
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'success'  => false,
        'message'  => 'Unauthorized. Please log in.',
    ]);
    exit;
}
