<?php
// admin/edit-event.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Edit Event - Admin';
$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: manage-events.php');
    exit;
}

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: manage-events.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean($_POST['title'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $location = clean($_POST['location'] ?? '');
    $event_date = clean($_POST['event_date'] ?? '');
    $start_time = clean($_POST['start_time'] ?? '');
    $end_time = clean($_POST['end_time'] ?? '');
    $featured_image = clean($_POST['featured_image'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    if (empty($title) || empty($event_date)) {
        $error = 'Title and event date are required.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE events SET 
                    title = ?, description = ?, location = ?, event_date = ?, 
                    start_time = ?, end_time = ?, featured_image = ?, is_published = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $title, $description, $location, $event_date, $start_time, $end_time,
                $featured_image, $is_published, $id
            ]);
            
            $logStmt = $pdo->prepare("
                INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
                VALUES (?, 'updated_event', 'events', ?, 'Updated event: ' . ?, ?)
            ");
            $logStmt->execute([$_SESSION['admin_id'], $id, $title, $_SERVER['REMOTE_ADDR']]);
            
            $success = 'Event updated successfully!';
            
            // Refresh event data
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch();
        } catch (Exception $e) {
            $error = 'Error updating event: ' . $e->getMessage();
        }
    }
}
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
        /* Same styles as add-event.php */
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
        .form-container { background: #fff; padding: 40px; border-radius: 12px; max-width: 800px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem; }
        .form-group label .required { color: #dc3545; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #1a4d2e; }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group.checkbox { display: flex; align-items: center; gap: 10px; }
        .form-group.checkbox label { margin-bottom: 0; cursor: pointer; }
        .form-group.checkbox input { width: auto; padding: 0; }
        .btn-submit { padding: 14px 40px; background: linear-gradient(135deg, #FFD700, #f5c842); color: #1a4d2e; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,215,0,0.4); }
        .btn-back { padding: 14px 24px; background: #6c757d; color: #fff; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-back:hover { background: #5a6268; color: #fff; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,0,0,0.1); color: #ff6b6b; }
        .button-group { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 10px; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .form-container { padding: 20px; } }
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
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-edit"></i> Edit Event</h1>
            <a href="manage-events.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Events</a>
        </div>

        <?php if ($success): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Event Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" required placeholder="Event title" value="<?= clean($event['title']) ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Event description"><?= clean($event['description']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="event_date">Event Date <span class="required">*</span></label>
                        <input type="date" id="event_date" name="event_date" required value="<?= $event['event_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="School Grounds, Main Hall, etc." value="<?= clean($event['location']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" id="start_time" name="start_time" value="<?= $event['start_time'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" id="end_time" name="end_time" value="<?= $event['end_time'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="featured_image">Featured Image URL</label>
                    <input type="text" id="featured_image" name="featured_image" placeholder="assets/images/events/event.jpg" value="<?= clean($event['featured_image']) ?>">
                </div>

                <div class="form-group checkbox">
                    <input type="checkbox" id="is_published" name="is_published" <?= $event['is_published'] ? 'checked' : '' ?>>
                    <label for="is_published">Published</label>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Update Event</button>
                    <a href="manage-events.php" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>