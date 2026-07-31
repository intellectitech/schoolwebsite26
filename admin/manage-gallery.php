<?php
// admin/manage-gallery.php - WITH PHOTO UPLOAD FOR EACH ALBUM
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/upload.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Manage Gallery - Admin';
$message = '';
$error = '';

// Handle album delete
if (isset($_GET['delete_album']) && is_numeric($_GET['delete_album'])) {
    $id = (int)$_GET['delete_album'];
    try {
        // Delete all photos from the album
        $photos = $pdo->prepare("SELECT image_path FROM gallery_photos WHERE album_id = ?");
        $photos->execute([$id]);
        foreach ($photos->fetchAll() as $photo) {
            deleteImage($photo['image_path']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM gallery_albums WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Album deleted successfully!';
    } catch (Exception $e) {
        $error = 'Error deleting album.';
    }
}

// Handle photo delete
if (isset($_GET['delete_photo']) && is_numeric($_GET['delete_photo'])) {
    $id = (int)$_GET['delete_photo'];
    try {
        $photo = $pdo->prepare("SELECT image_path FROM gallery_photos WHERE id = ?");
        $photo->execute([$id]);
        $photoData = $photo->fetch();
        if ($photoData) {
            deleteImage($photoData['image_path']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM gallery_photos WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Photo deleted successfully!';
    } catch (Exception $e) {
        $error = 'Error deleting photo.';
    }
}

// Handle toggle album publish
if (isset($_GET['toggle_album']) && is_numeric($_GET['toggle_album'])) {
    $id = (int)$_GET['toggle_album'];
    try {
        $stmt = $pdo->prepare("UPDATE gallery_albums SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Album status updated!';
    } catch (Exception $e) {
        $error = 'Error updating status.';
    }
}

// Handle photo upload for album
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo'])) {
    $album_id = (int)$_POST['album_id'];
    $caption = clean($_POST['caption'] ?? '');
    $sort_order = (int)$_POST['sort_order'];
    $uploadedImage = '';
    
    // Check if image was uploaded
    if (isset($_FILES['photo_image']) && $_FILES['photo_image']['error'] === UPLOAD_ERR_OK) {
        $result = uploadImage($_FILES['photo_image'], 'gallery', 5242880);
        if ($result['success']) {
            $uploadedImage = $result['path'];
        } else {
            $error = 'Image upload failed: ' . $result['error'];
        }
    }
    
    $image_path = !empty($uploadedImage) ? $uploadedImage : clean($_POST['photo_url'] ?? '');
    
    if (empty($image_path)) {
        $error = 'Please upload an image or enter a URL.';
    } elseif (empty($album_id)) {
        $error = 'Invalid album.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO gallery_photos (album_id, image_path, caption, sort_order) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$album_id, $image_path, $caption, $sort_order]);
            $message = 'Photo added successfully!';
            
            // Refresh the page to show new photo
            header('Location: manage-gallery.php?album_id=' . $album_id . '&success=1');
            exit;
        } catch (Exception $e) {
            $error = 'Error adding photo: ' . $e->getMessage();
        }
    }
}

// Fetch all albums with photo counts
$albumsStmt = $pdo->query("
    SELECT a.*, 
           (SELECT COUNT(*) FROM gallery_photos WHERE album_id = a.id) as photo_count
    FROM gallery_albums a
    ORDER BY a.sort_order, a.created_at DESC
");
$albums = $albumsStmt->fetchAll();

// Fetch photos for selected album
$selectedAlbumId = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;
$photos = [];
$selectedAlbum = null;

if ($selectedAlbumId) {
    $albumStmt = $pdo->prepare("SELECT * FROM gallery_albums WHERE id = ?");
    $albumStmt->execute([$selectedAlbumId]);
    $selectedAlbum = $albumStmt->fetch();
    
    if ($selectedAlbum) {
        $photoStmt = $pdo->prepare("
            SELECT * FROM gallery_photos 
            WHERE album_id = ? 
            ORDER BY sort_order, created_at
        ");
        $photoStmt->execute([$selectedAlbumId]);
        $photos = $photoStmt->fetchAll();
    }
}

// Handle multiple photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_multiple'])) {
    $album_id = (int)$_POST['album_id'];
    $uploaded = 0;
    $errors = [];
    
    if (isset($_FILES['multiple_photos']) && !empty($_FILES['multiple_photos']['name'][0])) {
        $fileCount = count($_FILES['multiple_photos']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['multiple_photos']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['multiple_photos']['name'][$i],
                    'type' => $_FILES['multiple_photos']['type'][$i],
                    'tmp_name' => $_FILES['multiple_photos']['tmp_name'][$i],
                    'error' => $_FILES['multiple_photos']['error'][$i],
                    'size' => $_FILES['multiple_photos']['size'][$i]
                ];
                
                $result = uploadImage($file, 'gallery', 5242880);
                if ($result['success']) {
                    $stmt = $pdo->prepare("
                        INSERT INTO gallery_photos (album_id, image_path, caption, sort_order) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$album_id, $result['path'], '', 0]);
                    $uploaded++;
                } else {
                    $errors[] = 'Image ' . ($i+1) . ': ' . $result['error'];
                }
            }
        }
        
        if ($uploaded > 0) {
            $message = $uploaded . ' photo(s) uploaded successfully!';
            if (!empty($errors)) {
                $error = implode('; ', $errors);
            }
            header('Location: manage-gallery.php?album_id=' . $album_id . '&success=1');
            exit;
        } elseif (!empty($errors)) {
            $error = implode('; ', $errors);
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

        .btn-add { padding: 12px 24px; background: linear-gradient(135deg, #009624, #00C853); color: #fff; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; box-shadow: 0 10px 30px rgba(0,200,83,0.3); }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(0,200,83,0.4); color: #fff; }

        .btn-edit { padding: 6px 14px; background: rgba(0,123,255,0.2); color: #4d9fff; border: 1px solid rgba(0,123,255,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-edit:hover { background: rgba(0,123,255,0.3); color: #4d9fff; }

        .btn-delete { padding: 6px 14px; background: rgba(255,0,0,0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: rgba(255,0,0,0.25); color: #ff6b6b; }

        .btn-toggle { padding: 6px 14px; border: none; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-toggle.published { background: rgba(0,200,83,0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); }
        .btn-toggle.published:hover { background: rgba(0,200,83,0.25); }
        .btn-toggle.draft { background: rgba(255,107,107,0.15); color: #FF6B6B; border: 1px solid rgba(255,107,107,0.1); }
        .btn-toggle.draft:hover { background: rgba(255,107,107,0.25); }

        .btn-view { padding: 6px 14px; background: rgba(23,162,184,0.15); color: #17a2b8; border: 1px solid rgba(23,162,184,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-view:hover { background: rgba(23,162,184,0.25); color: #17a2b8; }

        .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #009624, #00C853); color: #fff; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,200,83,0.3); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(0,200,83,0.4); }
        
        .btn-upload-multiple { padding: 10px 24px; background: #FF6B00; color: #fff; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(255,107,0,0.3); }
        .btn-upload-multiple:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(255,107,0,0.4); }

        .table-container { background: #fff; border-radius: 20px; padding: 25px; overflow-x: auto; box-shadow: 0 5px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; color: #666; font-weight: 600; border-bottom: 2px solid #e0e0e0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }

        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.published { background: rgba(0,200,83,0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); }
        .status-badge.draft { background: rgba(255,107,107,0.15); color: #FF6B6B; border: 1px solid rgba(255,107,107,0.1); }

        .album-cover { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; border: 1px solid #e0e0e0; }
        .album-cover-placeholder { width: 60px; height: 60px; background: #f0f0f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 1.5rem; }

        .photo-count { font-size: 0.85rem; color: #999; }
        .actions { display: flex; gap: 6px; flex-wrap: wrap; }

        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #f5c6cb; }

        .no-data { text-align: center; padding: 60px 0; color: #999; }
        .no-data i { font-size: 3rem; display: block; margin-bottom: 15px; color: #ccc; }

        .form-container { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #0a0a0a; font-size: 0.9rem; }
        .form-group label .required { color: #FF6B6B; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #00C853; }
        .form-group textarea { min-height: 60px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        .file-upload-wrapper {
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            background: #fafafa;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .file-upload-wrapper:hover { border-color: #00C853; background: rgba(0,200,83,0.02); }
        .file-upload-wrapper.dragover { border-color: #00C853; background: rgba(0,200,83,0.05); }
        .file-upload-wrapper input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .file-upload-wrapper .upload-icon { font-size: 2rem; color: #ccc; margin-bottom: 8px; }
        .file-upload-wrapper .upload-text { color: #999; font-size: 0.9rem; }
        .file-upload-wrapper .upload-text strong { color: #00C853; }
        .file-upload-wrapper .preview { margin-top: 10px; }
        .file-upload-wrapper .preview img { max-height: 80px; border-radius: 8px; border: 1px solid #e0e0e0; }

        .section-title { color: #0a0a0a; font-size: 1.2rem; font-weight: 700; margin: 30px 0 20px; padding-bottom: 10px; border-bottom: 2px solid #00C853; }
        .section-title i { color: #00C853; margin-right: 10px; }

        .photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin-top: 20px; }
        .photo-item { position: relative; border-radius: 12px; overflow: hidden; border: 1px solid #e0e0e0; background: #fff; transition: all 0.3s; }
        .photo-item:hover { border-color: #00C853; transform: translateY(-3px); }
        .photo-item img { width: 100%; height: 160px; object-fit: cover; }
        .photo-item .info { padding: 12px; }
        .photo-item .info .caption { font-size: 0.8rem; color: #333; margin-bottom: 4px; }
        .photo-item .info .order { font-size: 0.7rem; color: #999; }
        .photo-item .delete-btn { position: absolute; top: 8px; right: 8px; background: rgba(255,0,0,0.8); color: #fff; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .photo-item .delete-btn:hover { transform: scale(1.1); background: #ff0000; }

        .no-photos { text-align: center; padding: 40px 0; color: #999; }
        .no-photos i { font-size: 2.5rem; display: block; margin-bottom: 15px; color: #ccc; }
        
        .multiple-upload-box {
            border: 2px dashed #FF6B00;
            background: rgba(255,107,0,0.03);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .multiple-upload-box .icon { font-size: 3rem; color: #FF6B00; margin-bottom: 10px; }
        .multiple-upload-box .text { color: #666; }
        .multiple-upload-box .text strong { color: #FF6B00; }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .admin-header { flex-direction: column; align-items: stretch; } .photo-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); } }
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
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="manage-gallery.php" class="active"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-images"></i> Manage Gallery</h1>
            <a href="add-album.php" class="btn-add"><i class="fas fa-plus"></i> Add Album</a>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <!-- Albums List -->
        <div class="table-container">
            <h3 style="color:#0a0a0a;margin-bottom:15px;"><i class="fas fa-folder-open" style="color:#00C853;"></i> Albums</h3>
            <?php if (!empty($albums)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Album Name</th>
                            <th>Photos</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($albums as $album): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($album['cover_image'])): ?>
                                        <img src="../<?= clean($album['cover_image']) ?>" alt="<?= clean($album['name']) ?>" class="album-cover">
                                    <?php else: ?>
                                        <div class="album-cover-placeholder"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= clean($album['name']) ?></strong></td>
                                <td><span class="photo-count"><i class="fas fa-camera"></i> <?= $album['photo_count'] ?> photos</span></td>
                                <td>
                                    <span class="status-badge <?= $album['is_published'] ? 'published' : 'draft' ?>">
                                        <?= $album['is_published'] ? 'Published' : 'Draft' ?>
                                    </span>
                                </td>
                                <td><?= formatDate($album['created_at'], 'M j, Y') ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit-album.php?id=<?= $album['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?album_id=<?= $album['id'] ?>" class="btn-view"><i class="fas fa-images"></i> Photos</a>
                                        <a href="?toggle_album=<?= $album['id'] ?>" class="btn-toggle <?= $album['is_published'] ? 'published' : 'draft' ?>" onclick="return confirm('Toggle publish status?')">
                                            <?= $album['is_published'] ? '<i class="fas fa-eye"></i> Hide' : '<i class="fas fa-eye-slash"></i> Show' ?>
                                        </a>
                                        <a href="?delete_album=<?= $album['id'] ?>" class="btn-delete" onclick="return confirm('Delete this album and all its photos?')"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-images"></i>
                    <p>No albums created yet. <a href="add-album.php" style="color:#00C853;font-weight:600;">Create your first album</a></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Photos Section for Selected Album -->
        <?php if ($selectedAlbumId && $selectedAlbum): ?>
            <div style="margin-top:30px;">
                <h3 class="section-title">
                    <i class="fas fa-camera"></i> 
                    <?= clean($selectedAlbum['name']) ?> - Photos
                    <span style="font-size:0.8rem;color:#999;font-weight:400;margin-left:10px;">
                        (<?= count($photos) ?> photos)
                    </span>
                    <a href="manage-gallery.php" class="btn-delete" style="float:right;font-size:0.8rem;padding:6px 16px;background:rgba(200,200,200,0.3);color:#999;" onclick="return confirm('Close album view?')">
                        <i class="fas fa-times"></i> Close Album
                    </a>
                </h3>

                <!-- Upload Multiple Photos -->
                <div class="multiple-upload-box">
                    <div class="icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="text">
                        <strong>Upload Multiple Photos</strong><br>
                        <span style="font-size:0.9rem;color:#999;">Select multiple images at once (Hold Ctrl/Cmd to select multiple)</span>
                    </div>
                    <form method="POST" action="" enctype="multipart/form-data" style="margin-top:15px;">
                        <input type="hidden" name="album_id" value="<?= $selectedAlbumId ?>">
                        <div class="file-upload-wrapper" style="min-height:80px;border-color:#FF6B00;">
                            <input type="file" name="multiple_photos[]" accept="image/*" multiple>
                            <div class="upload-icon"><i class="fas fa-images" style="color:#FF6B00;"></i></div>
                            <div class="upload-text">
                                <strong style="color:#FF6B00;">Click to select multiple photos</strong><br>
                                <span style="font-size:0.85rem;color:#999;">JPG, PNG, WEBP, GIF (Max 5MB each)</span>
                            </div>
                            <div class="preview" id="multiplePreview"></div>
                        </div>
                        <button type="submit" name="upload_multiple" class="btn-upload-multiple" style="margin-top:10px;">
                            <i class="fas fa-upload"></i> Upload All Photos
                        </button>
                    </form>
                </div>

                <!-- Add Single Photo Form -->
                <div class="form-container">
                    <h4 style="color:#0a0a0a;margin-bottom:15px;font-size:0.95rem;">
                        <i class="fas fa-plus-circle" style="color:#00C853;"></i> Add Single Photo
                    </h4>
                    <form method="POST" action="?album_id=<?= $selectedAlbumId ?>" enctype="multipart/form-data">
                        <input type="hidden" name="album_id" value="<?= $selectedAlbumId ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Photo Image <span class="required">*</span></label>
                                <div class="file-upload-wrapper" id="fileUploadWrapper">
                                    <input type="file" id="photo_image" name="photo_image" accept="image/*">
                                    <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                    <div class="upload-text">
                                        <strong>Click to upload</strong> or drag and drop<br>
                                        <span style="font-size:0.85rem;color:#999;">JPG, PNG, WEBP, GIF (Max 5MB)</span>
                                    </div>
                                    <div class="preview" id="imagePreview"></div>
                                </div>
                                <div class="helper-text" style="font-size:0.85rem;color:#999;margin-top:5px;"><i class="fas fa-info-circle"></i> Or enter URL below</div>
                                <input type="text" id="photo_url" name="photo_url" placeholder="Or enter image URL" style="margin-top:10px;width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:12px;font-size:1rem;">
                            </div>
                            <div class="form-group">
                                <label for="caption">Caption</label>
                                <input type="text" id="caption" name="caption" placeholder="Brief caption for this photo">
                                <label for="sort_order" style="margin-top:10px;">Sort Order</label>
                                <input type="number" id="sort_order" name="sort_order" value="0" min="0">
                            </div>
                        </div>
                        <button type="submit" name="add_photo" class="btn-submit"><i class="fas fa-plus"></i> Add Photo</button>
                    </form>
                </div>

                <!-- Photos Grid -->
                <?php if (!empty($photos)): ?>
                    <div class="photo-grid">
                        <?php foreach ($photos as $photo): ?>
                            <div class="photo-item">
                                <img src="../<?= clean($photo['image_path']) ?>" alt="<?= clean($photo['caption'] ?? 'Photo') ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22160%22><rect width=%22200%22 height=%22160%22 fill=%22%23f0f0f0%22/><text x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2214%22 fill=%22%23ccc%22 text-anchor=%22middle%22 dy=%22.3em%22>Image not found</text></svg>'">
                                <a href="?delete_photo=<?= $photo['id'] ?>&album_id=<?= $selectedAlbumId ?>" class="delete-btn" onclick="return confirm('Delete this photo?')">×</a>
                                <div class="info">
                                    <div class="caption"><?= clean($photo['caption'] ?? 'Untitled') ?></div>
                                    <div class="order">Order: <?= $photo['sort_order'] ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-photos">
                        <i class="fas fa-image"></i>
                        <p>No photos in this album yet. Upload your first photo above!</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
// Single image preview
document.getElementById('photo_image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxHeight = '80px';
            img.style.borderRadius = '8px';
            img.style.border = '1px solid #e0e0e0';
            preview.appendChild(img);
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Multiple images preview
document.querySelector('input[name="multiple_photos[]"]').addEventListener('change', function(e) {
    const preview = document.getElementById('multiplePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files.length > 0) {
        const count = this.files.length;
        
        const info = document.createElement('div');
        info.style.padding = '10px';
        info.style.background = '#fff3e0';
        info.style.borderRadius = '8px';
        info.style.color = '#FF6B00';
        info.style.fontWeight = '600';
        info.textContent = count + ' photo(s) selected';
        preview.appendChild(info);
        
        // Show first image preview
        if (this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxHeight = '60px';
                img.style.borderRadius = '8px';
                img.style.border = '1px solid #e0e0e0';
                img.style.marginTop = '10px';
                preview.appendChild(img);
                
                if (count > 1) {
                    const note = document.createElement('div');
                    note.style.fontSize = '0.8rem';
                    note.style.color = '#999';
                    note.textContent = '...and ' + (count - 1) + ' more';
                    preview.appendChild(note);
                }
            };
            reader.readAsDataURL(this.files[0]);
        }
    }
});

// Drag and drop for single upload
const wrapper = document.getElementById('fileUploadWrapper');
wrapper.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
});
wrapper.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
});
wrapper.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('photo_image').files = files;
        document.getElementById('photo_image').dispatchEvent(new Event('change'));
    }
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>