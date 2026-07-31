<?php
// ============================================================
//  admin/gallery.php — Manage Gallery Albums
//  Photos within an album are managed on gallery-photos.php.
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// ── HANDLE ACTIONS (publish/unpublish, delete, add album) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
  $id = (int) ($_POST['id'] ?? 0);
  $action = $_POST['action'] ?? '';

  if ($id && $action === 'publish') {
    $pdo->prepare('UPDATE gallery_albums SET is_published = 1 WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'gallery_albums', $id, 'Published album');
  } elseif ($id && $action === 'unpublish') {
    $pdo->prepare('UPDATE gallery_albums SET is_published = 0 WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'gallery_albums', $id, 'Unpublished album');
  } elseif ($id && $action === 'delete') {
    // Photos reference their album with a foreign key (no cascade), so
    // every photo — and its file on disk — has to go first.
    $photoStmt = $pdo->prepare('SELECT filename FROM gallery_photos WHERE album_id = ?');
    $photoStmt->execute([$id]);
    foreach ($photoStmt->fetchAll() as $photo) {
      deleteGalleryImageFile($photo['filename']);
    }
    $pdo->prepare('DELETE FROM gallery_photos WHERE album_id = ?')->execute([$id]);

    $coverStmt = $pdo->prepare('SELECT cover_image FROM gallery_albums WHERE id = ?');
    $coverStmt->execute([$id]);
    $cover = $coverStmt->fetchColumn();
    $pdo->prepare('DELETE FROM gallery_albums WHERE id = ?')->execute([$id]);
    deleteGalleryImageFile($cover);

    auditLog($pdo, $_SESSION['admin_id'], 'delete', 'gallery_albums', $id, 'Deleted album and its photos');
    setFlashSuccess('Album and all its photos have been deleted.');
  }
  redirectTo('gallery.php');
}

$flash = getFlash();

// ── LIST ──────────────────────────────────────────────────
$albums = $pdo->query(
  'SELECT a.*, (SELECT COUNT(*) FROM gallery_photos p WHERE p.album_id = a.id) AS photo_count
     FROM gallery_albums a
     ORDER BY a.sort_order ASC, a.id ASC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery · Admin · Uganda Martyrs Primary School</title>
  <meta name="robots" content="noindex, nofollow">
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="admin-body">
  <div class="admin-shell">
    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
      <header class="admin-topbar admin-topbar-with-action">
        <div>
          <h1>Gallery</h1>
          <p><?= count($albums) ?> album<?= count($albums) === 1 ? '' : 's' ?></p>
        </div>
        <a href="gallery-form.php" class="btn btn-primary">+ New Album</a>
      </header>

      <?php if ($flash['success']): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash['success']) ?></div><?php endif; ?>
      <?php if ($flash['errors']): ?>
        <div class="alert alert-error">
          <ul><?php foreach ($flash['errors'] as $err): ?>
              <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <section class="admin-panel">
        <?php if ($albums): ?>
          <table class="admin-table admin-table-news">
            <thead>
              <tr>
                <th></th>
                <th>Album</th>
                <th>Photos</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($albums as $a): ?>
                <?php $hasRealImage = $a['cover_image'] && file_exists(__DIR__ . '/../' . $a['cover_image']); ?>
                <tr>
                  <td>
                    <?php if ($hasRealImage): ?>
                      <img class="admin-thumb" src="../<?= htmlspecialchars($a['cover_image']) ?>" alt="">
                    <?php else: ?>
                      <span class="admin-thumb admin-thumb-color" style="background:#2F6B4F"></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= htmlspecialchars($a['name']) ?>
                    <?php if (!$a['is_published']): ?><span class="admin-badge admin-badge-draft"
                        style="margin-left:8px">Draft</span><?php endif; ?>
                  </td>
                  <td><?= (int) $a['photo_count'] ?></td>
                  <td><span
                      class="admin-badge admin-badge-<?= $a['is_published'] ? 'published' : 'draft' ?>"><?= $a['is_published'] ? 'Published' : 'Draft' ?></span>
                  </td>
                  <td class="admin-table-actions">
                    <a href="gallery-photos.php?album=<?= (int) $a['id'] ?>">Manage Photos</a>
                    <a href="gallery-form.php?edit=<?= (int) $a['id'] ?>">Edit</a>
                    <form method="POST" action="gallery.php">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                      <input type="hidden" name="action" value="<?= $a['is_published'] ? 'unpublish' : 'publish' ?>">
                      <button type="submit" class="link-btn"><?= $a['is_published'] ? 'Unpublish' : 'Publish' ?></button>
                    </form>
                    <form method="POST" action="gallery.php"
                      onsubmit="return confirm('Delete this album and all <?= (int) $a['photo_count'] ?> of its photos permanently? This cannot be undone.');">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                      <input type="hidden" name="action" value="delete">
                      <button type="submit" class="link-btn link-btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="admin-empty">No albums yet. <a href="gallery-form.php">Create the first one →</a></p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>

</html>
