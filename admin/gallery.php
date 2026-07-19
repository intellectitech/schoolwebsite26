<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Gallery";
$active_nav = "gallery";

$images = ds_read('gallery');
usort($images, function ($a, $b) { return strtotime($b['uploaded_at'] ?? 'now') - strtotime($a['uploaded_at'] ?? 'now'); });

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['message']); ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h2>All Photos (<?php echo count($images); ?>)</h2>
    <a href="gallery_edit.php" class="btn btn-gold btn-sm">+ Add Photo</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($images)): ?>
      <div class="empty-state"><div class="ic">&#128247;</div>No gallery photos yet. <br><a href="gallery_edit.php" class="btn btn-primary btn-sm" style="margin-top:14px;">Add your first photo</a></div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Photo</th><th>Title</th><th>Category</th><th>Uploaded</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($images as $img): ?>
          <tr>
            <td><img src="<?php echo htmlspecialchars('../' . $img['file_path']); ?>" alt="" class="thumb"></td>
            <td><strong><?php echo htmlspecialchars($img['title']); ?></strong><br><span style="color:var(--text-light); font-size:0.8rem;"><?php echo htmlspecialchars($img['caption']); ?></span></td>
            <td><?php echo htmlspecialchars($img['category'] ?? '—'); ?></td>
            <td><?php echo date('d M Y', strtotime($img['uploaded_at'] ?? 'now')); ?></td>
            <td class="row-actions">
              <a href="gallery_edit.php?id=<?php echo $img['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
              <form action="gallery_delete.php" method="POST" class="js-confirm-delete" data-confirm="Delete this photo? This cannot be undone.">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="id" value="<?php echo $img['id']; ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
