<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'testimonials';
$editRow = null;
$notice = '';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
    header('Location: testimonials.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int) ($_POST['id'] ?? 0);
    $authorName  = trim($_POST['author_name'] ?? '');
    $authorRole  = trim($_POST['author_role'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $photo       = trim($_POST['photo'] ?? '');
    $rating      = (int) ($_POST['rating'] ?? 5);
    $sortOrder   = (int) ($_POST['sort_order'] ?? 0);
    $isPublished = isset($_POST['is_published']) ? 1 : 0;

    if ($authorName === '' || $content === '') {
        $notice = 'Author name and testimonial content are required.';
    } else {
        if ($id) {
            $pdo->prepare(
                'UPDATE testimonials SET author_name=?, author_role=?, photo=?, content=?, rating=?, sort_order=?, is_published=? WHERE id=?'
            )->execute([$authorName, $authorRole, $photo, $content, $rating, $sortOrder, $isPublished, $id]);
        } else {
            $pdo->prepare(
                'INSERT INTO testimonials (author_name, author_role, photo, content, rating, sort_order, is_published) VALUES (?,?,?,?,?,?,?)'
            )->execute([$authorName, $authorRole, $photo, $content, $rating, $sortOrder, $isPublished]);
        }
        header('Location: testimonials.php?msg=saved');
        exit;
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch();
}

$list = $pdo->query('SELECT * FROM testimonials ORDER BY sort_order ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Testimonials — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Testimonials</h1>
    <p class="admin-sub">What parents and alumni say about the school.</p>

    <?php if ($notice): ?><div class="admin-alert admin-alert-error"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg'])): ?><div class="admin-alert admin-alert-success">Saved successfully.</div><?php endif; ?>

    <div class="admin-card">
        <h2 style="margin-bottom:10px"><?= $editRow ? 'Edit Testimonial' : 'Add New Testimonial' ?></h2>
        <form method="POST" class="admin-form">
            <input type="hidden" name="id" value="<?= $editRow['id'] ?? '' ?>">
            <label>Author Name</label>
            <input type="text" name="author_name" required value="<?= htmlspecialchars($editRow['author_name'] ?? '') ?>">

            <label>Author Role (e.g. Parent, Alumna)</label>
            <input type="text" name="author_role" value="<?= htmlspecialchars($editRow['author_role'] ?? '') ?>">

            <label>Photo path (optional)</label>
            <input type="text" name="photo" value="<?= htmlspecialchars($editRow['photo'] ?? '') ?>">

            <label>Testimonial</label>
            <textarea name="content" rows="4" required><?= htmlspecialchars($editRow['content'] ?? '') ?></textarea>

            <label>Rating (1–5)</label>
            <input type="text" name="rating" value="<?= htmlspecialchars($editRow['rating'] ?? '5') ?>">

            <label>Sort Order</label>
            <input type="text" name="sort_order" value="<?= htmlspecialchars($editRow['sort_order'] ?? '0') ?>">

            <label style="display:flex;align-items:center;gap:8px;margin-top:16px">
                <input type="checkbox" name="is_published" style="width:auto"
                       <?= (!isset($editRow) || $editRow['is_published']) ? 'checked' : '' ?>>
                Published
            </label>

            <div style="margin-top:18px">
                <button type="submit" class="admin-btn admin-btn-primary"><?= $editRow ? 'Update' : 'Add Testimonial' ?></button>
                <?php if ($editRow): ?><a href="testimonials.php" class="admin-btn">Cancel</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom:14px">All Testimonials</h2>
        <table class="admin-table">
            <thead><tr><th>Author</th><th>Role</th><th>Rating</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($list as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['author_name']) ?></td>
                    <td><?= htmlspecialchars($t['author_role']) ?></td>
                    <td><?= (int)$t['rating'] ?>/5</td>
                    <td><?= $t['is_published'] ? 'Published' : 'Draft' ?></td>
                    <td>
                        <a href="testimonials.php?edit=<?= $t['id'] ?>" class="admin-btn admin-btn-edit admin-btn-sm">Edit</a>
                        <a href="testimonials.php?delete=<?= $t['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm"
                           onclick="return confirm('Delete this testimonial?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
