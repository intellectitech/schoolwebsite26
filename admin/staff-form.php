<?php
// ============================================================
//  admin/staff-form.php — Add / Edit a Staff Member
//  ?edit=ID  → edit that staff member
//  (no param) → new staff member
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$staffMember = null;

if ($editId) {
  $stmt = $pdo->prepare('SELECT * FROM staff WHERE id = ?');
  $stmt->execute([$editId]);
  $staffMember = $stmt->fetch();
  if (!$staffMember) {
    setFlashErrors(['That staff member could not be found.']);
    redirectTo('staff.php');
  }
}

$departments = $pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();

// ── HANDLE SAVE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo($editId ? "staff-form.php?edit={$editId}" : 'staff-form.php');
  }

  $data = [
    'department_id' => (int) ($_POST['department_id'] ?? 0),
    'first_name' => clean($_POST['first_name'] ?? ''),
    'last_name' => clean($_POST['last_name'] ?? ''),
    'title' => clean($_POST['title'] ?? 'Mr.'),
    'role' => clean($_POST['role'] ?? ''),
    'subjects' => clean($_POST['subjects'] ?? ''),
    'qualification' => clean($_POST['qualification'] ?? ''),
    'bio' => clean($_POST['bio'] ?? ''),
    'email' => clean($_POST['email'] ?? ''),
    'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    'is_managemnet' => isset($_POST['is_management']) ? 1 : 0,
    'is_active' => isset($_POST['is_active']) ? 1 : 0,
  ];
  $removeImage = isset($_POST['remove_image']);

  $errors = [];
  if ($data['first_name'] === '')
    $errors[] = 'Please enter a first name.';
  if ($data['last_name'] === '')
    $errors[] = 'Please enter a last name.';
  if ($data['role'] === '')
    $errors[] = 'Please enter a role (e.g. "Head of Mathematics").';
  if (!$data['department_id'])
    $errors[] = 'Please choose a department.';
  if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
    $errors[] = 'Please enter a valid email address, or leave it blank.';

  if (!empty($errors)) {
    setFlashErrors($errors);
    setOldInput($data);
    redirectTo($editId ? "staff-form.php?edit={$editId}" : 'staff-form.php');
  }

  $upload = uploadStaffImage('photo');
  if ($upload['error']) {
    setFlashErrors([$upload['error']]);
    setOldInput($data);
    redirectTo($editId ? "staff-form.php?edit={$editId}" : 'staff-form.php');
  }

  $photoPath = $editId ? $staffMember['photo'] : '';
  if ($removeImage) {
    deleteStaffImageFile($photoPath);
    $photoPath = '';
  }
  if ($upload['path']) {
    deleteStaffImageFile($photoPath); // replace: drop the old file
    $photoPath = $upload['path'];
  }

  if ($editId) {
    $pdo->prepare(
      'UPDATE staff SET department_id = ?, first_name = ?, last_name = ?, title = ?, role = ?, subjects = ?,
              qualification = ?, photo = ?, is_management = ?, sort_order = ?, is_active = ?, bio = ?, email = ?
             WHERE id = ?'
    )->execute([
          $data['department_id'],
          $data['first_name'],
          $data['last_name'],
          $data['title'],
          $data['role'],
          $data['subjects'],
          $data['qualification'],
          $photoPath,
          $data['is_managemnet'],
          $data['sort_order'],
          $data['is_active'],
          $data['bio'],
          $data['email'],
          $editId,
        ]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'staff', $editId, 'Updated staff member: ' . $data['first_name'] . ' ' . $data['last_name']);
    setFlashSuccess(trim($data['first_name'] . ' ' . $data['last_name']) . ' has been updated.');
  } else {
    $pdo->prepare(
      'INSERT INTO staff (department_id, first_name, last_name, title, role, subjects, qualification, photo,
              is_management, sort_order, is_active, bio, email)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([
          $data['department_id'],
          $data['first_name'],
          $data['last_name'],
          $data['title'],
          $data['role'],
          $data['subjects'],
          $data['qualification'],
          $photoPath,
          $data['is_managemnet'],
          $data['sort_order'],
          $data['is_active'],
          $data['bio'],
          $data['email'],
        ]);
    $newId = $pdo->lastInsertId();
    auditLog($pdo, $_SESSION['admin_id'], 'insert', 'staff', $newId, 'Added staff member: ' . $data['first_name'] . ' ' . $data['last_name']);
    setFlashSuccess(trim($data['first_name'] . ' ' . $data['last_name']) . ' has been added.');
  }
  redirectTo('staff.php');
}

$flash = getFlash();

// Values used to fill the form: old input (after a validation error) > existing record (edit mode) > blank (new)
$f = $flash['old'];
$v = function ($key, $default = '') use ($f, $staffMember) {
  if ($f)
    return $f[$key] ?? $default;
  if ($staffMember)
    return $staffMember[$key] ?? $default;
  return $default;
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit Staff Member' : 'New Staff Member' ?> · Admin · Uganda Martyrs Primary School</title>
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
      <header class="admin-topbar">
        <h1><?= $editId ? 'Edit Staff Member' : 'New Staff Member' ?></h1>
        <p><a href="staff.php">← Back to all staff</a></p>
      </header>

      <section class="admin-panel">
        <?php if ($flash['errors']): ?>
          <div class="alert alert-error">
            <ul><?php foreach ($flash['errors'] as $err): ?>
                <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="staff-form.php<?= $editId ? '?edit=' . $editId : '' ?>"
          enctype="multipart/form-data" class="admin-form">
          <?= csrfField() ?>

          <div class="field-row">
            <div class="field">
              <label for="title">Title</label>
              <select id="title" name="title">
                <?php foreach (['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Fr.', 'Sr.', 'Prof.'] as $t): ?>
                  <option value="<?= $t ?>" <?= $v('title', 'Mr.') === $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label for="first_name">First name</label>
              <input id="first_name" name="first_name" type="text" required maxlength="100"
                value="<?= htmlspecialchars($v('first_name')) ?>">
            </div>
            <div class="field">
              <label for="last_name">Last name</label>
              <input id="last_name" name="last_name" type="text" required maxlength="100"
                value="<?= htmlspecialchars($v('last_name')) ?>">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label for="department_id">Department</label>
              <select id="department_id" name="department_id" required>
                <option value="">Choose one…</option>
                <?php foreach ($departments as $dept): ?>
                  <option value="<?= (int) $dept['id'] ?>" <?= (int) $v('department_id') === (int) $dept['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <p class="field-hint">Don't see the right department? <a href="staff.php">Add one from the staff
                  list →</a></p>
            </div>
            <div class="field">
              <label for="role">Role</label>
              <input id="role" name="role" type="text" required maxlength="200"
                value="<?= htmlspecialchars($v('role')) ?>" placeholder="e.g. Head of Mathematics">
            </div>
          </div>

          <div class="field">
            <label for="subjects">Subjects taught <span style="text-transform:none;letter-spacing:normal">(optional,
                comma-separated)</span></label>
            <input id="subjects" name="subjects" type="text" maxlength="300"
              value="<?= htmlspecialchars($v('subjects')) ?>" placeholder="e.g. Mathematics, Additional Mathematics">
          </div>

          <div class="field">
            <label for="qualification">Qualification <span
                style="text-transform:none;letter-spacing:normal">(optional)</span></label>
            <input id="qualification" name="qualification" type="text" maxlength="300"
              value="<?= htmlspecialchars($v('qualification')) ?>" placeholder="e.g. MSc Mathematics">
          </div>

          <div class="field">
            <label for="bio">Short bio <span style="text-transform:none;letter-spacing:normal">(optional)</span></label>
            <textarea id="bio" name="bio" rows="3"><?= htmlspecialchars($v('bio')) ?></textarea>
          </div>

          <div class="field">
            <label for="email">Email <span style="text-transform:none;letter-spacing:normal">(optional)</span></label>
            <input id="email" name="email" type="email" maxlength="200" value="<?= htmlspecialchars($v('email')) ?>">
          </div>

          <div class="field">
            <label for="photo">Photo <span style="text-transform:none;letter-spacing:normal">(optional — JPG, PNG or
                WEBP, up to 3MB)</span></label>
            <?php if ($editId && $staffMember['photo'] && file_exists(__DIR__ . '/../' . $staffMember['photo'])): ?>
              <div class="admin-image-preview">
                <img src="../<?= htmlspecialchars($staffMember['photo']) ?>" alt="Current photo">
                <label class="admin-checkbox-inline">
                  <input type="checkbox" name="remove_image" value="1"> Remove this photo
                </label>
              </div>
            <?php endif; ?>
            <input id="photo" name="photo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
          </div>

          <div class="field-row">
            <div class="field">
              <label for="sort_order">Sort order <span style="text-transform:none;letter-spacing:normal">(lower numbers
                  show first)</span></label>
              <input id="sort_order" name="sort_order" type="number"
                value="<?= htmlspecialchars($v('sort_order', '0')) ?>">
            </div>
            <div class="field" style="display:flex;align-items:flex-end;gap:20px;padding-bottom:12px">
              <label class="admin-checkbox-inline">
                <input type="checkbox" name="is_management" value="1" <?= $v('is_management', $staffMember ? $staffMember['is_management'] : 0) ? 'checked' : '' ?>>
                Management
              </label>
              <label class="admin-checkbox-inline">
                <input type="checkbox" name="is_active" value="1" <?= $v('is_active', $staffMember ? $staffMember['is_active'] : 1) ? 'checked' : '' ?>>
                Active (visible on the site)
              </label>
            </div>
          </div>

          <div class="admin-actions">
            <button type="submit" class="btn btn-primary"><?= $editId ? 'Save Changes' : 'Add Staff Member' ?></button>
            <a href="staff.php" class="btn btn-ghost">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>

</html>