<?php
session_start();

// Optional: Enforce that only logged-in administrators can access this page
// if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

// Helper function to insert log entries into admin_activity_log
if (!function_exists('logActivity')) {
    function logActivity($pdo, $admin_id, $action, $target_type, $target_id = null, $details = null) {
        $stmt = $pdo->prepare("INSERT INTO admin_activity_log (admin_id, action, target_type, target_id, details, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$admin_id, $action, $target_type, $target_id, $details]);
    }
}

// 1. Database Connection Configuration
$host     = '127.0.0.1';
$db       = 'school_website_db';
$user     = 'root'; 
$pass     = ''; 
$charset  = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$success_message = '';
$error_message = '';

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'Editor'; // Default fallback role
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (!empty($name) && $email && strlen($password) >= 6) {
        try {
            // Check if the email is already taken to prevent duplicates
            $checkEmail = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
            $checkEmail->execute([$email]);
            
            if ($checkEmail->fetch()) {
                $error_message = "This email address is already registered to an account.";
            } else {
                // Handle Profile Photo Upload Setup
                $profile_photo_name = null;
                if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
                    $fileName    = $_FILES['profile_photo']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    // Whitelist safe image formats
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                    
                    if (in_array($fileExtension, $allowedExtensions)) {
                        // Create a unique name to prevent overwriting existing files
                        $profile_photo_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
                        
                        // Define upload directory path (make sure this folder exists or create it)
                        $uploadFileDir = './uploads/profiles/';
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }
                        
                        move_uploaded_file($fileTmpPath, $uploadFileDir . $profile_photo_name);
                    } else {
                        $error_message = "Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP.";
                    }
                }

                // If no profile image error occurred, proceed to insert data
                if (empty($error_message)) {
                    // Hash the password securely
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    // Insert the user mapping into the database
                    $insertStmt = $pdo->prepare("
                        INSERT INTO admin_users (name, email, password, role, profile_photo, is_active, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    
                    $insertStmt->execute([
                        $name,
                        $email,
                        $hashed_password,
                        $role,
                        $profile_photo_name,
                        $is_active
                    ]);

                    // LOG ACTIVITY ENTRY
                    $newUserId = $pdo->lastInsertId();
                    $adminId   = $_SESSION['admin_id'] ?? null;

                    logActivity(
                        $pdo,
                        $adminId,
                        'CREATE',
                        'user',
                        $newUserId,
                        "Created user account for: $name ($email) with role: $role"
                    );

                    $success_message = "New user account created successfully!";
                }
            }
        } catch (\PDOException $e) {
            $error_message = "A system database error occurred: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all options correctly. Passwords must be at least 6 characters.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Administration Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-canvas: #f4f7fc;
            --surface: #ffffff;
            --primary: #1e40af;       /* Deep Ocean Blue */
            --accent: #2563eb;        /* Electric Blue */
            --text-main: #1e293b;     /* Dark Slate */
            --text-muted: #64748b;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-lg: 0 20px 25px -5px rgba(37, 99, 235, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-canvas); 
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .form-card { 
            width: 100%;
            max-width: 540px; 
            background: var(--surface); 
            padding: 40px; 
            border-radius: var(--radius-md); 
            box-shadow: var(--shadow-lg); 
            border: 1px solid rgba(37, 99, 235, 0.05);
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .header h2 { 
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        input, select {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.95rem;
            font-family: inherit;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--radius-sm);
            box-sizing: border-box;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        input:focus, select:focus {
            outline: none;
            background-color: var(--surface);
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        /* Checkbox Switch component wrapper */
        .toggle-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
            cursor: pointer;
        }

        .toggle-group input {
            width: auto;
            margin: 0;
            transform: scale(1.2);
            cursor: pointer;
        }

        .toggle-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-main);
        }

        .btn-submit { 
            background-color: var(--accent); 
            color: white; 
            border: none; 
            padding: 14px; 
            width: 100%;
            border-radius: var(--radius-sm); 
            cursor: pointer; 
            font-size: 0.95rem; 
            font-weight: 600;
            transition: all 0.2s ease; 
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            margin-top: 10px;
        }

        .btn-submit:hover { 
            background-color: var(--primary); 
            box-shadow: 0 6px 16px rgba(30, 64, 175, 0.3);
        }

        .alert { 
            padding: 14px 16px; 
            margin-bottom: 24px; 
            border-radius: var(--radius-sm); 
            font-size: 0.88rem;
            font-weight: 500;
        }
        .alert-success { background-color: #f0fdf4; color: #166534; border-left: 4px solid #10b981; }
        .alert-danger { background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
    </style>
</head>
<body>

<div class="form-card">
    <div class="header">
        <h2>Add Account User</h2>
        <p>Provision a new management account with specific system database roles</p>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <!-- Note: enctype="multipart/form-data" is required for file uploads -->
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="name">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="andres1234.doe@.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6" placeholder="Minimum 6 characters">
            </div>

            <div class="form-group">
                <label for="role">Administrative Role</label>
                <select id="role" name="role">
                    <option value="Editor">Editor (Manage content only)</option>
                    <option value="Manager">Manager (Photo Studio Access)</option>
                    <option value="Administrator">Administrator (Full Access)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="profile_photo">Profile Photo</label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
            </div>

            <div class="form-group">
                <label>Account Status</label>
                <label class="toggle-group" for="is_active">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                    <span class="toggle-label">Activate account immediately</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">Create User Account</button>
        </div>
    </form>
</div>

</body>
</html>