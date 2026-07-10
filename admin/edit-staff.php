<?php
// admin/edit-staff.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Edit Staff - Admin';
$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: manage-staff.php');
    exit;
}

// Fetch staff member
$stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->execute([$id]);
$staff = $stmt->fetch();

if (!$staff) {
    header('Location: manage-staff.php');
    exit;
}

// Fetch departments
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_id = isset($_POST['department_id']) && $_POST['department_id'] ? (int)$_POST['department_id'] : null;
    $first_name = clean($_POST['first_name'] ?? '');
    $last_name = clean($_POST['last_name'] ?? '');
    $title = clean($_POST['title'] ?? '');
    $role = clean($_POST['role'] ?? '');
    $subjects = clean($_POST['subjects'] ?? '');
    $qualification = clean($_POST['qualification'] ?? '');
    $bio = clean($_POST['bio'] ?? '');
    $photo = clean($_POST['photo'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $is_management = isset($_POST['is_management']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    
    if (empty($first_name) || empty($last_name) || empty($role)) {
        $error = 'First name, last name, and role are required.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE staff SET 
                    department_id = ?, first_name = ?, last_name = ?, title = ?, role = ?, 
                    subjects = ?, qualification = ?, bio = ?, photo = ?, email = ?, 
                    is_management = ?, is_active = ?, sort_order = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $department_id, $first_name, $last_name, $title, $role, $subjects,
                $qualification, $bio, $photo, $email, $is_management, $is_active, $sort_order, $id
            ]);
            
            $logStmt = $pdo->prepare("
                INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
                VALUES (?, 'updated_staff', 'staff', ?, 'Updated staff member: ' . ?, ?)
            ");
            $logStmt->execute([$_SESSION['admin_id'], $id, $first_name . ' ' . $last_name, $_SERVER['REMOTE_ADDR']]);
            
            $success = 'Staff member updated successfully!';
            
            // Refresh staff data
            $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
            $stmt->execute([$id]);
            $staff = $stmt->fetch();
        } catch (Exception $e) {
            $error = 'Error updating staff member: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Same styles as add-staff.php */
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #0d2617; color: #fff; padding: 30px 20px; min-height: 100vh; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .admin-sidebar .logo { text-align: center; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 30px; }
        .admin-sidebar .logo i { font-size: 2.5rem; color: #FFD700; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.2rem; margin-top: 10px; }
        .admin-sidebar .user { padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .admin-sidebar .user .name { font-weight: 600; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.7; }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.7); border-radius: 8px; transition: all 0.3s; margin-bottom: 4px; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: rgba(255,215,0,0.1); color: #FFD700; }
        .admin-sidebar nav a i { width: 20px; }
        .admin-content { flex: 1; padding: 30px; background: #f5f5f5; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .admin-header h1 { color: #0d2617; }
        .form-container { background: #fff; padding: 40px; border-radius: 12px; max-width: 800px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem; }
        .form-group label .required { color: #dc3545; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #1a4d2e; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group.checkbox { display: flex; align-items: center; gap: 10px; }
        .form-group.checkbox label { margin-bottom: 0; cursor: pointer; }
        .form-group.checkbox input { width: auto; padding: 0; }
        .btn-submit { padding: 14px 40px; background: linear-gradient(135deg, #FFD700, #f5c842); color: #1a4d2e; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,215,0,0.4); }
        .btn-back { padding: 14px 24px; background: #6c757d; color: #fff; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-back:hover { background: #5a6268; color: #fff; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,0,0,0.1); color: #ff6b6b; }
        .button-group { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 10px; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .form-container { padding: 20px; } }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <h2>School Admin</h2>
        </div>
        <div class="user">
            <div class="name"><?= clean($_SESSION['admin_name']) ?></div>
            <div class="role"><?= clean($_SESSION['admin_role'] ?? 'Admin') ?></div>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage-news.php"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php" class="active"><i class="fas fa-users"></i> Staff</a>
            <a href="upload-images.php"><i class="fas fa-images"></i> Hero Images</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-edit"></i> Edit Staff Member</h1>
            <a href="manage-staff.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Staff</a>
        </div>

        <?php if ($success): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" required placeholder="First name" value="<?= clean($staff['first_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Last name" value="<?= clean($staff['last_name']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <select id="title" name="title">
                            <option value="">Select</option>
                            <option value="Mr" <?= ($staff['title'] == 'Mr') ? 'selected' : '' ?>>Mr</option>
                            <option value="Mrs" <?= ($staff['title'] == 'Mrs') ? 'selected' : '' ?>>Mrs</option>
                            <option value="Ms" <?= ($staff['title'] == 'Ms') ? 'selected' : '' ?>>Ms</option>
                            <option value="Dr" <?= ($staff['title'] == 'Dr') ? 'selected' : '' ?>>Dr</option>
                            <option value="Prof" <?= ($staff['title'] == 'Prof') ? 'selected' : '' ?>>Prof</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Role/Position <span class="required">*</span></label>
                        <input type="text" id="role" name="role" required placeholder="e.g. Head of Sciences" value="<?= clean($staff['role']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select id="department_id" name="department_id">
                            <option value="">No Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" <?= ($staff['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                    <?= clean($dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?= $staff['sort_order'] ?>" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="subjects">Subjects (comma-separated)</label>
                    <input type="text" id="subjects" name="subjects" placeholder="Mathematics, Physics, Chemistry" value="<?= clean($staff['subjects']) ?>">
                </div>

                <div class="form-group">
                    <label for="qualification">Qualifications</label>
                    <input type="text" id="qualification" name="qualification" placeholder="B.Ed Mathematics, Makerere" value="<?= clean($staff['qualification']) ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Brief biography..."><?= clean($staff['bio']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="staff@school.ug" value="<?= clean($staff['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="photo">Photo URL</label>
                        <input type="text" id="photo" name="photo" placeholder="assets/images/staff/photo.jpg" value="<?= clean($staff['photo']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_management" name="is_management" <?= $staff['is_management'] ? 'checked' : '' ?>>
                        <label for="is_management">Leadership Team Member</label>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_active" name="is_active" <?= $staff['is_active'] ? 'checked' : '' ?>>
                        <label for="is_active">Active (visible on website)</label>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Update Staff</button>
                    <a href="manage-staff.php" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>