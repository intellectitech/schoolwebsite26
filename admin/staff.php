<?php
// ============================================================
//  admin/staff.php — Manage Staff Directory
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// ── HANDLE ACTIONS (toggle active, delete, add department) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
  $action = $_POST['action'] ?? '';

  if ($action === 'add_department') {
    $deptName = clean($_POST['department_name'] ?? '');
    $deptHead = clean($_POST['department_head'] ?? '');
    if ($deptName !== '') {
      $pdo->prepare('INSERT INTO departments (name, description, head_of_dept) VALUES (?, ?, ?)')
        ->execute([$deptName, '', $deptHead]);
      auditLog($pdo, $_SESSION['admin_id'], 'insert', 'departments', $pdo->lastInsertId(), 'Added department: ' . $deptName);
      setFlashSuccess('Department added.');
    } else {
      setFlashErrors(['Please give the department a name.']);
    }
    redirectTo('staff.php');
  }

  $id = (int) ($_POST['id'] ?? 0);

  if ($id && $action === 'toggle_active') {
    $pdo->prepare('UPDATE staff SET is_active = NOT is_active WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'staff', $id, 'Toggled active status');
  } elseif ($id && $action === 'delete') {
    $photoStmt = $pdo->prepare('SELECT photo FROM staff WHERE id = ?');
    $photoStmt->execute([$id]);
    $photo = $photoStmt->fetchColumn();
    $pdo->prepare('DELETE FROM staff WHERE id = ?')->execute([$id]);
    deleteStaffImageFile($photo);
    auditLog($pdo, $_SESSION['admin_id'], 'delete', 'staff', $id, 'Deleted staff member');
    setFlashSuccess('Staff member removed.');
  }
  redirectTo('staff.php');
}

$flash = getFlash();

// ── LIST ──────────────────────────────────────────────────
$staff = $pdo->query(
  'SELECT s.*, d.name AS department_name
     FROM staff s LEFT JOIN departments d ON d.id = s.department_id
     ORDER BY s.is_active DESC, s.sort_order ASC, s.last_name ASC'
)->fetchAll();
$departments = $pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff · Admin · Uganda Martyrs Primary School</title>
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
          <h1>Staff</h1>
          <p><?= count($staff) ?> member<?= count($staff) === 1 ? '' : 's' ?></p>
        </div>
        <a href="staff-form.php" class="btn btn-primary">+ New Staff Member</a>
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
        <?php if ($staff): ?>
          <table class="admin-table admin-table-news">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Department</th>
                <th>Role</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($staff as $s): ?>
                <?php $hasRealImage = $s['photo'] && file_exists(__DIR__ . '/../' . $s['photo']); ?>
                <tr>
                  <td>
                    <?php if ($hasRealImage): ?>
                      <img class="admin-thumb" src="../<?= htmlspecialchars($s['photo']) ?>" alt="">
                    <?php else: ?>
                      <span class="admin-thumb admin-thumb-color" style="background:#A6402E"></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= htmlspecialchars(trim($s['title'] . ' ' . $s['first_name'] . ' ' . $s['last_name'])) ?>
                    <?php if ($s['is_managemnet']): ?><span class="admin-badge admin-badge-featured">Management</span><?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($s['department_name'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($s['role']) ?></td>
                  <td><span
                      class="admin-badge admin-badge-<?= $s['is_active'] ? 'published' : 'draft' ?>"><?= $s['is_active'] ? 'Active' : 'Inactive' ?></span>
                  </td>
                  <td class="admin-table-actions">
                    <a href="staff-form.php?edit=<?= (int) $s['id'] ?>">Edit</a>
                    <form method="POST" action="staff.php">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                      <input type="hidden" name="action" value="toggle_active">
                      <button type="submit" class="link-btn"><?= $s['is_active'] ? 'Deactivate' : 'Activate' ?></button>
                    </form>
                    <form method="POST" action="staff.php"
                      onsubmit="return confirm('Delete this staff member permanently?');">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                      <input type="hidden" name="action" value="delete">
                      <button type="submit" class="link-btn link-btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="admin-empty">No staff members yet. <a href="staff-form.php">Add the first one →</a></p>
        <?php endif; ?>
      </section>

      <section class="admin-panel admin-panel-narrow">
        <div class="admin-panel-head">
          <h2>Departments</h2>
        </div>
        <ul class="admin-category-list">
          <?php foreach ($departments as $dept): ?>
            <li><span class="admin-color-dot" style="background:#2F6B4F"></span><?= htmlspecialchars($dept['name']) ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <form method="POST" action="staff.php" class="admin-inline-form">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="add_department">
          <input type="text" name="department_name" placeholder="New department name" maxlength="150" required>
          <input type="text" name="department_head" placeholder="Head of department (optional)" maxlength="200">
          <button type="submit" class="btn btn-ghost">Add Department</button>
        </form>
      </section>
    </main>
  </div>
</body>

</html>
