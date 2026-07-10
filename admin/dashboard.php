<?php
// admin/dashboard.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Dashboard - Admin';

// Get stats
$totalNews = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalPublished = $pdo->query("SELECT COUNT(*) FROM news WHERE is_published = 1")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$totalEnquiries = $pdo->query("SELECT COUNT(*) FROM admissions_enquiries WHERE status = 'new'")->fetchColumn();

// Recent activity
$activityStmt = $pdo->query("
    SELECT a.*, u.name as admin_name 
    FROM audit_log a 
    LEFT JOIN admin_users u ON u.id = a.admin_id 
    ORDER BY a.created_at DESC 
    LIMIT 10
");
$recentActivity = $activityStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 260px;
            background: #0d2617;
            color: #fff;
            padding: 30px 20px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .admin-sidebar .logo {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 30px;
        }
        .admin-sidebar .logo i {
            font-size: 2.5rem;
            color: #FFD700;
        }
        .admin-sidebar .logo h2 {
            color: #fff;
            font-size: 1.2rem;
            margin-top: 10px;
        }
        .admin-sidebar .user {
            padding: 15px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .admin-sidebar .user .name {
            font-weight: 600;
        }
        .admin-sidebar .user .role {
            font-size: 0.8rem;
            opacity: 0.7;
        }
        .admin-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            border-radius: 8px;
            transition: all 0.3s;
            margin-bottom: 4px;
        }
        .admin-sidebar nav a:hover,
        .admin-sidebar nav a.active {
            background: rgba(255,215,0,0.1);
            color: #FFD700;
        }
        .admin-sidebar nav a i {
            width: 20px;
        }
        .admin-content {
            flex: 1;
            padding: 30px;
            background: #f5f5f5;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .admin-header h1 {
            color: #0d2617;
        }
        .stats-grid-admin {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #FFD700;
        }
        .stat-card .number {
            font-size: 2rem;
            font-weight: 800;
            color: #0d2617;
        }
        .stat-card .label {
            color: #666;
            font-size: 0.9rem;
        }
        .stat-card .icon {
            float: right;
            font-size: 2rem;
            opacity: 0.3;
        }
        .activity-log {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .activity-log h3 {
            margin-bottom: 20px;
            color: #0d2617;
        }
        .activity-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-item .action {
            font-weight: 500;
        }
        .activity-item .time {
            color: #999;
            font-size: 0.8rem;
        }
        .logout-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            width: 100%;
            font-size: 1rem;
            font-family: inherit;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: rgba(255,0,0,0.1);
            color: #ff6b6b;
        }
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 200px;
                padding: 20px 15px;
            }
            .stats-grid-admin {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .admin-wrapper {
                flex-direction: column;
            }
            .admin-sidebar {
                width: 100%;
                min-height: auto;
                height: auto;
                position: static;
            }
            .stats-grid-admin {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <h2>School Admin</h2>
        </div>
        <div class="user">
            <div class="name"><?= clean($_SESSION['admin_name']) ?></div>
            <div class="role"><?= clean($_SESSION['admin_role'] ?? 'Admin') ?></div>
        </div>
        <nav>
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage-news.php"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="upload-images.php"><i class="fas fa-images"></i> Hero Images</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top: 20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-home"></i> Dashboard</h1>
            <span>Welcome back, <?= clean($_SESSION['admin_name']) ?>!</span>
        </div>

        <!-- Stats -->
        <div class="stats-grid-admin">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-newspaper"></i></div>
                <div class="number"><?= $totalNews ?></div>
                <div class="label">Total News Articles</div>
                <div style="font-size:0.8rem;color:#4CAF50;"><?= $totalPublished ?> published</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-envelope"></i></div>
                <div class="number"><?= $totalMessages ?></div>
                <div class="label">Unread Messages</div>
                <div style="font-size:0.8rem;color:#FF6B6B;"><?= $totalMessages ?> need attention</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-question-circle"></i></div>
                <div class="number"><?= $totalEnquiries ?></div>
                <div class="label">New Enquiries</div>
                <div style="font-size:0.8rem;color:#FFD700;"><?= $totalEnquiries ?> pending</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div class="number"><?= $pdo->query("SELECT COUNT(*) FROM staff WHERE is_active = 1")->fetchColumn() ?></div>
                <div class="label">Active Staff</div>
                <div style="font-size:0.8rem;color:#4CAF50;"><?= $pdo->query("SELECT COUNT(*) FROM staff WHERE is_management = 1")->fetchColumn() ?> management</div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="activity-log">
            <h3><i class="fas fa-clock"></i> Recent Activity</h3>
            <?php if (!empty($recentActivity)): ?>
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <span>
                            <span class="action"><?= clean($activity['admin_name'] ?? 'System') ?></span>
                            <span style="color:#666;"><?= clean($activity['action']) ?></span>
                            <?php if ($activity['table_name']): ?>
                                <span style="color:#999;">on <?= clean($activity['table_name']) ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="time"><?= formatDate($activity['created_at'], 'M j, Y g:i A') ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#999;text-align:center;padding:20px;">No activity recorded yet.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>