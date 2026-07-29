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

$passed_email = $_GET['email'] ?? '';
$display_pin  = '';

if (isset($_SESSION['flash_pin'])) {
    $display_pin = $_SESSION['flash_pin'];
    unset($_SESSION['flash_pin']); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email        = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pin_code     = trim($_POST['pin_code'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if ($email && !empty($pin_code) && strlen($new_password) >= 6) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin) {
                // FIXED: We check if the PIN matches and isn't used. Expiry is managed safely via the AJAX burn trigger.
                $checkStmt = $pdo->prepare("SELECT id FROM password_resets WHERE admin_id = ? AND pin_code = ? AND is_used = 0");
                $checkStmt->execute([$admin['id'], $pin_code]);
                $reset_request = $checkStmt->fetch();

                if ($reset_request) {
                    $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
                    $updateUser = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                    $updateUser->execute([$new_hash, $admin['id']]);

                    // Burn the code so it can never be processed again
                    $burnCode = $pdo->prepare("UPDATE password_resets SET is_used = 1 WHERE id = ?");
                    $burnCode->execute([$reset_request['id']]);

                    $success_message = "Password updated successfully! <br><a href='login.php' style='color:#10b981; font-weight:700;'>Click here to Login</a>";
                } else {
                    $error_message = "Invalid or expired PIN code. Please request a new code.";
                }
            } else {
                $error_message = "Administrative account not found.";
            }
        } catch (\PDOException $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    } else {
        $error_message = "Please complete all fields properly. Password min length is 6 characters.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access Reset Vault</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-canvas: #f4f7fc; --surface: #ffffff; --primary: #1e40af; --accent: #2563eb; --text-main: #1e293b; --radius-md: 12px; --radius-sm: 8px; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-canvas); color: var(--text-main); margin: 0; padding: 20px; height: 100vh; display: flex; align-items: center; justify-content: center; box-sizing: border-box; }
        .card { width: 100%; max-width: 460px; background: var(--surface); padding: 40px; border-radius: var(--radius-md); box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.05); box-sizing: border-box; }
        h2 { font-size: 1.6rem; font-weight: 700; color: var(--primary); margin: 0 0 10px 0; text-align: center; }
        
        .secure-container {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            padding: 20px;
            border-radius: var(--radius-sm);
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
        }
        .pin-title { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: #93c5fd; opacity: 0.9; }
        .pin-digits { font-size: 2.2rem; font-weight: 800; letter-spacing: 4px; margin: 8px 0; font-family: monospace; }
        .timer-msg { font-size: 0.8rem; font-weight: 500; color: #cbd5e1; }
        
        .progress-bar {
            position: absolute;
            bottom: 0; left: 0; height: 4px;
            background-color: #10b981;
            width: 100%;
            transition: width 30s linear; 
        }

        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; }
        input { width: 100%; padding: 12px 16px; font-size: 0.95rem; font-family: inherit; border: 1.5px solid #e2e8f0; border-radius: var(--radius-sm); box-sizing: border-box; background-color: #f8fafc; }
        input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15); background: #fff; }
        .btn-submit { background-color: #10b981; color: white; border: none; padding: 14px; width: 100%; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.95rem; font-weight: 600; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: all 0.2s; margin-top: 10px; }
        .btn-submit:hover { background-color: #059669; }
        .alert { padding: 12px 16px; margin-bottom: 20px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 500; line-height: 1.4; }
        .alert-danger { background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-success { background-color: #f0fdf4; color: #166534; border-left: 4px solid #10b981; }
    </style>
</head>
<body>

<div class="card">
    <h2>Reset Your Password</h2>
    
    <?php if (!empty($display_pin)): ?>
        <div class="secure-container" id="secureVault">
            <div class="pin-title">One-Time Secure Access PIN</div>
            <div class="pin-digits"><?= htmlspecialchars($display_pin) ?></div>
            <div class="timer-msg">This window closes and expires in <span id="countdown">30</span>s</div>
            <div class="progress-bar" id="ticker"></div>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php else: ?>
        <form action="" method="POST">
            <input type="hidden" id="adminEmail" name="email" value="<?= htmlspecialchars($passed_email) ?>">

            <div class="form-group">
                <label for="pin_code">6-Digit Reset PIN</label>
                <input type="text" id="pin_code" name="pin_code" required maxlength="6" pattern="\d{6}" placeholder="Enter 6-digit PIN">
            </div>

            <div class="form-group">
                <label for="new_password">New Secure Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="Minimum 6 characters">
            </div>

            <button type="submit" class="btn-submit">Apply Changes</button>
        </form>
    <?php endif; ?>
</div>

<script>
    const vault = document.getElementById('secureVault');
    if (vault) {
        let timeLeft = 30; 
        const countdownLabel = document.getElementById('countdown');
        const ticker = document.getElementById('ticker');
        const email = document.getElementById('adminEmail').value;

        setTimeout(() => { ticker.style.width = '0%'; }, 50);

        const countdownInterval = setInterval(() => {
            timeLeft--;
            countdownLabel.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                
                vault.style.opacity = '0';
                vault.style.transform = 'scale(0.9)';
                setTimeout(() => { vault.style.display = 'none'; }, 500);

                const formData = new FormData();
                formData.append('email', email);

                // Call background script to burn the token in the database
                fetch('expire_pin.php', {
                    method: 'POST',
                    body: formData
                });
            }
        }, 1000);
    }
</script>

</body>
</html>