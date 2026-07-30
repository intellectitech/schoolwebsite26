<?php
// admin/messages.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Messages - Admin';
$message = '';
$error = '';

if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $id = (int)$_GET['read'];
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1, replied_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Message marked as read.';
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Message deleted.';
}

$messagesStmt = $pdo->query("
    SELECT * FROM contact_messages 
    ORDER BY is_read ASC, created_at DESC
");
$messages = $messagesStmt->fetchAll();
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
        .admin-sidebar .logo { text-align: center; padding-bottom: 30px; border-bottom: 2px solid rgba(0,200,83,0.2); margin-bottom: 30px; }
        .admin-sidebar .logo .icon-wrapper { display: inline-block; width: 55px; height: 55px; background: linear-gradient(135deg, #009624, #00C853); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 10px 30px rgba(0,200,83,0.25); }
        .admin-sidebar .logo i { font-size: 2rem; color: #fff; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.1rem; font-weight: 700; }
        .admin-sidebar .user { padding: 15px; background: rgba(255,255,255,0.05); border-radius: 16px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
        .admin-sidebar .user .name { font-weight: 600; color: #00C853; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.5; color: rgba(255,255,255,0.6); }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.5); border-radius: 14px; transition: all 0.3s ease; margin-bottom: 4px; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: rgba(0,200,83,0.12); color: #00C853; border: 1px solid rgba(0,200,83,0.1); transform: translateX(4px); }
        .admin-sidebar nav a i { width: 20px; color: rgba(255,255,255,0.3); transition: all 0.3s ease; }
        .admin-sidebar nav a:hover i, .admin-sidebar nav a.active i { color: #00C853; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.4); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 14px; transition: all 0.3s ease; margin-top: 10px; }
        .logout-btn:hover { background: rgba(255,0,0,0.08); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }

        .admin-content { flex: 1; padding: 30px; background: #f5e6d3; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; padding: 20px 30px; background: #fff; border-radius: 20px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); border-left: 4px solid #00C853; }
        .admin-header h1 { color: #0a0a0a; font-size: 1.6rem; font-weight: 700; }
        .admin-header h1 i { color: #00C853; margin-right: 10px; }
        .admin-header .badge-count { background: rgba(255,107,107,0.15); color: #FF6B6B; padding: 6px 16px; border-radius: 50px; font-size: 0.85rem; border: 1px solid rgba(255,107,107,0.1); }

        .btn-read { padding: 6px 14px; background: rgba(0,200,83,0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-read:hover { background: rgba(0,200,83,0.25); color: #00C853; }

        .btn-delete { padding: 6px 14px; background: rgba(255,0,0,0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: rgba(255,0,0,0.25); color: #ff6b6b; }

        .table-container { background: #fff; border-radius: 20px; padding: 25px; overflow-x: auto; box-shadow: 0 5px 30px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; color: #666; font-weight: 600; border-bottom: 2px solid #e0e0e0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }
        tr.unread { background: rgba(255,107,107,0.03); }

        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.read { background: rgba(0,200,83,0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); }
        .status-badge.unread { background: rgba(255,107,107,0.15); color: #FF6B6B; border: 1px solid rgba(255,107,107,0.1); }

        .message-preview { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #999; }

        .actions { display: flex; gap: 6px; flex-wrap: wrap; }

        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #c3e6cb; }

        .no-data { text-align: center; padding: 60px 0; color: #999; }
        .no-data i { font-size: 3rem; display: block; margin-bottom: 15px; color: #ccc; }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .admin-header { flex-direction: column; align-items: stretch; } }
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
            <a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a>
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
            <h1><i class="fas fa-envelope"></i> Contact Messages</h1>
            <span class="badge-count">
                <i class="fas fa-circle" style="color:#FF6B6B;font-size:0.5rem;"></i> 
                <?= $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn() ?> unread
            </span>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (!empty($messages)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="<?= $msg['is_read'] ? '' : 'unread' ?>">
                                <td>
                                    <strong><?= clean($msg['name']) ?></strong><br>
                                    <span style="font-size:0.8rem;color:#999;"><?= clean($msg['email']) ?></span>
                                    <?php if ($msg['phone']): ?>
                                        <br><span style="font-size:0.8rem;color:#999;"><i class="fas fa-phone"></i> <?= clean($msg['phone']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= clean($msg['subject']) ?></td>
                                <td class="message-preview"><?= clean($msg['message']) ?></td>
                                <td>
                                    <span class="status-badge <?= $msg['is_read'] ? 'read' : 'unread' ?>">
                                        <?= $msg['is_read'] ? 'Read' : 'Unread' ?>
                                    </span>
                                </td>
                                <td style="font-size:0.85rem;color:#999;">
                                    <?= formatDate($msg['created_at'], 'M j, Y g:i A') ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <?php if (!$msg['is_read']): ?>
                                            <a href="?read=<?= $msg['id'] ?>" class="btn-read"><i class="fas fa-check"></i> Read</a>
                                        <?php endif; ?>
                                        <a href="?delete=<?= $msg['id'] ?>" class="btn-delete" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-envelope-open"></i>
                    <p>No messages yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>