<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Change Password";
$active_nav = "settings";

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();

    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($current === '' || $new === '' || $confirm === '') {
        $errors[] = "Please fill in all fields.";
    } elseif (strlen($new) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    } elseif ($new !== $confirm) {
        $errors[] = "New password and confirmation do not match.";
    } else {
        $admin = ds_read_object('admin');

        if (!$admin || !password_verify($current, $admin['password_hash'])) {
            $errors[] = "Current password is incorrect.";
        } else {
            $admin['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
            if (ds_write_object('admin', $admin)) {
                $success = true;
            } else {
                $errors[] = "Could not save the new password — check that the /data folder is writable.";
            }
        }
    }
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="panel" style="max-width:520px;">
  <div class="panel-header">
    <h2>Change Password</h2>
  </div>
  <div class="panel-body">
    <?php if ($success): ?>
      <div class="alert alert-success">Your password has been changed successfully.</div>
    <?php endif; ?>
    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
    <?php endforeach; ?>

    <form method="POST" action="change_password.php">
      <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
      <div class="field">
        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
      </div>
      <div class="field">
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" required autocomplete="new-password">
        <div class="field-hint">At least 8 characters.</div>
      </div>
      <div class="field">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-gold">Update Password</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
