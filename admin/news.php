<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "News & Blog";
$active_nav = "news";

$posts = ds_read('news');
usort($posts, function ($a, $b) { return strtotime($b['published_at']) - strtotime($a['published_at']); });

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['message']); ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h2>All Posts (<?php echo count($posts); ?>)</h2>
    <a href="news_edit.php" class="btn btn-gold btn-sm">+ New Post</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($posts)): ?>
      <div class="empty-state"><div class="ic">&#128221;</div>No news or blog posts yet. <br><a href="news_edit.php" class="btn btn-primary btn-sm" style="margin-top:14px;">Create your first post</a></div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Cover</th><th>Title</th><th>Category</th><th>Status</th><th>Published</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $post): ?>
          <tr>
            <td><img src="<?php echo htmlspecialchars('../' . $post['cover_image']); ?>" alt="" class="thumb"></td>
            <td><strong><?php echo htmlspecialchars($post['title']); ?></strong><br><span style="color:var(--text-light); font-size:0.8rem;"><?php echo htmlspecialchars($post['excerpt']); ?></span></td>
            <td><?php echo htmlspecialchars($post['category'] ?? '—'); ?></td>
            <td><span class="pill pill-<?php echo !empty($post['is_published']) ? 'published' : 'draft'; ?>"><?php echo !empty($post['is_published']) ? 'Published' : 'Draft'; ?></span></td>
            <td><?php echo date('d M Y', strtotime($post['published_at'])); ?></td>
            <td class="row-actions">
              <a href="news_edit.php?id=<?php echo $post['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="../news.php?slug=<?php echo urlencode($post['slug']); ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
              <form action="news_delete.php" method="POST" class="js-confirm-delete" data-confirm="Delete this post? This cannot be undone.">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
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
