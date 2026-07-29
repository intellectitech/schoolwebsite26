<?php
// expire_pin.php
$host     = '127.0.0.1';
$db       = 'school_website_db';
$user     = 'root'; 
$pass     = ''; 
$charset  = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (\PDOException $e) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($email) {
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // FIXED: Instantly burn the pin inside the database by marking it as used
            $update = $pdo->prepare("UPDATE password_resets SET is_used = 1 WHERE admin_id = ? AND is_used = 0");
            $update->execute([$admin['id']]);
        }
    }
}