<?php
// ============================================================
//  config/database.php — Database Connection
//  Namugongo Parents' School
//  Uses the school_website_db schema.
// ============================================================

define('DB_HOST',    'localhost');
define('DB_NAME',    'school_website_db');
define('DB_USER',    'root');        // XAMPP default
define('DB_PASS',    '');            // XAMPP default: blank
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = 'mysql:host=' . DB_HOST
         . ';dbname=' . DB_NAME
         . ';charset=' . DB_CHARSET;

    $pdo = new PDO($dsn, DB_USER, DB_PASS);

    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);

} catch (PDOException $e) {
    error_log('DB Connection failed: ' . $e->getMessage());
    die('<p style="font-family:Arial;color:red;padding:2rem">
        Database unavailable. Make sure XAMPP\'s MySQL is running and the
        "school_website_db" database has been imported. Please try again later.</p>');
}
