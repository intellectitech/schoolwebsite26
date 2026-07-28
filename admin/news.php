<?php
// ============================================================
//  admin/news.php — Manage News Articles
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// ── HANDLE ACTIONS (publish/unpublish, delete, add category) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
  $id = (int) ($_POST['id'] ?? 0);
  $action = $_POST['action'] ?? '';

  if ($id && $action === 'publish') {
    $pdo->prepare('UPDATE news SET is_published = 1 WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'news', $id, 'Published article');
  } elseif ($id && $action === 'unpublish') {
    $pdo->prepare('UPDATE news SET is_published = 0 WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'news', $id, 'Unpublished article');
  } elseif ($id && $action === 'delete') {
    $imgStmt = $pdo->prepare('SELECT featured_image FROM news WHERE id = ?');
    $imgStmt->execute([$id]);
    $img = $imgStmt->fetchColumn();
    $pdo->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
    deleteNewsImageFile($img);
    auditLog($pdo, $_SESSION['admin_id'], 'delete', 'news', $id, 'Deleted article');
  } elseif ($action === 'add_category') {
    $catName = clean($_POST['category_name'] ?? '');
    $catColor = clean($_POST['category_color'] ?? '#1565C0');
    if ($catName !== '' && preg_match('/^#[0-9a-fA-F]{6}$/', $catColor)) {
      $catSlug = uniqueSlug($pdo, 'news_categories', 'slug', slugify($catName));
      $pdo->prepare('INSERT INTO news_categories (name, slug, color) VALUES (?, ?, ?)')
        ->execute([$catName, $catSlug, $catColor]);
      auditLog($pdo, $_SESSION['admin_id'], 'insert', 'news_categories', $pdo->lastInsertId(), 'Added category: ' . $catName);
      setFlashSuccess('Category "' . $catName . '" added.');
    } else {
      setFlashErrors(['Please give the category a name.']);
    }
  }
  redirectTo('news.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
}

$flash = getFlash();

// ── FILTER TABS ────────────────────────────────────────────
$status = $_GET['status'] ?? 'all';
$where = '';
if ($status === 'published')
  $where = 'WHERE is_published = 1';
if ($status === 'draft')
  $where = 'WHERE is_published = 0';

$counts = [
  'all' => (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn(),
  'published' => (int) $pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 1')->fetchColumn(),
  'draft' => (int) $pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 0')->fetchColumn(),
];

// ── LIST ──────────────────────────────────────────────────
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$total = (int) $pdo->query("SELECT COUNT(*) FROM news {$where}")->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));

$stmt = $pdo->prepare(
  "SELECT n.*, nc.name AS cat_name, nc.color AS cat_color
     FROM news n LEFT JOIN news_categories nc ON nc.id = n.category_id
     {$where}
     ORDER BY n.published_at DESC
     LIMIT ? OFFSET ?"
);
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM news_categories ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News &amp; Stories · Admin · Uganda Martyrs Primary School</title>
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
          <h1>News &amp; Stories</h1>
          <p><?= $total ?> article<?= $total === 1 ? '' : 's' ?></p>
        </div>
        <a href="news-form.php" class="btn btn-primary">+ New Article</a>
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

      <div class="news-cat-tabs" role="group" aria-label="Filter articles by status">
        <a href="news.php" class="cat-tab <?= $status === 'all' ? 'active' : '' ?>">All (<?= $counts['all'] ?>)</a>
        <a href="news.php?status=published" class="cat-tab <?= $status === 'published' ? 'active' : '' ?>">Published
          (<?= $counts['published'] ?>)</a>
        <a href="news.php?status=draft" class="cat-tab <?= $status === 'draft' ? 'active' : '' ?>">Drafts
          (<?= $counts['draft'] ?>)</a>
      </div>

      <section class="admin-panel">
        <?php if ($articles): ?>
          <table class="admin-table admin-table-news">
            <thead>
              <tr>
                <th></th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Published</th>
                <th>Views</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($articles as $a): ?>
                <?php $hasRealImage = $a['featured_image'] && file_exists(__DIR__ . '/../' . $a['featured_image']); ?>
                <tr>
                  <td>
                    <?php if ($hasRealImage): ?>
                      <img class="admin-thumb" src="../<?= htmlspecialchars($a['featured_image']) ?>" alt="">
                    <?php else: ?>
                      <span class="admin-thumb admin-thumb-color"
                        style="background:<?= htmlspecialchars($a['cat_color'] ?? '#16233D') ?>"></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= htmlspecialchars($a['title']) ?>
                    <?php if ($a['is_featured']): ?><span class="admin-badge admin-badge-featured">★
                        Featured</span><?php endif; ?>
                  </td>
                  <td><span class="admin-badge"
                      style="background:<?= htmlspecialchars($a['cat_color'] ?? '#999') ?>;color:#fff"><?= htmlspecialchars($a['cat_name'] ?? '—') ?></span>
                  </td>
                  <td><span
                      class="admin-badge admin-badge-<?= $a['is_published'] ? 'published' : 'draft' ?>"><?= $a['is_published'] ? 'Published' : 'Draft' ?></span>
                  </td>
                  <td><?= date('d M Y', strtotime($a['published_at'])) ?></td>
                  <td><?= (int) $a['views'] ?></td>
                  <td class="admin-table-actions">
                    <a href="news-form.php?edit=<?= (int) $a['id'] ?>">Edit</a>
                    <form method="POST" action="news.php<?= $status !== 'all' ? '?status=' . urlencode($status) : '' ?>">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                      <input type="hidden" name="action" value="<?= $a['is_published'] ? 'unpublish' : 'publish' ?>">
                      <button type="submit" class="link-btn"><?= $a['is_published'] ? 'Unpublish' : 'Publish' ?></button>
                    </form>
                    <form method="POST" action="news.php<?= $status !== 'all' ? '?status=' . urlencode($status) : '' ?>"
                      onsubmit="return confirm('Delete this article permanently? This cannot be undone.');">
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

          <?php if ($totalPages > 1): ?>
            <div class="news-pagination">
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="pagination-btn <?= $i === $page ? 'active' : '' ?>"
                  href="news.php?page=<?= $i ?><?= $status !== 'all' ? '&status=' . urlencode($status) : '' ?>"><?= $i ?></a>
              <?php endfor; ?>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <p class="admin-empty">No articles here yet.</p>
        <?php endif; ?>
      </section>

      <section class="admin-panel admin-panel-narrow">
        <div class="admin-panel-head">
          <h2>Categories</h2>
        </div>
        <ul class="admin-category-list">
          <?php foreach ($categories as $cat): ?>
            <li><span class="admin-color-dot"
                style="background:<?= htmlspecialchars($cat['color']) ?>"></span><?= htmlspecialchars($cat['name']) ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <form method="POST" action="news.php" class="admin-inline-form">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="add_category">
          <input type="text" name="category_name" placeholder="New category name" maxlength="100" required>
          <input type="color" name="category_color" value="#1565C0" title="Category color">
          <button type="submit" class="btn btn-ghost">Add Category</button>
        </form>
      </section>
    </main>
  </div>
</body>

</html>