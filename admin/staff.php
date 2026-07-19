<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Staff";
$active_nav = "staff";

$staff = ds_read('staff');
usort($staff, function ($a, $b) { return ($a['display_order'] ?? 0) <=> ($b['display_order'] ?? 0); });

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['message']); ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h2>School Leadership &amp; Staff (<?php echo count($staff); ?>)</h2>
    <a href="staff_edit.php" class="btn btn-gold btn-sm">+ Add Staff Member</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($staff)): ?>
      <div class="empty-state"><div class="ic">&#128101;</div>No staff members added yet.</div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Photo</th><th>Name</th><th>Role</th><th>Order</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($staff as $member): ?>
          <tr>
            <td><img src="<?php echo htmlspecialchars('../' . ($member['photo_path'] ?: 'assets/images/staff/headteacher.svg')); ?>" alt="" class="thumb" style="border-radius:50%; width:44px; height:44px;"></td>
            <td><strong><?php echo htmlspecialchars($member['full_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($member['role_title']); ?></td>
            <td><?php echo (int) ($member['display_order'] ?? 0); ?></td>
            <td class="row-actions">
              <a href="staff_edit.php?id=<?php echo $member['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
              <form action="staff_delete.php" method="POST" class="js-confirm-delete" data-confirm="Delete this staff member?">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
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
