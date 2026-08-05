<?php
/**
 * includes/config.php
 * Central application configuration for Namugongo Model Primary School.
 * - Database connection (PDO, prepared statements only)
 * - Site-wide constants used for SEO / branding across every page
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------
// Database connection
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'namugongo_school');
define('DB_USER', 'root');
define('DB_PASS', '');

$pdo = null;
$dbError = null;

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Never leak raw DB credentials/errors to the public in production.
    $dbError = 'We could not connect to the school database right now. Please try again shortly.';
}

// ---------------------------------------------------------------------
// Site-wide constants (used for SEO meta tags, footer, contact widgets)
// ---------------------------------------------------------------------
define('SITE_NAME', 'Namugongo Model Primary School');
define('SITE_TAGLINE', 'Education Is My Future');
define('SITE_URL', 'https://www.namugongomodelschool.edu.ug');
define('SITE_PHONE', '+256 702 123 456');
define('SITE_EMAIL', 'info@namugongomodelschool.edu.ug');
define('SITE_ADDRESS', 'Namugongo, Kampala, Uganda');
define('SITE_DESCRIPTION', 'Namugongo Model Primary School in Kampala, Uganda offers a safe, caring and inspiring learning environment where every child grows academically, socially and morally.');

/**
 * Helper: safely escape output for HTML context.
 */
function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Helper: current page filename, used to mark the active nav link.
 */
function current_page()
{
    return basename($_SERVER['SCRIPT_NAME']);
}
