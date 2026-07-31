<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_staff.php

$subPage = $_GET['sub'] ?? 'staff';
$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// ==========================================
// 1. HANDLERS FOR STAFF CRUD
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_staff'])) {
    $staffId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $departmentId = (int)$_POST['department_id'] ?: null;
    $subjects = trim($_POST['subjects'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $isManagement = isset($_POST['is_management']) ? 1 : 0;
    $sortOrder = (int)$_POST['sort_order'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    if ($firstName && $lastName && $role) {
        $photo = $_POST['existing_photo'] ?? '';
        
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = 'uploads/staff/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $photo = $destPath;
                }
            }
        }
        
        try {
            if ($staffId > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE staff SET department_id = ?, first_name = ?, last_name = ?, title = ?, role = ?, subjects = ?, qualification = ?, bio = ?, photo = ?, email = ?, is_management = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$departmentId, $firstName, $lastName, $title, $role, $subjects, $qualification, $bio, $photo, $email, $isManagement, $sortOrder, $isActive, $staffId]);
                
                log_audit_action('update', 'staff', $staffId, "Updated staff member '$firstName $lastName'");
                header("Location: admin.php?page=staff&sub=staff&msg=staff_updated");
                exit;
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO staff (department_id, first_name, last_name, title, role, subjects, qualification, bio, photo, email, is_management, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$departmentId, $firstName, $lastName, $title, $role, $subjects, $qualification, $bio, $photo, $email, $isManagement, $sortOrder, $isActive]);
                
                log_audit_action('create', 'staff', $pdo->lastInsertId(), "Created staff member '$firstName $lastName'");
                header("Location: admin.php?page=staff&sub=staff&msg=staff_created");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = "Database error: " . $e->getMessage();
        }
    } else {
        $errorMsg = "First name, last name, and role are required.";
    }
}

if ($subPage === 'staff' && $action === 'delete' && isset($_GET['id'])) {
    $delStaffId = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
        $stmt->execute([$delStaffId]);
        log_audit_action('delete', 'staff', $delStaffId, "Deleted staff ID $delStaffId");
        header("Location: admin.php?page=staff&sub=staff&msg=staff_deleted");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error deleting staff member: " . $e->getMessage();
    }
}

// ==========================================
// 2. HANDLERS FOR DEPARTMENTS CRUD
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_dept'])) {
    $deptId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $deptName = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $headOfDept = trim($_POST['head_of_dept'] ?? '');
    
    if ($deptName) {
        try {
            if ($deptId > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE departments SET name = ?, description = ?, head_of_dept = ? WHERE id = ?");
                $stmt->execute([$deptName, $description, $headOfDept, $deptId]);
                log_audit_action('update', 'departments', $deptId, "Updated department '$deptName'");
                header("Location: admin.php?page=staff&sub=depts&msg=dept_updated");
                exit;
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO departments (name, description, head_of_dept) VALUES (?, ?, ?)");
                $stmt->execute([$deptName, $description, $headOfDept]);
                log_audit_action('create', 'departments', $pdo->lastInsertId(), "Created department '$deptName'");
                header("Location: admin.php?page=staff&sub=depts&msg=dept_created");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = "Department name already exists or database error: " . $e->getMessage();
        }
    }
}

if ($subPage === 'depts' && $action === 'delete' && isset($_GET['id'])) {
    $delDeptId = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$delDeptId]);
        log_audit_action('delete', 'departments', $delDeptId, "Deleted department ID $delDeptId");
        header("Location: admin.php?page=staff&sub=depts&msg=dept_deleted");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error deleting department (make sure it contains no subjects or staff): " . $e->getMessage();
    }
}

// ==========================================
// 3. HANDLERS FOR SUBJECTS CRUD
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_subject'])) {
    $subjId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $subjName = trim($_POST['name'] ?? '');
    $deptId = (int)$_POST['department_id'] ?: null;
    $levels = $_POST['levels'] ?? []; // Array containing O_LEVEL, A_LEVEL
    $isCompulsory = isset($_POST['is_compulsory']) ? 1 : 0;
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)$_POST['sort_order'];
    
    if ($subjName && !empty($levels)) {
        // Convert levels array to comma separated string (SET type in MySQL)
        $levelSet = implode(',', $levels);
        
        try {
            if ($subjId > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE subjects SET department_id = ?, name = ?, level = ?, is_compulsory = ?, description = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([$deptId, $subjName, $levelSet, $isCompulsory, $description, $sortOrder, $subjId]);
                log_audit_action('update', 'subjects', $subjId, "Updated subject '$subjName'");
                header("Location: admin.php?page=staff&sub=subjects&msg=subj_updated");
                exit;
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO subjects (department_id, name, level, is_compulsory, description, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$deptId, $subjName, $levelSet, $isCompulsory, $description, $sortOrder]);
                log_audit_action('create', 'subjects', $pdo->lastInsertId(), "Created subject '$subjName'");
                header("Location: admin.php?page=staff&sub=subjects&msg=subj_created");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = "Database error: " . $e->getMessage();
        }
    } else {
        $errorMsg = "Subject name and at least one level are required.";
    }
}

if ($subPage === 'subjects' && $action === 'delete' && isset($_GET['id'])) {
    $delSubjId = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$delSubjId]);
        log_audit_action('delete', 'subjects', $delSubjId, "Deleted subject ID $delSubjId");
        header("Location: admin.php?page=staff&sub=subjects&msg=subj_deleted");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error deleting subject: " . $e->getMessage();
    }
}

// ==========================================
// 4. FETCH DATA
// ==========================================
try {
    $depts = $pdo->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $depts = [];
}

try {
    $staffList = $pdo->query("SELECT s.*, d.name AS dept_name FROM staff s LEFT JOIN departments d ON s.department_id = d.id ORDER BY s.sort_order ASC")->fetchAll();
} catch (PDOException $e) {
    $staffList = [];
}

try {
    $subjectsList = $pdo->query("SELECT s.*, d.name AS dept_name FROM subjects s LEFT JOIN departments d ON s.department_id = d.id ORDER BY s.sort_order ASC")->fetchAll();
} catch (PDOException $e) {
    $subjectsList = [];
}

// Edit values helper
$editStaff = null;
if ($subPage === 'staff' && $action === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($staffList as $s) {
        if ((int)$s['id'] === $editId) { $editStaff = $s; break; }
    }
}

$editDept = null;
if ($subPage === 'depts' && $action === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($depts as $d) {
        if ((int)$d['id'] === $editId) { $editDept = $d; break; }
    }
}

$editSubj = null;
if ($subPage === 'subjects' && $action === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($subjectsList as $sub) {
        if ((int)$sub['id'] === $editId) { $editSubj = $sub; break; }
    }
}
?>

<!-- Tab Selector Navigation -->
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 1.5px solid var(--border-color); padding-bottom: 0.8rem; text-align: left;">
    <a href="admin.php?page=staff&sub=staff" style="text-decoration: none; font-weight: 600; font-size: 0.95rem; color: <?= $subPage === 'staff' ? 'var(--accent-yellow)' : 'var(--text-muted)' ?>; border-bottom: 2.5px solid <?= $subPage === 'staff' ? 'var(--accent-yellow)' : 'transparent' ?>; padding-bottom: 0.8rem; padding-right: 0.5rem; margin-right: 1.5rem;"><i class="fas fa-users-cog"></i> Faculty Members</a>
    <a href="admin.php?page=staff&sub=depts" style="text-decoration: none; font-weight: 600; font-size: 0.95rem; color: <?= $subPage === 'depts' ? 'var(--accent-yellow)' : 'var(--text-muted)' ?>; border-bottom: 2.5px solid <?= $subPage === 'depts' ? 'var(--accent-yellow)' : 'transparent' ?>; padding-bottom: 0.8rem; padding-right: 0.5rem; margin-right: 1.5rem;"><i class="fas fa-sitemap"></i> Departments</a>
    <a href="admin.php?page=staff&sub=subjects" style="text-decoration: none; font-weight: 600; font-size: 0.95rem; color: <?= $subPage === 'subjects' ? 'var(--accent-yellow)' : 'var(--text-muted)' ?>; border-bottom: 2.5px solid <?= $subPage === 'subjects' ? 'var(--accent-yellow)' : 'transparent' ?>; padding-bottom: 0.8rem; padding-right: 0.5rem;"><i class="fas fa-book-open"></i> Subjects Board</a>
</div>

<?php if ($msg): ?>
    <div style="background: rgba(0,184,148,0.15); color: var(--accent-green); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; text-align: left;">
        <i class="fas fa-check-circle"></i> Success: <?= e(str_replace('_', ' ', $msg)) ?>!
    </div>
<?php endif; ?>

<?php if (isset($errorMsg)): ?>
    <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; text-align: left;">
        <i class="fas fa-exclamation-triangle"></i> <?= e($errorMsg) ?>
    </div>
<?php endif; ?>

<div style="text-align: left;">

    <!-- =======================================================
         A. STAFF SUBPAGE
         ======================================================= -->
    <?php if ($subPage === 'staff'): ?>
        <?php if ($action === 'add' || ($action === 'edit' && $editStaff)): ?>
            <!-- Add/Edit Staff Form -->
            <div class="dashboard-card">
                <div class="card-header-flex" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3><?= $editStaff ? 'Edit Staff Profile' : 'Add Staff Profile' ?></h3>
                    <a href="admin.php?page=staff&sub=staff" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; border: 1px solid var(--border-color);"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>

                <form method="POST" action="admin.php?page=staff&sub=staff" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php if ($editStaff): ?>
                        <input type="hidden" name="id" value="<?= $editStaff['id'] ?>">
                        <input type="hidden" name="existing_photo" value="<?= e($editStaff['photo']) ?>">
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Title (e.g. Mr., Dr.)</label>
                            <input type="text" name="title" class="form-control" placeholder="Mr., Ms." style="padding-left: 1rem;" value="<?= $editStaff ? e($editStaff['title']) : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">First Name *</label>
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" required style="padding-left: 1rem;" value="<?= $editStaff ? e($editStaff['first_name']) : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Last Name *</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required style="padding-left: 1rem;" value="<?= $editStaff ? e($editStaff['last_name']) : '' ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Role / Designation *</label>
                            <input type="text" name="role" class="form-control" placeholder="e.g. Headteacher, Chemistry Teacher" required style="padding-left: 1rem;" value="<?= $editStaff ? e($editStaff['role']) : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="email@domain.com" style="padding-left: 1rem;" value="<?= $editStaff ? e($editStaff['email']) : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Primary Department</label>
                            <select name="department_id" class="form-control" style="padding-left: 1rem; height: 46px; background-color: var(--bg-base); border: 1px solid var(--border-color); color: #fff; border-radius: 10px;">
                                <option value="">-- No Department --</option>
                                <?php foreach ($depts as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= ($editStaff && (int)$editStaff['department_id'] === (int)$d['id']) ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Subjects Taught (comma separated)</label>
                            <input type="text" name="subjects" class="form-control" placeholder="e.g. Physics, Mathematics" style="padding-left: 1rem;" value="<?= $editStaff ? e($editStaff['subjects']) : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Qualification details</label>
                            <input type="text" name="qualification" class="form-control" placeholder="e.g. BSc in Education (Kyambogo)" style="padding-left: 1rem;" value="<?= $editStaff ? e($editStaff['qualification']) : '' ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Profile Photo</label>
                        <div style="display: flex; gap: 1.5rem; align-items: center;">
                            <?php if ($editStaff && $editStaff['photo']): ?>
                                <img src="<?= e($editStaff['photo']) ?>" style="height: 60px; width: 60px; object-fit: cover; border-radius: 50%; border: 2px solid var(--accent-yellow);">
                            <?php endif; ?>
                            <input type="file" name="photo" accept="image/*" style="font-size: 0.8rem; color: var(--text-muted);">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Short Biography</label>
                        <textarea name="bio" rows="4" class="form-control" placeholder="Tell us about the faculty member..." style="padding-left: 1rem; line-height: 1.6;"><?= $editStaff ? e($editStaff['bio']) : '' ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="is_management" value="1" <?= ($editStaff && $editStaff['is_management']) ? 'checked' : '' ?> style="width: 16px; height: 16px;"> Management / Leadership Team
                        </label>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem;">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" style="padding-left: 0.8rem; height: 38px;" value="<?= $editStaff ? (int)$editStaff['sort_order'] : '0' ?>">
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" <?= (!$editStaff || $editStaff['is_active']) ? 'checked' : '' ?> style="width: 16px; height: 16px;"> Active Profile
                        </label>
                    </div>

                    <button type="submit" name="save_staff" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.8rem; border-radius: 10px; cursor: pointer; font-size: 0.95rem; text-align: center;"><i class="fas fa-save"></i> Save Profile</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Staff List Grid -->
            <div class="dashboard-card">
                <div class="card-header-flex">
                    <h3>School Faculty & Staff Profiles</h3>
                    <a href="admin.php?page=staff&sub=staff&action=add" class="btn" style="background: var(--accent-yellow); color: #111; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 700;"><i class="fas fa-plus"></i> Add Profile</a>
                </div>

                <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                                <th style="padding: 1rem; width: 60px;">Photo</th>
                                <th style="padding: 1rem;">Name</th>
                                <th style="padding: 1rem;">Department</th>
                                <th style="padding: 1rem;">Role</th>
                                <th style="padding: 1rem;">Subjects</th>
                                <th style="padding: 1rem;">Order</th>
                                <th style="padding: 1rem;">Status</th>
                                <th style="padding: 1rem; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($staffList)): ?>
                                <?php foreach ($staffList as $s): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='none'">
                                        <td style="padding: 0.8rem;">
                                            <img src="<?= e($s['photo'] ?: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=100&q=80') ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 1px solid var(--border-color);">
                                        </td>
                                        <td style="padding: 0.8rem; font-weight: 600;">
                                            <?= e($s['title'] ? $s['title'] . ' ' : '') ?><?= e($s['first_name'] . ' ' . $s['last_name']) ?>
                                            <?php if ($s['is_management']): ?>
                                                <span style="font-size: 0.65rem; background: var(--accent-red); color: #fff; padding: 0.15rem 0.35rem; border-radius: 3px; margin-left: 0.4rem; font-weight: 700;">MANAGEMENT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 0.8rem; color: #aae0fa;"><?= e($s['dept_name'] ?: 'None') ?></td>
                                        <td style="padding: 0.8rem;"><?= e($s['role']) ?></td>
                                        <td style="padding: 0.8rem; font-style: italic; color: var(--text-muted);"><?= e($s['subjects'] ?: 'None') ?></td>
                                        <td style="padding: 0.8rem; font-weight: 600;"><?= (int)$s['sort_order'] ?></td>
                                        <td style="padding: 0.8rem;">
                                            <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 700; <?= $s['is_active'] ? 'background: rgba(0,184,148,0.15); color: var(--accent-green);' : 'background: rgba(208,17,22,0.15); color: var(--accent-red);' ?>">
                                                <?= $s['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td style="padding: 0.8rem; text-align: center;">
                                            <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                                <a href="admin.php?page=staff&sub=staff&action=edit&id=<?= $s['id'] ?>" class="btn" style="background: rgba(0,132,255,0.15); color: var(--accent-blue); padding: 0.35rem 0.7rem; border-radius: 6px; font-size: 0.75rem; text-decoration: none; font-weight: 600;"><i class="far fa-edit"></i> Edit</a>
                                                <a href="admin.php?page=staff&sub=staff&action=delete&id=<?= $s['id'] ?>" onclick="return confirm('Are you sure you want to delete this staff member?')" class="btn" style="color: var(--accent-red); padding: 0.35rem; font-size: 0.85rem;" title="Delete"><i class="far fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">No faculty profiles registered. Click "Add Profile" to create one.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- =======================================================
         B. DEPARTMENTS SUBPAGE
         ======================================================= -->
    <?php if ($subPage === 'depts'): ?>
        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start;">
            <!-- Left panel: List -->
            <div style="flex: 2; min-width: 320px;" class="dashboard-card">
                <h3>Departments List</h3>
                <div class="table-responsive" style="overflow-x: auto; width: 100%; margin-top: 1rem;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                                <th style="padding: 0.8rem;">Name</th>
                                <th style="padding: 0.8rem;">Head of Dept</th>
                                <th style="padding: 0.8rem;">Description</th>
                                <th style="padding: 0.8rem; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($depts)): ?>
                                <?php foreach ($depts as $d): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                        <td style="padding: 0.8rem; font-weight: 600;"><?= e($d['name']) ?></td>
                                        <td style="padding: 0.8rem; color: #aae0fa; font-weight: 600;"><?= e($d['head_of_dept'] ?: 'Unassigned') ?></td>
                                        <td style="padding: 0.8rem; color: var(--text-muted);"><?= e($d['description']) ?></td>
                                        <td style="padding: 0.8rem; text-align: center;">
                                            <div style="display: inline-flex; gap: 0.4rem;">
                                                <a href="admin.php?page=staff&sub=depts&action=edit&id=<?= $d['id'] ?>" class="btn" style="background: rgba(0,132,255,0.1); color: var(--accent-blue); padding: 0.25rem 0.5rem; border-radius: 4px; text-decoration: none; font-size: 0.72rem;">Edit</a>
                                                <a href="admin.php?page=staff&sub=depts&action=delete&id=<?= $d['id'] ?>" onclick="return confirm('Are you sure you want to delete this department?')" class="btn" style="color: var(--accent-red); font-size: 0.8rem;"><i class="far fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-muted);">No departments registered.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right panel: Form -->
            <div style="flex: 1; min-width: 280px; max-width: 400px;" class="dashboard-card">
                <h3><?= $editDept ? 'Edit Department' : 'Create Department' ?></h3>
                <form method="POST" action="admin.php?page=staff&sub=depts" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                    <?php if ($editDept): ?>
                        <input type="hidden" name="id" value="<?= $editDept['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Department Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Science, Languages" style="padding-left: 0.8rem; height: 38px;" value="<?= $editDept ? e($editDept['name']) : '' ?>">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Head of Department</label>
                        <input type="text" name="head_of_dept" class="form-control" placeholder="e.g. Mr. Ssali Eric" style="padding-left: 0.8rem; height: 38px;" value="<?= $editDept ? e($editDept['head_of_dept']) : '' ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Department mandate details..." style="padding-left: 0.8rem; font-size: 0.85rem;"><?= $editDept ? e($editDept['description']) : '' ?></textarea>
                    </div>

                    <button type="submit" name="save_dept" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.6rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; text-align: center;"><i class="fas fa-save"></i> Save Department</button>
                    <?php if ($editDept): ?>
                        <a href="admin.php?page=staff&sub=depts" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-color); text-align: center; text-decoration: none; padding: 0.6rem; border-radius: 8px; font-size: 0.85rem;">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- =======================================================
         C. SUBJECTS SUBPAGE
         ======================================================= -->
    <?php if ($subPage === 'subjects'): ?>
        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start;">
            <!-- Left panel: List -->
            <div style="flex: 2; min-width: 320px;" class="dashboard-card">
                <h3>Subjects board</h3>
                <div class="table-responsive" style="overflow-x: auto; width: 100%; margin-top: 1rem;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                                <th style="padding: 0.8rem;">Subject Name</th>
                                <th style="padding: 0.8rem;">Department</th>
                                <th style="padding: 0.8rem;">Level</th>
                                <th style="padding: 0.8rem;">Status</th>
                                <th style="padding: 0.8rem;">Order</th>
                                <th style="padding: 0.8rem; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($subjectsList)): ?>
                                <?php foreach ($subjectsList as $sub): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                        <td style="padding: 0.8rem; font-weight: 600;"><?= e($sub['name']) ?></td>
                                        <td style="padding: 0.8rem; color: #aae0fa;"><?= e($sub['dept_name'] ?: 'General') ?></td>
                                        <td style="padding: 0.8rem;">
                                            <span style="font-size: 0.72rem; font-weight: 600; color: #fff; background: rgba(255,255,255,0.08); padding: 0.15rem 0.4rem; border-radius: 4px;">
                                                <?= e(str_replace('_', ' ', $sub['level'])) ?>
                                            </span>
                                        </td>
                                        <td style="padding: 0.8rem;">
                                            <span style="padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.72rem; font-weight: 700; <?= $sub['is_compulsory'] ? 'background: rgba(0,184,148,0.15); color: var(--accent-green);' : 'background: rgba(255,255,255,0.1); color: var(--text-muted);' ?>">
                                                <?= $sub['is_compulsory'] ? 'Compulsory' : 'Elective' ?>
                                            </span>
                                        </td>
                                        <td style="padding: 0.8rem; font-weight: 600;"><?= (int)$sub['sort_order'] ?></td>
                                        <td style="padding: 0.8rem; text-align: center;">
                                            <div style="display: inline-flex; gap: 0.4rem;">
                                                <a href="admin.php?page=staff&sub=subjects&action=edit&id=<?= $sub['id'] ?>" class="btn" style="background: rgba(0,132,255,0.1); color: var(--accent-blue); padding: 0.25rem 0.5rem; border-radius: 4px; text-decoration: none; font-size: 0.72rem;">Edit</a>
                                                <a href="admin.php?page=staff&sub=subjects&action=delete&id=<?= $sub['id'] ?>" onclick="return confirm('Are you sure you want to delete this subject?')" class="btn" style="color: var(--accent-red); font-size: 0.8rem;"><i class="far fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">No subjects offered yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right panel: Form -->
            <div style="flex: 1; min-width: 280px; max-width: 400px;" class="dashboard-card">
                <h3><?= $editSubj ? 'Edit Subject' : 'Add Subject' ?></h3>
                <form method="POST" action="admin.php?page=staff&sub=subjects" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                    <?php if ($editSubj): ?>
                        <input type="hidden" name="id" value="<?= $editSubj['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Subject Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Mathematics, Geography" style="padding-left: 0.8rem; height: 38px;" value="<?= $editSubj ? e($editSubj['name']) : '' ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Academic Level *</label>
                        <div style="display: flex; gap: 1rem; margin-top: 0.3rem;">
                            <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem; cursor: pointer;">
                                <input type="checkbox" name="levels[]" value="O_LEVEL" <?= (!$editSubj || strpos($editSubj['level'], 'O_LEVEL') !== false) ? 'checked' : '' ?>> O-Level
                            </label>
                            <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem; cursor: pointer;">
                                <input type="checkbox" name="levels[]" value="A_LEVEL" <?= ($editSubj && strpos($editSubj['level'], 'A_LEVEL') !== false) ? 'checked' : '' ?>> A-Level
                            </label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Department</label>
                        <select name="department_id" class="form-control" style="padding-left: 0.8rem; height: 38px; background-color: var(--bg-base); border: 1px solid var(--border-color); color: #fff; border-radius: 8px;">
                            <option value="">-- No Department --</option>
                            <?php foreach ($depts as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= ($editSubj && (int)$editSubj['department_id'] === (int)$d['id']) ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem; cursor: pointer; flex: 1.5;">
                            <input type="checkbox" name="is_compulsory" value="1" <?= ($editSubj && $editSubj['is_compulsory']) ? 'checked' : '' ?>> Compulsory Core Subject
                        </label>
                        <div class="form-group" style="margin-bottom: 0; flex: 1;">
                            <label style="color: var(--text-muted); font-size: 0.75rem; margin-bottom: 0.2rem;">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" style="padding-left: 0.6rem; height: 34px;" value="<?= $editSubj ? (int)$editSubj['sort_order'] : '0' ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Brief subject overview..." style="padding-left: 0.8rem; font-size: 0.85rem;"><?= $editSubj ? e($editSubj['description']) : '' ?></textarea>
                    </div>

                    <button type="submit" name="save_subject" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.6rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; text-align: center;"><i class="fas fa-save"></i> Save Subject</button>
                    <?php if ($editSubj): ?>
                        <a href="admin.php?page=staff&sub=subjects" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-color); text-align: center; text-decoration: none; padding: 0.6rem; border-radius: 8px; font-size: 0.85rem;">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>
