<?php
// admin/settings.php - SECURE & FIXED VERSION
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Get current settings
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM school_info");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

/**
 * Secure File Upload Handler
 */
function uploadFile($file, $folder) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error occurred.'];
    }
    
    // Max size: 5MB
    if ($file['size'] > 5242880) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
    }
    
    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'ico'];
    if (!in_array($ext, $allowedExts, true)) {
        return ['success' => false, 'error' => 'Invalid file extension.'];
    }
    
    // Validate actual MIME type (Security Fix)
    // Updated code - no finfo_close()
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
// finfo_close() removed - PHP handles it automatically
    
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/x-icon', 'image/vnd.microsoft.icon'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        return ['success' => false, 'error' => 'Invalid file content type.'];
    }
    
    $root = dirname(__DIR__);
    $uploadDir = $root . '/assets/images/' . $folder . '/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Secure directory permissions
    }
    
    $filename = $folder . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $fullPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return ['success' => true, 'path' => 'assets/images/' . $folder . '/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to save uploaded file.'];
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Action 1: Remove Hero Image (Handled via submit button in single main form)
    if (isset($_POST['remove_hero_index']) && is_numeric($_POST['remove_hero_index'])) {
        $index = (int)$_POST['remove_hero_index'];
        $current = $settings['hero_images'] ?? '';
        $images = $current ? array_map('trim', explode(',', $current)) : [];
        $images = array_values(array_filter($images));
        
        if (isset($images[$index])) {
            $root = dirname(__DIR__);
            $targetPath = realpath($root . '/' . $images[$index]);
            $allowedBase = realpath($root . '/assets/images/hero/');
            
            // Prevent Directory Traversal during unlink
            if ($targetPath && $allowedBase && strpos($targetPath, $allowedBase) === 0 && file_exists($targetPath)) {
                unlink($targetPath);
            }
            
            unset($images[$index]);
            $images = array_values($images);
            
            $newHero = implode(', ', $images);
            $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('hero_images', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$newHero, $newHero]);
            $settings['hero_images'] = $newHero;
            $message = 'Hero image removed successfully!';
        }
    } 
    // Action 2: Standard Save / Upload Form Submission
    else {
        // Handle Logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFile($_FILES['logo'], 'logo');
            if ($result['success']) {
                $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('school_logo', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$result['path'], $result['path']]);
                $settings['school_logo'] = $result['path'];
                $message = 'Logo uploaded successfully!';
            } else {
                $error = 'Logo error: ' . $result['error'];
            }
        }
        
        // Handle Favicon
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFile($_FILES['favicon'], 'logo');
            if ($result['success']) {
                $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('favicon', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$result['path'], $result['path']]);
                $settings['favicon'] = $result['path'];
                $message = 'Favicon uploaded successfully!';
            } else {
                $error = 'Favicon error: ' . $result['error'];
            }
        }
        
        // Handle Hero Images (Multiple)
        if (isset($_FILES['hero']) && !empty($_FILES['hero']['name'][0])) {
            $uploaded = [];
            $errors = [];
            
            $current = $settings['hero_images'] ?? '';
            $images = $current ? array_map('trim', explode(',', $current)) : [];
            $images = array_values(array_filter($images));
            
            $fileCount = count($_FILES['hero']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['hero']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['hero']['name'][$i],
                        'type' => $_FILES['hero']['type'][$i],
                        'tmp_name' => $_FILES['hero']['tmp_name'][$i],
                        'error' => $_FILES['hero']['error'][$i],
                        'size' => $_FILES['hero']['size'][$i]
                    ];
                    
                    $result = uploadFile($file, 'hero');
                    if ($result['success']) {
                        $uploaded[] = $result['path'];
                    } else {
                        $errors[] = 'Image ' . ($i + 1) . ': ' . $result['error'];
                    }
                }
            }
            
            foreach ($uploaded as $path) {
                if (count($images) >= 5) {
                    array_shift($images); // Maintain max 5 images
                }
                $images[] = $path;
            }
            
            if (!empty($uploaded)) {
                $newHero = implode(', ', $images);
                $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('hero_images', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$newHero, $newHero]);
                $settings['hero_images'] = $newHero;
                $message = count($uploaded) . ' hero image(s) uploaded successfully!';
            }
            if (!empty($errors)) {
                $error = implode('; ', $errors);
            }
        }
        
        // Update Text Settings
        $textFields = [
            'school_name', 'school_phone', 'school_email', 'school_address', 
            'founded_year', 'total_students', 'total_teachers', 'pass_rate', 
            'meta_description'
        ];
        
        foreach ($textFields as $field) {
            if (isset($_POST[$field])) {
                $value = clean($_POST[$field]);
                $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$field, $value, $value]);
                $settings[$field] = $value;
            }
        }
        
        if (empty($message) && empty($error)) {
            $message = 'Settings updated successfully!';
        }
    }
}

// Retrieve finalized state for HTML rendering
$schoolLogo = $settings['school_logo'] ?? '';
$favicon = $settings['favicon'] ?? '';
$heroImages = $settings['hero_images'] ?? '';
$images = $heroImages ? array_map('trim', explode(',', $heroImages)) : [];
$images = array_values(array_filter($images));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
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

        .settings-form { background: #fff; border-radius: 20px; padding: 40px; max-width: 900px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #0a0a0a; font-size: 0.9rem; }
        .form-group label .required { color: #FF6B6B; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #00C853; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        .file-upload-wrapper {
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            background: #fafafa;
        }
        .file-upload-wrapper:hover { border-color: #00C853; background: rgba(0,200,83,0.02); }
        .file-upload-wrapper input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .file-upload-wrapper .upload-icon { font-size: 2rem; color: #ccc; margin-bottom: 8px; }
        .file-upload-wrapper .upload-text { color: #999; font-size: 0.9rem; }
        .file-upload-wrapper .upload-text strong { color: #00C853; }
        .file-upload-wrapper .preview { margin-top: 10px; }
        .file-upload-wrapper .preview img { max-height: 80px; border-radius: 8px; border: 1px solid #e0e0e0; }

        .btn-save { padding: 14px 40px; background: linear-gradient(135deg, #009624, #00C853); color: #fff; border: none; border-radius: 14px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,200,83,0.3); }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(0,200,83,0.4); }

        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #f5c6cb; }

        .section-title { color: #0a0a0a; font-size: 1.2rem; font-weight: 700; margin: 30px 0 20px; padding-bottom: 10px; border-bottom: 2px solid #00C853; }
        .section-title i { color: #00C853; margin-right: 10px; }
        .helper-text { font-size: 0.85rem; color: #999; margin-top: 5px; }

        .logo-preview { display: flex; align-items: center; gap: 20px; padding: 15px; background: #f8f9fa; border-radius: 12px; margin-top: 10px; border: 1px solid #e0e0e0; }
        .logo-preview img { max-height: 60px; max-width: 200px; }
        .logo-preview .no-logo { color: #999; font-style: italic; }

        .hero-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 10px; }
        .hero-preview-item { position: relative; border-radius: 12px; overflow: hidden; border: 1px solid #e0e0e0; background: #fff; }
        .hero-preview-item img { width: 100%; height: 120px; object-fit: cover; }
        .hero-preview-item .order { position: absolute; bottom: 5px; left: 5px; background: rgba(0,0,0,0.7); color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 0.7rem; }
        .hero-preview-item .remove-btn { position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.8); color: #fff; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-size: 0.8rem; transition: all 0.3s; display: flex; align-items: center; justify-content: center; }
        .hero-preview-item .remove-btn:hover { transform: scale(1.1); background: #ff0000; }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .settings-form { padding: 20px; } .admin-header { flex-direction: column; align-items: stretch; } }
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
            <div class="name"><?= clean($_SESSION['admin_name'] ?? 'Admin') ?></div>
            <div class="role"><?= clean($_SESSION['admin_role'] ?? 'Admin') ?></div>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage-news.php"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-cog"></i> Site Settings</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <!-- SINGLE MAIN FORM (Prevents Form Nesting Issues) -->
        <form method="POST" action="" enctype="multipart/form-data" class="settings-form">
            
            <!-- LOGO SECTION -->
            <h3 class="section-title"><i class="fas fa-image"></i> Logo</h3>
            <div class="form-group">
                <label>School Logo</label>
                <?php if (!empty($schoolLogo)): ?>
                    <div class="logo-preview">
                        <span style="font-weight:600;color:#333;">Current Logo:</span>
                        <img src="../<?= clean($schoolLogo) ?>" alt="School Logo">
                    </div>
                <?php else: ?>
                    <div class="logo-preview">
                        <span class="no-logo"><i class="fas fa-image"></i> No logo uploaded</span>
                    </div>
                <?php endif; ?>
                <div class="file-upload-wrapper">
                    <input type="file" name="logo" accept="image/*">
                    <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="upload-text">
                        <strong>Click to upload</strong> or drag and drop<br>
                        <span style="font-size:0.85rem;color:#999;">JPG, PNG, WEBP (Max 2MB)</span>
                    </div>
                    <div class="preview"></div>
                </div>
            </div>

            <!-- FAVICON SECTION -->
            <h3 class="section-title"><i class="fas fa-image"></i> Favicon</h3>
            <div class="form-group">
                <label>Favicon</label>
                <?php if (!empty($favicon)): ?>
                    <div class="logo-preview">
                        <span style="font-weight:600;color:#333;">Current Favicon:</span>
                        <img src="../<?= clean($favicon) ?>" alt="Favicon" style="max-height:32px;">
                    </div>
                <?php else: ?>
                    <div class="logo-preview">
                        <span class="no-logo"><i class="fas fa-image"></i> No favicon uploaded</span>
                    </div>
                <?php endif; ?>
                <div class="file-upload-wrapper">
                    <input type="file" name="favicon" accept="image/*">
                    <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="upload-text">
                        <strong>Click to upload</strong> or drag and drop<br>
                        <span style="font-size:0.85rem;color:#999;">ICO, PNG (Max 1MB)</span>
                    </div>
                    <div class="preview"></div>
                </div>
            </div>

            <!-- HERO IMAGES SECTION -->
            <h3 class="section-title"><i class="fas fa-images"></i> Hero Images</h3>
            <div class="form-group">
                <label>Upload Hero Images</label>
                <div class="file-upload-wrapper" style="min-height:100px;">
                    <input type="file" name="hero[]" accept="image/*" multiple>
                    <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="upload-text">
                        <strong>Click to upload</strong> or drag and drop multiple images<br>
                        <span style="font-size:0.85rem;color:#999;">JPG, PNG, WEBP (Max 5MB each)<br>
                        <strong>Hold Ctrl (Windows) or Cmd (Mac) to select multiple</strong></span>
                    </div>
                    <div class="preview"></div>
                </div>
                <div class="helper-text"><i class="fas fa-info-circle"></i> Select multiple images at once. Max 5 images total.</div>
            </div>

            <?php if (!empty($images)): ?>
                <div class="form-group">
                    <label>Current Hero Images (<?= count($images) ?>)</label>
                    <div class="hero-preview-grid">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="hero-preview-item">
                                <img src="../<?= clean($image) ?>" alt="Slide <?= $index + 1 ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22120%22><rect width=%22200%22 height=%22120%22 fill=%22%23f0f0f0%22/><text x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2212%22 fill=%22%23ccc%22 text-anchor=%22middle%22 dy=%22.3em%22>Image not found</text></svg>'">
                                <span class="order">Slide <?= $index + 1 ?></span>
                                <button type="submit" name="remove_hero_index" value="<?= $index ?>" class="remove-btn" onclick="return confirm('Remove this hero image?');" title="Remove Image">×</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- SCHOOL INFORMATION SECTION -->
            <h3 class="section-title"><i class="fas fa-school"></i> School Information</h3>
            <div class="form-group">
                <label for="school_name">School Name <span class="required">*</span></label>
                <input type="text" id="school_name" name="school_name" value="<?= clean($settings['school_name'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="school_phone">Phone Number</label>
                    <input type="text" id="school_phone" name="school_phone" value="<?= clean($settings['school_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="school_email">Email Address</label>
                    <input type="email" id="school_email" name="school_email" value="<?= clean($settings['school_email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="school_address">Address</label>
                <input type="text" id="school_address" name="school_address" value="<?= clean($settings['school_address'] ?? '') ?>">
            </div>

            <!-- STATISTICS SECTION -->
            <h3 class="section-title"><i class="fas fa-chart-bar"></i> Statistics</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="founded_year">UCE</label>
                    <input type="text" id="founded_year" name="founded_year" value="<?= clean($settings['founded_year'] ?? '1985') ?>">
                </div>
                <div class="form-group">
                    <label for="total_students">Total Students</label>
                    <input type="text" id="total_students" name="total_students" value="<?= clean($settings['total_students'] ?? '1,200') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_teachers">Total Teachers</label>
                    <input type="text" id="total_teachers" name="total_teachers" value="<?= clean($settings['total_teachers'] ?? '60') ?>">
                </div>
                <div class="form-group">
                    <label for="pass_rate">Pass Rate (%)</label>
                    <input type="text" id="pass_rate" name="pass_rate" value="<?= clean($settings['pass_rate'] ?? '86') ?>">
                </div>
            </div>

            <!-- SEO SECTION -->
            <h3 class="section-title"><i class="fas fa-search"></i> SEO</h3>
            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="3"><?= clean($settings['meta_description'] ?? '') ?></textarea>
                <div class="helper-text"><i class="fas fa-info-circle"></i> Appears in Google search results. Keep under 160 characters.</div>
            </div>

            <!-- SAVE BUTTON -->
            <div style="margin-top:30px;padding-top:20px;border-top:2px solid #e0e0e0;">
                <button type="submit" name="save_settings" class="btn-save"><i class="fas fa-save"></i> Save All Settings</button>
            </div>
        </form>
    </main>
</div>

<script>
// Multiple file preview handler
document.querySelectorAll('.file-upload-wrapper input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function() {
        const preview = this.parentElement.querySelector('.preview');
        preview.innerHTML = '';
        
        if (this.files && this.files.length > 0) {
            const count = this.files.length;
            
            const info = document.createElement('div');
            info.style.padding = '10px';
            info.style.background = '#f0f7f0';
            info.style.borderRadius = '8px';
            info.style.color = '#00C853';
            info.style.fontWeight = '600';
            info.textContent = count + ' file(s) selected';
            preview.appendChild(info);
            
            if (this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxHeight = '80px';
                    img.style.borderRadius = '8px';
                    img.style.border = '1px solid #e0e0e0';
                    img.style.marginTop = '10px';
                    preview.appendChild(img);
                    
                    if (count > 1) {
                        const note = document.createElement('div');
                        note.style.fontSize = '0.8rem';
                        note.style.color = '#999';
                        note.textContent = '...and ' + (count - 1) + ' more';
                        preview.appendChild(note);
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        }
    });
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>