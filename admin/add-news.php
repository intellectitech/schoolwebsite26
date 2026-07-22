<?php
// admin/add-news.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Add News - Admin';
$error = '';
$success = '';

// Fetch categories
$categories = $pdo->query("SELECT * FROM news_categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = isset($_POST['category_id']) && $_POST['category_id'] ? (int)$_POST['category_id'] : null;
    $title = clean($_POST['title'] ?? '');
    $slug = clean($_POST['slug'] ?? '');
    $excerpt = clean($_POST['excerpt'] ?? '');
    $body = $_POST['body'] ?? '';
    $featured_image = clean($_POST['featured_image'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $published_at = clean($_POST['published_at'] ?? '');
    
    // Auto-generate slug if empty
    if (empty($slug)) {
        $slug = createSlug($title);
    }
    
    // Check if slug is unique
    $checkStmt = $pdo->prepare("SELECT id FROM news WHERE slug = ?");
    $checkStmt->execute([$slug]);
    if ($checkStmt->fetch()) {
        $slug = $slug . '-' . uniqid();
    }
    
    if (empty($title) || empty($body)) {
        $error = 'Title and body are required.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, 
                                  author_id, is_published, is_featured, published_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $category_id, $title, $slug, $excerpt, $body, $featured_image,
                $_SESSION['admin_id'], $is_published, $is_featured, 
                $published_at ?: date('Y-m-d H:i:s')
            ]);
            
            $newsId = $pdo->lastInsertId();
            
            // Log activity
            $logStmt = $pdo->prepare("
                INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
                VALUES (?, 'created_news', 'news', ?, 'Created news article: ' . ?, ?)
            ");
            $logStmt->execute([$_SESSION['admin_id'], $newsId, $title, $_SERVER['REMOTE_ADDR']]);
            
            $success = 'News article created successfully!';
            
            // Clear form
            $_POST = [];
        } catch (Exception $e) {
            $error = 'Error creating article: ' . $e->getMessage();
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
        /* Same sidebar styles as manage-news.php */
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
        .form-container { background: #fff; padding: 40px; border-radius: 12px; max-width: 900px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem; }
        .form-group label .required { color: #dc3545; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #1a4d2e; }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .form-group textarea.body-editor { min-height: 300px; }
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
            <a href="manage-news.php" class="active"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="upload-images.php"><i class="fas fa-images"></i> Hero Images</a>
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
            <h1><i class="fas fa-plus-circle"></i> Add News Article</h1>
            <a href="manage-news.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to News</a>
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
                        <label for="title">Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" required placeholder="Article title" value="<?= clean($_POST['title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" placeholder="auto-generated-from-title" value="<?= clean($_POST['slug'] ?? '') ?>">
                        <small style="color:#999;">Leave blank to auto-generate from title</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">Uncategorized</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= clean($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="published_at">Publish Date</label>
                        <input type="datetime-local" id="published_at" name="published_at" value="<?= clean($_POST['published_at'] ?? date('Y-m-d\TH:i')) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="excerpt">Excerpt (Summary)</label>
                    <textarea id="excerpt" name="excerpt" rows="3" placeholder="Brief summary for listing pages"><?= clean($_POST['excerpt'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="body">Body Content <span class="required">*</span></label>
                    <textarea id="body" name="body" class="body-editor" required placeholder="Full article content..."><?= clean($_POST['body'] ?? '') ?></textarea>
                    <small style="color:#999;">HTML tags are allowed (p, h1-h6, ul, ol, li, strong, em, a, img)</small>
                </div>

                <div class="form-group">
                    <label for="featured_image">Featured Image URL</label>
                    <input type="text" id="featured_image" name="featured_image" placeholder="assets/images/news/article.jpg" value="<?= clean($_POST['featured_image'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_published" name="is_published" <?= isset($_POST['is_published']) ? 'checked' : 'checked' ?>>
                        <label for="is_published">Publish immediately</label>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_featured" name="is_featured" <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
                        <label for="is_featured">Feature on homepage hero</label>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Save Article</button>
                    <a href="manage-news.php" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>