<?php
// gallery.php
include 'db_connect.php';
require_once __DIR__ . '/auth.php';
require_login();

// Safe HTML output helper
if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }
}

// ---------------------------------------------------------------------
// SCHEMA INSPECTOR (Prevents 1054 Unknown Column errors)
// ---------------------------------------------------------------------
function getTableColumns(PDO $pdo, string $tableName): array {
    try {
        $stmt = $pdo->prepare("
            SELECT COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :tableName
        ");
        $stmt->execute([':tableName' => $tableName]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    } catch (PDOException $e) {
        return [];
    }
}

$albumCols = getTableColumns($pdo, 'gallery_albums');
$photoCols = getTableColumns($pdo, 'gallery_photos');

// Detect Album Title Column
$albumTitleCol = 'title';
foreach (['title', 'album_title', 'album_name', 'name'] as $col) {
    if (in_array($col, $albumCols)) { $albumTitleCol = $col; break; }
}
$hasAlbumDesc = in_array('description', $albumCols);

// Detect Photo Columns
$hasPhotoDesc = in_array('description', $photoCols);
$hasPhotoCaption = in_array('caption', $photoCols);
$hasPhotoPath = in_array('photo', $photoCols) ? 'photo' : (in_array('image_path', $photoCols) ? 'image_path' : '');

// ---------------------------------------------------------------------
// HANDLE FORM SUBMISSIONS
// ---------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_album') {
    $album_title = trim($_POST['album_title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!empty($album_title)) {
        try {
            if ($hasAlbumDesc) {
                $stmt = $pdo->prepare("INSERT INTO gallery_albums ({$albumTitleCol}, description) VALUES (:title, :desc)");
                $stmt->execute([':title' => $album_title, ':desc' => $description]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO gallery_albums ({$albumTitleCol}) VALUES (:title)");
                $stmt->execute([':title' => $album_title]);
            }
            header('Location: gallery.php?success=' . urlencode('Album created successfully!'));
            exit();
        } catch (PDOException $e) {
            header('Location: gallery.php?error=' . urlencode('Error creating album: ' . $e->getMessage()));
            exit();
        }
    }
}

// ---------------------------------------------------------------------
// SAFE FETCHING (DYNAMIC SQL)
// ---------------------------------------------------------------------

// Build dynamic SELECT for Albums
$albumSelect = "album_id, {$albumTitleCol} AS album_title";
if ($hasAlbumDesc) { $albumSelect .= ", description"; }

$albums = [];
if (!empty($albumCols)) {
    $albums = $pdo->query("SELECT {$albumSelect} FROM gallery_albums ORDER BY album_id DESC")->fetchAll(PDO::FETCH_ASSOC);
}

// Build dynamic SELECT for Photos
$photos = [];
if (!empty($photoCols)) {
    $photosQuery = "
        SELECT p.*, a.{$albumTitleCol} AS album_title 
        FROM gallery_photos p 
        LEFT JOIN gallery_albums a ON p.album_id = a.album_id 
        ORDER BY p.photo_id DESC
    ";
    $photos = $pdo->query($photosQuery)->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery & Albums Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            padding: 25px;
        }

        .container { max-width: 1200px; margin: 0 auto; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }

        .btn {
            padding: 9px 16px; border: none; border-radius: 6px; cursor: pointer;
            font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center;
            gap: 8px; text-decoration: none;
        }

        .btn-primary { background-color: var(--secondary-blue); color: white; }
        .btn-secondary { background-color: var(--primary-blue); color: white; }
        .btn-cancel { background-color: #94a3b8; color: white; }
        .btn:hover { opacity: 0.9; }

        .section-title {
            margin: 30px 0 15px 0; border-bottom: 2px solid var(--border-color);
            padding-bottom: 8px; color: var(--primary-blue);
        }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        .card { background: var(--card-bg); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
        .card-img { width: 100%; height: 180px; object-fit: cover; display: block; }
        .card-body { padding: 15px; }
        .card-title { font-size: 1rem; margin: 0 0 5px 0; color: var(--primary-blue); }
        .card-sub { font-size: 0.85rem; color: var(--text-muted); }

        .modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5);
            align-items: center; justify-content: center; z-index: 1000;
        }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #fff; padding: 25px; border-radius: 8px; width: 100%; max-width: 480px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 9px; border: 1px solid var(--border-color); border-radius: 6px; box-sizing: border-box;
        }

        .flash { padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .flash-success { background: #dcfce7; color: #15803d; }
        .flash-error { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>

<div class="container">

    <!-- Flash Notifications -->
    <?php if (isset($_GET['success'])): ?>
        <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> <?= h($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($_GET['error']); ?></div>
    <?php endif; ?>

    <!-- Header Actions -->
    <div class="header-actions">
        <h2><i class="fa-solid fa-images"></i> Photo Gallery Management</h2>
        <div>
            <button class="btn btn-secondary" onclick="openModal('addAlbumModal')">
                <i class="fa-solid fa-folder-plus"></i> New Album
            </button>
            <button class="btn btn-primary" onclick="openModal('uploadPhotoModal')">
                <i class="fa-solid fa-upload"></i> Upload Photo
            </button>
        </div>
    </div>

    <!-- Album Section -->
    <h3 class="section-title"><i class="fa-solid fa-folder"></i> Albums</h3>
    <div class="grid">
        <?php if (empty($albums)): ?>
            <p style="color:var(--text-muted);">No albums created yet.</p>
        <?php else: ?>
            <?php foreach ($albums as $album): ?>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><?= h($album['album_title']); ?></h4>
                        <p class="card-sub"><?= h($album['description'] ?? 'No description available'); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Photos Section -->
    <h3 class="section-title"><i class="fa-solid fa-image"></i> Gallery Photos</h3>
    <div class="grid">
        <?php if (empty($photos)): ?>
            <p style="color:var(--text-muted);">No photos uploaded yet.</p>
        <?php else: ?>
            <?php foreach ($photos as $photo): ?>
                <?php 
                    $src = $photo['photo'] ?? ($photo['image_path'] ?? '');
                    $desc = $photo['description'] ?? ($photo['caption'] ?? ('Photo #' . $photo['photo_id']));
                ?>
                <div class="card">
                    <img src="<?= h($src); ?>" alt="Photo" class="card-img" onerror="this.src='https://via.placeholder.com/240x180?text=No+Image';">
                    <div class="card-body">
                        <h4 class="card-title"><?= h($desc); ?></h4>
                        <span class="card-sub">Album: <?= h($photo['album_title'] ?? 'Unassigned'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- Modal: Create Album -->
<div class="modal-overlay" id="addAlbumModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-folder-plus"></i> Create Album</h3>
        <form action="gallery.php" method="POST">
            <input type="hidden" name="action" value="create_album">
            <div class="form-group">
                <label>Album Title</label>
                <input type="text" name="album_title" required>
            </div>
            <?php if ($hasAlbumDesc): ?>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            <?php endif; ?>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-cancel" onclick="closeModal('addAlbumModal')">Cancel</button>
                <button type="submit" class="btn btn-secondary">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Upload Photo -->
<div class="modal-overlay" id="uploadPhotoModal">
    <div class="modal-box">
        <h3><i class="fa-solid fa-upload"></i> Upload Photo</h3>
        <form action="actions/add_gallery_photo.php" method="POST">
            <div class="form-group">
                <label>Album</label>
                <select name="album_id">
                    <option value="">— Select Album —</option>
                    <?php foreach ($albums as $alb): ?>
                        <option value="<?= (int)$alb['album_id']; ?>"><?= h($alb['album_title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Photo Path / Filename</label>
                <input type="text" name="photo" placeholder="e.g. uploads/school1.jpg" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-cancel" onclick="closeModal('uploadPhotoModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Photo</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
</script>

</body>
</html>