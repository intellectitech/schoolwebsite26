<?php
// Define the function (usually in an auth or helper file)
function require_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if the user session variable exists
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit();
    }
}

// Now you can call it safely
include __DIR__ . '/db_connect.php';
require_login();
?>