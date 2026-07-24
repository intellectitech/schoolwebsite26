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
$totalStaff = $pdo->query("SELECT COUNT(*) FROM staff WHERE is_active = 1")->fetchColumn();
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events WHERE is_published = 1 AND event_date >= CURDATE()")->fetchColumn();

// Recent activity
$activityStmt = $pdo->query("
    SELECT a.*, u.name as admin_name 
    FROM audit_log a 
    LEFT JOIN admin_users u ON u.id = a.admin_id 
    ORDER BY a.created_at DESC 
    LIMIT 10
");
$recentActivity = $activityStmt->fetchAll();

// Recent enquiries
$recentEnquiries = $pdo->query("
    SELECT * FROM admissions_enquiries 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();

// Recent messages
$recentMessages = $pdo->query("
    SELECT * FROM contact_messages 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();
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
        /* Admin Wrapper */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: #0d2617;
            color: #fff;
            padding: 30px 20px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
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
            text-decoration: none;
        }

        .admin-sidebar nav a:hover,
        .admin-sidebar nav a.active {
            background: rgba(255,215,0,0.1);
            color: #FFD700;
        }

        .admin-sidebar nav a i {
            width: 20px;
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

        /* Content */
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
            flex-wrap: wrap;
            gap: 15px;
        }

        .admin-header h1 {
            color: #0d2617;
        }

        /* Stats Grid */
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
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
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

        /* Activity Log */
        .activity-log {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
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

        /* Recent Tables */
        .recent-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .recent-box {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .recent-box h3 {
            margin-bottom: 20px;
            color: #0d2617;
        }

        .recent-box table {
            width: 100%;
            font-size: 0.9rem;
        }

        .recent-box td {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .recent-box tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-badge.new {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.contacted {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.enrolled {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.declined {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.read {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.unread {
            background: #fff3cd;
            color: #856404;
        }

        .text-muted {
            color: #999;
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stats-grid-admin {
                grid-template-columns: repeat(2, 1fr);
            }
            .recent-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                width: 200px;
                padding: 20px 15px;
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
            .admin-header {
                flex-direction: column;
                align-items: stretch;
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
            <h2>Mbogo High School</h2>
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
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
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
                <div class="number"><?= $totalStaff ?></div>
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

        <!-- Recent Enquiries & Messages -->
        <div class="recent-grid">
            <!-- Recent Enquiries -->
            <div class="recent-box">
                <h3><i class="fas fa-question-circle"></i> Recent Enquiries</h3>
                <?php if (!empty($recentEnquiries)): ?>
                    <table>
                        <?php foreach ($recentEnquiries as $enquiry): ?>
                            <tr>
                                <td>
                                    <strong><?= clean($enquiry['student_name']) ?></strong><br>
                                    <span class="text-muted"><?= clean($enquiry['parent_name']) ?></span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $enquiry['status'] ?>">
                                        <?= ucfirst($enquiry['status']) ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?= formatDate($enquiry['created_at'], 'M j') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p style="color:#999;text-align:center;padding:20px;">No enquiries yet.</p>
                <?php endif; ?>
            </div>

            <!-- Recent Messages -->
            <div class="recent-box">
                <h3><i class="fas fa-envelope"></i> Recent Messages</h3>
                <?php if (!empty($recentMessages)): ?>
                    <table>
                        <?php foreach ($recentMessages as $msg): ?>
                            <tr>
                                <td>
                                    <strong><?= clean($msg['name']) ?></strong><br>
                                    <span class="text-muted"><?= clean($msg['subject']) ?></span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $msg['is_read'] ? 'read' : 'unread' ?>">
                                        <?= $msg['is_read'] ? 'Read' : 'Unread' ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?= formatDate($msg['created_at'], 'M j') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p style="color:#999;text-align:center;padding:20px;">No messages yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>