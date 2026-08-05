<?php
require_once __DIR__ . '/includes/config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $loginError = 'Both username and password are required.';
    } elseif (!$pdo instanceof PDO) {
        $loginError = 'Unable to connect to the database. Please try again later.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, password, full_name FROM admin_users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_name'] = $user['full_name'] ?: $user['username'];
                header('Location: dashboard.php');
                exit;
            }
            $loginError = 'Invalid username or password.';
        } catch (PDOException $e) {
            $loginError = 'Unable to connect to the database. Please try again later.';
        }
    }
}

$pageTitle = 'Admin Login';
$pageDescription = 'Secure administrator login for Namugongo Model Primary School staff.';
require_once __DIR__ . '/includes/header.php';
?>
        <section class="page-hero">
            <div class="container">
                <h1>Admin Login</h1>
                <p>Enter your school administrator credentials to access the secure dashboard and manage events, news, and messages.</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="form-card reveal">
                    <h3>Administrator Access</h3>
                    <?php if ($loginError) : ?>
                        <p class="notice notice-error"><?php echo h($loginError); ?></p>
                    <?php endif; ?>
                    <?php if (isset($_GET['loggedout'])) : ?>
                        <p class="notice notice-success">You have been logged out successfully.</p>
                    <?php endif; ?>
                    <form method="POST" action="admin_login.php">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo h($_POST['username'] ?? ''); ?>" required>
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <button type="submit" class="btn">Log In</button>
                    </form>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
