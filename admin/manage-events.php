<?php
// admin/manage-events.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Manage Events - Admin';
$message = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        
        $logStmt = $pdo->prepare("
            INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
            VALUES (?, 'deleted_event', 'events', ?, 'Deleted event', ?)
        ");
        $logStmt->execute([$_SESSION['admin_id'], $id, $_SERVER['REMOTE_ADDR']]);
        
        $message = 'Event deleted successfully!';
    } catch (Exception $e) {
        $error = 'Error deleting event.';
    }
}

// Handle toggle publish
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $stmt = $pdo->prepare("UPDATE events SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        
        $logStmt = $pdo->prepare("
            INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
            VALUES (?, 'toggled_event_status', 'events', ?, 'Toggled event publish status', ?)
        ");
        $logStmt->execute([$_SESSION['admin_id'], $id, $_SERVER['REMOTE_ADDR']]);
        
        $message = 'Event status updated!';
    } catch (Exception $e) {
        $error = 'Error updating status.';
    }
}

// Fetch all events
$eventsStmt = $pdo->query("
    SELECT * FROM events 
    ORDER BY event_date DESC, created_at DESC
");
$events = $eventsStmt->fetchAll();
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
        /* Same admin styles as manage-news.php */
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
        .btn-add { padding: 12px 24px; background: #1a4d2e; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-add:hover { background: #2d7a4a; color: #fff; }
        .btn-edit { padding: 6px 14px; background: #007bff; color: #fff; border: none; border-radius: 4px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-edit:hover { background: #0056b3; color: #fff; }
        .btn-delete { padding: 6px 14px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: #c82333; color: #fff; }
        .btn-toggle { padding: 6px 14px; border: none; border-radius: 4px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-toggle.published { background: #28a745; color: #fff; }
        .btn-toggle.published:hover { background: #218838; }
        .btn-toggle.draft { background: #ffc107; color: #333; }
        .btn-toggle.draft:hover { background: #e0a800; }
        .table-container { background: #fff; border-radius: 12px; padding: 20px; overflow-x: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; background: #f8f9fa; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0; }
        td { padding: 12px 15px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }
        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.published { background: #d4edda; color: #155724; }
        .status-badge.draft { background: #fff3cd; color: #856404; }
        .status-badge.upcoming { background: #cce5ff; color: #004085; }
        .status-badge.past { background: #e9ecef; color: #6c757d; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,0,0,0.1); color: #ff6b6b; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .admin-header { flex-direction: column; align-items: stretch; } }
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
            <a href="manage-events.php" class="active"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="upload-images.php"><i class="fas fa-images"></i> Hero Images</a>
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-calendar"></i> Manage Events</h1>
            <a href="add-event.php" class="btn-add"><i class="fas fa-plus"></i> Add Event</a>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (!empty($events)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): 
                            $eventDate = strtotime($event['event_date']);
                            $today = strtotime(date('Y-m-d'));
                            $status = $eventDate >= $today ? 'upcoming' : 'past';
                        ?>
                            <tr>
                                <td><?= $event['id'] ?></td>
                                <td><strong><?= clean($event['title']) ?></strong></td>
                                <td><?= formatDate($event['event_date']) ?></td>
                                <td><?= clean($event['location'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="status-badge <?= $status ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                    <span class="status-badge <?= $event['is_published'] ? 'published' : 'draft' ?>">
                                        <?= $event['is_published'] ? 'Live' : 'Draft' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="edit-event.php?id=<?= $event['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?toggle=<?= $event['id'] ?>" class="btn-toggle <?= $event['is_published'] ? 'published' : 'draft' ?>" onclick="return confirm('Toggle publish status?')">
                                            <?= $event['is_published'] ? '<i class="fas fa-eye"></i> Hide' : '<i class="fas fa-eye-slash"></i> Show' ?>
                                        </a>
                                        <a href="?delete=<?= $event['id'] ?>" class="btn-delete" onclick="return confirm('Delete this event?')"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center;padding:40px 0;">
                    <i class="fas fa-calendar" style="font-size:3rem;color:#ccc;margin-bottom:15px;"></i>
                    <p style="color:#666;">No events yet. <a href="add-event.php" style="color:#1a4d2e;font-weight:600;">Create your first event</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>