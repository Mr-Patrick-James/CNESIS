<?php
/**
 * CORS Helper
 *
 * Replaces the wildcard Access-Control-Allow-Origin header with a strict
 * same-origin (or explicit whitelist) policy.
 *
 * Include this ONCE per request, before any other header() calls.
 * auth_guard.php already calls this automatically for all guarded endpoints.
 *
 * ALLOWED_ORIGINS can be extended for staging/preview environments by setting
 * the CORS_EXTRA_ORIGINS environment variable as a comma-separated list.
 */

// ── Allowed origins ──────────────────────────────────────────────────────────
// Build the allow-list from the current server host + any env-supplied extras.
$_corsAllowed = [];

// Always allow the same host the server is running on (covers both http and https)
if (!empty($_SERVER['HTTP_HOST'])) {
    $_corsScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $_corsAllowed[] = $_corsScheme . '://' . $_SERVER['HTTP_HOST'];
}

// Optional: extra origins from environment (e.g. for staging)
$_corsExtra = getenv('CORS_EXTRA_ORIGINS');
if (!empty($_corsExtra)) {
    foreach (explode(',', $_corsExtra) as $_o) {
        $_o = trim($_o);
        if ($_o !== '') {
            $_corsAllowed[] = $_o;
        }
    }
}

// ── Validate the incoming Origin against the allow-list ─────────────────────
$_corsOrigin   = $_SERVER['HTTP_ORIGIN'] ?? '';
$_corsGranted  = in_array($_corsOrigin, $_corsAllowed, true);

// If no Origin header (e.g. same-origin form POST or curl), skip CORS headers
if (!empty($_corsOrigin)) {
    if ($_corsGranted) {
        header('Access-Control-Allow-Origin: ' . $_corsOrigin);
    }
    // Always include Vary so caches do not serve the wrong origin's response
    header('Vary: Origin');
    // Required for session cookies to be sent cross-origin
    header('Access-Control-Allow-Credentials: true');
}

// ── Pre-flight OPTIONS fast-exit ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    if ($_corsGranted) {
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 3600');
        http_response_code(204);
    } else {
        http_response_code(403);
    }
    exit;
}
