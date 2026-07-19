<?php
/**
 * admin/login.php
 * Admin sign-in screen. Verifies against the admin_users table.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/datastore.php';

// Already logged in? Go straight to dashboard.
if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$timeout = isset($_GET['timeout']);
$loggedout = isset($_GET['loggedout']);

// Basic login rate-limiting per session (slow down brute force attempts)
if (!isset($_SESSION['login_attempts'])) { $_SESSION['login_attempts'] = 0; }
if (!isset($_SESSION['login_locked_until'])) { $_SESSION['login_locked_until'] = 0; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (time() < $_SESSION['login_locked_until']) {
        $error = "Too many failed attempts. Please wait a minute and try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = "Please enter both username and password.";
        } else {
            $admin = ds_read_object('admin');

            if ($admin && strtolower($admin['username']) === strtolower($username) && password_verify($password, $admin['password_hash'])) {
                // Success
                session_regenerate_id(true);
                $_SESSION['admin_id'] = 1;
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['login_attempts'] = 0;
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['login_locked_until'] = time() + 60;
                    $error = "Too many failed attempts. Please wait a minute and try again.";
                } else {
                    $error = "Incorrect username or password.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | Mbuya Parents' School</title>
<link rel="icon" type="image/svg+xml" href="../assets/images/badge.svg">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="login-wrap">
  <div class="login-card">
    <img src="../assets/images/badge.svg" alt="Mbuya Parents' School badge" class="login-badge">
    <h1>Admin Dashboard</h1>
    <p class="login-sub">Mbuya Parents' School</p>

    <?php if ($timeout): ?>
      <div class="alert alert-info">You were signed out due to inactivity. Please log in again.</div>
    <?php endif; ?>
    <?php if ($loggedout): ?>
      <div class="alert alert-success">You have been logged out successfully.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username" required autofocus value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-gold btn-block">Log In</button>
    </form>

    <p class="login-note">
      Default demo login &mdash; <strong>admin</strong> / <strong>MbuyaAdmin@2026</strong><br>
      Please change this password after your first login.
    </p>
  </div>
</div>

</body>
</html>
