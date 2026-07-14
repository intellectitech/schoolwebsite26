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

// Update status
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

// Delete enquiry
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM admissions_enquiries WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Enquiry deleted.';
}

// Fetch enquiries
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
        /* Same admin styles */
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #0d2617; color: #fff; padding: 30px 20px; min-height: 100vh; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .admin-sidebar .logo { text-align: center; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 30px; }
        .admin-sidebar .logo i { font-size: 2.5rem; color: #FFD700; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.2rem; margin-top: 10px; }
        .admin-sidebar .user { padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .admin-sidebar .user .name { font-weight: 600; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.7; }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.7); border-radius: 8px; transition: all 0.3s; margin-bottom: 4px; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: rgba(255,215,0,0.1); color: #FFD700; }
        .admin-sidebar nav a i { width: 20px; }
        .admin-content { flex: 1; padding: 30px; background: #f5f5f5; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .admin-header h1 { color: #0d2617; }
        .table-container { background: #fff; border-radius: 12px; padding: 20px; overflow-x: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; background: #f8f9fa; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0; }
        td { padding: 12px 15px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }
        tr.status-new { background: #fff8e1; }
        .status-badge { display: inline-block; padding: 4px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.new { background: #fff3cd; color: #856404; }
        .status-badge.contacted { background: #cce5ff; color: #004085; }
        .status-badge.enrolled { background: #d4edda; color: #155724; }
        .status-badge.declined { background: #f8d7da; color: #721c24; }
        .btn-status { padding: 4px 10px; border: none; border-radius: 4px; font-size: 0.75rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-status.new { background: #ffc107; color: #333; }
        .btn-status.contacted { background: #007bff; color: #fff; }
        .btn-status.enrolled { background: #28a745; color: #fff; }
        .btn-status.declined { background: #dc3545; color: #fff; }
        .btn-delete { padding: 4px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 0.75rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: #c82333; color: #fff; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .actions { display: flex; gap: 4px; flex-wrap: wrap; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,0,0,0.1); color: #ff6b6b; }
        .stats-summary { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; }
        .stats-summary .stat { padding: 10px 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .stats-summary .stat .num { font-size: 1.5rem; font-weight: 800; color: #1a4d2e; }
        .stats-summary .stat .label { font-size: 0.85rem; color: #666; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } }
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
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage-news.php"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php" class="active"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="upload-images.php"><i class="fas fa-images"></i> Hero Images</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
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
                <div class="num" style="color:#ffc107;"><?= $newCount ?></div>
                <div class="label">New (Needs Attention)</div>
            </div>
            <div class="stat">
                <div class="num" style="color:#28a745;"><?= $pdo->query("SELECT COUNT(*) FROM admissions_enquiries WHERE status = 'enrolled'")->fetchColumn() ?></div>
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
                                    <span style="font-size:0.8rem;color:#666;"><i class="fas fa-phone"></i> <?= clean($enq['parent_phone']) ?></span>
                                    <?php if ($enq['parent_email']): ?>
                                        <br><span style="font-size:0.8rem;color:#666;"><?= clean($enq['parent_email']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= clean($enq['student_name']) ?>
                                    <?php if ($enq['ple_aggregate']): ?>
                                        <br><span style="font-size:0.8rem;color:#666;">PLE: <?= $enq['ple_aggregate'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span style="font-weight:600;"><?= clean($enq['entry_level']) ?></span></td>
                                <td>
                                    <span class="status-badge <?= $enq['status'] ?>">
                                        <?= ucfirst($enq['status']) ?>
                                    </span>
                                    <?php if ($enq['current_school']): ?>
                                        <br><span style="font-size:0.7rem;color:#666;"><?= clean($enq['current_school']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:0.85rem;color:#666;">
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
                                        <div style="font-size:0.75rem;color:#666;margin-top:5px;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            "<?= clean($enq['message']) ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center;padding:40px 0;">
                    <i class="fas fa-inbox" style="font-size:3rem;color:#ccc;margin-bottom:15px;"></i>
                    <p style="color:#666;">No enquiries yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>