<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_website_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/school-website');


try{
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}