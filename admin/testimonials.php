<?php
// ============================================================
//  admin/testimonials.php — Manage Testimonials
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// ── HANDLE ACTIONS (toggle published, delete) ────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
  $action = $_POST['action'] ?? '';
  $id = (int) ($_POST['id'] ?? 0);

  if ($id && $action === 'toggle_published') {
    $pdo->prepare('UPDATE testimonials SET is_published = NOT is_published WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'testimonials', $id, 'Toggled published status');
  } elseif ($id && $action === 'delete') {
    $photoStmt = $pdo->prepare('SELECT photo, author_name FROM testimonials WHERE id = ?');
    $photoStmt->execute([$id]);
    $row = $photoStmt->fetch();
    $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
    if ($row) {
      deleteTestimonialImageFile($row['photo']);
      auditLog($pdo, $_SESSION['admin_id'], 'delete', 'testimonials', $id, 'Deleted testimonial from: ' . $row['author_name']);
    }
    setFlashSuccess('Testimonial removed.');
  }
  redirectTo('testimonials.php');
}

$flash = getFlash();

// ── LIST ──────────────────────────────────────────────────
$testimonials = $pdo->query(
  'SELECT * FROM testimonials ORDER BY is_published DESC, sort_order ASC, id DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Testimonials · Admin · Uganda Martyrs Primary School</title>
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
          <h1>Testimonials</h1>
          <p><?= count($testimonials) ?> testimonial<?= count($testimonials) === 1 ? '' : 's' ?></p>
        </div>
        <a href="testimonial-form.php" class="btn btn-primary">+ New Testimonial</a>
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

      <section class="admin-panel">
        <?php if ($testimonials): ?>
          <table class="admin-table admin-table-news">
            <thead>
              <tr>
                <th></th>
                <th>Author</th>
                <th>Role</th>
                <th>Quote</th>
                <th>Rating</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($testimonials as $t): ?>
                <?php $hasRealImage = $t['photo'] && file_exists(__DIR__ . '/../' . $t['photo']); ?>
                <tr>
                  <td>
                    <?php if ($hasRealImage): ?>
                      <img class="admin-thumb" src="../<?= htmlspecialchars($t['photo']) ?>" alt="">
                    <?php else: ?>
                      <span class="admin-thumb admin-thumb-color" style="background:#A6402E"></span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($t['author_name']) ?></td>
                  <td><?= htmlspecialchars($t['author_role']) ?></td>
                  <td><?= htmlspecialchars(excerpt($t['content'], 70)) ?></td>
                  <td><?= str_repeat('★', max(0, min(5, (int) $t['rating']))) . str_repeat('☆', 5 - max(0, min(5, (int) $t['rating']))) ?></td>
                  <td><span
                      class="admin-badge admin-badge-<?= $t['is_published'] ? 'published' : 'draft' ?>"><?= $t['is_published'] ? 'Published' : 'Hidden' ?></span>
                  </td>
                  <td class="admin-table-actions">
                    <a href="testimonial-form.php?edit=<?= (int) $t['id'] ?>">Edit</a>
                    <form method="POST" action="testimonials.php">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                      <input type="hidden" name="action" value="toggle_published">
                      <button type="submit" class="link-btn"><?= $t['is_published'] ? 'Hide' : 'Publish' ?></button>
                    </form>
                    <form method="POST" action="testimonials.php"
                      onsubmit="return confirm('Delete this testimonial permanently?');">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                      <input type="hidden" name="action" value="delete">
                      <button type="submit" class="link-btn link-btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="admin-empty">No testimonials yet. <a href="testimonial-form.php">Add the first one →</a></p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>

</html>
