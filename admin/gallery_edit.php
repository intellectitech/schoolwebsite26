<?php
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/upload_helper.php';

$editing = false;
$image = ['id' => null, 'category' => '', 'title' => '', 'file_path' => '', 'caption' => ''];

if (!empty($_GET['id']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $row = $stmt->fetch();
        if ($row) { $image = $row; $editing = true; }
    } catch (Exception $e) {}
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();

    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $caption     = trim($_POST['caption'] ?? '');
    $existingId  = $_POST['id'] ?: null;
    $existingPath = $_POST['existing_path'] ?? '';

    if ($title === '') { $errors[] = "Title is required."; }

    $uploadError = '';
    $uploadedPath = handle_image_upload('photo', 'gallery', $uploadError);
    if ($uploadError) { $errors[] = $uploadError; }

    $filePath = $uploadedPath ?: $existingPath;
    if (!$filePath) { $errors[] = "Please upload a photo."; }

    if (empty($errors) && $pdo) {
        try {
            if ($existingId) {
                $stmt = $pdo->prepare("UPDATE gallery_images SET category=:cat, title=:title, file_path=:path, caption=:cap WHERE id=:id");
                $stmt->execute([':cat' => $category, ':title' => $title, ':path' => $filePath, ':cap' => $caption, ':id' => $existingId]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Photo updated successfully.'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO gallery_images (category, title, file_path, caption) VALUES (:cat, :title, :path, :cap)");
                $stmt->execute([':cat' => $category, ':title' => $title, ':path' => $filePath, ':cap' => $caption]);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Photo added successfully.'];
            }
            header('Location: gallery.php');
            exit;
        } catch (Exception $e) {
            $errors[] = "Could not save the photo. Please try again.";
        }
    } elseif (!$pdo) {
        $errors[] = "Database not connected — changes cannot be saved right now.";
    }

    $image = array_merge($image, [
        'title' => $title, 'category' => $category, 'caption' => $caption,
        'file_path' => $filePath ?: $existingPath, 'id' => $existingId,
    ]);
    if ($existingId) { $editing = true; }
}

$page_title = $editing ? "Edit Photo" : "Add Photo";
$active_nav = "gallery";
require_once __DIR__ . '/includes/admin_header.php';
?>

<?php foreach ($errors as $err): ?>
  <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
<?php endforeach; ?>

<div class="panel">
  <div class="panel-header">
    <h2><?php echo $editing ? 'Edit Photo' : 'Add New Photo'; ?></h2>
    <a href="gallery.php" class="btn btn-outline btn-sm">&larr; Back to Gallery</a>
  </div>
  <div class="panel-body">
    <form method="POST" action="gallery_edit.php<?php echo $editing ? '?id=' . $image['id'] : ''; ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($image['id'] ?? ''); ?>">
      <input type="hidden" name="existing_path" value="<?php echo htmlspecialchars($image['file_path']); ?>">

      <div class="form-grid">
        <div class="field field-full">
          <label for="title">Photo Title *</label>
          <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($image['title']); ?>">
        </div>

        <div class="field">
          <label for="category">Category</label>
          <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($image['category']); ?>" placeholder="e.g. School Life, Sports & Swimming" list="galCategoryList">
          <datalist id="galCategoryList">
            <option value="School Life"><option value="Sports & Swimming"><option value="ICT & Academics"><option value="Events & Celebrations">
          </datalist>
        </div>

        <div class="field">
          <label for="caption">Caption</label>
          <input type="text" id="caption" name="caption" value="<?php echo htmlspecialchars($image['caption']); ?>" placeholder="Shown under the photo in the lightbox">
        </div>

        <div class="field field-full">
          <label for="photo">Photo <?php echo $editing ? '' : '*'; ?></label>
          <?php if (!empty($image['file_path'])): ?>
            <div class="image-preview-current">
              <img src="<?php echo htmlspecialchars('../' . $image['file_path']); ?>" alt="Current photo">
              <span>Current file: <?php echo htmlspecialchars($image['file_path']); ?></span>
            </div>
          <?php endif; ?>
          <input type="file" id="photo" name="photo" accept="image/*" <?php echo $editing ? '' : 'required'; ?>>
          <div class="field-hint">JPG, PNG, GIF, WEBP or SVG, up to 5MB. Landscape (4:3) photos work best.</div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-gold"><?php echo $editing ? 'Save Changes' : 'Add Photo'; ?></button>
        <a href="gallery.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
