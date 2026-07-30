<?php
// ============================================================
//  admin/login.php — Admin Login Page
//  Open at: http://localhost/school-website/admin/login.php
// ============================================================
session_start();

// Already logged in? Go straight to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']    ?? '');
    $pass  =       $_POST['password'] ?? '';   // raw — checked with password_verify()

    // Fetch active admin by email
    $stmt = $pdo->prepare(
        'SELECT * FROM admin_users WHERE email = ? AND is_active = 1 LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify password hash
    if ($user && password_verify($pass, $user['password'])) {
        // Set session
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_role'] = $user['role'];

        // Update last_login timestamp
        $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')
            ->execute([$user['id']]);
        auditLog($pdo, $user['id'], 'LOGIN', 'admin_users', $user['id'], $user['name'] . ' logged in');

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login — <?= htmlspecialchars(getSetting($pdo, 'school_name', "Namugongo Parents' School")) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
    body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:#0f172a}
    .box{background:#fff;padding:40px;border-radius:12px;max-width:380px;width:100%}
    .box input{width:100%;padding:12px;margin-bottom:14px;border:1px solid #cbd5e1;border-radius:8px}
    .box button{width:100%;padding:12px;background:#1565C0;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer}
    .msg-err{color:#dc2626;font-weight:600;margin-bottom:14px}
</style>
</head>
<body>
<div class="box">
    <h2 style="margin-bottom:20px">Admin Login</h2>
    <?php if ($error): ?><p class="msg-err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log In</button>
    </form>
    <p style="margin-top:16px;text-align:center"><a href="../index.php">&larr; Back to Website</a></p>
</div>
</body>
</html>
