<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Admissions Inquiries";
$active_nav = "admissions";

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    admin_verify_csrf();
    $id = $_POST['inquiry_id'] ?? null;
    $status = $_POST['status'] ?? 'new';
    $validStatuses = ['new', 'contacted', 'enrolled', 'closed'];
    if ($id && $pdo && in_array($status, $validStatuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE admissions_inquiries SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inquiry status updated.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not update status.'];
        }
    }
    header('Location: admissions.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

$filter = $_GET['status'] ?? 'all';
$validFilters = ['all', 'new', 'contacted', 'enrolled', 'closed'];
if (!in_array($filter, $validFilters)) { $filter = 'all'; }

$inquiries = [];
if ($pdo) {
    try {
        if ($filter === 'all') {
            $inquiries = $pdo->query("SELECT * FROM admissions_inquiries ORDER BY submitted_at DESC")->fetchAll();
        } else {
            $stmt = $pdo->prepare("SELECT * FROM admissions_inquiries WHERE status = :status ORDER BY submitted_at DESC");
            $stmt->execute([':status' => $filter]);
            $inquiries = $stmt->fetchAll();
        }
    } catch (Exception $e) { $inquiries = []; }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['message']); ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h2>Admission Inquiries (<?php echo count($inquiries); ?>)</h2>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
      <?php foreach (['all'=>'All','new'=>'New','contacted'=>'Contacted','enrolled'=>'Enrolled','closed'=>'Closed'] as $key => $label): ?>
        <a href="admissions.php?status=<?php echo $key; ?>" class="btn btn-sm <?php echo $filter === $key ? 'btn-primary' : 'btn-outline'; ?>"><?php echo $label; ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="table-wrap">
    <?php if (empty($inquiries)): ?>
      <div class="empty-state"><div class="ic">&#127891;</div>No admission inquiries<?php echo $filter !== 'all' ? ' with this status' : ''; ?> yet.</div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Parent</th><th>Child</th><th>Class</th><th>Message</th><th>Submitted</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($inquiries as $row): ?>
          <tr>
            <td>
              <strong><?php echo htmlspecialchars($row['parent_name']); ?></strong><br>
              <span style="color:var(--text-light); font-size:0.8rem;"><?php echo htmlspecialchars($row['parent_email']); ?><br><?php echo htmlspecialchars($row['parent_phone']); ?></span>
            </td>
            <td><?php echo htmlspecialchars($row['child_name']); ?></td>
            <td><?php echo htmlspecialchars($row['desired_class']); ?></td>
            <td style="max-width:220px; white-space:normal;"><?php echo htmlspecialchars($row['message'] ?: '—'); ?></td>
            <td><?php echo date('d M Y', strtotime($row['submitted_at'])); ?></td>
            <td>
              <form action="admissions.php<?php echo $filter !== 'all' ? '?status=' . urlencode($filter) : ''; ?>" method="POST" style="display:flex; gap:6px; align-items:center;">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="inquiry_id" value="<?php echo $row['id']; ?>">
                <select name="status" onchange="this.form.submit()" style="padding:6px 8px; border-radius:6px; border:1.5px solid var(--border); font-size:0.8rem;">
                  <?php foreach (['new','contacted','enrolled','closed'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $row['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                  <?php endforeach; ?>
                </select>
                <input type="hidden" name="update_status" value="1">
                <noscript><button type="submit" class="btn btn-sm btn-outline">Update</button></noscript>
              </form>
            </td>
            <td class="row-actions">
              <form action="admissions_delete.php" method="POST" class="js-confirm-delete" data-confirm="Delete this admission inquiry permanently? This cannot be undone.">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <?php if ($filter !== 'all'): ?><input type="hidden" name="status_filter" value="<?php echo htmlspecialchars($filter); ?>"><?php endif; ?>
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
