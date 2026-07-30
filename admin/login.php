<?php
// admin/login.php
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/functions.php';

$error = '';

// Get school logo for login page
$schoolLogo = getSetting($pdo, 'school_logo', '');
$schoolName = getSetting($pdo, 'school_name', 'School');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role'];
            
            $stmt = $pdo->prepare("
                INSERT INTO audit_log (admin_id, action, table_name, description, ip_address) 
                VALUES (?, 'login', 'admin_users', 'Admin logged in', ?)
            ");
            $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= clean($schoolName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(0, 200, 83, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(255, 107, 107, 0.03) 0%, transparent 50%);
            animation: liquidBg 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes liquidBg {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
            100% { transform: rotate(360deg) scale(1); }
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(40px);
            border-radius: 30px;
            padding: 50px 40px;
            max-width: 420px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
            animation: glassIn 0.8s ease-out;
        }

        @keyframes glassIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 32px;
            background: linear-gradient(135deg, rgba(0, 200, 83, 0.3), transparent 50%, rgba(255, 107, 107, 0.2));
            z-index: -1;
            animation: liquidBorder 4s ease-in-out infinite;
        }

        @keyframes liquidBorder {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .login-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-card .logo .logo-img {
            max-height: 80px;
            margin-bottom: 15px;
        }

        .login-card .logo .icon-wrapper {
            display: inline-block;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #009624, #00C853);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 30px rgba(0, 200, 83, 0.3);
        }

        .login-card .logo i {
            font-size: 2.5rem;
            color: #fff;
        }

        .login-card .logo h1 {
            font-size: 1.8rem;
            color: #fff;
            font-weight: 700;
        }

        .login-card .logo p {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: none;
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-group input:focus {
            outline: none;
            border-color: #00C853;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 30px rgba(0, 200, 83, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #009624, #00C853);
            color: #fff;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 200, 83, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 200, 83, 0.4);
        }

        .error {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 0, 0, 0.1);
        }

        .demo-credentials {
            background: rgba(255, 255, 255, 0.04);
            padding: 15px 20px;
            border-radius: 14px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .demo-credentials p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.5);
            margin: 3px 0;
        }

        .demo-credentials strong {
            color: #00C853;
        }

        .admin-link {
            text-align: center;
            margin-top: 20px;
        }

        .admin-link a {
            color: rgba(255, 255, 255, 0.3);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .admin-link a:hover {
            color: #00C853;
        }

        @media (max-width: 480px) {
            .login-card { padding: 30px 25px; border-radius: 20px; }
            .login-card .logo h1 { font-size: 1.4rem; }
            .login-card .logo .icon-wrapper { width: 60px; height: 60px; }
            .login-card .logo .icon-wrapper i { font-size: 2rem; }
            .login-card .logo .logo-img { max-height: 60px; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <?php if (!empty($schoolLogo)): ?>
                <img src="../<?= clean($schoolLogo) ?>" alt="<?= clean($schoolName) ?>" class="logo-img">
            <?php else: ?>
                <div class="icon-wrapper">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            <?php endif; ?>
            <h1><?= clean($schoolName) ?></h1>
            <p>Admin Login</p>
        </div>
        
        
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" placeholder="admin@school.ug" required autofocus>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <div class="admin-link">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Website</a>
        </div>
    </div>
</body>
</html>