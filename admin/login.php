<?php
// admin/login.php - NO PASSWORD HASHING
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Direct password comparison - NO HASHING
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role'];
            
            // Log login
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
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ============================================
           LIQUID GLASS ADMIN LOGIN
           Colors: White, Orange (#FF6B00), Black
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(255, 107, 0, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(255, 107, 0, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 50% 20%, rgba(255, 107, 0, 0.06) 0%, transparent 50%);
            animation: liquidBg 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes liquidBg {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
            100% { transform: rotate(360deg) scale(1); }
        }

        /* Floating Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: orbFloat 15s ease-in-out infinite;
            z-index: 0;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(255, 107, 0, 0.15);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: rgba(255, 107, 0, 0.1);
            bottom: -50px;
            left: -50px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(255, 107, 0, 0.08);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Login Card - Liquid Glass */
        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border-radius: 30px;
            padding: 50px 40px;
            max-width: 420px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: glassIn 0.8s ease-out;
        }

        @keyframes glassIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Liquid Shine Effect */
        .login-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 32px;
            background: linear-gradient(135deg, rgba(255, 107, 0, 0.3), transparent 50%, rgba(255, 107, 0, 0.1));
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

        .login-card .logo .icon-wrapper {
            display: inline-block;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #FF6B00, #ff8c38);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 30px rgba(255, 107, 0, 0.3);
        }

        .login-card .logo i {
            font-size: 2.5rem;
            color: #fff;
        }

        .login-card .logo h1 {
            font-size: 1.8rem;
            color: #fff;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .login-card .logo p {
            color: rgba(255, 255, 255, 0.5);
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
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            letter-spacing: 0.5px;
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
            border-color: #FF6B00;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 30px rgba(255, 107, 0, 0.1), inset 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #FF6B00, #e85e00);
            color: #fff;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255, 107, 0, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(255, 107, 0, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error {
            background: rgba(255, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 0, 0, 0.1);
        }

        .demo-credentials {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            border-radius: 14px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .demo-credentials p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
            margin: 3px 0;
        }

        .demo-credentials strong {
            color: #FF6B00;
        }

        .admin-link {
            text-align: center;
            margin-top: 20px;
        }

        .admin-link a {
            color: rgba(255, 255, 255, 0.4);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .admin-link a:hover {
            color: #FF6B00;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 25px;
                border-radius: 20px;
            }
            
            .login-card .logo h1 {
                font-size: 1.4rem;
            }
            
            .login-card .logo .icon-wrapper {
                width: 60px;
                height: 60px;
            }
            
            .login-card .logo .icon-wrapper i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Login Card -->
    <div class="login-card">
        <div class="logo">
            <div class="icon-wrapper">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>Admin Login</h1>
            <p>School Management Panel</p>
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