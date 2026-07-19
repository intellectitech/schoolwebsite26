<?php
/**
 * admin/includes/admin_auth.php
 * ---------------------------------------------------------------
 * Session guard for the admin dashboard. Include this at the very
 * top of every protected admin page (before any HTML output).
 *
 * Provides:
 *   - Session start with sensible cookie settings
 *   - Redirect to login.php if not authenticated
 *   - $_SESSION['admin_id'] / $_SESSION['admin_username'] available
 *     to any page that includes this file
 * ---------------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/datastore.php';

// Simple session timeout (2 hours of inactivity)
$timeout = 7200;
if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['admin_last_activity'] = time();

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

/**
 * Generates (or returns existing) CSRF token for this session.
 */
function admin_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a submitted CSRF token, dies with an error if invalid.
 */
function admin_verify_csrf() {
    $submitted = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $submitted)) {
        http_response_code(403);
        die('Security check failed. Please go back and try again.');
    }
}
