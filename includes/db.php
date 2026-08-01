<?php
/**
 * includes/db.php
 * ---------------------------------------------------------------
 * Central database connection for the Mbuya Parents' School website
 * and its Admin Dashboard. Uses PDO + MySQL.
 *
 * Update the constants below with your real hosting database
 * credentials before going live. Your host's control panel (e.g.
 * cPanel) will show you the database name, username, password and
 * host to use — these are usually NOT "root" with no password like
 * the local defaults below.
 * ---------------------------------------------------------------
 */

// ----- Update these for your hosting environment -----
define('DB_HOST', 'localhost');
define('DB_NAME', 'mbuya_parents_school');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

$pdo = null;

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Database not reachable — pages that need $pdo should check for
    // null and show a friendly message rather than a fatal error.
    $pdo = null;
    $db_connection_error = $e->getMessage();
}
