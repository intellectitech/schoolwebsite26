<?php
// admin/upload-images.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Hero Images - Admin';
$message = '';
$error = '';

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hero_image'])) {
    $targetDir = '../assets/images/';
    
    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $file = $_FILES['hero_image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    
    if (!in_array($ext, $allowedExts)) {
        $error = 'Only JPG, PNG, WEBP, and GIF images are allowed.';
    } elseif ($file['size'] > 2097152) { // 2MB
        $error = 'Image size must be under 2MB.';
    } else {
        $fileName = 'hero_' . time() . '.' . $ext;
        $targetFile = $targetDir . $fileName;
        
        // Compress and save
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Get current hero images
            $currentImages = getSetting($pdo, 'hero_images', '');
            $images = $currentImages ? array_map('trim', explode(',', $currentImages)) : [];
            $images = array_filter($images); // Remove empty
            
            // Add new image (limit to 5)
            if (count($images) >= 5) {
                array_shift($images);
            }
            $images[] = 'assets/images/' . $fileName;
            
            // Update settings
            $newImages = implode(', ', $images);
            $stmt = $pdo->prepare("
                INSERT INTO school_info (setting_key, setting_value, updated_by) 
                VALUES ('hero_images', ?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?
            ");
            $stmt->execute([$newImages, $_SESSION['admin_id'], $newImages, $_SESSION['admin_id']]);
            
            $message = 'Image uploaded successfully!';
        } else {
            $error = 'Error uploading file.';
        }
    }
}

// Get current hero images
$heroImages = getSetting($pdo, 'hero_images', '');
$images = $heroImages ? array_map('trim', explode(',', $heroImages)) : [];
$images = array_filter($images);
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
        .upload-container { background: #fff; padding: 40px; border-radius: 12px; max-width: 900px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .upload-container h3 { color: #1a4d2e; margin-bottom: 20px; }
        .drop-zone { border: 2px dashed #e0e0e0; border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .drop-zone:hover { border-color: #1a4d2e; background: #f8f9fa; }
        .drop-zone i { font-size: 3rem; color: #1a4d2e; display: block; margin-bottom: 15px; }
        .drop-zone p { color: #666; }
        .drop-zone .btn-upload { padding: 12px 30px; background: #1a4d2e; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 15px; }
        .drop-zone .btn-upload:hover { background: #2d7a4a; }
        #fileInput { display: none; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 30px; }
        .image-item { position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .image-item img { width: 100%; height: 150px; object-fit: cover; }
        .image-item .order { position: absolute; bottom: 5px; left: 5px; background: rgba(0,0,0,0.7); color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 0.75rem; }
        .image-item .remove-btn { position: absolute; top: 5px; right: 5px; background: rgba(220,53,69,0.9); color: #fff; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 1rem; transition: all 0.3s; }
        .image-item .remove-btn:hover { transform: scale(1.1); background: #dc3545; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,0,0,0.1); color: #ff6b6b; }
        .btn-back { padding: 14px 24px; background: #6c757d; color: #fff; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-back:hover { background: #5a6268; color: #fff; }
        .btn-secondary { padding: 14px 24px; background: #1a4d2e; color: #fff; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-secondary:hover { background: #2d7a4a; color: #fff; }
        .header-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } }
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
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="upload-images.php"><i class="fas fa-images"></i> Hero Images</a>
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-images"></i> Hero Images Manager</h1>
            <div class="header-actions">
                <a href="settings.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Settings</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <div class="upload-container">
            <h3><i class="fas fa-upload"></i> Upload Hero Image</h3>
            <p style="color:#666;margin-bottom:20px;">Upload images for the homepage slider. Recommended: 1920x1080px, under 2MB. Max 5 images.</p>
            
            <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                <div class="drop-zone" id="dropZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p><strong>Drag & Drop</strong> your image here or click to browse</p>
                    <p style="font-size:0.85rem;color:#999;">JPG, PNG, WEBP, GIF (Max 2MB)</p>
                    <button type="button" class="btn-upload" onclick="document.getElementById('fileInput').click()">
                        <i class="fas fa-folder-open"></i> Choose Image
                    </button>
                    <input type="file" id="fileInput" name="hero_image" accept="image/*" required>
                </div>
            </form>

            <!-- Current Images -->
            <?php if (!empty($images)): ?>
                <h3 style="color:#1a4d2e;margin:30px 0 15px;"><i class="fas fa-list"></i> Current Hero Images (<?= count($images) ?>)</h3>
                <div class="image-grid">
                    <?php foreach ($images as $index => $image): ?>
                        <div class="image-item">
                            <img src="../<?= clean($image) ?>" alt="Hero <?= $index + 1 ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22150%22><rect width=%22200%22 height=%22150%22 fill=%22%23e0e0e0%22/><text x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2214%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22>Image not found</text></svg>'">
                            <span class="order">Slide <?= $index + 1 ?></span>
                            <a href="remove-hero.php?index=<?= $index ?>" class="remove-btn" onclick="return confirm('Remove this image from slider?')">×</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align:center;padding:40px 0;color:#999;margin-top:20px;">
                    <i class="fas fa-images" style="font-size:3rem;display:block;margin-bottom:15px;color:#ccc;"></i>
                    <p>No hero images uploaded yet.</p>
                    <p style="font-size:0.9rem;">Upload your first image above.</p>
                </div>
            <?php endif; ?>

            <div style="margin-top:30px;padding-top:20px;border-top:1px solid #e0e0e0;display:flex;gap:15px;flex-wrap:wrap;">
                <a href="settings.php" class="btn-secondary"><i class="fas fa-cog"></i> Manage All Settings</a>
                <a href="../index.php" target="_blank" class="btn-secondary" style="background:#17a2b8;"><i class="fas fa-eye"></i> View Homepage</a>
            </div>
        </div>
    </main>
</div>

<script>
// Drag and drop functionality
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = '#FFD700';
    this.style.background = '#f0f7f0';
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = '#e0e0e0';
    this.style.background = '';
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = '#e0e0e0';
    this.style.background = '';
    
    if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        document.getElementById('uploadForm').submit();
    }
});

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        document.getElementById('uploadForm').submit();
    }
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>