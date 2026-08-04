<?php
// ============================================================
//  admin/testimonial-form.php — Add / Edit a Testimonial
//  ?edit=ID   → edit that testimonial
//  (no param) → new testimonial
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$testimonial = null;

if ($editId) {
  $stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
  $stmt->execute([$editId]);
  $testimonial = $stmt->fetch();
  if (!$testimonial) {
    setFlashErrors(['That testimonial could not be found.']);
    redirectTo('testimonials.php');
  }
}

// ── HANDLE SAVE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo($editId ? "testimonial-form.php?edit={$editId}" : 'testimonial-form.php');
  }

  $data = [
    'author_name' => clean($_POST['author_name'] ?? ''),
    'author_role' => clean($_POST['author_role'] ?? ''),
    'content' => clean($_POST['content'] ?? ''),
    'rating' => (int) ($_POST['rating'] ?? 5),
    'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    'is_published' => isset($_POST['is_published']) ? 1 : 0,
  ];
  $removeImage = isset($_POST['remove_image']);

  $errors = [];
  if ($data['author_name'] === '')
    $errors[] = 'Please enter the author\'s name.';
  if ($data['author_role'] === '')
    $errors[] = 'Please enter the author\'s role (e.g. "Parent" or "Alumni 2024").';
  if ($data['content'] === '')
    $errors[] = 'Please enter the testimonial text.';
  if ($data['rating'] < 1 || $data['rating'] > 5)
    $errors[] = 'Please choose a rating between 1 and 5.';

  if (!empty($errors)) {
    setFlashErrors($errors);
    setOldInput($data);
    redirectTo($editId ? "testimonial-form.php?edit={$editId}" : 'testimonial-form.php');
  }

  $upload = uploadTestimonialImage('photo');
  if ($upload['error']) {
    setFlashErrors([$upload['error']]);
    setOldInput($data);
    redirectTo($editId ? "testimonial-form.php?edit={$editId}" : 'testimonial-form.php');
  }

  $photoPath = $editId ? $testimonial['photo'] : '';
  if ($removeImage) {
    deleteTestimonialImageFile($photoPath);
    $photoPath = '';
  }
  if ($upload['path']) {
    deleteTestimonialImageFile($photoPath); // replace: drop the old file
    $photoPath = $upload['path'];
  }

  if ($editId) {
    $pdo->prepare(
      'UPDATE testimonials SET author_name = ?, author_role = ?, photo = ?, content = ?, rating = ?, sort_order = ?, is_published = ?
             WHERE id = ?'
    )->execute([
          $data['author_name'], $data['author_role'], $photoPath, $data['content'], $data['rating'],
          $data['sort_order'], $data['is_published'], $editId,
        ]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'testimonials', $editId, 'Updated testimonial from: ' . $data['author_name']);
    setFlashSuccess('Testimonial from ' . $data['author_name'] . ' has been updated.');
  } else {
    $pdo->prepare(
      'INSERT INTO testimonials (author_name, author_role, photo, content, rating, sort_order, is_published)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
    )->execute([
          $data['author_name'], $data['author_role'], $photoPath, $data['content'], $data['rating'],
          $data['sort_order'], $data['is_published'],
        ]);
    $newId = $pdo->lastInsertId();
    auditLog($pdo, $_SESSION['admin_id'], 'insert', 'testimonials', $newId, 'Added testimonial from: ' . $data['author_name']);
    setFlashSuccess('Testimonial from ' . $data['author_name'] . ' has been added.');
  }
  redirectTo('testimonials.php');
}

$flash = getFlash();

// Values used to fill the form: old input (after a validation error) > existing record (edit mode) > blank (new)
$f = $flash['old'];
$v = function ($key, $default = '') use ($f, $testimonial) {
  if ($f)
    return $f[$key] ?? $default;
  if ($testimonial)
    return $testimonial[$key] ?? $default;
  return $default;
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit Testimonial' : 'New Testimonial' ?> · Admin · Uganda Martyrs Primary School</title>
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
        <h1><?= $editId ? 'Edit Testimonial' : 'New Testimonial' ?></h1>
        <p><a href="testimonials.php">← Back to all testimonials</a></p>
      </header>

      <section class="admin-panel">
        <?php if ($flash['errors']): ?>
          <div class="alert alert-error">
            <ul><?php foreach ($flash['errors'] as $err): ?>
                <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="testimonial-form.php<?= $editId ? '?edit=' . $editId : '' ?>"
          enctype="multipart/form-data" class="admin-form">
          <?= csrfField() ?>

          <div class="field-row">
            <div class="field">
              <label for="author_name">Author name</label>
              <input id="author_name" name="author_name" type="text" required maxlength="200"
                value="<?= htmlspecialchars($v('author_name')) ?>">
            </div>
            <div class="field">
              <label for="author_role">Author role</label>
              <input id="author_role" name="author_role" type="text" required maxlength="100"
                value="<?= htmlspecialchars($v('author_role')) ?>" placeholder="e.g. Parent, Alumni 2024">
            </div>
          </div>

          <div class="field">
            <label for="content">Testimonial</label>
            <textarea id="content" name="content" rows="4" required><?= htmlspecialchars($v('content')) ?></textarea>
          </div>

          <div class="field">
            <label for="photo">Photo <span style="text-transform:none;letter-spacing:normal">(optional — JPG, PNG or
                WEBP, up to 3MB)</span></label>
            <?php if ($editId && $testimonial['photo'] && file_exists(__DIR__ . '/../' . $testimonial['photo'])): ?>
              <div class="admin-image-preview">
                <img src="../<?= htmlspecialchars($testimonial['photo']) ?>" alt="Current photo">
                <label class="admin-checkbox-inline">
                  <input type="checkbox" name="remove_image" value="1"> Remove this photo
                </label>
              </div>
            <?php endif; ?>
            <input id="photo" name="photo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
          </div>

          <div class="field-row">
            <div class="field">
              <label for="rating">Rating</label>
              <select id="rating" name="rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                  <option value="<?= $i ?>" <?= (int) $v('rating', 5) === $i ? 'selected' : '' ?>>
                    <?= str_repeat('★', $i) . str_repeat('☆', 5 - $i) ?> (<?= $i ?>)
                  </option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="field">
              <label for="sort_order">Sort order <span style="text-transform:none;letter-spacing:normal">(lower
                  numbers show first)</span></label>
              <input id="sort_order" name="sort_order" type="number" value="<?= htmlspecialchars($v('sort_order', '0')) ?>">
            </div>
            <div class="field" style="display:flex;align-items:flex-end;padding-bottom:12px">
              <label class="admin-checkbox-inline">
                <input type="checkbox" name="is_published" value="1" <?= $v('is_published', $testimonial ? $testimonial['is_published'] : 1) ? 'checked' : '' ?>>
                Published (visible on the site)
              </label>
            </div>
          </div>

          <div class="admin-actions">
            <button type="submit" class="btn btn-primary"><?= $editId ? 'Save Changes' : 'Add Testimonial' ?></button>
            <a href="testimonials.php" class="btn btn-ghost">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>

</html>
