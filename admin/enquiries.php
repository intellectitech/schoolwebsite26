<?php
// admin/enquiries.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Admissions Enquiries - Admin';
$message = '';
$error = '';

if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = clean($_GET['status']);
    $validStatuses = ['new', 'contacted', 'enrolled', 'declined'];
    
    if (in_array($status, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE admissions_enquiries SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $message = 'Enquiry status updated!';
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM admissions_enquiries WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Enquiry deleted.';
}

$enquiriesStmt = $pdo->query("
    SELECT * FROM admissions_enquiries 
    ORDER BY FIELD(status, 'new', 'contacted', 'enrolled', 'declined'), created_at DESC
");
$enquiries = $enquiriesStmt->fetchAll();
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0a0a0a; color: #fff; min-height: 100vh; }
        .admin-wrapper { display: flex; min-height: 100vh; }

        .admin-sidebar {
            width: 260px;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(40px);
            padding: 30px 20px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.06);
        }
        .admin-sidebar .logo { text-align: center; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 30px; }
        .admin-sidebar .logo .icon-wrapper { display: inline-block; width: 55px; height: 55px; background: linear-gradient(135deg, #FF6B00, #e85e00); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 10px 30px rgba(255,107,0,0.25); }
        .admin-sidebar .logo i { font-size: 2rem; color: #fff; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.1rem; font-weight: 700; }
        .admin-sidebar .user { padding: 15px; background: rgba(255,255,255,0.04); border-radius: 16px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.06); }
        .admin-sidebar .user .name { font-weight: 600; color: #fff; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.5; color: rgba(255,255,255,0.6); }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.5); border-radius: 14px; transition: all 0.3s ease; margin-bottom: 4px; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: rgba(255,107,0,0.12); color: #FF6B00; border: 1px solid rgba(255,107,0,0.1); transform: translateX(4px); }
        .admin-sidebar nav a i { width: 20px; color: rgba(255,255,255,0.3); transition: all 0.3s ease; }
        .admin-sidebar nav a:hover i, .admin-sidebar nav a.active i { color: #FF6B00; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.4); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 14px; transition: all 0.3s ease; margin-top: 10px; }
        .logout-btn:hover { background: rgba(255,0,0,0.08); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }

        .admin-content { flex: 1; padding: 30px; background: #0a0a0a; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; padding: 20px 30px; background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .admin-header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; }
        .admin-header h1 i { color: #FF6B00; margin-right: 10px; }

        .table-container { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border-radius: 20px; padding: 25px; overflow-x: auto; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; color: rgba(255,255,255,0.5); font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; color: rgba(255,255,255,0.8); }
        tr:hover { background: rgba(255,255,255,0.02); }
        tr.status-new { background: rgba(255,107,0,0.03); }

        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.new { background: rgba(255,107,0,0.15); color: #FF6B00; border: 1px solid rgba(255,107,0,0.1); }
        .status-badge.contacted { background: rgba(0,123,255,0.15); color: #4d9fff; border: 1px solid rgba(0,123,255,0.1); }
        .status-badge.enrolled { background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.1); }
        .status-badge.declined { background: rgba(255,0,0,0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }

        .btn-status { padding: 4px 10px; border: none; border-radius: 6px; font-size: 0.7rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; font-weight: 600; }
        .btn-status.new { background: rgba(255,107,0,0.15); color: #FF6B00; border: 1px solid rgba(255,107,0,0.1); }
        .btn-status.contacted { background: rgba(0,123,255,0.15); color: #4d9fff; border: 1px solid rgba(0,123,255,0.1); }
        .btn-status.enrolled { background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.1); }
        .btn-status.declined { background: rgba(255,0,0,0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }
        .btn-status:hover { opacity: 0.8; }

        .btn-delete { padding: 4px 10px; background: rgba(255,0,0,0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); border-radius: 6px; font-size: 0.7rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: rgba(255,0,0,0.25); color: #ff6b6b; }

        .actions { display: flex; gap: 4px; flex-wrap: wrap; }
        .no-data { text-align: center; padding: 60px 0; color: rgba(255,255,255,0.2); }
        .no-data i { font-size: 3rem; display: block; margin-bottom: 15px; color: rgba(255,255,255,0.05); }

        .stats-summary { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; }
        .stats-summary .stat { padding: 12px 24px; background: rgba(255,255,255,0.03); border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); }
        .stats-summary .stat .num { font-size: 1.5rem; font-weight: 800; color: #fff; }
        .stats-summary .stat .label { font-size: 0.8rem; color: rgba(255,255,255,0.4); }

        .alert-success { background: rgba(76,175,80,0.1); color: #4CAF50; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid rgba(76,175,80,0.1); }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .admin-header { flex-direction: column; align-items: stretch; } .stats-summary { flex-direction: column; } }
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
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage-news.php"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php" class="active"><i class="fas fa-question-circle"></i> Enquiries</a>
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
            <h1><i class="fas fa-question-circle"></i> Admissions Enquiries</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>

        <?php 
            $newCount = $pdo->query("SELECT COUNT(*) FROM admissions_enquiries WHERE status = 'new'")->fetchColumn();
            $totalCount = $pdo->query("SELECT COUNT(*) FROM admissions_enquiries")->fetchColumn();
        ?>
        <div class="stats-summary">
            <div class="stat">
                <div class="num"><?= $totalCount ?></div>
                <div class="label">Total Enquiries</div>
            </div>
            <div class="stat">
                <div class="num" style="color:#FF6B00;"><?= $newCount ?></div>
                <div class="label">New (Needs Attention)</div>
            </div>
            <div class="stat">
                <div class="num" style="color:#4CAF50;"><?= $pdo->query("SELECT COUNT(*) FROM admissions_enquiries WHERE status = 'enrolled'")->fetchColumn() ?></div>
                <div class="label">Enrolled</div>
            </div>
        </div>

        <div class="table-container">
            <?php if (!empty($enquiries)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Parent</th>
                            <th>Student</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enquiries as $enq): ?>
                            <tr class="status-<?= $enq['status'] ?>">
                                <td>
                                    <strong><?= clean($enq['parent_name']) ?></strong><br>
                                    <span style="font-size:0.8rem;color:rgba(255,255,255,0.4);"><i class="fas fa-phone"></i> <?= clean($enq['parent_phone']) ?></span>
                                    <?php if ($enq['parent_email']): ?>
                                        <br><span style="font-size:0.8rem;color:rgba(255,255,255,0.4);"><?= clean($enq['parent_email']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= clean($enq['student_name']) ?>
                                    <?php if ($enq['ple_aggregate']): ?>
                                        <br><span style="font-size:0.8rem;color:rgba(255,255,255,0.4);">PLE: <?= $enq['ple_aggregate'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span style="font-weight:600;color:#FF6B00;"><?= clean($enq['entry_level']) ?></span></td>
                                <td>
                                    <span class="status-badge <?= $enq['status'] ?>">
                                        <?= ucfirst($enq['status']) ?>
                                    </span>
                                    <?php if ($enq['current_school']): ?>
                                        <br><span style="font-size:0.7rem;color:rgba(255,255,255,0.3);"><?= clean($enq['current_school']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:0.85rem;color:rgba(255,255,255,0.4);">
                                    <?= formatDate($enq['created_at'], 'M j, Y') ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="?status=new&id=<?= $enq['id'] ?>" class="btn-status new">New</a>
                                        <a href="?status=contacted&id=<?= $enq['id'] ?>" class="btn-status contacted">Contacted</a>
                                        <a href="?status=enrolled&id=<?= $enq['id'] ?>" class="btn-status enrolled">Enrolled</a>
                                        <a href="?status=declined&id=<?= $enq['id'] ?>" class="btn-status declined">Declined</a>
                                        <a href="?delete=<?= $enq['id'] ?>" class="btn-delete" onclick="return confirm('Delete this enquiry?')"><i class="fas fa-trash"></i></a>
                                    </div>
                                    <?php if ($enq['message']): ?>
                                        <div style="font-size:0.75rem;color:rgba(255,255,255,0.3);margin-top:5px;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            "<?= clean($enq['message']) ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-inbox"></i>
                    <p>No enquiries yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>