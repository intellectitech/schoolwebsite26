<?php
// includes/functions.php

// Sanitize input
function clean($input) {
    if (is_array($input)) {
        return array_map('clean', $input);
    }
    return htmlspecialchars(trim(stripslashes($input)), ENT_QUOTES, 'UTF-8');
}

// Get setting from school_info table with caching
function getSetting($pdo, $key, $default = '') {
    static $settings = null;
    if ($settings === null) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM school_info");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    return $settings[$key] ?? $default;
}

// Redirect with message
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header('Location: ' . $url);
    exit;
}

// Display flash message
function displayFlash() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'success';
        $class = $type === 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $class . '">' . clean($_SESSION['flash_message']) . '</div>';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}

// Format date
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

// Truncate text
function truncate($text, $length = 150, $ending = '...') {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $ending;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_name']);
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('admin/login.php', 'Please login first', 'danger');
    }
}

// Generate slug
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}
?>