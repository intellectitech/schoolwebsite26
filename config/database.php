<?php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_website_db');
define('DB_USER', 'sadam');
define('DB_PASS', 'sadam123');

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
    die('Database Connection Failed: ' . $e->getMessage());
}
?>