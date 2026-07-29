<?php
// admin/manage-news.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Manage News - Admin';
$message = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);
        
        $logStmt = $pdo->prepare("
            INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
            VALUES (?, 'deleted_news', 'news', ?, 'Deleted news article', ?)
        ");
        $logStmt->execute([$_SESSION['admin_id'], $id, $_SERVER['REMOTE_ADDR']]);
        
        $message = 'News article deleted successfully!';
    } catch (Exception $e) {
        $error = 'Error deleting article.';
    }
}

// Handle toggle publish
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $stmt = $pdo->prepare("UPDATE news SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$id]);
        
        $logStmt = $pdo->prepare("
            INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
            VALUES (?, 'toggled_news_status', 'news', ?, 'Toggled news publish status', ?)
        ");
        $logStmt->execute([$_SESSION['admin_id'], $id, $_SERVER['REMOTE_ADDR']]);
        
        $message = 'News status updated!';
    } catch (Exception $e) {
        $error = 'Error updating status.';
    }
}

// Fetch all news with category names
$newsStmt = $pdo->query("
    SELECT n.*, nc.name as category_name 
    FROM news n
    LEFT JOIN news_categories nc ON nc.id = n.category_id
    ORDER BY n.created_at DESC
");
$news = $newsStmt->fetchAll();
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
        /* Liquid Glass Admin Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0a0a0a; color: #fff; min-height: 100vh; }
        .admin-wrapper { display: flex; min-height: 100vh; }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(40px);
            padding: 30px 20px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.06);
        }
        .admin-sidebar .logo { text-align: center; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 30px; }
        .admin-sidebar .logo .icon-wrapper { display: inline-block; width: 55px; height: 55px; background: linear-gradient(135deg, #FF6B00, #e85e00); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 10px 30px rgba(255,107,0,0.25); }
        .admin-sidebar .logo i { font-size: 2rem; color: #fff; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.1rem; font-weight: 700; }
        .admin-sidebar .user { padding: 15px; background: rgba(255,255,255,0.04); border-radius: 16px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.06); }
        .admin-sidebar .user .name { font-weight: 600; color: #fff; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.5; color: rgba(255,255,255,0.6); }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.5); border-radius: 14px; transition: all 0.3s ease; margin-bottom: 4px; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: rgba(255,107,0,0.12); color: #FF6B00; border: 1px solid rgba(255,107,0,0.1); transform: translateX(4px); }
        .admin-sidebar nav a i { width: 20px; color: rgba(255,255,255,0.3); transition: all 0.3s ease; }
        .admin-sidebar nav a:hover i, .admin-sidebar nav a.active i { color: #FF6B00; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.4); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 14px; transition: all 0.3s ease; margin-top: 10px; }
        .logout-btn:hover { background: rgba(255,0,0,0.08); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }
        .logout-btn i { color: rgba(255,255,255,0.3); }
        .logout-btn:hover i { color: #ff6b6b; }

        /* Content */
        .admin-content { flex: 1; padding: 30px; background: #0a0a0a; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; padding: 20px 30px; background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .admin-header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; }
        .admin-header h1 i { color: #FF6B00; margin-right: 10px; }
        .btn-add { padding: 12px 24px; background: linear-gradient(135deg, #FF6B00, #e85e00); color: #fff; border: none; border-radius: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; box-shadow: 0 10px 30px rgba(255,107,0,0.3); }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(255,107,0,0.4); color: #fff; }

        .btn-edit { padding: 6px 14px; background: rgba(0,123,255,0.2); color: #4d9fff; border: 1px solid rgba(0,123,255,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-edit:hover { background: rgba(0,123,255,0.3); color: #4d9fff; }

        .btn-delete { padding: 6px 14px; background: rgba(255,0,0,0.15); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-delete:hover { background: rgba(255,0,0,0.25); color: #ff6b6b; }

        .btn-toggle { padding: 6px 14px; border: none; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-toggle.published { background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.1); }
        .btn-toggle.published:hover { background: rgba(76,175,80,0.25); }
        .btn-toggle.draft { background: rgba(255,107,0,0.15); color: #FF6B00; border: 1px solid rgba(255,107,0,0.1); }
        .btn-toggle.draft:hover { background: rgba(255,107,0,0.25); }

        .table-container { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border-radius: 20px; padding: 25px; overflow-x: auto; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; color: rgba(255,255,255,0.5); font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; color: rgba(255,255,255,0.8); }
        tr:hover { background: rgba(255,255,255,0.02); }

        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.published { background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.1); }
        .status-badge.draft { background: rgba(255,107,0,0.15); color: #FF6B00; border: 1px solid rgba(255,107,0,0.1); }

        .category-badge { display: inline-block; padding: 2px 10px; border-radius: 50px; font-size: 0.75rem; background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.06); }

        .actions { display: flex; gap: 6px; flex-wrap: wrap; }

        .alert-success { background: rgba(76,175,80,0.1); color: #4CAF50; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid rgba(76,175,80,0.1); }
        .alert-danger { background: rgba(255,0,0,0.1); color: #ff6b6b; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid rgba(255,0,0,0.1); }

        .no-data { text-align: center; padding: 60px 0; color: rgba(255,255,255,0.2); }
        .no-data i { font-size: 3rem; display: block; margin-bottom: 15px; color: rgba(255,255,255,0.05); }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.06); } .admin-header { flex-direction: column; align-items: stretch; padding: 15px; } }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
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
            <a href="manage-news.php" class="active"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-newspaper"></i> Manage News</h1>
            <a href="add-news.php" class="btn-add"><i class="fas fa-plus"></i> Add News</a>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (!empty($news)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news as $item): ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td>
                                    <strong><?= clean($item['title']) ?></strong>
                                    <?php if (!empty($item['featured_image'])): ?>
                                        <br><span style="font-size:0.75rem;color:rgba(255,255,255,0.3);"><i class="fas fa-image"></i> Has image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        <?= clean($item['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $item['is_published'] ? 'published' : 'draft' ?>">
                                        <?= $item['is_published'] ? 'Published' : 'Draft' ?>
                                    </span>
                                </td>
                                <td><?= number_format($item['views'] ?? 0) ?></td>
                                <td><?= formatDate($item['created_at'], 'M j, Y') ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit-news.php?id=<?= $item['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?toggle=<?= $item['id'] ?>" class="btn-toggle <?= $item['is_published'] ? 'published' : 'draft' ?>" onclick="return confirm('Toggle publish status?')">
                                            <?= $item['is_published'] ? '<i class="fas fa-eye"></i> Hide' : '<i class="fas fa-eye-slash"></i> Show' ?>
                                        </a>
                                        <a href="?delete=<?= $item['id'] ?>" class="btn-delete" onclick="return confirm('Delete this article permanently?')"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-newspaper"></i>
                    <p>No news articles yet. <a href="add-news.php" style="color:#FF6B00;">Create your first article</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>