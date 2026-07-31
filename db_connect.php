<?php
/**
 * Database Connection Configuration
 * Database: school_website_db
 */

// 1. Connection Parameters
$host     = '127.0.0.1'; // or 'localhost'
$dbname   = 'school_website_db';
$username = 'root';      // Default XAMPP/MariaDB username
$password = '';          // Default XAMPP password (leave empty unless changed)
$charset  = 'utf8mb4';

// 2. DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// 3. PDO Options
$options = [
    // Throw exceptions on SQL errors for easier debugging
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Return query results as associative arrays by default
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Emulate prepared statements off for better native security
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4. Establish Connection
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // If the connection fails, log error and terminate script safely
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please contact system administrator.");
}