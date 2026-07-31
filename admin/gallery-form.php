<?php
// ============================================================
//  admin/gallery-form.php — Add / Edit a Gallery Album
//  ?edit=ID  → edit that album
//  (no param) → new album
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$album = null;

if ($editId) {
  $stmt = $pdo->prepare('SELECT * FROM gallery_albums WHERE id = ?');
  $stmt->execute([$editId]);
  $album = $stmt->fetch();
  if (!$album) {
    setFlashErrors(['That album could not be found.']);
    redirectTo('gallery.php');
  }
}

// ── HANDLE SAVE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo($editId ? "gallery-form.php?edit={$editId}" : 'gallery-form.php');
  }

  $name = clean($_POST['name'] ?? '');
  $description = clean($_POST['description'] ?? '');
  $sortOrder = (int) ($_POST['sort_order'] ?? 0);
  $isPublished = isset($_POST['is_published']) ? 1 : 0;
  $removeImage = isset($_POST['remove_image']);

  $errors = [];

  if ($name === '')
    $errors[] = 'Please give the album a name.';
  if (mb_strlen($name) > 190)
    $errors[] = 'That name is too long.';

  if (!empty($errors)) {
    setFlashErrors($errors);
    setOldInput([
      'name' => $name,
      'description' => $description,
      'sort_order' => $sortOrder,
      'is_published' => $isPublished,
    ]);
    redirectTo($editId ? "gallery-form.php?edit={$editId}" : 'gallery-form.php');
  }

  // Only touch the filesystem once every other field has already passed.
  $upload = uploadGalleryImage('cover_image');
  if ($upload['error']) {
    setFlashErrors([$upload['error']]);
    setOldInput([
      'name' => $name,
      'description' => $description,
      'sort_order' => $sortOrder,
      'is_published' => $isPublished,
    ]);
    redirectTo($editId ? "gallery-form.php?edit={$editId}" : 'gallery-form.php');
  }

  $imagePath = $editId ? $album['cover_image'] : '';
  if ($removeImage) {
    deleteGalleryImageFile($imagePath);
    $imagePath = '';
  }
  if ($upload['path']) {
    deleteGalleryImageFile($imagePath); // replace: drop the old file
    $imagePath = $upload['path'];
  }

  if ($editId) {
    $pdo->prepare(
      'UPDATE gallery_albums SET name = ?, description = ?, cover_image = ?, sort_order = ?, is_published = ?
             WHERE id = ?'
    )->execute([$name, $description, $imagePath, $sortOrder, $isPublished, $editId]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'gallery_albums', $editId, 'Updated album: ' . $name);
    setFlashSuccess('"' . $name . '" has been updated.');
    redirectTo('gallery.php');
  } else {
    $pdo->prepare(
      'INSERT INTO gallery_albums (name, description, cover_image, sort_order, is_published)
             VALUES (?, ?, ?, ?, ?)'
    )->execute([$name, $description, $imagePath, $sortOrder, $isPublished]);
    $newId = $pdo->lastInsertId();
    auditLog($pdo, $_SESSION['admin_id'], 'insert', 'gallery_albums', $newId, 'Created album: ' . $name);
    setFlashSuccess('"' . $name . '" has been created. Now add some photos to it.');
    redirectTo('gallery-photos.php?album=' . $newId);
  }
}

$flash = getFlash();

// Values used to fill the form: old input (after a validation error) > existing album (edit mode) > blank (new)
$f = $flash['old'];
$v = function ($key, $default = '') use ($f, $album) {
  if ($f)
    return $f[$key] ?? $default;
  if ($album)
    return $album[$key] ?? $default;
  return $default;
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit Album' : 'New Album' ?> · Admin · Uganda Martyrs Primary School</title>
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
        <h1><?= $editId ? 'Edit Album' : 'New Album' ?></h1>
        <p><a href="gallery.php">← Back to all albums</a></p>
      </header>

      <section class="admin-panel">
        <?php if ($flash['errors']): ?>
          <div class="alert alert-error">
            <ul><?php foreach ($flash['errors'] as $err): ?>
                <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="gallery-form.php<?= $editId ? '?edit=' . $editId : '' ?>"
          enctype="multipart/form-data" class="admin-form">
          <?= csrfField() ?>

          <div class="field">
            <label for="name">Album name</label>
            <input id="name" name="name" type="text" required maxlength="190"
              value="<?= htmlspecialchars($v('name')) ?>" placeholder="e.g. Sports Day 2026">
          </div>

          <div class="field">
            <label for="description">Description <span style="text-transform:none;letter-spacing:normal">(optional)</span></label>
            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($v('description')) ?></textarea>
          </div>

          <div class="field">
            <label for="cover_image">Cover photo <span style="text-transform:none;letter-spacing:normal">(optional —
                JPG, PNG or WEBP, up to 3MB. Shown as the album thumbnail; if left blank, the album's most recent photo
                is used instead.)</span></label>
            <?php if ($editId && $album['cover_image'] && file_exists(__DIR__ . '/../' . $album['cover_image'])): ?>
              <div class="admin-image-preview">
                <img src="../<?= htmlspecialchars($album['cover_image']) ?>" alt="Current album cover">
                <label class="admin-checkbox-inline">
                  <input type="checkbox" name="remove_image" value="1"> Remove this photo
                </label>
              </div>
            <?php endif; ?>
            <input id="cover_image" name="cover_image" type="file"
              accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
          </div>

          <div class="field-row">
            <div class="field">
              <label for="sort_order">Sort order <span style="text-transform:none;letter-spacing:normal">(lower numbers
                  show first)</span></label>
              <input id="sort_order" name="sort_order" type="number" value="<?= htmlspecialchars($v('sort_order', '0')) ?>">
            </div>
            <div class="field" style="display:flex;align-items:flex-end;padding-bottom:12px">
              <label class="admin-checkbox-inline">
                <input type="checkbox" name="is_published" value="1" <?= $v('is_published', $album ? $album['is_published'] : 1) ? 'checked' : '' ?>>
                Published (visible on the site)
              </label>
            </div>
          </div>

          <div class="admin-actions">
            <button type="submit" class="btn btn-primary"><?= $editId ? 'Save Changes' : 'Create Album' ?></button>
            <a href="gallery.php" class="btn btn-ghost">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>

</html>
