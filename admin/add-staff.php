<?php
// admin/add-staff.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/upload.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Add Staff - Admin';
$error = '';
$success = '';

$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();

$uploadedImage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $result = uploadImage($_FILES['photo'], 'staff', 5242880);
    if ($result['success']) {
        $uploadedImage = $result['path'];
    } else {
        $error = $result['error'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $department_id = isset($_POST['department_id']) && $_POST['department_id'] ? (int)$_POST['department_id'] : null;
    $first_name = clean($_POST['first_name'] ?? '');
    $last_name = clean($_POST['last_name'] ?? '');
    $title = clean($_POST['title'] ?? '');
    $role = clean($_POST['role'] ?? '');
    $subjects = clean($_POST['subjects'] ?? '');
    $qualification = clean($_POST['qualification'] ?? '');
    $bio = clean($_POST['bio'] ?? '');
    $photo = !empty($uploadedImage) ? $uploadedImage : clean($_POST['photo_url'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $is_management = isset($_POST['is_management']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    
    if (empty($first_name) || empty($last_name) || empty($role)) {
        $error = 'First name, last name, and role are required.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO staff (department_id, first_name, last_name, title, role, subjects, 
                                  qualification, bio, photo, email, is_management, is_active, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $department_id, $first_name, $last_name, $title, $role, $subjects,
                $qualification, $bio, $photo, $email, $is_management, $is_active, $sort_order
            ]);
            
            $staffId = $pdo->lastInsertId();
            
            $logStmt = $pdo->prepare("
                INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
                VALUES (?, 'created_staff', 'staff', ?, 'Created staff member: ' . ?, ?)
            ");
            $logStmt->execute([$_SESSION['admin_id'], $staffId, $first_name . ' ' . $last_name, $_SERVER['REMOTE_ADDR']]);
            
            $success = 'Staff member added successfully!';
            $_POST = [];
            $uploadedImage = '';
        } catch (Exception $e) {
            $error = 'Error adding staff member: ' . $e->getMessage();
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
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5e6d3; color: #1a1a1a; min-height: 100vh; }
        .admin-wrapper { display: flex; min-height: 100vh; }

        .admin-sidebar {
            width: 260px;
            background: #0a0a0a;
            color: #fff;
            padding: 30px 20px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            border-right: 2px solid #00C853;
        }
        .admin-sidebar .logo { text-align: center; padding-bottom: 30px; border-bottom: 2px solid rgba(0,200,83,0.2); margin-bottom: 30px; }
        .admin-sidebar .logo .icon-wrapper { display: inline-block; width: 55px; height: 55px; background: linear-gradient(135deg, #009624, #00C853); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 10px 30px rgba(0,200,83,0.25); }
        .admin-sidebar .logo i { font-size: 2rem; color: #fff; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.1rem; font-weight: 700; }
        .admin-sidebar .user { padding: 15px; background: rgba(255,255,255,0.05); border-radius: 16px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
        .admin-sidebar .user .name { font-weight: 600; color: #00C853; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.5; color: rgba(255,255,255,0.6); }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.5); border-radius: 14px; transition: all 0.3s ease; margin-bottom: 4px; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: rgba(0,200,83,0.12); color: #00C853; border: 1px solid rgba(0,200,83,0.1); transform: translateX(4px); }
        .admin-sidebar nav a i { width: 20px; color: rgba(255,255,255,0.3); transition: all 0.3s ease; }
        .admin-sidebar nav a:hover i, .admin-sidebar nav a.active i { color: #00C853; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.4); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 14px; transition: all 0.3s ease; margin-top: 10px; }
        .logout-btn:hover { background: rgba(255,0,0,0.08); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }

        .admin-content { flex: 1; padding: 30px; background: #f5e6d3; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; padding: 20px 30px; background: #fff; border-radius: 20px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); border-left: 4px solid #00C853; }
        .admin-header h1 { color: #0a0a0a; font-size: 1.6rem; font-weight: 700; }
        .admin-header h1 i { color: #00C853; margin-right: 10px; }

        .form-container { background: #fff; border-radius: 20px; padding: 40px; max-width: 800px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #0a0a0a; font-size: 0.9rem; }
        .form-group label .required { color: #FF6B6B; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #00C853; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group.checkbox { display: flex; align-items: center; gap: 10px; }
        .form-group.checkbox label { margin-bottom: 0; cursor: pointer; }
        .form-group.checkbox input { width: auto; padding: 0; accent-color: #00C853; }

        .file-upload-wrapper {
            position: relative;
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        .file-upload-wrapper:hover { border-color: #00C853; background: rgba(0,200,83,0.02); }
        .file-upload-wrapper input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .file-upload-wrapper .upload-icon { font-size: 2rem; color: #ccc; margin-bottom: 8px; }
        .file-upload-wrapper .upload-text { color: #999; font-size: 0.9rem; }
        .file-upload-wrapper .upload-text strong { color: #00C853; }
        .file-upload-wrapper .preview { margin-top: 10px; }
        .file-upload-wrapper .preview img { max-height: 100px; border-radius: 8px; border: 1px solid #e0e0e0; }

        .btn-submit { padding: 14px 40px; background: linear-gradient(135deg, #009624, #00C853); color: #fff; border: none; border-radius: 14px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,200,83,0.3); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(0,200,83,0.4); }
        .btn-back { padding: 12px 24px; background: #e0e0e0; color: #666; border: none; border-radius: 14px; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-back:hover { background: #ccc; }

        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .button-group { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 10px; }
        .helper-text { font-size: 0.85rem; color: #999; margin-top: 5px; }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .form-container { padding: 20px; } .admin-header { flex-direction: column; align-items: stretch; } }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="logo">
            <div class="icon-wrapper"><i class="fas fa-graduation-cap"></i></div>
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
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-plus-circle"></i> Add Staff Member</h1>
            <a href="manage-staff.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Staff</a>
        </div>

        <?php if ($success): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" required placeholder="First name" value="<?= clean($_POST['first_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Last name" value="<?= clean($_POST['last_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <select id="title" name="title">
                            <option value="">Select</option>
                            <option value="Mr" <?= (isset($_POST['title']) && $_POST['title'] == 'Mr') ? 'selected' : '' ?>>Mr</option>
                            <option value="Mrs" <?= (isset($_POST['title']) && $_POST['title'] == 'Mrs') ? 'selected' : '' ?>>Mrs</option>
                            <option value="Ms" <?= (isset($_POST['title']) && $_POST['title'] == 'Ms') ? 'selected' : '' ?>>Ms</option>
                            <option value="Dr" <?= (isset($_POST['title']) && $_POST['title'] == 'Dr') ? 'selected' : '' ?>>Dr</option>
                            <option value="Prof" <?= (isset($_POST['title']) && $_POST['title'] == 'Prof') ? 'selected' : '' ?>>Prof</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Role/Position <span class="required">*</span></label>
                        <input type="text" id="role" name="role" required placeholder="e.g. Head of Sciences" value="<?= clean($_POST['role'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select id="department_id" name="department_id">
                            <option value="">No Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" <?= (isset($_POST['department_id']) && $_POST['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                    <?= clean($dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?= clean($_POST['sort_order'] ?? 0) ?>" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="subjects">Subjects (comma-separated)</label>
                    <input type="text" id="subjects" name="subjects" placeholder="Mathematics, Physics, Chemistry" value="<?= clean($_POST['subjects'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="qualification">Qualifications</label>
                    <input type="text" id="qualification" name="qualification" placeholder="B.Ed Mathematics, Makerere" value="<?= clean($_POST['qualification'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Brief biography..."><?= clean($_POST['bio'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Photo</label>
                    <div class="file-upload-wrapper" id="fileUploadWrapper">
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="upload-text">
                            <strong>Click to upload</strong> or drag and drop<br>
                            <span style="font-size:0.85rem;color:#999;">JPG, PNG, WEBP, GIF (Max 5MB)</span>
                        </div>
                        <div class="preview" id="imagePreview"></div>
                    </div>
                    <div class="helper-text"><i class="fas fa-info-circle"></i> Or enter URL below</div>
                    <input type="text" id="photo_url" name="photo_url" placeholder="Or enter image URL" style="margin-top:10px;" value="<?= clean($_POST['photo_url'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="staff@school.ug" value="<?= clean($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_management" name="is_management" <?= isset($_POST['is_management']) ? 'checked' : '' ?>>
                        <label for="is_management">Leadership Team Member</label>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_active" name="is_active" <?= isset($_POST['is_active']) ? 'checked' : 'checked' ?>>
                        <label for="is_active">Active (visible on website)</label>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" name="submit" class="btn-submit"><i class="fas fa-save"></i> Add Staff</button>
                    <a href="manage-staff.php" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.getElementById('photo').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxHeight = '100px';
            img.style.borderRadius = '8px';
            img.style.border = '1px solid #e0e0e0';
            preview.appendChild(img);
        }
        reader.readAsDataURL(this.files[0]);
    }
});

const wrapper = document.getElementById('fileUploadWrapper');
wrapper.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = '#00C853';
    this.style.background = 'rgba(0,200,83,0.02)';
});
wrapper.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = '#e0e0e0';
    this.style.background = '';
});
wrapper.addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = '#e0e0e0';
    this.style.background = '';
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>