<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Manage Admins";
$active_nav = "admins";

$errors = [];
$successMsg = null;

// ---------- Add new admin ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    admin_verify_csrf();

    $newUsername = trim($_POST['new_username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_new_password'] ?? '';

    if ($newUsername === '' || $newPassword === '' || $confirmPassword === '') {
        $errors[] = "Please fill in a username and password.";
    } elseif (!preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $newUsername)) {
        $errors[] = "Username must be 3-50 characters: letters, numbers, underscores or dots only.";
    } elseif (strlen($newPassword) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = "Password and confirmation do not match.";
    } elseif ($pdo) {
        try {
            $check = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = :u");
            $check->execute([':u' => $newUsername]);
            if ($check->fetchColumn() > 0) {
                $errors[] = "That username is already taken. Please choose another.";
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (:u, :h)");
                $stmt->execute([':u' => $newUsername, ':h' => $hash]);
                $successMsg = "Admin account \"" . htmlspecialchars($newUsername) . "\" was created successfully.";
            }
        } catch (Exception $e) {
            $errors[] = "Could not create the admin account. Please try again.";
        }
    } else {
        $errors[] = "Database not connected — cannot create an account right now.";
    }
}

// ---------- Delete an admin ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admin'])) {
    admin_verify_csrf();
    $deleteId = $_POST['admin_id'] ?? null;

    if ($deleteId && $pdo) {
        if ((string) $deleteId === (string) $_SESSION['admin_id']) {
            $errors[] = "You can't delete your own account while logged in as it.";
        } else {
            try {
                $totalAdmins = (int) $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
                if ($totalAdmins <= 1) {
                    $errors[] = "You can't delete the last remaining admin account.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = :id");
                    $stmt->execute([':id' => $deleteId]);
                    $successMsg = "Admin account removed successfully.";
                }
            } catch (Exception $e) {
                $errors[] = "Could not delete that admin account.";
            }
        }
    }
}

$admins = [];
if ($pdo) {
    try {
        $admins = $pdo->query("SELECT * FROM admin_users ORDER BY created_at ASC")->fetchAll();
    } catch (Exception $e) { $admins = []; }
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($successMsg): ?><div class="alert alert-success"><?php echo $successMsg; ?></div><?php endif; ?>
<?php foreach ($errors as $err): ?>
  <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
<?php endforeach; ?>

<div class="panel">
  <div class="panel-header">
    <h2>Admin Accounts (<?php echo count($admins); ?>)</h2>
  </div>
  <div class="table-wrap">
    <?php if (empty($admins)): ?>
      <div class="empty-state"><div class="ic">&#128100;</div>No admin accounts found.</div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Username</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($admins as $admin): ?>
          <tr>
            <td>
              <strong><?php echo htmlspecialchars($admin['username']); ?></strong>
              <?php if ((string) $admin['id'] === (string) $_SESSION['admin_id']): ?>
                <span class="pill pill-published" style="margin-left:8px;">You</span>
              <?php endif; ?>
            </td>
            <td><?php echo date('d M Y', strtotime($admin['created_at'])); ?></td>
            <td class="row-actions">
              <?php if ((string) $admin['id'] === (string) $_SESSION['admin_id']): ?>
                <a href="change_password.php" class="btn btn-outline btn-sm">Change My Password</a>
              <?php elseif (count($admins) > 1): ?>
                <form action="admins.php" method="POST" class="js-confirm-delete" data-confirm="Remove admin account &quot;<?php echo htmlspecialchars($admin['username']); ?>&quot;? This cannot be undone.">
                  <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                  <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                  <input type="hidden" name="delete_admin" value="1">
                  <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<div class="panel" style="max-width:520px;">
  <div class="panel-header">
    <h2>Add a New Admin</h2>
  </div>
  <div class="panel-body">
    <p style="color:var(--text-light); font-size:0.9rem; margin-top:-6px;">Create separate logins for other staff who help manage the website — up to a handful of accounts is plenty for most schools.</p>
    <form method="POST" action="admins.php">
      <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
      <div class="field">
        <label for="new_username">Username</label>
        <input type="text" id="new_username" name="new_username" required placeholder="e.g. deputy_head" value="<?php echo htmlspecialchars($_POST['new_username'] ?? ''); ?>">
        <div class="field-hint">3-50 characters: letters, numbers, underscores or dots only.</div>
      </div>
      <div class="field">
        <label for="new_password">Password</label>
        <input type="password" id="new_password" name="new_password" required autocomplete="new-password">
        <div class="field-hint">At least 8 characters.</div>
      </div>
      <div class="field">
        <label for="confirm_new_password">Confirm Password</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" required autocomplete="new-password">
      </div>
      <div class="form-actions">
        <button type="submit" name="add_admin" value="1" class="btn btn-gold">Create Admin Account</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
