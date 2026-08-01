<?php
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/upload_helper.php';

$editing = false;
$post = [
    'id' => null, 'category' => '', 'title' => '', 'slug' => '',
    'excerpt' => '', 'body' => '', 'cover_image' => '', 'is_published' => 1,
    'published_at' => date('Y-m-d'),
];

if (!empty($_GET['id']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM news_posts WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $row = $stmt->fetch();
        if ($row) {
            $post = $row;
            $post['published_at'] = date('Y-m-d', strtotime($row['published_at']));
            $editing = true;
        }
    } catch (Exception $e) {}
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();

    $title        = trim($_POST['title'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $excerpt      = trim($_POST['excerpt'] ?? '');
    $body         = trim($_POST['body'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $published_at = $_POST['published_at'] ?: date('Y-m-d');
    $existingId   = $_POST['id'] ?: null;
    $existingCover = $_POST['existing_cover'] ?? '';

    if ($title === '') { $errors[] = "Title is required."; }
    if ($body === '') { $errors[] = "Post content is required."; }

    $uploadError = '';
    $uploadedPath = handle_image_upload('cover_image', 'news', $uploadError);
    if ($uploadError) { $errors[] = $uploadError; }

    $coverImage = $uploadedPath ?: $existingCover;
    if (!$editing && !$coverImage) {
        $coverImage = 'assets/images/news/news-1.svg';
    }

    if (empty($errors) && $pdo) {
        $slug = make_slug($title);
        try {
            if ($existingId) {
                $check = $pdo->prepare("SELECT COUNT(*) FROM news_posts WHERE slug = :slug AND id != :id");
                $check->execute([':slug' => $slug, ':id' => $existingId]);
                if ($check->fetchColumn() > 0) { $slug .= '-' . $existingId; }

                $stmt = $pdo->prepare("UPDATE news_posts SET category=:cat, title=:title, slug=:slug,
                    excerpt=:excerpt, body=:body, cover_image=:cover, is_published=:pub, published_at=:pubdate
                    WHERE id=:id");
                $stmt->execute([
                    ':cat' => $category, ':title' => $title, ':slug' => $slug,
                    ':excerpt' => $excerpt, ':body' => $body, ':cover' => $coverImage,
                    ':pub' => $is_published, ':pubdate' => $published_at, ':id' => $existingId,
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post updated successfully.'];
            } else {
                $check = $pdo->prepare("SELECT COUNT(*) FROM news_posts WHERE slug = :slug");
                $check->execute([':slug' => $slug]);
                if ($check->fetchColumn() > 0) { $slug .= '-' . substr(uniqid(), -5); }

                $stmt = $pdo->prepare("INSERT INTO news_posts (category, title, slug, excerpt, body, cover_image, is_published, published_at)
                    VALUES (:cat, :title, :slug, :excerpt, :body, :cover, :pub, :pubdate)");
                $stmt->execute([
                    ':cat' => $category, ':title' => $title, ':slug' => $slug,
                    ':excerpt' => $excerpt, ':body' => $body, ':cover' => $coverImage,
                    ':pub' => $is_published, ':pubdate' => $published_at,
                ]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post created successfully.'];
            }
            header('Location: news.php');
            exit;
        } catch (Exception $e) {
            $errors[] = "Could not save the post. Please try again.";
        }
    } elseif (!$pdo) {
        $errors[] = "Database not connected — changes cannot be saved right now.";
    }

    $post = array_merge($post, [
        'title' => $title, 'category' => $category, 'excerpt' => $excerpt,
        'body' => $body, 'is_published' => $is_published, 'published_at' => $published_at,
        'cover_image' => $coverImage ?: $existingCover, 'id' => $existingId,
    ]);
    if ($existingId) { $editing = true; }
}

$page_title = $editing ? "Edit Post" : "New Post";
$active_nav = "news";
require_once __DIR__ . '/includes/admin_header.php';
?>

<?php foreach ($errors as $err): ?>
  <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
<?php endforeach; ?>

<div class="panel">
  <div class="panel-header">
    <h2><?php echo $editing ? 'Edit Post' : 'Create New Post'; ?></h2>
    <a href="news.php" class="btn btn-outline btn-sm">&larr; Back to News</a>
  </div>
  <div class="panel-body">
    <form method="POST" action="news_edit.php<?php echo $editing ? '?id=' . $post['id'] : ''; ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($post['id'] ?? ''); ?>">
      <input type="hidden" name="existing_cover" value="<?php echo htmlspecialchars($post['cover_image']); ?>">

      <div class="form-grid">
        <div class="field field-full">
          <label for="title">Post Title *</label>
          <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($post['title']); ?>">
        </div>

        <div class="field">
          <label for="category">Category</label>
          <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($post['category']); ?>" placeholder="e.g. Academics, Events, Parent Corner" list="categoryList">
          <datalist id="categoryList">
            <option value="School News"><option value="Academics"><option value="Events"><option value="Parent Corner">
          </datalist>
        </div>

        <div class="field">
          <label for="published_at">Publish Date</label>
          <input type="date" id="published_at" name="published_at" value="<?php echo htmlspecialchars($post['published_at']); ?>">
        </div>

        <div class="field field-full">
          <label for="excerpt">Short Excerpt</label>
          <textarea id="excerpt" name="excerpt" rows="2" placeholder="A one or two sentence summary shown on listing pages..."><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
        </div>

        <div class="field field-full">
          <label for="body">Full Post Content *</label>
          <textarea id="body" name="body" rows="10" required><?php echo htmlspecialchars($post['body']); ?></textarea>
          <div class="field-hint">Plain paragraphs are fine — line breaks are preserved automatically.</div>
        </div>

        <div class="field field-full">
          <label for="cover_image">Cover Image</label>
          <?php if (!empty($post['cover_image'])): ?>
            <div class="image-preview-current">
              <img src="<?php echo htmlspecialchars('../' . $post['cover_image']); ?>" alt="Current cover">
              <span>Current image: <?php echo htmlspecialchars($post['cover_image']); ?></span>
            </div>
          <?php endif; ?>
          <input type="file" id="cover_image" name="cover_image" accept="image/*">
          <div class="field-hint">Upload a new image to replace the current cover. JPG, PNG, GIF, WEBP or SVG, up to 5MB.</div>
        </div>

        <div class="field field-full checkbox-field">
          <input type="checkbox" id="is_published" name="is_published" value="1" <?php echo $post['is_published'] ? 'checked' : ''; ?>>
          <label for="is_published" style="margin:0;">Published (visible on the live site)</label>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-gold"><?php echo $editing ? 'Save Changes' : 'Create Post'; ?></button>
        <a href="news.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
