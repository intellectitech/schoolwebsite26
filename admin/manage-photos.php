<?php
// admin/manage-photos.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Manage Photos - Admin';
$message = '';
$error = '';

$albumId = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;

if (!$albumId) {
    header('Location: manage-gallery.php');
    exit;
}

// Fetch album info
$albumStmt = $pdo->prepare("SELECT * FROM gallery_albums WHERE id = ?");
$albumStmt->execute([$albumId]);
$album = $albumStmt->fetch();

if (!$album) {
    header('Location: manage-gallery.php');
    exit;
}

// Handle photo delete
if (isset($_GET['delete_photo']) && is_numeric($_GET['delete_photo'])) {
    $id = (int)$_GET['delete_photo'];
    try {
        $stmt = $pdo->prepare("DELETE FROM gallery_photos WHERE id = ?");
        $stmt->execute([$id]);
        
        $logStmt = $pdo->prepare("
            INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
            VALUES (?, 'deleted_photo', 'gallery_photos', ?, 'Deleted photo from album: ' . ?, ?)
        ");
        $logStmt->execute([$_SESSION['admin_id'], $id, $album['name'], $_SERVER['REMOTE_ADDR']]);
        
        $message = 'Photo deleted successfully!';
    } catch (Exception $e) {
        $error = 'Error deleting photo.';
    }
}

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo'])) {
    $image_path = clean($_POST['image_path'] ?? '');
    $caption = clean($_POST['caption'] ?? '');
    $sort_order = (int)$_POST['sort_order'];
    
    if (empty($image_path)) {
        $error = 'Image path is required.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO gallery_photos (album_id, image_path, caption, sort_order) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$albumId, $image_path, $caption, $sort_order]);
            
            $logStmt = $pdo->prepare("
                INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
                VALUES (?, 'added_photo', 'gallery_photos', ?, 'Added photo to album: ' . ?, ?)
            ");
            $logStmt->execute([$_SESSION['admin_id'], $pdo->lastInsertId(), $album['name'], $_SERVER['REMOTE_ADDR']]);
            
            $message = 'Photo added successfully!';
        } catch (Exception $e) {
            $error = 'Error adding photo: ' . $e->getMessage();
        }
    }
}

// Fetch photos
$photosStmt = $pdo->prepare("
    SELECT * FROM gallery_photos 
    WHERE album_id = ? 
    ORDER BY sort_order, created_at
");
$photosStmt->execute([$albumId]);
$photos = $photosStmt->fetchAll();
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
        .btn-back { padding: 12px 24px; background: #6c757d; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-back:hover { background: #5a6268; color: #fff; }
        .btn-delete { padding: 6px 14px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: #c82333; color: #fff; }
        .form-container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px 14px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #1a4d2e; }
        .form-group textarea { min-height: 60px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn-submit { padding: 12px 30px; background: #1a4d2e; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-submit:hover { background: #2d7a4a; }
        .photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .photo-item { position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); background: #fff; }
        .photo-item img { width: 100%; height: 180px; object-fit: cover; }
        .photo-item .info { padding: 12px; }
        .photo-item .info .caption { font-size: 0.85rem; color: #333; margin-bottom: 5px; }
        .photo-item .info .order { font-size: 0.75rem; color: #999; }
        .photo-item .delete-btn { position: absolute; top: 5px; right: 5px; background: rgba(220,53,69,0.9); color: #fff; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s; }
        .photo-item .delete-btn:hover { transform: scale(1.1); background: #dc3545; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,0,0,0.1); color: #ff6b6b; }
        .no-photos { text-align: center; padding: 40px; color: #999; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
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
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="manage-gallery.php" class="active"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-images"></i> <?= clean($album['name']) ?> - Photos</h1>
            <a href="manage-gallery.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Albums</a>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <!-- Add Photo Form -->
        <div class="form-container">
            <h3 style="color:#1a4d2e;margin-bottom:20px;"><i class="fas fa-plus-circle"></i> Add Photo</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="image_path">Image URL <span class="required">*</span></label>
                    <input type="text" id="image_path" name="image_path" required placeholder="assets/images/gallery/photo.jpg">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="caption">Caption</label>
                        <input type="text" id="caption" name="caption" placeholder="Brief caption for this photo">
                    </div>
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="0" min="0">
                    </div>
                </div>
                <button type="submit" name="add_photo" class="btn-submit"><i class="fas fa-plus"></i> Add Photo</button>
            </form>
        </div>

        <!-- Photos Grid -->
        <h3 style="color:#1a4d2e;margin-bottom:15px;">
            <i class="fas fa-camera"></i> Photos (<?= count($photos) ?>)
        </h3>
        <?php if (!empty($photos)): ?>
            <div class="photo-grid">
                <?php foreach ($photos as $photo): ?>
                    <div class="photo-item">
                        <img src="../<?= clean($photo['image_path']) ?>" alt="<?= clean($photo['caption'] ?? 'Photo') ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22180%22><rect width=%22200%22 height=%22180%22 fill=%22%23e9ecef%22/><text x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2214%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22>Image not found</text></svg>'">
                        <a href="?delete_photo=<?= $photo['id'] ?>&album_id=<?= $albumId ?>" class="delete-btn" onclick="return confirm('Delete this photo?')">×</a>
                        <div class="info">
                            <div class="caption"><?= clean($photo['caption'] ?? 'Untitled') ?></div>
                            <div class="order">Order: <?= $photo['sort_order'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-photos">
                <i class="fas fa-image" style="font-size:3rem;display:block;margin-bottom:15px;color:#ccc;"></i>
                <p>No photos in this album yet.</p>
            </div>
        <?php endif; ?>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>