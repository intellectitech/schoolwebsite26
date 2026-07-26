<?php
// ============================================================
//  admin/enquiries.php — Manage Admission Enquiries
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

$validStatuses = ['new', 'contacted', 'enrolled', 'declined'];

// ── HANDLE ACTIONS (update status, save notes) ───────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && $action === 'update_status') {
        $status = $_POST['status'] ?? '';
        $notes  = clean($_POST['admin_notes'] ?? '');
        if (in_array($status, $validStatuses, true)) {
            $pdo->prepare('UPDATE admission_enquiries SET status = ?, admin_notes = ? WHERE id = ?')
                ->execute([$status, $notes, $id]);
            auditLog($pdo, $_SESSION['admin_id'], 'update', 'admission_enquiries', $id, 'Status set to ' . $status);
        }
    } elseif ($id && $action === 'delete') {
        $pdo->prepare('DELETE FROM admission_enquiries WHERE id = ?')->execute([$id]);
        auditLog($pdo, $_SESSION['admin_id'], 'delete', 'admission_enquiries', $id, 'Deleted enquiry');
    }
    header('Location: enquiries.php' . ($id && $action === 'update_status' ? '?view=' . $id : ''));
    exit;
}

// ── FILTER + LIST ─────────────────────────────────────────────
$statusFilter = $_GET['status'] ?? '';
$where  = '';
$params = [];
if (in_array($statusFilter, $validStatuses, true)) {
    $where = 'WHERE status = ?';
    $params[] = $statusFilter;
}

$stmt = $pdo->prepare("SELECT * FROM admission_enquiries $where ORDER BY created_at DESC, id DESC");
$stmt->execute($params);
$enquiries = $stmt->fetchAll();

$viewing = null;
if (isset($_GET['view'])) {
    $vStmt = $pdo->prepare('SELECT * FROM admission_enquiries WHERE id = ?');
    $vStmt->execute([(int) $_GET['view']]);
    $viewing = $vStmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admission Enquiries · Admin · Uganda Martyrs Primary School</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <h1>Admission Enquiries</h1>
      <p><?= count($enquiries) ?> enquir<?= count($enquiries) === 1 ? 'y' : 'ies' ?><?= $statusFilter ? ' — ' . htmlspecialchars(ucfirst($statusFilter)) : '' ?></p>
    </header>

    <?php if ($viewing): ?>
      <section class="admin-panel admin-message-detail">
        <div class="admin-panel-head">
          <h2><?= htmlspecialchars($viewing['student_name']) ?> — <?= htmlspecialchars($viewing['entry_level']) ?></h2>
          <a href="enquiries.php">← Back to all enquiries</a>
        </div>
        <dl class="admin-detail-list">
          <dt>Parent / Guardian</dt><dd><?= htmlspecialchars($viewing['parent_name']) ?></dd>
          <dt>Phone</dt><dd><?= htmlspecialchars($viewing['parent_phone']) ?></dd>
          <?php if ($viewing['parent_email']): ?><dt>Email</dt><dd><a href="mailto:<?= htmlspecialchars($viewing['parent_email']) ?>"><?= htmlspecialchars($viewing['parent_email']) ?></a></dd><?php endif; ?>
          <?php if ($viewing['current_school']): ?><dt>Current school</dt><dd><?= htmlspecialchars($viewing['current_school']) ?></dd><?php endif; ?>
          <dt>Received</dt><dd><?= date('l, d F Y', strtotime($viewing['created_at'])) ?></dd>
        </dl>
        <?php if ($viewing['message']): ?><p class="admin-message-body"><?= nl2br(htmlspecialchars($viewing['message'])) ?></p><?php endif; ?>

        <form method="POST" action="enquiries.php" class="admin-status-form">
          <?= csrfField() ?>
          <input type="hidden" name="id" value="<?= (int) $viewing['id'] ?>">
          <input type="hidden" name="action" value="update_status">
          <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
              <?php foreach ($validStatuses as $s): ?>
                <option value="<?= $s ?>" <?= $viewing['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label for="admin_notes">Internal notes</label>
            <textarea id="admin_notes" name="admin_notes" rows="3"><?= htmlspecialchars($viewing['admin_notes']) ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </section>
    <?php endif; ?>

    <div class="admin-filter-tabs">
      <a href="enquiries.php" class="cat-tab <?= !$statusFilter ? 'active' : '' ?>">All</a>
      <?php foreach ($validStatuses as $s): ?>
        <a href="enquiries.php?status=<?= $s ?>" class="cat-tab <?= $statusFilter === $s ? 'active' : '' ?>"><?= ucfirst($s) ?></a>
      <?php endforeach; ?>
    </div>

    <section class="admin-panel">
      <?php if ($enquiries): ?>
        <table class="admin-table">
          <thead><tr><th>Parent</th><th>Child</th><th>Grade</th><th>Phone</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($enquiries as $e): ?>
              <tr>
                <td><?= htmlspecialchars($e['parent_name']) ?></td>
                <td><?= htmlspecialchars($e['student_name']) ?></td>
                <td><?= htmlspecialchars($e['entry_level']) ?></td>
                <td><?= htmlspecialchars($e['parent_phone']) ?></td>
                <td><span class="admin-badge admin-badge-<?= htmlspecialchars($e['status']) ?>"><?= htmlspecialchars(ucfirst($e['status'])) ?></span></td>
                <td><a href="enquiries.php?view=<?= (int) $e['id'] ?>">View</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="admin-empty">No enquiries<?= $statusFilter ? ' with this status' : '' ?> yet.</p>
      <?php endif; ?>
    </section>
  </main>
</div>
</body>
</html>
