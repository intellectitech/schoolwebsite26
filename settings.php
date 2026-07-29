<?php
// Ensure session is started and user is authorized
if (session_id() == '' || !isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

// --- Database Connection Config ---
$host = '127.0.0.1'; $db = 'school_website_db'; $user = 'root'; $pass = ''; $charset = 'utf8mb4';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\PDOException $e) { die("Database error: " . $e->getMessage()); }

// Fetch the fresh real-time record for this user
$stmt = $pdo->prepare("SELECT email, created_at, profile_photo FROM admin_users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$user_profile = $stmt->fetch();

// Format variables safely
$current_name  = $_SESSION['admin_name'] ?? 'User Account';
$current_role  = $_SESSION['admin_role'] ?? 'Staff Member';
$current_email = $user_profile['email'] ?? 'Not available';
$photo_filename = $user_profile['profile_photo'] ?? '';

// Convert the database timestamp into a beautiful readable format (e.g., "October 24, 2025")
$join_date = !empty($user_profile['created_at']) 
    ? date("F j, Y", strtotime($user_profile['created_at'])) 
    : 'Unknown Date';
?>