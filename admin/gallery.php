<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'gallery';
$notice = '';

// Ensure a default album exists
$defaultAlbum = $pdo->query('SELECT id FROM gallery_albums ORDER BY id ASC LIMIT 1')->fetchColumn();
if (!$defaultAlbum) {
    $pdo->prepare(
        'INSERT INTO gallery_albums (name, description, cover_image, sort_order, is_published) VALUES (?,?,?,?,?)'
    )->execute(['General', 'School photos', '', 0, 1]);
    $defaultAlbum = (int) $pdo->lastInsertId();
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT filename FROM gallery_photos WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        deleteLocalImage($row['filename']);
        $pdo->prepare('DELETE FROM gallery_photos WHERE id = ?')->execute([$id]);
        auditLog($pdo, $_SESSION['admin_id'], 'DELETE', 'gallery_photos', $id, 'Deleted gallery photo');
    }
    header('Location: gallery.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption   = trim($_POST['caption'] ?? '');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $albumId   = (int) ($_POST['album_id'] ?? $defaultAlbum);
    $filename  = '';

    $upload = saveUploadedImage($_FILES['photo'] ?? [], 'gallery');
    if (!empty($upload['skipped'])) {
        $notice = 'Please choose a photo to upload.';
    } elseif (!$upload['ok']) {
        $notice = $upload['error'];
    } else {
        $filename = $upload['path'];
    }

    if ($notice === '' && $filename !== '') {
        $pdo->prepare(
            'INSERT INTO gallery_photos (album_id, filename, caption, sort_order, uploaded_by) VALUES (?,?,?,?,?)'
        )->execute([$albumId, $filename, $caption, $sortOrder, $_SESSION['admin_id']]);
        auditLog($pdo, $_SESSION['admin_id'], 'INSERT', 'gallery_photos', (int) $pdo->lastInsertId(), 'Uploaded: ' . $filename);
        header('Location: gallery.php?msg=saved');
        exit;
    }
}

$albums = $pdo->query('SELECT * FROM gallery_albums ORDER BY sort_order ASC, id ASC')->fetchAll();
$images = $pdo->query(
    'SELECT p.*, a.name AS album_name FROM gallery_photos p
     LEFT JOIN gallery_albums a ON a.id = p.album_id
     ORDER BY p.sort_order ASC, p.id ASC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Gallery — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Gallery</h1>
    <p class="admin-sub">Photos shown on the public Gallery page. Choose a file below to upload directly (JPG, PNG, GIF, or WebP, max 5 MB).</p>

    <?php if ($notice): ?><div class="admin-alert admin-alert-error"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?><div class="admin-alert admin-alert-success">Photo uploaded successfully.</div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?><div class="admin-alert admin-alert-success">Photo removed.</div><?php endif; ?>

    <div class="admin-card">
        <h2 style="margin-bottom:10px">Add Photo</h2>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <label>Album</label>
            <select name="album_id">
                <?php foreach ($albums as $a): ?>
                <option value="<?= (int)$a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Photo</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" required>
            <label>Caption</label>
            <input type="text" name="caption" placeholder="School Photo">
            <label>Sort Order</label>
            <input type="text" name="sort_order" value="0">
            <div style="margin-top:18px">
                <button type="submit" class="admin-btn admin-btn-primary">Add Photo</button>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom:14px">All Photos (<?= count($images) ?>)</h2>
        <table class="admin-table">
            <thead><tr><th>Preview</th><th>Path</th><th>Caption</th><th>Album</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($images as $img):
                    $src = $img['filename'];
                    if ($src && strpos($src, 'assets/') !== 0 && strpos($src, 'http') !== 0) {
                        $src = 'assets/images/' . ltrim($src, '/');
                    }
                ?>
                <tr>
                    <td><img class="thumb" src="../<?= htmlspecialchars($src) ?>"></td>
                    <td><?= htmlspecialchars($img['filename']) ?></td>
                    <td><?= htmlspecialchars($img['caption']) ?></td>
                    <td><?= htmlspecialchars($img['album_name'] ?? '—') ?></td>
                    <td>
                        <a href="gallery.php?delete=<?= $img['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm"
                           onclick="return confirm('Remove this photo?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
