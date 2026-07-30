<?php
// admin/dashboard.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Dashboard - Admin';

$totalNews = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalPublished = $pdo->query("SELECT COUNT(*) FROM news WHERE is_published = 1")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$totalEnquiries = $pdo->query("SELECT COUNT(*) FROM admissions_enquiries WHERE status = 'new'")->fetchColumn();
$totalStaff = $pdo->query("SELECT COUNT(*) FROM staff WHERE is_active = 1")->fetchColumn();

$activityStmt = $pdo->query("
    SELECT a.*, u.name as admin_name 
    FROM audit_log a 
    LEFT JOIN admin_users u ON u.id = a.admin_id 
    ORDER BY a.created_at DESC 
    LIMIT 10
");
$recentActivity = $activityStmt->fetchAll();

$recentEnquiries = $pdo->query("
    SELECT * FROM admissions_enquiries 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();

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
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5e6d3; color: #1a1a1a; min-height: 100vh; }
        
        .admin-wrapper { display: flex; min-height: 100vh; }

        /* Sidebar - Matching Website Theme */
        .admin-sidebar {
            width: 260px;
            background: #0a0a0a;
            color: #fff;
            padding: 30px 20px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            border-right: 2px solid #00C853;
        }

        .admin-sidebar .logo {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid rgba(0, 200, 83, 0.2);
            margin-bottom: 30px;
        }

        .admin-sidebar .logo .icon-wrapper {
            display: inline-block;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #009624, #00C853);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 10px 30px rgba(0, 200, 83, 0.25);
        }

        .admin-sidebar .logo i { font-size: 2rem; color: #fff; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.1rem; font-weight: 700; }

        .admin-sidebar .user {
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .admin-sidebar .user .name { font-weight: 600; color: #00C853; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.5; color: rgba(255,255,255,0.6); }

        .admin-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.5);
            border-radius: 14px;
            transition: all 0.3s ease;
            margin-bottom: 4px;
            text-decoration: none;
        }

        .admin-sidebar nav a:hover,
        .admin-sidebar nav a.active {
            background: rgba(0, 200, 83, 0.12);
            color: #00C853;
            border: 1px solid rgba(0, 200, 83, 0.1);
            transform: translateX(4px);
        }

        .admin-sidebar nav a i { width: 20px; color: rgba(255,255,255,0.3); transition: all 0.3s ease; }
        .admin-sidebar nav a:hover i, .admin-sidebar nav a.active i { color: #00C853; }

        .logout-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            width: 100%;
            font-size: 1rem;
            font-family: inherit;
            border-radius: 14px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .logout-btn:hover {
            background: rgba(255, 0, 0, 0.08);
            color: #ff6b6b;
            border: 1px solid rgba(255, 0, 0, 0.1);
        }

        .logout-btn i { color: rgba(255,255,255,0.3); }
        .logout-btn:hover i { color: #ff6b6b; }

        /* Content */
        .admin-content { flex: 1; padding: 30px; background: #f5e6d3; }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
            padding: 20px 30px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.05);
            border-left: 4px solid #00C853;
        }

        .admin-header h1 { color: #0a0a0a; font-size: 1.6rem; font-weight: 700; }
        .admin-header h1 i { color: #00C853; margin-right: 10px; }
        .admin-header span { color: #8D6E63; font-weight: 400; }

        /* Stats */
        .stats-grid-admin {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.05);
            border-left: 4px solid #00C853;
            transition: all 0.3s;
        }

        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 50px rgba(0,0,0,0.08); }
        .stat-card .number { font-size: 2rem; font-weight: 800; color: #0a0a0a; }
        .stat-card .label { color: #8D6E63; font-size: 0.9rem; }
        .stat-card .icon { float: right; font-size: 2rem; opacity: 0.15; color: #00C853; }
        .stat-card .sub-info { font-size: 0.8rem; margin-top: 5px; }
        .stat-card .sub-info.published { color: #00C853; }
        .stat-card .sub-info.pending { color: #FF6B6B; }

        /* Activity Log */
        .activity-log {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .activity-log h3 { margin-bottom: 20px; color: #0a0a0a; display: flex; align-items: center; gap: 10px; }
        .activity-log h3 i { color: #00C853; }

        .activity-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .activity-item:last-child { border-bottom: none; }
        .activity-item .action { font-weight: 500; color: #0a0a0a; }
        .activity-item .action .admin-name { color: #00C853; }
        .activity-item .time { color: #999; font-size: 0.8rem; }

        /* Recent */
        .recent-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .recent-box { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); }
        .recent-box h3 { margin-bottom: 20px; color: #0a0a0a; display: flex; align-items: center; gap: 10px; }
        .recent-box h3 i { color: #00C853; }
        .recent-box table { width: 100%; font-size: 0.9rem; }
        .recent-box td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .recent-box tr:last-child td { border-bottom: none; }
        .recent-box .text-muted { color: #999; font-size: 0.85rem; }

        .status-badge {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-badge.new { background: rgba(255, 107, 107, 0.15); color: #FF6B6B; border: 1px solid rgba(255,107,107,0.1); }
        .status-badge.contacted { background: rgba(0, 123, 255, 0.15); color: #4d9fff; border: 1px solid rgba(0,123,255,0.1); }
        .status-badge.enrolled { background: rgba(0, 200, 83, 0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); }
        .status-badge.declined { background: rgba(255, 0, 0, 0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }
        .status-badge.read { background: rgba(0, 200, 83, 0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); }
        .status-badge.unread { background: rgba(255, 107, 107, 0.15); color: #FF6B00; border: 1px solid rgba(255,107,0,0.1); }

        .no-data { color: #999; text-align: center; padding: 30px 0; font-size: 0.95rem; }

        @media (max-width: 992px) {
            .stats-grid-admin { grid-template-columns: repeat(2, 1fr); }
            .recent-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .admin-sidebar { width: 200px; padding: 20px 15px; }
            .admin-header { flex-direction: column; align-items: stretch; text-align: center; padding: 20px; }
        }
        @media (max-width: 480px) {
            .admin-wrapper { flex-direction: column; }
            .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; border-right: none; border-bottom: 2px solid #00C853; }
            .stats-grid-admin { grid-template-columns: 1fr; }
            .admin-header { flex-direction: column; align-items: stretch; text-align: center; padding: 15px; }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="logo">
            <div class="icon-wrapper"><i class="fas fa-graduation-cap"></i></div>
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
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-home"></i> Dashboard</h1>
            <span>Welcome back, <?= clean($_SESSION['admin_name']) ?>!</span>
        </div>

        <div class="stats-grid-admin">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-newspaper"></i></div>
                <div class="number"><?= $totalNews ?></div>
                <div class="label">Total News Articles</div>
                <div class="sub-info published"><?= $totalPublished ?> published</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-envelope"></i></div>
                <div class="number"><?= $totalMessages ?></div>
                <div class="label">Unread Messages</div>
                <div class="sub-info pending"><?= $totalMessages ?> need attention</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-question-circle"></i></div>
                <div class="number"><?= $totalEnquiries ?></div>
                <div class="label">New Enquiries</div>
                <div class="sub-info pending"><?= $totalEnquiries ?> pending</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div class="number"><?= $totalStaff ?></div>
                <div class="label">Active Staff</div>
                <div class="sub-info published"><?= $pdo->query("SELECT COUNT(*) FROM staff WHERE is_management = 1")->fetchColumn() ?> management</div>
            </div>
        </div>

        <div class="activity-log">
            <h3><i class="fas fa-clock"></i> Recent Activity</h3>
            <?php if (!empty($recentActivity)): ?>
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <span>
                            <span class="action">
                                <span class="admin-name"><?= clean($activity['admin_name'] ?? 'System') ?></span>
                                <span style="color:#666;"><?= clean($activity['action']) ?></span>
                            </span>
                            <?php if ($activity['table_name']): ?>
                                <span style="color:#999;font-size:0.8rem;">on <?= clean($activity['table_name']) ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="time"><?= formatDate($activity['created_at'], 'M j, Y g:i A') ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">No activity recorded yet.</div>
            <?php endif; ?>
        </div>

        <div class="recent-grid">
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
                                <td><span class="status-badge <?= $enquiry['status'] ?>"><?= ucfirst($enquiry['status']) ?></span></td>
                                <td class="text-muted"><?= formatDate($enquiry['created_at'], 'M j') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <div class="no-data">No enquiries yet.</div>
                <?php endif; ?>
            </div>

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
                                <td><span class="status-badge <?= $msg['is_read'] ? 'read' : 'unread' ?>"><?= $msg['is_read'] ? 'Read' : 'Unread' ?></span></td>
                                <td class="text-muted"><?= formatDate($msg['created_at'], 'M j') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <div class="no-data">No messages yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>