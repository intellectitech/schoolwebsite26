<?php
// ============================================================
//  admin/create_admin.php
//  Run ONCE to create your first admin, then DELETE this file.
//  Adapted for school_website_db (email + password + name).
// ============================================================
require_once __DIR__ . '/../includes/functions.php';

$done = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = 'Valid email is required and password must be at least 6 characters.';
    } else {
        $check = $pdo->prepare('SELECT id FROM admin_users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'That email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            // Match school_website_db columns
            $pdo->prepare(
                'INSERT INTO admin_users (name, email, password, role, profile_photo, is_active)
                 VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([$name ?: 'Administrator', $email, $hash, 'super_admin', '', 1]);
            $done = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Admin Account</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
    body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:#0f172a}
    .box{background:#fff;padding:40px;border-radius:12px;max-width:400px;width:100%}
    .box input{width:100%;padding:12px;margin-bottom:14px;border:1px solid #cbd5e1;border-radius:8px}
    .box button{width:100%;padding:12px;background:#1565C0;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer}
    .msg-ok{color:#16a34a;font-weight:600}
    .msg-err{color:#dc2626;font-weight:600}
</style>
</head>
<body>
<div class="box">
    <h2 style="margin-bottom:20px">Create Admin Account</h2>
    <?php if ($done): ?>
        <p class="msg-ok">Admin account created successfully!</p>
        <p style="margin-top:14px"><a href="login.php">Go to Login &rarr;</a></p>
        <p style="margin-top:20px;color:#b91c1c;font-size:14px">
            ⚠ For security, delete this file (admin/create_admin.php) now.
        </p>
    <?php else: ?>
        <?php if ($error): ?><p class="msg-err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password (min 6 characters)" required>
            <button type="submit">Create Admin</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
