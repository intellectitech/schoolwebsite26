<?php
session_start();

include 'database.php'; 





$message = "";
$message_type = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, name, password, is_active FROM admin_users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($user['is_active'] == 0) {
                $message = "Your account is deactivated. Contact the Super Admin.";
                $message_type = "error";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];

                $current_time = date("Y-m-d H:i:s");
                $update_stmt = $conn->prepare("UPDATE admin_users SET last_login = ? WHERE id = ?");
                $update_stmt->bind_param("si", $current_time, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();

                $message = "Logged in successfully! Redirecting...";
                $message_type = "success";

                echo "<script>
                    setTimeout(function(){
                        window.location.href = 'dashboard.php';
                    }, 2000);
                </script>";
            } else {
                $message = "Wrong password or username.";
                $message_type = "error";
            }
        } else {
            $message = "Wrong password or username.";
            $message_type = "error";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
        $message_type = "error";
    }
}
$conn->close();
?>

































<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Administrator Login</title>

<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/login.css">

</head>

<body>

  <?php if (!empty($message)): ?>
        <div class="alert-popup alert-<?php echo $message_type; ?>">
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

 

<div class="container">

    <!-- Left Side -->
    <div class="left">

        <div class="overlay">

              <img style="width: 100px;border-radius: 500%;background-color: white;height: 90px;margin-left: 30px; box-shadow: 0 10px 30px rgba(0, 2, 10, 0.754)    ;" src="assets/images/logoo.svg" alt="">

            <h1>St. Henry’s College Namugongo</h1>

            <h2>ADMINISTRATOR</h2>

           <p>
                Welcome back Administrator.
                Manage students, teachers, 
                admissions and much more from one dashboard.
            </p>

        </div>

    </div>

    <!-- Right Side -->

    <div class="right">

        <form  action="login.php" method="post"    class="login-box" action="dashboard.html">

            <h1>Administrator Login</h1>

            <p>Sign in to continue</p>

            <div class="input-box">

                <i class='bx bx-user'></i>

                <input
                type="text"
                placeholder="Administrator Username"  name="name"
                required>

            </div>





    <div class="input-box">

                <i class='bx bx-user'></i>

                <input
                type="text"
                placeholder="email"  name="email"
                required>

            </div>







            <div class="input-box password">

                <i class='bx bx-lock-alt'></i>

                <input
                id="password"
                type="password"
                placeholder="Password"  name="password"
                required>

                <i
                class='bx bx-hide'
                id="togglePassword"></i>

            </div>

            <div class="options">

                <label>

                    <input type="checkbox">

                    Remember Me

                </label>

                <a href="#">

                    Forgot Password?

                </a>

            </div>

            <button class="btn-login"   type="submit">

                Login

            </button>



        </form>

  


    </div>

    


</div>














<script src="assets/js/login.js"></script>


 <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
        }
        .login-container h2 {
            margin-bottom: 24px;
            color: #333333;
            font-size: 28px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #666666;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cccccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #1565C0;
            outline: none;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #1565C0;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #0d47a1;
        }
        .alert-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: #ffffff;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.5s ease forward, fadeOut 0.5s ease 2.5s forwards;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background-color: #2e7d32;
            border-left: 6px solid #1b5e20;
        }
        .alert-error {
            background-color: #d32f2f;
            border-left: 6px solid #b71c1c;
        }
        @keyframes slideIn {
            from { transform: translateX(120%); }
            to { transform: translateX(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(120%); hidden: true; }
        }

    </style>























</body>
</html>