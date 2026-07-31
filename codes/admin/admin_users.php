<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_users.php

// Secure check: Only super admins can load this file
if ($_SESSION['admin_role'] !== 'super_admin') {
    echo "<h3>Access Denied</h3><p>You do not have permission to view this resource.</p>";
    exit;
}

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle save (create/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $userId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'editor';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    if ($name && $email) {
        $photo = $_POST['existing_photo'] ?? '';
        
        // Handle photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
            $fileName = $_FILES['profile_photo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = 'uploads/admins/';
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
            if ($userId > 0) {
                // Update
                if ($password) {
                    // Update with new password
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE admin_users SET name = ?, email = ?, password = ?, role = ?, profile_photo = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $hashedPassword, $role, $photo, $isActive, $userId]);
                } else {
                    // Update without changing password
                    $stmt = $pdo->prepare("UPDATE admin_users SET name = ?, email = ?, role = ?, profile_photo = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $role, $photo, $isActive, $userId]);
                }
                
                log_audit_action('update', 'admin_users', $userId, "Updated admin user profile '$name' ($role)");
                header("Location: admin.php?page=admins&msg=updated");
                exit;
            } else {
                // Insert
                if ($password) {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("INSERT INTO admin_users (name, email, password, role, profile_photo, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $hashedPassword, $role, $photo, $isActive]);
                    
                    log_audit_action('create', 'admin_users', $pdo->lastInsertId(), "Created admin user '$name' ($role)");
                    header("Location: admin.php?page=admins&msg=created");
                    exit;
                } else {
                    $errorMsg = "Password is required for new accounts.";
                }
            }
        } catch (PDOException $e) {
            $errorMsg = "Email address already registered or database error: " . $e->getMessage();
        }
    } else {
        $errorMsg = "Name and email are required.";
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    $delId = (int)$_GET['id'];
    
    // Prevent deleting self!
    if ($delId === (int)$_SESSION['admin_id']) {
        $errorMsg = "You cannot delete your own account!";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
            $stmt->execute([$delId]);
            log_audit_action('delete', 'admin_users', $delId, "Deleted admin user account ID $delId");
            header("Location: admin.php?page=admins&msg=deleted");
            exit;
        } catch (PDOException $e) {
            $errorMsg = "Error deleting user: " . $e->getMessage();
        }
    }
}

// Fetch all admins
try {
    $users = $pdo->query("SELECT * FROM admin_users ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $users = [];
}

// Fetch edit item
$editUser = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($users as $u) {
        if ((int)$u['id'] === $editId) { $editUser = $u; break; }
    }
}
?>

<div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start; text-align: left;">
    
    <!-- Left Panel: List -->
    <div style="flex: 2; min-width: 320px;" class="dashboard-card">
        <div class="card-header-flex">
            <h3>Administrative Users Management</h3>
            <?php if ($msg === 'created'): ?>
                <span style="color: var(--accent-green); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-check-circle"></i> User created!</span>
            <?php elseif ($msg === 'updated'): ?>
                <span style="color: var(--accent-green); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-check-circle"></i> User updated!</span>
            <?php elseif ($msg === 'deleted'): ?>
                <span style="color: var(--accent-red); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-trash-alt"></i> User deleted!</span>
            <?php endif; ?>
        </div>

        <div class="table-responsive" style="overflow-x: auto; width: 100%; margin-top: 1rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                        <th style="padding: 0.8rem; width: 50px;">Avatar</th>
                        <th style="padding: 0.8rem;">Name</th>
                        <th style="padding: 0.8rem;">Email</th>
                        <th style="padding: 0.8rem;">Role</th>
                        <th style="padding: 0.8rem;">Last Login</th>
                        <th style="padding: 0.8rem;">Status</th>
                        <th style="padding: 0.8rem; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td style="padding: 0.6rem;">
                                    <img src="<?= e($u['profile_photo'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80') ?>" style="width: 35px; height: 35px; object-fit: cover; border-radius: 50%; border: 1px solid var(--border-color);">
                                </td>
                                <td style="padding: 0.6rem; font-weight: 600;"><?= e($u['name']) ?></td>
                                <td style="padding: 0.6rem; color: #aae0fa;"><?= e($u['email']) ?></td>
                                <td style="padding: 0.6rem;">
                                    <span style="font-size: 0.7rem; background: rgba(255,255,255,0.08); padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">
                                        <?= e(str_replace('_', ' ', strtoupper($u['role']))) ?>
                                    </span>
                                </td>
                                <td style="padding: 0.6rem; color: var(--text-muted); font-size: 0.8rem;">
                                    <?= $u['last_login'] ? date('M d, Y &bull; g:i a', strtotime($u['last_login'])) : 'Never' ?>
                                </td>
                                <td style="padding: 0.6rem;">
                                    <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 700; <?= $u['is_active'] ? 'background: rgba(0,184,148,0.15); color: var(--accent-green);' : 'background: rgba(208,17,22,0.15); color: var(--accent-red);' ?>">
                                        <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td style="padding: 0.6rem; text-align: center;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="admin.php?page=admins&action=edit&id=<?= $u['id'] ?>" class="btn" style="background: rgba(0,132,255,0.1); color: var(--accent-blue); padding: 0.25rem 0.5rem; border-radius: 4px; text-decoration: none; font-size: 0.72rem;">Edit</a>
                                        <?php if ($u['id'] !== (int)$_SESSION['admin_id']): ?>
                                            <a href="admin.php?page=admins&action=delete&id=<?= $u['id'] ?>" onclick="return confirm('Are you sure you want to delete this admin account?')" class="btn" style="color: var(--accent-red); font-size: 0.8rem;"><i class="far fa-trash-alt"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">No admin users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div style="flex: 1; min-width: 280px; max-width: 400px;" class="dashboard-card">
        <h3><?= $editUser ? 'Edit Admin User' : 'Register Admin User' ?></h3>
        
        <?php if (isset($errorMsg)): ?>
            <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 0.8rem; border-radius: 6px; margin: 1rem 0; font-size: 0.8rem;">
                <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php?page=admins" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
            <?php if ($editUser): ?>
                <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                <input type="hidden" name="existing_photo" value="<?= e($editUser['profile_photo']) ?>">
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Full Name *</label>
                <input type="text" name="name" class="form-control" required placeholder="Full Name" style="padding-left: 0.8rem; height: 38px;" value="<?= $editUser ? e($editUser['name']) : '' ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Email Address *</label>
                <input type="email" name="email" class="form-control" required placeholder="email@borgia.com" style="padding-left: 0.8rem; height: 38px;" value="<?= $editUser ? e($editUser['email']) : '' ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Password <?= $editUser ? '(Leave empty to keep current)' : '*' ?></label>
                <input type="password" name="password" class="form-control" placeholder="Password" style="padding-left: 0.8rem; height: 38px;" <?= $editUser ? '' : 'required' ?>>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">System Role</label>
                <select name="role" class="form-control" style="padding-left: 0.8rem; height: 38px; background-color: var(--bg-base); border: 1px solid var(--border-color); color: #fff; border-radius: 8px;">
                    <option value="editor" <?= ($editUser && $editUser['role'] === 'editor') ? 'selected' : '' ?>>Editor (Manage content)</option>
                    <option value="news_editor" <?= ($editUser && $editUser['role'] === 'news_editor') ? 'selected' : '' ?>>News Editor (News Board only)</option>
                    <option value="staff" <?= ($editUser && $editUser['role'] === 'staff') ? 'selected' : '' ?>>Staff (View metrics/enquiries)</option>
                    <option value="super_admin" <?= ($editUser && $editUser['role'] === 'super_admin') ? 'selected' : '' ?>>Super Admin (Full access + user admin)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Profile Photo</label>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <?php if ($editUser && $editUser['profile_photo']): ?>
                        <img src="<?= e($editUser['profile_photo']) ?>" style="height: 45px; width: 45px; object-fit: cover; border-radius: 50%;">
                    <?php endif; ?>
                    <input type="file" name="profile_photo" accept="image/*" style="font-size: 0.8rem; color: var(--text-muted);">
                </div>
            </div>

            <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem; cursor: pointer; margin: 0.3rem 0;">
                <input type="checkbox" name="is_active" value="1" <?= (!$editUser || $editUser['is_active']) ? 'checked' : '' ?> style="width: 15px; height: 15px;"> Account Enabled
            </label>

            <button type="submit" name="save_user" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.6rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; text-align: center;"><i class="fas fa-save"></i> Save Account</button>
            <?php if ($editUser): ?>
                <a href="admin.php?page=admins" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-color); text-align: center; text-decoration: none; padding: 0.6rem; border-radius: 8px; font-size: 0.85rem;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

</div>
