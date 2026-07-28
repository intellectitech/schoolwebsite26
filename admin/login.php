<?php
// ============================================================
//  admin/login.php — Admin Login
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Already logged in? Go straight to the dashboard.
if (isset($_SESSION['admin_id'])) {
  header('Location: dashboard.php');
  exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    $errors[] = 'Your session expired. Please try again.';
  } else {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
      $errors[] = 'Please enter both your email and password.';
    } else {
      $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE email = ? AND is_active = 1 LIMIT 1');
      $stmt->execute([$email]);
      $admin = $stmt->fetch();

      if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];

        $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$admin['id']]);
        auditLog($pdo, $admin['id'], 'login', 'admin_users', $admin['id'], 'Logged in');

        header('Location: dashboard.php');
        exit;
      }

      $errors[] = 'Incorrect email or password.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login · Uganda Martyrs Primary School</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
</head>

<body class="admin-body">

  <div class="admin-login-wrap">
    <div class="admin-login-card reveal in">
      <div class="admin-login-brand">
        <img src="../images/ESD_69e8c39b15887.webp" alt="School logo" class="admin-login-logo">
        <h1>Admin Sign In</h1>
        <p>Uganda Martyrs Primary School, Namugongo</p>
      </div>

      <?php if ($errors): ?>
        <div class="alert alert-error">
          <ul><?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" class="admin-login-form" novalidate>
        <?= csrfField() ?>
        <div class="field">
          <label for="l-email">Email address</label>
          <input id="l-email" name="email" type="email" required autofocus
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="l-password">Password</label>
          <input id="l-password" name="password" type="password" required>
        </div>
        <button type="submit" class="btn btn-primary admin-login-btn">Sign In</button>
        <p class="admin-login-forgot"><a href="forgot-password.php">Forgot your password?</a></p>
      </form>

      <p class="admin-login-back"><a href="../index.php">← Back to the school website</a></p>
    </div>
  </div>

</body>

</html>