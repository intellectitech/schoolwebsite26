<?php
// ============================================================
//  admin/messages.php — Manage Contact Messages
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// ── HANDLE ACTIONS (mark read/unread, delete) ────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && $action === 'mark_read') {
        $pdo->prepare('UPDATE contact_messages SET is_read = 1, replied_at = NOW() WHERE id = ?')->execute([$id]);
        auditLog($pdo, $_SESSION['admin_id'], 'update', 'contact_messages', $id, 'Marked as read/replied');
    } elseif ($id && $action === 'mark_unread') {
        $pdo->prepare('UPDATE contact_messages SET is_read = 0, replied_at = NULL WHERE id = ?')->execute([$id]);
        auditLog($pdo, $_SESSION['admin_id'], 'update', 'contact_messages', $id, 'Marked as unread');
    } elseif ($id && $action === 'delete') {
        $pdo->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
        auditLog($pdo, $_SESSION['admin_id'], 'delete', 'contact_messages', $id, 'Deleted message');
    }
    header('Location: messages.php');
    exit;
}

// ── LIST ──────────────────────────────────────────────────────
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;

$total   = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));

$stmt = $pdo->prepare('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?');
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll();

// ── SINGLE MESSAGE VIEW ─────────────────────────────────────
$viewing = null;
if (isset($_GET['view'])) {
    $vStmt = $pdo->prepare('SELECT * FROM contact_messages WHERE id = ?');
    $vStmt->execute([(int) $_GET['view']]);
    $viewing = $vStmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Messages · Admin · Uganda Martyrs Primary School</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <h1>Contact Messages</h1>
      <p><?= $total ?> total message<?= $total === 1 ? '' : 's' ?></p>
    </header>

    <?php if ($viewing): ?>
      <section class="admin-panel admin-message-detail">
        <div class="admin-panel-head">
          <h2><?= htmlspecialchars($viewing['subject']) ?></h2>
          <a href="messages.php">← Back to all messages</a>
        </div>
        <dl class="admin-detail-list">
          <dt>From</dt><dd><?= htmlspecialchars($viewing['name']) ?></dd>
          <dt>Email</dt><dd><a href="mailto:<?= htmlspecialchars($viewing['email']) ?>"><?= htmlspecialchars($viewing['email']) ?></a></dd>
          <?php if ($viewing['phone']): ?><dt>Phone</dt><dd><?= htmlspecialchars($viewing['phone']) ?></dd><?php endif; ?>
          <dt>Received</dt><dd><?= date('l, d F Y \a\t H:i', strtotime($viewing['created_at'])) ?></dd>
          <dt>Status</dt><dd><?= $viewing['is_read'] ? 'Read' : 'Unread' ?></dd>
        </dl>
        <p class="admin-message-body"><?= nl2br(htmlspecialchars($viewing['message'])) ?></p>
        <div class="admin-actions">
          <form method="POST" action="messages.php">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int) $viewing['id'] ?>">
            <input type="hidden" name="action" value="<?= $viewing['is_read'] ? 'mark_unread' : 'mark_read' ?>">
            <button type="submit" class="btn btn-ghost"><?= $viewing['is_read'] ? 'Mark as unread' : 'Mark as read' ?></button>
          </form>
          <form method="POST" action="messages.php" onsubmit="return confirm('Delete this message? This cannot be undone.');">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= (int) $viewing['id'] ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      </section>
    <?php endif; ?>

    <section class="admin-panel">
      <?php if ($messages): ?>
        <table class="admin-table">
          <thead><tr><th>Status</th><th>Name</th><th>Subject</th><th>Received</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($messages as $m): ?>
              <tr class="<?= $m['is_read'] ? '' : 'admin-row-unread' ?>">
                <td><?php if (!$m['is_read']): ?><span class="admin-badge admin-badge-new">New</span><?php endif; ?></td>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['subject']) ?></td>
                <td><?= date('d M Y, H:i', strtotime($m['created_at'])) ?></td>
                <td><a href="messages.php?view=<?= (int) $m['id'] ?>">View</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
          <div class="news-pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a class="pagination-btn <?= $i === $page ? 'active' : '' ?>" href="messages.php?page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <p class="admin-empty">No messages yet.</p>
      <?php endif; ?>
    </section>
  </main>
</div>
</body>
</html>
