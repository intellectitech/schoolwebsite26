<?php
session_start();
$host     = '127.0.0.1';
$db       = 'school_website_db';
$user     = 'root'; 
$pass     = ''; 
$charset  = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin) {
            // Generate a 6-digit numeric PIN
            $pin_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // STRICT DATABASE TIMER: set expiration exactly 30 seconds into the future
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 seconds')); 

            // Clear any old recovery pins for this specific admin
            $clearOld = $pdo->prepare("DELETE FROM password_resets WHERE admin_id = ?");
            $clearOld->execute([$admin['id']]);

            // Save pin code and expiration to database
            $insertStmt = $pdo->prepare("INSERT INTO password_resets (admin_id, pin_code, expires_at) VALUES (?, ?, ?)");
            $insertStmt->execute([$admin['id'], $pin_code, $expires_at]);

            // Flash the pin to session memory so reset_password.php can display it once
            $_SESSION['flash_pin'] = $pin_code;

            header("Location: reset_password.php?email=" . urlencode($email));
            exit;
        } else {
            $error_message = "This administrative email is not registered.";
        }
    } else {
        $error_message = "Please insert a valid email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery Terminal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-canvas: #f4f7fc; --surface: #ffffff; --primary: #1e40af; --accent: #2563eb; --text-main: #1e293b; --radius-md: 12px; --radius-sm: 8px; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-canvas); color: var(--text-main); margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { width: 100%; max-width: 440px; background: var(--surface); padding: 40px; border-radius: var(--radius-md); box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.05); box-sizing: border-box; }
        h2 { font-size: 1.6rem; font-weight: 700; color: var(--primary); margin: 0 0 10px 0; text-align: center; }
        p { font-size: 0.9rem; color: #64748b; text-align: center; line-height: 1.5; margin-bottom: 25px; }
        .form-group { margin-bottom: 24px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        input { width: 100%; padding: 12px 16px; font-size: 0.95rem; font-family: inherit; border: 1.5px solid #e2e8f0; border-radius: var(--radius-sm); box-sizing: border-box; background-color: #f8fafc; }
        input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15); }
        .btn-submit { background-color: var(--accent); color: white; border: none; padding: 14px; width: 100%; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.95rem; font-weight: 600; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); transition: all 0.2s; }
        .btn-submit:hover { background-color: var(--primary); }
        .alert { padding: 12px 16px; margin-bottom: 20px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 500; background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
        .back-link { display: block; text-align: center; margin-top: 15px; font-size: 0.85rem; color: #64748b; text-decoration: none; font-weight: 500; }
        .back-link:hover { color: var(--accent); }
    </style>
</head>
<body>

<div class="card">
    <h2>Forgot Password?</h2>
    <p>Enter your account email. The system will display a dynamic 30-second PIN code on the next screen.</p>

    <?php if (isset($error_message)): ?>
        <div class="alert"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="email">Administrative Email</label>
            <input type="email" id="email" name="email" required placeholder="enter your email">
        </div>
        <button type="submit" class="btn-submit">Generate Reset Code</button>
        <a href="login.php" class="back-link">← Return to Login</a>
    </form>
</div>

</body>
</html>