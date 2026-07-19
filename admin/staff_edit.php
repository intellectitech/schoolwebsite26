<?php
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/upload_helper.php';

$editing = false;
$member = ['id' => null, 'full_name' => '', 'role_title' => '', 'bio' => '', 'photo_path' => '', 'display_order' => 0];

$allStaff = ds_read('staff');

if (!empty($_GET['id'])) {
    $found = ds_find($allStaff, $_GET['id']);
    if ($found) { $member = $found; $editing = true; }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();

    $full_name = trim($_POST['full_name'] ?? '');
    $role_title = trim($_POST['role_title'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $display_order = (int) ($_POST['display_order'] ?? 0);
    $existingId = $_POST['id'] ?: null;
    $existingPhoto = $_POST['existing_photo'] ?? '';

    if ($full_name === '') { $errors[] = "Full name is required."; }
    if ($role_title === '') { $errors[] = "Role/title is required."; }

    $uploadError = '';
    $uploadedPath = handle_image_upload('photo', 'staff', $uploadError);
    if ($uploadError) { $errors[] = $uploadError; }

    $photoPath = $uploadedPath ?: $existingPhoto;
    if (!$photoPath) { $photoPath = 'assets/images/staff/headteacher.svg'; }

    if (empty($errors)) {
        $data = ['full_name' => $full_name, 'role_title' => $role_title, 'bio' => $bio, 'photo_path' => $photoPath, 'display_order' => $display_order];

        if ($existingId) {
            $allStaff = ds_update($allStaff, $existingId, $data);
            $flashMsg = 'Staff member updated successfully.';
        } else {
            $data['id'] = ds_next_id($allStaff);
            $allStaff[] = $data;
            $flashMsg = 'Staff member added successfully.';
        }

        if (ds_write('staff', $allStaff)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => $flashMsg];
            header('Location: staff.php');
            exit;
        } else {
            $errors[] = "Could not save the staff member — check that the /data folder is writable.";
        }
    }

    $member = array_merge($member, [
        'full_name' => $full_name, 'role_title' => $role_title, 'bio' => $bio,
        'photo_path' => $photoPath ?: $existingPhoto, 'display_order' => $display_order, 'id' => $existingId,
    ]);
    if ($existingId) { $editing = true; }
}

$page_title = $editing ? "Edit Staff Member" : "Add Staff Member";
$active_nav = "staff";
require_once __DIR__ . '/includes/admin_header.php';
?>

<?php foreach ($errors as $err): ?>
  <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
<?php endforeach; ?>

<div class="panel">
  <div class="panel-header">
    <h2><?php echo $editing ? 'Edit Staff Member' : 'Add New Staff Member'; ?></h2>
    <a href="staff.php" class="btn btn-outline btn-sm">&larr; Back to Staff</a>
  </div>
  <div class="panel-body">
    <form method="POST" action="staff_edit.php<?php echo $editing ? '?id=' . $member['id'] : ''; ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($member['id'] ?? ''); ?>">
      <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($member['photo_path']); ?>">

      <div class="form-grid">
        <div class="field">
          <label for="full_name">Full Name *</label>
          <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($member['full_name']); ?>">
        </div>
        <div class="field">
          <label for="role_title">Role / Title *</label>
          <input type="text" id="role_title" name="role_title" required value="<?php echo htmlspecialchars($member['role_title']); ?>" placeholder="e.g. Head Teacher">
        </div>
        <div class="field">
          <label for="display_order">Display Order</label>
          <input type="number" id="display_order" name="display_order" value="<?php echo (int) $member['display_order']; ?>" min="0">
          <div class="field-hint">Lower numbers appear first on the About page.</div>
        </div>
        <div class="field field-full">
          <label for="bio">Short Bio</label>
          <textarea id="bio" name="bio" rows="3"><?php echo htmlspecialchars($member['bio']); ?></textarea>
        </div>
        <div class="field field-full">
          <label for="photo">Photo</label>
          <?php if (!empty($member['photo_path'])): ?>
            <div class="image-preview-current">
              <img src="<?php echo htmlspecialchars('../' . $member['photo_path']); ?>" alt="Current photo">
              <span>Current: <?php echo htmlspecialchars($member['photo_path']); ?></span>
            </div>
          <?php endif; ?>
          <input type="file" id="photo" name="photo" accept="image/*">
          <div class="field-hint">Square (1:1) photos work best. JPG, PNG, GIF, WEBP or SVG, up to 5MB.</div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-gold"><?php echo $editing ? 'Save Changes' : 'Add Staff Member'; ?></button>
        <a href="staff.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
