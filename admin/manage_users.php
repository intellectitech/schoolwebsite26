<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'Manage Users - ' . getSetting($pdo, 'school_name');

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("
                    INSERT INTO admin_users (name, email, password, role, is_active) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    clean($_POST['name']),
                    clean($_POST['email']),
                    clean($_POST['password']), // Plain text
                    clean($_POST['role']),
                    isset($_POST['is_active']) ? 1 : 0
                ]);
                $_SESSION['flash'] = 'User added successfully!';
                $_SESSION['flash_type'] = 'success';
                break;
                
            case 'update':
                $stmt = $pdo->prepare("
                    UPDATE admin_users SET 
                        name = ?, email = ?, role = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    clean($_POST['name']),
                    clean($_POST['email']),
                    clean($_POST['role']),
                    isset($_POST['is_active']) ? 1 : 0,
                    (int)$_POST['id']
                ]);
                
                // Update password if provided
                if (!empty($_POST['password'])) {
                    $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                    $stmt->execute([clean($_POST['password']), (int)$_POST['id']]);
                }
                
                $_SESSION['flash'] = 'User updated successfully!';
                $_SESSION['flash_type'] = 'success';
                break;
                
            case 'delete':
                // Don't allow deleting yourself
                if ((int)$_POST['id'] === $_SESSION['admin_id']) {
                    $_SESSION['flash'] = 'You cannot delete your own account!';
                    $_SESSION['flash_type'] = 'error';
                } else {
                    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
                    $stmt->execute([(int)$_POST['id']]);
                    $_SESSION['flash'] = 'User deleted successfully!';
                    $_SESSION['flash_type'] = 'success';
                }
                break;
        }
        header('Location: manage-users.php');
        exit;
    }
}

// Fetch all users
$users = $pdo->query("SELECT * FROM admin_users ORDER BY id ASC")->fetchAll();

include '../includes/header.php';
?>

<main class="admin-main">
    <div class="container">
        <div class="admin-header">
            <h1>👤 Manage Admin Users</h1>
            <button class="btn btn-primary" onclick="openAddUser()">+ Add New User</button>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash-message flash-<?= $_SESSION['flash_type'] ?? 'info' ?>">
                <?= htmlspecialchars($_SESSION['flash']) ?>
                <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrapper">
            <?php if (empty($users)): ?>
                <p class="no-content">No users found.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><code style="font-size:0.75rem; background:#F1F5F9; padding:2px 6px; border-radius:4px;"><?= htmlspecialchars($user['password']) ?></code></td>
                                <td>
                                    <span class="role-badge role-<?= $user['role'] ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $user['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $user['is_active'] ? '✅ Active' : '❌ Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline edit-user" 
                                            data-id="<?= $user['id'] ?>"
                                            data-name="<?= htmlspecialchars($user['name']) ?>"
                                            data-email="<?= htmlspecialchars($user['email']) ?>"
                                            data-password="<?= htmlspecialchars($user['password']) ?>"
                                            data-role="<?= $user['role'] ?>"
                                            data-active="<?= $user['is_active'] ?>">
                                        ✏️ Edit
                                    </button>
                                    <?php if ($user['id'] !== $_SESSION['admin_id']): ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
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
</main>

<!-- Add/Edit User Modal -->
<div class="modal" id="userModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add User</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" id="userForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="userId" value="">
            
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" id="userName" required>
            </div>
            
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" id="userEmail" required>
            </div>
            
            <div class="form-group">
                <label>Password <span id="passwordLabel">*</span></label>
                <input type="text" name="password" id="userPassword" placeholder="Enter password">
                <small style="color:#94A3B8; font-size:0.75rem;">Leave blank to keep current password when editing</small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" id="userRole" required>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" id="userActive" checked>
                        Active
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Save User</button>
            <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-content {
    background: #FFFFFF;
    padding: 36px;
    border-radius: 16px;
    max-width: 550px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #E2E8F0;
}

.modal-header h2 {
    font-size: 1.4rem;
    color: #0A1628;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: #94A3B8;
    transition: all 0.3s ease;
    line-height: 1;
}

.modal-close:hover {
    color: #DC2626;
    transform: rotate(90deg);
}

/* Table Styles */
.admin-table-wrapper {
    overflow-x: auto;
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #E2E8F0;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.admin-table th {
    background: #F8FAFC;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    border-bottom: 2px solid #E2E8F0;
}

.admin-table td {
    padding: 10px 16px;
    border-bottom: 1px solid #F1F5F9;
}

.admin-table tr:hover td {
    background: #F8FAFC;
}

.status-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge.active { background: #D1FAE5; color: #065F46; }
.status-badge.inactive { background: #FEE2E2; color: #991B1B; }

.btn {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
}

.btn-primary {
    background: #C9A84C;
    color: #FFFFFF;
}

.btn-primary:hover {
    background: #A8893A;
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    color: #0A1628;
    border: 2px solid #E2E8F0;
}

.btn-outline:hover {
    background: #0A1628;
    color: #FFFFFF;
    border-color: #0A1628;
}

.btn-sm { padding: 4px 12px; font-size: 0.75rem; }
.btn-danger { background: #DC2626; color: #FFFFFF; }
.btn-danger:hover { background: #B91C1C; }

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-group { margin-bottom: 16px; }
.form-group label {
    display: block;
    font-weight: 600;
    font-size: 0.85rem;
    color: #334155;
    margin-bottom: 4px;
}
.form-group input,
.form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #E2E8F0;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}
.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #C9A84C;
    box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.1);
}

.no-content {
    text-align: center;
    color: #94A3B8;
    padding: 30px 0;
}

@media (max-width: 768px) {
    .form-row { grid-template-columns: 1fr; }
    .modal-content { padding: 24px; }
}
</style>

<script>
function openAddUser() {
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('formAction').value = 'add';
    document.getElementById('userId').value = '';
    document.getElementById('userForm').reset();
    document.getElementById('userActive').checked = true;
    document.getElementById('passwordLabel').textContent = '*';
    document.getElementById('userPassword').placeholder = 'Enter password';
    document.getElementById('userPassword').required = true;
    document.getElementById('userModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}

// Edit user
document.querySelectorAll('.edit-user').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('formAction').value = 'update';
        document.getElementById('userId').value = this.dataset.id;
        document.getElementById('userName').value = this.dataset.name;
        document.getElementById('userEmail').value = this.dataset.email;
        document.getElementById('userRole').value = this.dataset.role;
        document.getElementById('userActive').checked = this.dataset.active == '1';
        document.getElementById('passwordLabel').textContent = ' (leave blank to keep current)';
        document.getElementById('userPassword').placeholder = 'Leave blank to keep current';
        document.getElementById('userPassword').required = false;
        document.getElementById('userModal').style.display = 'flex';
    });
});

// Close modal on outside click
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

<?php include '../includes/footer.php'; ?>