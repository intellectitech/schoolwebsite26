<?php
// ============================================================
//  admin/news-form.php — Add / Edit a News Article
//  ?edit=ID  → edit that article
//  (no param) → new article
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

$editId  = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$article = null;

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM news WHERE id = ?');
    $stmt->execute([$editId]);
    $article = $stmt->fetch();
    if (!$article) {
        setFlashErrors(['That article could not be found.']);
        redirectTo('news.php');
    }
}

$categories = $pdo->query('SELECT * FROM news_categories ORDER BY name')->fetchAll();

// ── HANDLE SAVE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
        redirectTo($editId ? "news-form.php?edit={$editId}" : 'news-form.php');
    }

    $title       = clean($_POST['title']        ?? '');
    $slugInput   = clean($_POST['slug']          ?? '');
    $categoryId  = (int) ($_POST['category_id']  ?? 0);
    $excerpt     = clean($_POST['excerpt']       ?? '');
    $body        = clean($_POST['body']          ?? '');
    $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
    $isPublished = ($_POST['status'] ?? 'draft') === 'published' ? 1 : 0;
    $publishedAt = clean($_POST['published_at']  ?? '');
    $removeImage = isset($_POST['remove_image']);

    $errors = [];

    if ($title === '')                                     $errors[] = 'Please give the article a title.';
    if (mb_strlen($title) > 390)                            $errors[] = 'That title is too long.';
    if (!$categoryId || !in_array($categoryId, array_column($categories, 'id'), true))
                                                             $errors[] = 'Please choose a category.';
    if (mb_strlen($body) < 20)                              $errors[] = 'The story needs at least 20 characters.';
    if ($publishedAt === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $publishedAt))
                                                             $errors[] = 'Please choose a valid publish date.';

    if (!empty($errors)) {
        setFlashErrors($errors);
        setOldInput([
            'title' => $title, 'slug' => $slugInput, 'category_id' => $categoryId,
            'excerpt' => $excerpt, 'body' => $body, 'is_featured' => $isFeatured,
            'status' => $isPublished ? 'published' : 'draft', 'published_at' => $publishedAt,
        ]);
        redirectTo($editId ? "news-form.php?edit={$editId}" : 'news-form.php');
    }

    // Only touch the filesystem once every other field has already passed —
    // avoids saving an image for a submission that's about to be rejected.
    $upload = uploadNewsImage('featured_image');
    if ($upload['error']) {
        setFlashErrors([$upload['error']]);
        setOldInput([
            'title' => $title, 'slug' => $slugInput, 'category_id' => $categoryId,
            'excerpt' => $excerpt, 'body' => $body, 'is_featured' => $isFeatured,
            'status' => $isPublished ? 'published' : 'draft', 'published_at' => $publishedAt,
        ]);
        redirectTo($editId ? "news-form.php?edit={$editId}" : 'news-form.php');
    }

    // Slug: use what was typed, or derive from the title; always unique
    $baseSlug = $slugInput !== '' ? slugify($slugInput) : slugify($title);
    $slug     = uniqueSlug($pdo, 'news', 'slug', $baseSlug, $editId ?: null);

    // Excerpt: auto-fill from the story if the admin left it blank
    if ($excerpt === '') {
        $excerpt = excerpt($body, 180);
    }

    // Work out the final image path for this save
    $imagePath = $editId ? $article['featured_image'] : '';
    if ($removeImage) {
        deleteNewsImageFile($imagePath);
        $imagePath = '';
    }
    if ($upload['path']) {
        deleteNewsImageFile($imagePath); // replace: drop the old file
        $imagePath = $upload['path'];
    }

    if ($editId) {
        $pdo->prepare(
            'UPDATE news SET category_id = ?, title = ?, slug = ?, excerpt = ?, body = ?,
                featured_image = ?, is_published = ?, is_featured = ?, published_at = ?
             WHERE id = ?'
        )->execute([
            $categoryId, $title, $slug, $excerpt, $body,
            $imagePath, $isPublished, $isFeatured, $publishedAt . ' 00:00:00',
            $editId,
        ]);
        auditLog($pdo, $_SESSION['admin_id'], 'update', 'news', $editId, 'Updated article: ' . $title);
        setFlashSuccess('"' . $title . '" has been updated.');
    } else {
        $pdo->prepare(
            'INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, author_id, views, is_published, is_featured, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?)'
        )->execute([
            $categoryId, $title, $slug, $excerpt, $body, $imagePath,
            $_SESSION['admin_id'], $isPublished, $isFeatured, $publishedAt . ' 00:00:00',
        ]);
        $newId = $pdo->lastInsertId();
        auditLog($pdo, $_SESSION['admin_id'], 'insert', 'news', $newId, 'Created article: ' . $title);
        setFlashSuccess('"' . $title . '" has been ' . ($isPublished ? 'published' : 'saved as a draft') . '.');
    }

    redirectTo('news.php');
}

$flash = getFlash();

// Values used to fill the form: old input (after a validation error) > existing article (edit mode) > blank (new)
$f = $flash['old'];
$v = function ($key, $default = '') use ($f, $article) {
    if ($f) return $f[$key] ?? $default;
    if ($article) return $article[$key] ?? $default;
    return $default;
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit Article' : 'New Article' ?> · Admin · Uganda Martyrs Primary School</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <h1><?= $editId ? 'Edit Article' : 'New Article' ?></h1>
      <p><a href="news.php">← Back to all articles</a></p>
    </header>

    <section class="admin-panel">
      <?php if ($flash['errors']): ?>
        <div class="alert alert-error">
          <ul><?php foreach ($flash['errors'] as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="news-form.php<?= $editId ? '?edit=' . $editId : '' ?>" enctype="multipart/form-data" class="admin-form">
        <?= csrfField() ?>

        <div class="field">
          <label for="title">Title</label>
          <input id="title" name="title" type="text" required maxlength="390" value="<?= htmlspecialchars($v('title')) ?>" placeholder="e.g. Primary Seven Pupils Visit the Martyrs Shrine">
        </div>

        <div class="field-row">
          <div class="field">
            <label for="slug">URL slug <span style="text-transform:none;letter-spacing:normal">(optional — auto-created from the title if left blank)</span></label>
            <input id="slug" name="slug" type="text" value="<?= htmlspecialchars($v('slug')) ?>" placeholder="auto-generated-from-title">
          </div>
          <div class="field">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
              <option value="" disabled <?= $v('category_id') === '' ? 'selected' : '' ?>>Select category</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (int) $v('category_id') === (int) $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="field">
          <label for="excerpt">Short summary <span style="text-transform:none;letter-spacing:normal">(optional — used on listing cards; auto-created from the story if left blank)</span></label>
          <textarea id="excerpt" name="excerpt" rows="2" maxlength="500"><?= htmlspecialchars($v('excerpt')) ?></textarea>
        </div>

        <div class="field">
          <label for="body">Full story</label>
          <textarea id="body" name="body" rows="10" required><?= htmlspecialchars($v('body')) ?></textarea>
        </div>

        <div class="field">
          <label for="featured_image">Photo <span style="text-transform:none;letter-spacing:normal">(optional — JPG, PNG or WEBP, up to 3MB. If left blank, an illustrated placeholder is used instead.)</span></label>
          <?php if ($editId && $article['featured_image'] && file_exists(__DIR__ . '/../' . $article['featured_image'])): ?>
            <div class="admin-image-preview">
              <img src="../<?= htmlspecialchars($article['featured_image']) ?>" alt="Current article photo">
              <label class="admin-checkbox-inline">
                <input type="checkbox" name="remove_image" value="1"> Remove this photo
              </label>
            </div>
          <?php endif; ?>
          <input id="featured_image" name="featured_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        </div>

        <div class="field-row">
          <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
              <option value="draft" <?= $v('status', 'draft') === 'draft' ? 'selected' : '' ?>>Draft (not visible on the site)</option>
              <option value="published" <?= $v('status', 'draft') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
          </div>
          <div class="field">
            <label for="published_at">Publish date</label>
            <input id="published_at" name="published_at" type="date" required
                   value="<?= htmlspecialchars($v('published_at', $article ? date('Y-m-d', strtotime($article['published_at'])) : date('Y-m-d'))) ?>">
          </div>
        </div>

        <label class="admin-checkbox-inline">
          <input type="checkbox" name="is_featured" value="1" <?= $v('is_featured') ? 'checked' : '' ?>>
          Feature this story at the top of the News page
        </label>

        <div class="admin-actions">
          <button type="submit" class="btn btn-primary"><?= $editId ? 'Save Changes' : 'Save Article' ?></button>
          <a href="news.php" class="btn btn-ghost">Cancel</a>
        </div>
      </form>
    </section>
  </main>
</div>
</body>
</html>
