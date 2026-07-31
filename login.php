<?php
session_start();
require_once __DIR__ . '/db_connect.php';

$error = '';

// Handle PHP POST Request when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Ensure $pdo exists from db_connect.php
    if (isset($pdo) && $pdo !== null) {
        if (!empty($username) && !empty($password)) {
            try {
                // Query updated to target admin_users table
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username OR email = :email LIMIT 1");
                $stmt->execute([
                    ':username' => $username,
                    ':email'    => $username
                ]);
                $user = $stmt->fetch();

                if ($user) {
                    $storedPassword = $user['password'] ?? $user['user_pass'] ?? '';
                    $passwordValid = false;

                    // 1. Check Standard Password Hash
                    if (password_verify($password, $storedPassword)) {
                        $passwordValid = true;
                    } 
                    // 2. Check Direct Plaintext Match
                    elseif ($password === $storedPassword) {
                        $passwordValid = true;
                    } 
                    // 3. Check Legacy MD5 Hash
                    elseif (md5($password) === $storedPassword) {
                        $passwordValid = true;
                    }

                    if ($passwordValid) {
                        // Store essential session details dynamically
                        $_SESSION['user_id']  = $user['id'] ?? $user['user_id'] ?? $user['admin_id'] ?? 1;
                        $_SESSION['username'] = $user['username'] ?? $user['email'] ?? $username;
                        $_SESSION['role']     = $user['role'] ?? 'admin';

                        // Redirect to admin dashboard
                        header("Location: admin_dashboard.php");
                        exit();
                    } else {
                        $error = "Invalid password. Please try again.";
                    }
                } else {
                    $error = "No user account found matching that username or email in admin_users.";
                }
            } catch (PDOException $e) {
                error_log("Login Query Error: " . $e->getMessage());
                $error = "Database Error: " . $e->getMessage();
            }
        } else {
            $error = "Please enter both your username and password.";
        }
    } else {
        $error = "Database connection unavailable. Please verify db_connect.php settings.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School Portal - Login</title>
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    /* ==========================================================================
       1. COLOR PALETTE & RESET
       ========================================================================== */
    :root {
      --primary-blue: #1e3a8a;
      --secondary-blue: #3b82f6;
      --accent-green: #10b981;
      --dark-green: #047857;
      --light-green: #ecfdf5;
      --bg-light: #f8fafc;
      --text-dark: #0f172a;
      --text-muted: #64748b;
      --border-color: #cbd5e1;
      --error-red: #ef4444;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #0f172a 0%, var(--primary-blue) 50%, var(--dark-green) 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    /* ==========================================================================
       2. LOGIN CONTAINER STYLES
       ========================================================================== */
    .login-container {
      background-color: #ffffff;
      width: 100%;
      max-width: 420px;
      border-radius: 16px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
      overflow: hidden;
      animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-header {
      background: #ffffff;
      padding: 35px 30px 10px;
      text-align: center;
    }

    .brand-icon {
      width: 60px;
      height: 60px;
      background: var(--light-green);
      color: var(--accent-green);
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 15px;
      box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
    }

    .login-header h1 {
      color: var(--primary-blue);
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 6px;
    }

    .login-header p {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    .login-form {
      padding: 25px 30px 35px;
    }

    .form-group {
      margin-bottom: 20px;
      position: relative;
    }

    .form-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-wrapper i.input-icon {
      position: absolute;
      left: 14px;
      color: var(--text-muted);
      font-size: 1rem;
      transition: color 0.2s ease;
    }

    .input-wrapper input {
      width: 100%;
      padding: 12px 40px 12px 42px;
      border: 1.5px solid var(--border-color);
      border-radius: 8px;
      font-size: 0.95rem;
      color: var(--text-dark);
      outline: none;
      transition: all 0.2s ease;
    }

    .input-wrapper input:focus {
      border-color: var(--secondary-blue);
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }

    .input-wrapper input:focus + i.input-icon {
      color: var(--secondary-blue);
    }

    .toggle-password {
      position: absolute;
      right: 14px;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 1rem;
      transition: color 0.2s ease;
    }

    .toggle-password:hover {
      color: var(--text-dark);
    }

    .form-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      font-size: 0.85rem;
    }

    .remember-me {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-muted);
      cursor: pointer;
    }

    .remember-me input[type="checkbox"] {
      accent-color: var(--accent-green);
      width: 16px;
      height: 16px;
      cursor: pointer;
    }

    .forgot-link {
      color: var(--secondary-blue);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .forgot-link:hover {
      color: var(--primary-blue);
      text-decoration: underline;
    }

    .btn-submit {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: all 0.2s ease;
      box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .btn-submit:hover {
      opacity: 0.95;
      transform: translateY(-1px);
      box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
    }

    .error-alert {
      background-color: #fef2f2;
      border: 1px solid #fecaca;
      color: var(--error-red);
      padding: 10px;
      border-radius: 8px;
      font-size: 0.85rem;
      margin-bottom: 18px;
      display: <?php echo !empty($error) ? 'flex' : 'none'; ?>;
      align-items: center;
      gap: 8px;
    }

    .spinner {
      display: none;
      width: 18px;
      height: 18px;
      border: 2px solid #ffffff;
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-header">
      <div class="brand-icon">
        <i class="fa-solid fa-graduation-cap"></i>
      </div>
      <h1>Welcome Back</h1>
      <p>Sign in to access your administrative portal</p>
    </div>

    <!-- Self-submitting PHP form -->
    <form class="login-form" id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="handleLoginUI()">
      
      <!-- Error Alert Box -->
      <div class="error-alert" id="errorAlert">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span id="errorMessage"><?php echo htmlspecialchars($error); ?></span>
      </div>

      <!-- Username / Email Input -->
      <div class="form-group">
        <label for="username">Username or Email</label>
        <div class="input-wrapper">
          <input type="text" name="username" id="username" placeholder="Enter your username" required autocomplete="off" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
          <i class="fa-solid fa-user input-icon"></i>
        </div>
      </div>

      <!-- Password Input -->
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <input type="password" name="password" id="password" placeholder="Enter your password" required>
          <i class="fa-solid fa-lock input-icon"></i>
          <i class="fa-solid fa-eye toggle-password" id="toggleIcon" onclick="togglePasswordVisibility()"></i>
        </div>
      </div>

      <!-- Options -->
      <div class="form-options">
        <label class="remember-me">
          <input type="checkbox" id="remember" name="remember">
          Remember me
        </label>
        <a href="#" class="forgot-link" onclick="alert('Please contact your administrator to reset your credentials.')">Forgot password?</a>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn-submit" id="submitBtn">
        <span id="btnText">Sign In</span>
        <div class="spinner" id="btnSpinner"></div>
        <i class="fa-solid fa-arrow-right-to-bracket" id="btnIcon"></i>
      </button>
    </form>
  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggleIcon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }

    function handleLoginUI() {
      const submitBtn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnIcon = document.getElementById('btnIcon');
      const btnSpinner = document.getElementById('btnSpinner');

      btnText.textContent = 'Authenticating...';
      btnIcon.style.display = 'none';
      btnSpinner.style.display = 'inline-block';
    }
  </script>
</body>
</html>