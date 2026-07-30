<?php
// admin/manage-staff.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Manage Staff - Admin';
$message = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Staff member deleted successfully!';
    } catch (Exception $e) {
        $error = 'Error deleting staff member.';
    }
}

// Handle toggle active status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $stmt = $pdo->prepare("UPDATE staff SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Staff status updated successfully!';
    } catch (Exception $e) {
        $error = 'Error updating status.';
    }
}

// Handle toggle management status
if (isset($_GET['toggle_management']) && is_numeric($_GET['toggle_management'])) {
    $id = (int)$_GET['toggle_management'];
    try {
        $stmt = $pdo->prepare("UPDATE staff SET is_management = NOT is_management WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Management status updated successfully!';
    } catch (Exception $e) {
        $error = 'Error updating management status.';
    }
}

// Fetch all staff with department names
$staffStmt = $pdo->query("
    SELECT s.*, d.name as department_name 
    FROM staff s
    LEFT JOIN departments d ON d.id = s.department_id
    ORDER BY s.is_management DESC, s.sort_order, s.last_name
");
$staff = $staffStmt->fetchAll();

// Fetch departments for filter
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();

// Department filter
$deptFilter = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$deptCondition = $deptFilter ? "AND s.department_id = $deptFilter" : "";

if ($deptFilter) {
    $staffStmt = $pdo->prepare("
        SELECT s.*, d.name as department_name 
        FROM staff s
        LEFT JOIN departments d ON d.id = s.department_id
        WHERE s.is_active = 1 $deptCondition
        ORDER BY s.is_management DESC, s.sort_order, s.last_name
    ");
    $staffStmt->execute();
    $staff = $staffStmt->fetchAll();
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

        .btn-add { padding: 12px 24px; background: linear-gradient(135deg, #009624, #00C853); color: #fff; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; box-shadow: 0 10px 30px rgba(0,200,83,0.3); }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(0,200,83,0.4); color: #fff; }

        .btn-edit { padding: 6px 14px; background: rgba(0,123,255,0.2); color: #4d9fff; border: 1px solid rgba(0,123,255,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-edit:hover { background: rgba(0,123,255,0.3); color: #4d9fff; }

        .btn-delete { padding: 6px 14px; background: rgba(255,0,0,0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: rgba(255,0,0,0.25); color: #ff6b6b; }

        .btn-toggle { padding: 6px 14px; border: none; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-toggle.active { background: rgba(0,200,83,0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); }
        .btn-toggle.active:hover { background: rgba(0,200,83,0.25); }
        .btn-toggle.inactive { background: rgba(255,107,107,0.15); color: #FF6B6B; border: 1px solid rgba(255,107,107,0.1); }
        .btn-toggle.inactive:hover { background: rgba(255,107,107,0.25); }

        .btn-management { padding: 6px 14px; border: none; border-radius: 8px; font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; font-weight: 600; }
        .btn-management.on { background: rgba(255,215,0,0.15); color: #FFD700; border: 1px solid rgba(255,215,0,0.1); }
        .btn-management.on:hover { background: rgba(255,215,0,0.25); }
        .btn-management.off { background: rgba(200,200,200,0.15); color: #999; border: 1px solid rgba(200,200,200,0.1); }
        .btn-management.off:hover { background: rgba(200,200,200,0.25); }

        .staff-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .staff-filters a {
            padding: 8px 16px;
            border-radius: 50px;
            background: #fff;
            color: #333;
            transition: all 0.3s;
            font-size: 0.85rem;
            text-decoration: none;
            border: 1px solid #e0e0e0;
        }
        .staff-filters a:hover,
        .staff-filters a.active {
            background: #00C853;
            color: #fff;
            border-color: #00C853;
        }

        .table-container { background: #fff; border-radius: 20px; padding: 25px; overflow-x: auto; box-shadow: 0 5px 30px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; color: #666; font-weight: 600; border-bottom: 2px solid #e0e0e0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        tr:hover { background: #f8f9fa; }

        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.active { background: rgba(0,200,83,0.15); color: #00C853; border: 1px solid rgba(0,200,83,0.1); }
        .status-badge.inactive { background: rgba(255,107,107,0.15); color: #FF6B6B; border: 1px solid rgba(255,107,107,0.1); }

        .management-badge { display: inline-block; padding: 2px 10px; border-radius: 50px; font-size: 0.65rem; font-weight: 700; background: rgba(255,215,0,0.2); color: #FFD700; border: 1px solid rgba(255,215,0,0.1); }

        .actions { display: flex; gap: 6px; flex-wrap: wrap; }

        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #f5c6cb; }

        .no-data { text-align: center; padding: 60px 0; color: #999; }
        .no-data i { font-size: 3rem; display: block; margin-bottom: 15px; color: #ccc; }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .admin-header { flex-direction: column; align-items: stretch; } .staff-filters { justify-content: center; } }
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
            <h1><i class="fas fa-users"></i> Manage Staff</h1>
            <a href="add-staff.php" class="btn-add"><i class="fas fa-plus"></i> Add Staff Member</a>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <!-- Department Filters -->
        <div class="staff-filters">
            <a href="manage-staff.php" class="<?= $deptFilter == 0 ? 'active' : '' ?>">All Departments</a>
            <?php foreach ($departments as $dept): ?>
                <a href="manage-staff.php?department=<?= $dept['id'] ?>" class="<?= $deptFilter == $dept['id'] ? 'active' : '' ?>">
                    <?= clean($dept['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="table-container">
            <?php if (!empty($staff)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Leadership</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staff as $member): ?>
                            <tr>
                                <td>
                                    <strong><?= clean($member['title'] ?? '') ?> <?= clean($member['first_name']) ?> <?= clean($member['last_name']) ?></strong>
                                    <?php if (!empty($member['qualification'])): ?>
                                        <br><span style="font-size:0.75rem;color:#999;"><?= clean($member['qualification']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= clean($member['role']) ?></td>
                                <td><?= clean($member['department_name'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="status-badge <?= $member['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $member['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($member['is_management']): ?>
                                        <span class="management-badge"><i class="fas fa-star"></i> Leader</span>
                                    <?php else: ?>
                                        <span style="color:#999;font-size:0.75rem;">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="edit-staff.php?id=<?= $member['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?toggle=<?= $member['id'] ?>" class="btn-toggle <?= $member['is_active'] ? 'active' : 'inactive' ?>" onclick="return confirm('Toggle staff visibility?')">
                                            <?= $member['is_active'] ? '<i class="fas fa-eye"></i> Hide' : '<i class="fas fa-eye-slash"></i> Show' ?>
                                        </a>
                                        <a href="?toggle_management=<?= $member['id'] ?>" class="btn-management <?= $member['is_management'] ? 'on' : 'off' ?>" onclick="return confirm('Toggle leadership status?')">
                                            <?= $member['is_management'] ? '<i class="fas fa-star"></i> Leader' : '<i class="fas fa-star-o"></i> Make Leader' ?>
                                        </a>
                                        <a href="?delete=<?= $member['id'] ?>" class="btn-delete" onclick="return confirm('Delete this staff member permanently?')"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-users"></i>
                    <p>No staff members found. <a href="add-staff.php" style="color:#00C853;font-weight:600;">Add your first staff member</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>