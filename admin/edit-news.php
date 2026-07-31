<?php
// admin/edit-news.php - WITH IMAGE UPLOAD
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/upload.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Edit News - Admin';
$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: manage-news.php');
    exit;
}

// Fetch article
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: manage-news.php');
    exit;
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM news_categories ORDER BY name")->fetchAll();

$uploadedImage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle image upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $result = uploadImage($_FILES['featured_image'], 'news', 5242880);
        if ($result['success']) {
            $uploadedImage = $result['path'];
            // Delete old image if exists
            if (!empty($article['featured_image'])) {
                deleteImage($article['featured_image']);
            }
        } else {
            $error = 'Image upload failed: ' . $result['error'];
        }
    }
    
    if (empty($error)) {
        $category_id = isset($_POST['category_id']) && $_POST['category_id'] ? (int)$_POST['category_id'] : null;
        $title = clean($_POST['title'] ?? '');
        $slug = clean($_POST['slug'] ?? '');
        $excerpt = clean($_POST['excerpt'] ?? '');
        $body = $_POST['body'] ?? '';
        $featured_image = !empty($uploadedImage) ? $uploadedImage : clean($_POST['featured_image_url'] ?? $article['featured_image']);
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $published_at = clean($_POST['published_at'] ?? '');
        
        if (empty($slug)) {
            $slug = createSlug($title);
        }
        
        // Check if slug is unique (except current article)
        $checkStmt = $pdo->prepare("SELECT id FROM news WHERE slug = ? AND id != ?");
        $checkStmt->execute([$slug, $id]);
        if ($checkStmt->fetch()) {
            $slug = $slug . '-' . uniqid();
        }
        
        if (empty($title) || empty($body)) {
            $error = 'Title and body are required.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    UPDATE news SET 
                        category_id = ?, title = ?, slug = ?, excerpt = ?, body = ?, 
                        featured_image = ?, is_published = ?, is_featured = ?, published_at = ? 
                    WHERE id = ?
                ");
                $stmt->execute([
                    $category_id, $title, $slug, $excerpt, $body, $featured_image,
                    $is_published, $is_featured, $published_at, $id
                ]);
                
                // Log activity
                $logStmt = $pdo->prepare("
                    INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address) 
                    VALUES (?, 'updated_news', 'news', ?, 'Updated news article: ' . ?, ?)
                ");
                $logStmt->execute([$_SESSION['admin_id'], $id, $title, $_SERVER['REMOTE_ADDR']]);
                
                $success = 'News article updated successfully!';
                
                // Refresh article data
                $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
                $stmt->execute([$id]);
                $article = $stmt->fetch();
                $uploadedImage = '';
            } catch (Exception $e) {
                $error = 'Error updating article: ' . $e->getMessage();
            }
        }
    }
}
// Handle remove image
if (isset($_GET['remove_image'])) {
    if (!empty($article['featured_image'])) {
        deleteImage($article['featured_image']);
        $stmt = $pdo->prepare("UPDATE news SET featured_image = NULL WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Image removed successfully!';
        
        // Refresh article data
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
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

        .form-container { background: #fff; border-radius: 20px; padding: 40px; max-width: 900px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #0a0a0a; font-size: 0.9rem; }
        .form-group label .required { color: #FF6B6B; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #00C853; }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .form-group textarea.body-editor { min-height: 300px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group.checkbox { display: flex; align-items: center; gap: 10px; }
        .form-group.checkbox label { margin-bottom: 0; cursor: pointer; }
        .form-group.checkbox input { width: auto; padding: 0; accent-color: #00C853; }

        .file-upload-wrapper {
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            background: #fafafa;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .file-upload-wrapper:hover { border-color: #00C853; background: rgba(0,200,83,0.02); }
        .file-upload-wrapper.dragover { border-color: #00C853; background: rgba(0,200,83,0.05); }
        .file-upload-wrapper input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .file-upload-wrapper .upload-icon { font-size: 3rem; color: #ccc; margin-bottom: 10px; }
        .file-upload-wrapper .upload-text { color: #999; font-size: 0.9rem; }
        .file-upload-wrapper .upload-text strong { color: #00C853; }
        .file-upload-wrapper .preview { margin-top: 15px; }
        .file-upload-wrapper .preview img { max-height: 150px; border-radius: 8px; border: 1px solid #e0e0e0; }

        .current-image { margin-top: 10px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .current-image img { max-height: 80px; border-radius: 4px; }
        .current-image .remove-link { color: #dc3545; font-size: 0.85rem; text-decoration: none; padding: 4px 12px; border: 1px solid #dc3545; border-radius: 4px; transition: all 0.3s; }
        .current-image .remove-link:hover { background: #dc3545; color: #fff; }

        .btn-submit { padding: 14px 40px; background: linear-gradient(135deg, #009624, #00C853); color: #fff; border: none; border-radius: 14px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,200,83,0.3); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(0,200,83,0.4); }
        .btn-back { padding: 12px 24px; background: #e0e0e0; color: #666; border: none; border-radius: 14px; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-back:hover { background: #ccc; }
        .btn-view { padding: 12px 24px; background: rgba(23,162,184,0.15); color: #17a2b8; border: 1px solid rgba(23,162,184,0.1); border-radius: 14px; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-view:hover { background: rgba(23,162,184,0.25); }

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

    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-edit"></i> Edit News Article</h1>
            <div class="header-actions">
                <a href="../article.php?slug=<?= clean($article['slug']) ?>" target="_blank" class="btn-view"><i class="fas fa-eye"></i> View</a>
                <a href="manage-news.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
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
                        <label for="title">Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" required placeholder="Article title" value="<?= clean($article['title']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" placeholder="auto-generated-from-title" value="<?= clean($article['slug']) ?>">
                        <div class="helper-text"><i class="fas fa-info-circle"></i> Leave blank to auto-generate from title</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">Uncategorized</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($article['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= clean($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="published_at">Publish Date</label>
                        <input type="datetime-local" id="published_at" name="published_at" value="<?= date('Y-m-d\TH:i', strtotime($article['published_at'] ?? $article['created_at'])) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="excerpt">Excerpt (Summary)</label>
                    <textarea id="excerpt" name="excerpt" rows="3" placeholder="Brief summary for listing pages"><?= clean($article['excerpt']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="body">Body Content <span class="required">*</span></label>
                    <textarea id="body" name="body" class="body-editor" required placeholder="Full article content..."><?= clean($article['body']) ?></textarea>
                    <div class="helper-text"><i class="fas fa-info-circle"></i> HTML tags are allowed (p, h1-h6, ul, ol, li, strong, em, a, img)</div>
                </div>

                <div class="form-group">
                    <label>Featured Image</label>
                    
                    <?php if (!empty($article['featured_image'])): ?>
                        <div class="current-image">
                            <span style="font-weight:600;color:#333;">Current Image:</span>
                            <img src="../<?= clean($article['featured_image']) ?>" alt="Current featured image">
                            <a href="?remove_image=1" class="remove-link" onclick="return confirm('Remove this image?')">Remove</a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="file-upload-wrapper" id="fileUploadWrapper">
                        <input type="file" id="featured_image" name="featured_image" accept="image/*">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="upload-text">
                            <strong>Click to upload</strong> or drag and drop<br>
                            <span style="font-size:0.85rem;color:#999;">JPG, PNG, WEBP, GIF (Max 5MB)</span>
                        </div>
                        <div class="preview" id="imagePreview"></div>
                    </div>
                    <div class="helper-text"><i class="fas fa-info-circle"></i> Upload new image to replace current</div>
                    <input type="text" id="featured_image_url" name="featured_image_url" placeholder="Or enter image URL" style="margin-top:10px;" value="<?= clean($article['featured_image']) ?>">
                </div>

                <div class="form-row">
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_published" name="is_published" <?= $article['is_published'] ? 'checked' : '' ?>>
                        <label for="is_published">Published</label>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_featured" name="is_featured" <?= $article['is_featured'] ? 'checked' : '' ?>>
                        <label for="is_featured">Feature on homepage hero</label>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Update Article</button>
                    <a href="manage-news.php" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
// Image preview
document.getElementById('featured_image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxHeight = '150px';
            img.style.borderRadius = '8px';
            img.style.border = '1px solid #e0e0e0';
            preview.appendChild(img);
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Drag and drop
const wrapper = document.getElementById('fileUploadWrapper');
wrapper.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
});
wrapper.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
});
wrapper.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('featured_image').files = files;
        document.getElementById('featured_image').dispatchEvent(new Event('change'));
    }
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>