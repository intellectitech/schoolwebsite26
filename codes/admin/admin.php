<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin.php
ob_start();
session_start();

require_once 'database.php';
// ... rest of your file

require_once 'database.php';

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('log_audit_action')) {
    function log_audit_action($action, $table, $recordId, $description) {
        global $pdo;
        
        $adminId = $_SESSION['admin_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $stmt = $pdo->prepare(
            "INSERT INTO audit_logs (admin_id, action, table_name, record_id, description, ip_address) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$adminId, $action, $table, $recordId, $description, $ipAddress]);
    }
}

// Authentication Logic
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_photo'] = $user['profile_photo'];
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                log_audit_action('login', 'admin_users', $user['id'], 'User logged in successfully');
                
                header("Location: admin.php");
                exit;
            } else {
                $loginError = 'Invalid email or password, or account is inactive.';
            }
        } catch (PDOException $e) {
            $loginError = 'Database error: ' . $e->getMessage();
        }
    } else {
        $loginError = 'Please fill in all fields.';
    }
}

// Logout logic
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    if (isset($_SESSION['admin_id'])) {
        log_audit_action('logout', 'admin_users', $_SESSION['admin_id'], 'User logged out');
    }
    session_destroy();
    header("Location: admin.php");
    exit;
}

$isLoggedIn = isset($_SESSION['admin_id']);

if (!$isLoggedIn):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BorgiaHub | Admin Login</title>
    <link rel="icon" href="badge.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #11131c;
            --bg-card: #1b1e2e;
            --text-main: #ffffff;
            --text-muted: #8f9bb3;
            --accent-red: #d01116;
            --accent-yellow: #fed100;
            --border-color: rgba(255, 255, 255, 0.08);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .login-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .login-brand img {
            height: 60px;
            margin-bottom: 1rem;
        }
        .login-brand h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .login-brand h2 span {
            color: var(--accent-red);
        }
        .login-brand p {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 2rem;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            background-color: var(--bg-base);
            border: 1px solid var(--border-color);
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            border-radius: 10px;
            outline: none;
            color: var(--text-main);
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: var(--accent-yellow);
        }
        .login-btn {
            width: 100%;
            background-color: var(--accent-yellow);
            color: #111;
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 1rem;
        }
        .login-btn:hover {
            background-color: #e5bd00;
            transform: translateY(-2px);
        }
        .error-msg {
            background: rgba(208, 17, 22, 0.15);
            border: 1px solid var(--accent-red);
            color: #ff4d4d;
            padding: 0.8rem;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <img src="naps/logo.jpg" alt="Logo">
            <h2>Borgia<span>Hub</span></h2>
            <p>Admin Portal Authentication</p>
        </div>
        
        <?php if ($loginError): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.4rem;"></i> <?= htmlspecialchars($loginError) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="admin.php">
            <input type="hidden" name="login" value="1">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <i class="far fa-envelope"></i>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
            </div>
            
            <button type="submit" class="login-btn">Authenticate</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// Main Portal parameters
$currentPage = $_GET['page'] ?? 'dashboard';

// Role-based access control: which pages each role is allowed to open
$rolePermissions = [
    'super_admin' => ['dashboard', 'enquiries', 'messages', 'news', 'staff', 'testimonials', 'faqs', 'settings', 'admins'],
    'editor'      => ['dashboard', 'enquiries', 'messages', 'news', 'staff', 'testimonials', 'faqs', 'settings'],
    'staff'       => ['dashboard', 'enquiries', 'messages'],
    'news_editor' => ['news'],
];
$allowedPages = $rolePermissions[$_SESSION['admin_role']] ?? ['dashboard'];

// If the requested page isn't allowed for this role, bounce to the first page they ARE allowed to see
if (!in_array($currentPage, $allowedPages)) {
    $currentPage = $allowedPages[0];
}

function is_page_active($page) {
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}

function can_access($page) {
    global $allowedPages;
    return in_array($page, $allowedPages);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BorgiaHub | School Admin Portal</title>
    <link rel="icon" href="badge.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #11131c;
            --bg-card: #1b1e2e;
            --bg-hover: #262a42;
            --text-main: #ffffff;
            --text-muted: #8f9bb3;
            --accent-red: #d01116;
            --accent-red-hover: #9e0a0e;
            --accent-yellow: #fed100;
            --accent-green: #00b894;
            --accent-blue: #0084ff;
            --accent-purple: #9b5de5;
            --border-color: rgba(255, 255, 255, 0.08);
            --transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--bg-card);
            border-right: 1.5px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 1rem;
            flex-shrink: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 2rem;
            text-decoration: none;
            color: var(--text-main);
            padding-left: 0.5rem;
        }

        .sidebar-brand img {
            height: 38px;
            width: auto;
        }

        .sidebar-brand h2 {
            font-size: 1.15rem;
            font-weight: 700;
        }

        .sidebar-brand h2 span {
            color: var(--accent-red);
        }

        .menu-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            margin: 1rem 0 0.5rem 0.6rem;
            font-weight: 700;
        }

        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.7rem 0.9rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 10px;
            transition: var(--transition);
        }

        .menu-item a .menu-left {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .menu-item a i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
        }

        .menu-item a:hover {
            color: var(--text-main);
            background-color: var(--bg-hover);
        }

        .menu-item.active a {
            color: #111;
            background-color: var(--accent-yellow);
            font-weight: 700;
        }

        .menu-item.active a i {
            color: #111;
        }

        .arrow-icon {
            font-size: 0.65rem !important;
        }

        /* Workspace Header */
        .workspace {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            height: 100vh;
        }

        .workspace-header {
            height: 70px;
            background-color: var(--bg-card);
            border-bottom: 1.5px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            flex-shrink: 0;
        }

        .search-box {
            position: relative;
            width: 320px;
        }

        .search-box input {
            width: 100%;
            background-color: var(--bg-base);
            border: 1px solid var(--border-color);
            padding: 0.55rem 1rem 0.55rem 2.5rem;
            border-radius: 30px;
            outline: none;
            color: var(--text-main);
            font-size: 0.85rem;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .header-user-profile {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .header-icon-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.1rem;
            cursor: pointer;
            position: relative;
            padding: 0.4rem;
            border-radius: 50%;
        }

        .header-icon-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .badge-dot {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 7px;
            height: 7px;
            background-color: var(--accent-red);
            border-radius: 50%;
        }

        .user-info-wrapper {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .user-info-text {
            text-align: right;
        }

        .user-info-text h4 {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .user-info-text p {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent-yellow);
        }

        /* Dashboard Layout Grid */
        .dashboard-content {
            padding: 1.8rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
            flex-grow: 1;
        }

        /* Top 4 Stat Cards */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .metric-card {
            border-radius: 16px;
            padding: 1.2rem 1.4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 110px;
            position: relative;
            color: #111;
        }

        .card-bg-purple { background-color: #d8d4fd; }
        .card-bg-yellow { background-color: #fde8b3; }
        .card-bg-blue { background-color: #d1e5ff; }
        .card-bg-gold { background-color: #ffe699; }

        .metric-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .metric-badge {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }

        .metric-bottom h3 {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .metric-bottom p {
            font-size: 0.78rem;
            font-weight: 600;
            opacity: 0.75;
        }

        /* Main Workspace Grid */
        .main-workspace-grid {
            display: grid;
            grid-template-columns: 2.2fr 1fr;
            gap: 1.5rem;
        }

        .left-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .right-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .dashboard-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.4rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
        }

        .card-header-flex h3 {
            font-size: 1.05rem;
            font-weight: 600;
        }

        /* Form Controls styling in Subpages */
        .form-control {
            width: 100%;
            background-color: var(--bg-base);
            border: 1px solid var(--border-color);
            padding: 0.65rem 1rem;
            border-radius: 10px;
            outline: none;
            color: var(--text-main);
            font-size: 0.9rem;
            transition: 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--accent-yellow);
        }

        .btn {
            display: inline-block;
            background-color: var(--accent-yellow);
            color: #111;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: 0.25s;
        }

        .btn:hover {
            transform: translateY(-1.5px);
            opacity: 0.95;
        }

        .btn-secondary {
            background-color: rgba(255,255,255,0.06);
            color: #fff;
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: rgba(255,255,255,0.12);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .metrics-grid { grid-template-columns: repeat(2, 1fr); }
            .main-workspace-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a href="index.php" class="sidebar-brand">
            <img src="naps/logo.jpg" alt="Borgia Logo">
            <h2>Borgia<span>Hub</span></h2>
        </a>

        <h4 class="menu-title">Main Menu</h4>
        <ul class="menu-list">
            <?php if (can_access('dashboard')): ?>
            <li class="menu-item <?= is_page_active('dashboard') ?>">
                <a href="admin.php?page=dashboard">
                    <div class="menu-left">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            <?php if (can_access('enquiries')): ?>
            <li class="menu-item <?= is_page_active('enquiries') ?>">
                <a href="admin.php?page=enquiries">
                    <div class="menu-left">
                        <i class="fas fa-user-graduate"></i>
                        <span>Enquiries</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            <?php if (can_access('messages')): ?>
            <li class="menu-item <?= is_page_active('messages') ?>">
                <a href="admin.php?page=messages">
                    <div class="menu-left">
                        <i class="far fa-comment-dots"></i>
                        <span>Messages</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            <?php if (can_access('news')): ?>
            <li class="menu-item <?= is_page_active('news') ?>">
                <a href="admin.php?page=news">
                    <div class="menu-left">
                        <i class="far fa-newspaper"></i>
                        <span>News Board</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            <?php if (can_access('staff')): ?>
            <li class="menu-item <?= is_page_active('staff') ?>">
                <a href="admin.php?page=staff">
                    <div class="menu-left">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Staff & Depts</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            <?php if (can_access('testimonials')): ?>
            <li class="menu-item <?= is_page_active('testimonials') ?>">
                <a href="admin.php?page=testimonials">
                    <div class="menu-left">
                        <i class="far fa-comment-alt"></i>
                        <span>Testimonials</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            <?php if (can_access('faqs')): ?>
            <li class="menu-item <?= is_page_active('faqs') ?>">
                <a href="admin.php?page=faqs">
                    <div class="menu-left">
                        <i class="far fa-question-circle"></i>
                        <span>FAQs Portal</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <?php if (can_access('settings') || can_access('admins')): ?>
        <h4 class="menu-title">System Settings</h4>
        <ul class="menu-list">
            <?php if (can_access('settings')): ?>
            <li class="menu-item <?= is_page_active('settings') ?>">
                <a href="admin.php?page=settings">
                    <div class="menu-left">
                        <i class="fas fa-cog"></i>
                        <span>Global Info</span>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                <li class="menu-item <?= is_page_active('admins') ?>">
                    <a href="admin.php?page=admins">
                        <div class="menu-left">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin Users</span>
                        </div>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <?php endif; ?>

        <ul class="menu-list">
            <li class="menu-item">
                <a href="admin.php?action=logout">
                    <div class="menu-left">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Log Out</span>
                    </div>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Workspace Area -->
    <main class="workspace">
        <!-- Header Bar -->
        <header class="workspace-header">
            <div style="font-weight: 700; font-size: 1.1rem; color: #fff; text-transform: capitalize;">
                <?= e(str_replace('_', ' ', $currentPage)) ?> Area
            </div>

            <div class="header-user-profile">
                <div class="user-info-wrapper">
                    <div class="user-info-text">
                        <h4><?= e($_SESSION['admin_name']) ?></h4>
                        <p><?= e(ucfirst(str_replace('_', ' ', $_SESSION['admin_role']))) ?></p>
                    </div>
                    <img src="<?= e($_SESSION['admin_photo'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80') ?>" alt="User Avatar" class="user-avatar">
                </div>
            </div>
        </header>

        <!-- Dashboard Body Content -->
        <div class="dashboard-content">
            <?php
            switch ($currentPage) {
                case 'enquiries':
                    include 'admin_enquiries.php';
                    break;
                case 'messages':
                    include 'admin_messages.php';
                    break;
                case 'news':
                    include 'admin_news.php';
                    break;
                case 'staff':
                    include 'admin_staff.php';
                    break;
                case 'testimonials':
                    include 'admin_testimonials.php';
                    break;
                case 'faqs':
                    include 'admin_faqs.php';
                    break;
                case 'settings':
                    include 'admin_settings.php';
                    break;
                case 'admins':
                    if ($_SESSION['admin_role'] === 'super_admin') {
                        include 'admin_users.php';
                    } else {
                        echo "<h3 style='color: var(--accent-red);'>Access Denied</h3><p style='color: var(--text-muted);'>You do not have permission to view this resource.</p>";
                    }
                    break;
                case 'dashboard':
                default:
                    include 'admin_dashboard.php';
                    break;
            }
            ?>
        </div>
    </main>

</body>
</html>
