<?php
/**
 * includes/db.php
 * ---------------------------------------------------------------
 * Central database connection for the Mbuya Parents' School website.
 * Uses PDO + MySQL. Update the constants below with your real
 * hosting credentials before going live.
 *
 * If the database cannot be reached, $pdo is set to null and the
 * pages fall back to the built-in sample content arrays in
 * includes/sample_data.php so the site still displays correctly
 * (useful during development or if the DB is briefly unavailable).
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
    // Database not available — pages will use sample data fallback.
    $pdo = null;
}
