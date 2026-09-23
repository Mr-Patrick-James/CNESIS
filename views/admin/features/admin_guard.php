<?php
/**
 * Admin Page Guard
 *
 * Include this as the very first statement in every admin view page.
 * It starts the session and redirects unauthenticated / non-admin
 * visitors to the login page before any HTML is sent.
 *
 * Usage (place at the absolute top of the file, before <!DOCTYPE html>):
 *   <?php require_once __DIR__ . '/admin_guard.php'; ?>
 */

if (session_status() === PHP_SESSION_NONE) {
    // Harden session cookie settings in case php.ini defaults are weak
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

// Roles that may access admin pages
$_adminGuardAllowedRoles = ['admin', 'staff'];

if (
    empty($_SESSION['user_id']) ||
    !in_array($_SESSION['role'] ?? '', $_adminGuardAllowedRoles, true)
) {
    // Destroy any partial/stale session before bouncing
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

    // Redirect to login with a clear reason
    header('Location: ../../../index.php?error=unauthorized');
    exit;
}

// Make CSRF token available to admin pages for logout links
// Ensure one exists (fallback for existing sessions created before this was added)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$_adminCsrfToken = $_SESSION['csrf_token'];
?>
<script>
/* CSRF token for logout — set by admin_guard.php */
window.csrfToken = "<?php echo addslashes($_adminCsrfToken); ?>";
</script>
<?php
