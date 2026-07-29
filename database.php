<?php
$host    = '127.0.0.1';
$db      = 'school_website_db';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

// 1. PDO Connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("PDO Connection Failed: " . $e->getMessage());
}

// 2. MySQLi Connection (Fixes $conn errors in login.php & index.php)
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("MySQLi Connection Failed: " . $conn->connect_error);
}

$conn->set_charset($charset);
?>