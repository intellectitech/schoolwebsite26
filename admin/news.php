<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'news';
$categories = $pdo->query('SELECT * FROM news_categories ORDER BY name')->fetchAll();
$notice = '';
$editRow = null;

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'DELETE', 'news', $id, 'Deleted news article');
    header('Location: news.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int) ($_POST['id'] ?? 0);
    $title         = trim($_POST['title'] ?? '');
    $excerpt       = trim($_POST['excerpt'] ?? '');
    $body          = trim($_POST['body'] ?? '');
    $featuredImage = trim($_POST['featured_image'] ?? '');
    $categoryId    = $_POST['category_id'] ? (int) $_POST['category_id'] : 0;
    $isPublished   = isset($_POST['is_published']) ? 1 : 0;
    $isFeatured    = isset($_POST['is_featured']) ? 1 : 0;
    $publishedAt   = $_POST['published_at'] ?: date('Y-m-d H:i:s');
    $slug          = makeSlug($title);
    $authorId      = (int) $_SESSION['admin_id'];

    if ($title === '') {
        $notice = 'Title is required.';
    } elseif (!$categoryId) {
        $notice = 'Please select a category.';
    } else {
        if ($id) {
            $pdo->prepare(
                'UPDATE news SET category_id=?, title=?, slug=?, excerpt=?, body=?, featured_image=?, author_id=?, is_published=?, is_featured=?, published_at=?, updated_at=NOW() WHERE id=?'
            )->execute([$categoryId, $title, $slug, $excerpt, $body, $featuredImage, $authorId, $isPublished, $isFeatured, $publishedAt, $id]);
            auditLog($pdo, $_SESSION['admin_id'], 'UPDATE', 'news', $id, 'Updated: ' . $title);
        } else {
            $pdo->prepare(
                'INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, author_id, views, is_published, is_featured, published_at)
                 VALUES (?,?,?,?,?,?,?,0,?,?,?)'
            )->execute([$categoryId, $title, $slug, $excerpt, $body, $featuredImage, $authorId, $isPublished, $isFeatured, $publishedAt]);
            auditLog($pdo, $_SESSION['admin_id'], 'INSERT', 'news', $pdo->lastInsertId(), 'Created: ' . $title);
        }
        header('Location: news.php?msg=saved');
        exit;
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM news WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch();
}

$newsList = $pdo->query(
    'SELECT n.*, nc.name AS cat_name, a.name AS author_name
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     LEFT JOIN admin_users a ON a.id = n.author_id
     ORDER BY n.published_at DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>News Articles — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>News Articles</h1>
    <p class="admin-sub">Create, edit, and publish news that shows up on the site.</p>

    <?php if ($notice): ?><div class="admin-alert admin-alert-error"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?><div class="admin-alert admin-alert-success">Saved successfully.</div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?><div class="admin-alert admin-alert-success">Article deleted.</div><?php endif; ?>

    <div class="admin-card">
        <h2 style="margin-bottom:10px"><?= $editRow ? 'Edit Article' : 'Add New Article' ?></h2>
        <form method="POST" class="admin-form">
            <input type="hidden" name="id" value="<?= $editRow['id'] ?? '' ?>">
            <label>Title</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>">

            <label>Category</label>
            <select name="category_id" required>
                <option value="">— Select —</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= (($editRow['category_id'] ?? null) == $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Excerpt (short summary shown on cards)</label>
            <textarea name="excerpt" rows="2"><?= htmlspecialchars($editRow['excerpt'] ?? '') ?></textarea>

            <label>Full Article Body</label>
            <textarea name="body" rows="6"><?= htmlspecialchars($editRow['body'] ?? '') ?></textarea>

            <label>Featured Image (path, e.g. assets/images/image1.jpg)</label>
            <input type="text" name="featured_image" value="<?= htmlspecialchars($editRow['featured_image'] ?? '') ?>">

            <label>Published Date</label>
            <input type="text" name="published_at" placeholder="YYYY-MM-DD HH:MM:SS"
                   value="<?= htmlspecialchars($editRow['published_at'] ?? date('Y-m-d H:i:s')) ?>">

            <label style="display:flex;align-items:center;gap:8px;margin-top:16px">
                <input type="checkbox" name="is_published" style="width:auto"
                       <?= (!isset($editRow) || $editRow['is_published']) ? 'checked' : '' ?>>
                Published (visible on the site)
            </label>
            <label style="display:flex;align-items:center;gap:8px;margin-top:8px">
                <input type="checkbox" name="is_featured" style="width:auto"
                       <?= (!empty($editRow['is_featured'])) ? 'checked' : '' ?>>
                Featured
            </label>

            <div style="margin-top:18px">
                <button type="submit" class="admin-btn admin-btn-primary"><?= $editRow ? 'Update Article' : 'Add Article' ?></button>
                <?php if ($editRow): ?><a href="news.php" class="admin-btn">Cancel</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom:14px">All Articles</h2>
        <table class="admin-table">
            <thead><tr><th>Image</th><th>Title</th><th>Category</th><th>Author</th><th>Date</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($newsList as $n): ?>
                <tr>
                    <td><img class="thumb" src="../<?= htmlspecialchars($n['featured_image'] ?: '') ?>" onerror="this.style.display='none'"></td>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td><?= htmlspecialchars($n['cat_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($n['author_name'] ?? 'Admin') ?></td>
                    <td><?= date('d M Y', strtotime($n['published_at'])) ?></td>
                    <td><?= $n['is_published'] ? 'Published' : 'Draft' ?></td>
                    <td>
                        <a href="news.php?edit=<?= $n['id'] ?>" class="admin-btn admin-btn-edit admin-btn-sm">Edit</a>
                        <a href="news.php?delete=<?= $n['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm"
                           onclick="return confirm('Delete this article?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
