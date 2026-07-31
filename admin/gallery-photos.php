<?php
// ============================================================
//  admin/gallery-photos.php — Upload / Manage Photos in an Album
//  ?album=ID (required)
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

$albumId = (int) ($_GET['album'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM gallery_albums WHERE id = ?');
$stmt->execute([$albumId]);
$album = $stmt->fetch();
if (!$album) {
  setFlashErrors(['That album could not be found.']);
  redirectTo('gallery.php');
}

// ── HANDLE ACTIONS ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo("gallery-photos.php?album={$albumId}");
  }

  $formAction = $_POST['form_action'] ?? 'upload';

  if ($formAction === 'upload') {
    $caption = clean($_POST['caption'] ?? '');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    $upload = uploadGalleryImage('photo');
    if ($upload['error']) {
      setFlashErrors([$upload['error']]);
      redirectTo("gallery-photos.php?album={$albumId}");
    }
    if (!$upload['path']) {
      setFlashErrors(['Please choose a photo to upload.']);
      redirectTo("gallery-photos.php?album={$albumId}");
    }

    $pdo->prepare(
      'INSERT INTO gallery_photos (album_id, filename, caption, sort_order, uploaded_by)
             VALUES (?, ?, ?, ?, ?)'
    )->execute([$albumId, $upload['path'], $caption, $sortOrder, $_SESSION['admin_id']]);
    $newPhotoId = $pdo->lastInsertId();
    auditLog($pdo, $_SESSION['admin_id'], 'insert', 'gallery_photos', $newPhotoId, 'Added photo to album: ' . $album['name']);

    // The first photo added to an album with no cover yet becomes the cover automatically.
    if (!$album['cover_image']) {
      $pdo->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')->execute([$upload['path'], $albumId]);
    }

    setFlashSuccess('Photo added.');
  } elseif ($formAction === 'delete') {
    $photoId = (int) ($_POST['photo_id'] ?? 0);
    $photoStmt = $pdo->prepare('SELECT * FROM gallery_photos WHERE id = ? AND album_id = ?');
    $photoStmt->execute([$photoId, $albumId]);
    $photo = $photoStmt->fetch();
    if ($photo) {
      $pdo->prepare('DELETE FROM gallery_photos WHERE id = ?')->execute([$photoId]);
      deleteGalleryImageFile($photo['filename']);
      // If that photo was the album cover, clear it (falls back to "no cover" in the admin list).
      if ($album['cover_image'] === $photo['filename']) {
        $pdo->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')->execute(['', $albumId]);
      }
      auditLog($pdo, $_SESSION['admin_id'], 'delete', 'gallery_photos', $photoId, 'Deleted photo from album: ' . $album['name']);
      setFlashSuccess('Photo deleted.');
    }
  } elseif ($formAction === 'set_cover') {
    $photoId = (int) ($_POST['photo_id'] ?? 0);
    $photoStmt = $pdo->prepare('SELECT filename FROM gallery_photos WHERE id = ? AND album_id = ?');
    $photoStmt->execute([$photoId, $albumId]);
    $filename = $photoStmt->fetchColumn();
    if ($filename) {
      $pdo->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')->execute([$filename, $albumId]);
      setFlashSuccess('Cover photo updated.');
    }
  }
  redirectTo("gallery-photos.php?album={$albumId}");
}

$flash = getFlash();

$photos = $pdo->prepare('SELECT * FROM gallery_photos WHERE album_id = ? ORDER BY sort_order ASC, id ASC');
$photos->execute([$albumId]);
$photos = $photos->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Photos · <?= htmlspecialchars($album['name']) ?> · Admin · Uganda Martyrs Primary School</title>
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
      <header class="admin-topbar">
        <h1><?= htmlspecialchars($album['name']) ?></h1>
        <p><a href="gallery.php">← Back to all albums</a> · <?= count($photos) ?> photo<?= count($photos) === 1 ? '' : 's' ?></p>
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

      <section class="admin-panel admin-panel-narrow">
        <div class="admin-panel-head">
          <h2>Add a photo</h2>
        </div>
        <form method="POST" action="gallery-photos.php?album=<?= $albumId ?>" enctype="multipart/form-data"
          class="admin-form">
          <?= csrfField() ?>
          <input type="hidden" name="form_action" value="upload">

          <div class="field">
            <label for="photo">Photo <span style="text-transform:none;letter-spacing:normal">(JPG, PNG or WEBP, up to
                3MB)</span></label>
            <input id="photo" name="photo" type="file" required
              accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
          </div>
          <div class="field">
            <label for="caption">Caption <span style="text-transform:none;letter-spacing:normal">(optional)</span></label>
            <input id="caption" name="caption" type="text" maxlength="290" placeholder="e.g. Tug of war competition">
          </div>
          <div class="field">
            <label for="sort_order">Sort order <span style="text-transform:none;letter-spacing:normal">(lower numbers
                show first)</span></label>
            <input id="sort_order" name="sort_order" type="number" value="0">
          </div>

          <div class="admin-actions">
            <button type="submit" class="btn btn-primary">Upload Photo</button>
          </div>
        </form>
      </section>

      <section class="admin-panel">
        <div class="admin-panel-head">
          <h2>Photos in this album</h2>
        </div>
        <?php if ($photos): ?>
          <div class="admin-gallery-grid">
            <?php foreach ($photos as $p): ?>
              <?php $exists = $p['filename'] && file_exists(__DIR__ . '/../' . $p['filename']); ?>
              <div class="admin-gallery-photo">
                <?php if ($exists): ?>
                  <img src="../<?= htmlspecialchars($p['filename']) ?>" alt="<?= htmlspecialchars($p['caption']) ?>">
                <?php else: ?>
                  <span class="admin-gallery-photo-missing">File missing</span>
                <?php endif; ?>
                <?php if ($album['cover_image'] === $p['filename']): ?>
                  <span class="admin-badge admin-badge-featured admin-gallery-cover-badge">Cover</span>
                <?php endif; ?>
                <div class="admin-gallery-photo-body">
                  <p><?= htmlspecialchars($p['caption'] ?: '—') ?></p>
                  <div class="admin-table-actions">
                    <?php if ($album['cover_image'] !== $p['filename']): ?>
                      <form method="POST" action="gallery-photos.php?album=<?= $albumId ?>">
                        <?= csrfField() ?>
                        <input type="hidden" name="form_action" value="set_cover">
                        <input type="hidden" name="photo_id" value="<?= (int) $p['id'] ?>">
                        <button type="submit" class="link-btn">Set as cover</button>
                      </form>
                    <?php endif; ?>
                    <form method="POST" action="gallery-photos.php?album=<?= $albumId ?>"
                      onsubmit="return confirm('Delete this photo permanently?');">
                      <?= csrfField() ?>
                      <input type="hidden" name="form_action" value="delete">
                      <input type="hidden" name="photo_id" value="<?= (int) $p['id'] ?>">
                      <button type="submit" class="link-btn link-btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="admin-empty">No photos in this album yet. Add the first one above.</p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>

</html>
