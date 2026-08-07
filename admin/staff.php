<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'staff';
$departments = $pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();
$notice = '';
$editRow = null;

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT photo FROM staff WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        deleteLocalImage($row['photo']);
        $pdo->prepare('DELETE FROM staff WHERE id = ?')->execute([$id]);
        auditLog($pdo, $_SESSION['admin_id'], 'DELETE', 'staff', $id, 'Removed staff member');
    }
    header('Location: staff.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int) ($_POST['id'] ?? 0);
    $departmentId  = $_POST['department_id'] ? (int) $_POST['department_id'] : 0;
    $firstName     = trim($_POST['first_name'] ?? '');
    $lastName      = trim($_POST['last_name'] ?? '');
    $title         = trim($_POST['title'] ?? 'Mr.');
    $role          = trim($_POST['role'] ?? '');
    $subjects      = trim($_POST['subjects'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $bio           = trim($_POST['bio'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $photo         = trim($_POST['photo'] ?? '');
    $isManagement  = isset($_POST['is_managemnet']) ? 1 : 0;
    $isActive      = isset($_POST['is_active']) ? 1 : 0;
    $sortOrder     = (int) ($_POST['sort_order'] ?? 0);

    if ($firstName === '' || $lastName === '') {
        $notice = 'First and last name are required.';
    } elseif (!$departmentId) {
        $notice = 'Please select a department.';
    } elseif ($role === '') {
        $notice = 'Role / position is required.';
    } else {
        $upload = saveUploadedImage($_FILES['photo_file'] ?? [], 'staff');
        if (!empty($upload['skipped'])) {
            // keep existing path from hidden field
        } elseif (!$upload['ok']) {
            $notice = $upload['error'];
        } else {
            deleteLocalImage($photo);
            $photo = $upload['path'];
        }

        if ($notice === '') {
            if ($id) {
                $pdo->prepare(
                    'UPDATE staff SET department_id=?, first_name=?, last_name=?, title=?, role=?, subjects=?, qualification=?, photo=?, is_managemnet=?, sort_order=?, is_active=?, bio=?, email=? WHERE id=?'
                )->execute([$departmentId, $firstName, $lastName, $title, $role, $subjects, $qualification, $photo, $isManagement, $sortOrder, $isActive, $bio, $email, $id]);
                auditLog($pdo, $_SESSION['admin_id'], 'UPDATE', 'staff', $id, 'Updated: ' . $firstName . ' ' . $lastName);
            } else {
                $pdo->prepare(
                    'INSERT INTO staff (department_id, first_name, last_name, title, role, subjects, qualification, photo, is_managemnet, sort_order, is_active, bio, email)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)'
                )->execute([$departmentId, $firstName, $lastName, $title, $role, $subjects, $qualification, $photo, $isManagement, $sortOrder, $isActive, $bio, $email]);
                auditLog($pdo, $_SESSION['admin_id'], 'INSERT', 'staff', $pdo->lastInsertId(), 'Added: ' . $firstName . ' ' . $lastName);
            }
            header('Location: staff.php?msg=saved');
            exit;
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM staff WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch();
}

$staffList = $pdo->query(
    'SELECT s.*, d.name AS dept_name
     FROM staff s
     LEFT JOIN departments d ON d.id = s.department_id
     ORDER BY s.is_management DESC, d.name ASC, s.sort_order ASC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Staff</h1>
    <p class="admin-sub">Add, update, and remove staff profiles shown on the public staff directory.</p>

    <?php if ($notice): ?><div class="admin-alert admin-alert-error"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?><div class="admin-alert admin-alert-success">Saved successfully.</div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?><div class="admin-alert admin-alert-success">Staff member removed.</div><?php endif; ?>

    <div class="admin-card">
        <h2 style="margin-bottom:10px"><?= $editRow ? 'Edit Staff Member' : 'Add New Staff Member' ?></h2>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="id" value="<?= $editRow['id'] ?? '' ?>">
            <input type="hidden" name="photo" value="<?= htmlspecialchars($editRow['photo'] ?? '') ?>">

            <label>First Name</label>
            <input type="text" name="first_name" required value="<?= htmlspecialchars($editRow['first_name'] ?? '') ?>">

            <label>Last Name</label>
            <input type="text" name="last_name" required value="<?= htmlspecialchars($editRow['last_name'] ?? '') ?>">

            <label>Title</label>
            <select name="title">
                <?php foreach (['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.'] as $t): ?>
                <option value="<?= $t ?>" <?= (($editRow['title'] ?? 'Mr.') === $t) ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>

            <label>Department</label>
            <select name="department_id" required>
                <option value="">— Select —</option>
                <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>" <?= (($editRow['department_id'] ?? null) == $d['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Role / Position (e.g. Head Teacher, Senior Teacher)</label>
            <input type="text" name="role" required value="<?= htmlspecialchars($editRow['role'] ?? '') ?>">

            <label>Subjects Taught</label>
            <input type="text" name="subjects" value="<?= htmlspecialchars($editRow['subjects'] ?? '') ?>">

            <label>Qualification</label>
            <input type="text" name="qualification" value="<?= htmlspecialchars($editRow['qualification'] ?? '') ?>">

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($editRow['email'] ?? '') ?>">

            <label>Bio</label>
            <textarea name="bio" rows="4"><?= htmlspecialchars($editRow['bio'] ?? '') ?></textarea>

            <label>Photo (JPG, PNG, GIF, or WebP, max 5 MB)</label>
            <input type="file" name="photo_file" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (!empty($editRow['photo'])): ?>
            <p style="margin-top:8px;color:#6b7280;font-size:14px">Current: <?= htmlspecialchars($editRow['photo']) ?></p>
            <img class="thumb" src="../<?= htmlspecialchars($editRow['photo']) ?>" style="margin-top:8px" alt="">
            <?php endif; ?>

            <label>Sort Order (lower numbers show first)</label>
            <input type="text" name="sort_order" value="<?= htmlspecialchars($editRow['sort_order'] ?? '0') ?>">

            <label style="display:flex;align-items:center;gap:8px;margin-top:16px">
                <input type="checkbox" name="is_managemnet" style="width:auto"
                       <?= (!empty($editRow['is_managemnet'])) ? 'checked' : '' ?>>
                Leadership / Management (shows in the "School Leadership" section)
            </label>
            <label style="display:flex;align-items:center;gap:8px;margin-top:8px">
                <input type="checkbox" name="is_active" style="width:auto"
                       <?= (!isset($editRow) || $editRow['is_active']) ? 'checked' : '' ?>>
                Active (visible on the public staff directory)
            </label>

            <div style="margin-top:18px">
                <button type="submit" class="admin-btn admin-btn-primary"><?= $editRow ? 'Update Staff Member' : 'Add Staff Member' ?></button>
                <?php if ($editRow): ?><a href="staff.php" class="admin-btn">Cancel</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom:14px">All Staff</h2>
        <table class="admin-table">
            <thead><tr><th>Photo</th><th>Name</th><th>Role</th><th>Department</th><th>Type</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($staffList as $s): ?>
                <tr>
                    <td><img class="thumb" src="../<?= htmlspecialchars($s['photo'] ?: '') ?>" onerror="this.style.display='none'"></td>
                    <td><?= htmlspecialchars($s['title'] . ' ' . $s['first_name'] . ' ' . $s['last_name']) ?></td>
                    <td><?= htmlspecialchars($s['role']) ?></td>
                    <td><?= htmlspecialchars($s['dept_name'] ?? '—') ?></td>
                    <td><?= $s['is_management'] ? 'Leadership' : 'Teaching Staff' ?></td>
                    <td><?= $s['is_active'] ? 'Active' : 'Hidden' ?></td>
                    <td>
                        <a href="staff.php?edit=<?= $s['id'] ?>" class="admin-btn admin-btn-edit admin-btn-sm">Edit</a>
                        <a href="staff.php?delete=<?= $s['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm"
                           onclick="return confirm('Remove this staff member?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
