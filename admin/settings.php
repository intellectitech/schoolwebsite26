<?php
// admin/settings.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Settings - Admin';
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = $_POST['settings'] ?? [];
    
    try {
        foreach ($settings as $key => $value) {
            $value = clean($value);
            $stmt = $pdo->prepare("
                INSERT INTO school_info (setting_key, setting_value, updated_by) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?
            ");
            $stmt->execute([$key, $value, $_SESSION['admin_id'], $value, $_SESSION['admin_id']]);
        }
        
        // Log activity
        $stmt = $pdo->prepare("
            INSERT INTO audit_log (admin_id, action, table_name, description, ip_address) 
            VALUES (?, 'updated_settings', 'school_info', 'Updated site settings', ?)
        ");
        $stmt->execute([$_SESSION['admin_id'], $_SERVER['REMOTE_ADDR']]);
        
        $success = 'Settings updated successfully!';
    } catch (Exception $e) {
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get current settings
$settingsStmt = $pdo->query("SELECT setting_key, setting_value FROM school_info");
$currentSettings = $settingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get hero images for preview
$heroImages = $currentSettings['hero_images'] ?? '';
$images = $heroImages ? array_map('trim', explode(',', $heroImages)) : [];
$images = array_filter($images);

// Get logo
$schoolLogo = $currentSettings['school_logo'] ?? '';
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
        .settings-form { background: #fff; padding: 40px; border-radius: 12px; max-width: 900px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .settings-form .form-group { margin-bottom: 20px; }
        .settings-form label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem; }
        .settings-form label .required { color: #dc3545; }
        .settings-form input, .settings-form textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; }
        .settings-form input:focus, .settings-form textarea:focus { outline: none; border-color: #1a4d2e; }
        .settings-form textarea { resize: vertical; min-height: 80px; }
        .settings-form .btn-save { padding: 14px 40px; background: linear-gradient(135deg, #FFD700, #f5c842); color: #1a4d2e; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .settings-form .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,215,0,0.4); }
        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 8px; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,0,0,0.1); color: #ff6b6b; }
        .hero-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 10px; }
        .hero-preview-item { position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .hero-preview-item img { width: 100%; height: 120px; object-fit: cover; }
        .hero-preview-item .order { position: absolute; bottom: 5px; left: 5px; background: rgba(0,0,0,0.7); color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 0.7rem; }
        .logo-preview { display: flex; align-items: center; gap: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-top: 10px; }
        .logo-preview img { max-height: 60px; max-width: 200px; }
        .logo-preview .no-logo { color: #999; font-style: italic; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .helper-text { font-size: 0.85rem; color: #666; margin-top: 5px; }
        .helper-text i { color: #1a4d2e; }
        .section-divider { border: none; border-top: 2px solid #FFD700; margin: 30px 0; }
        .section-title { color: #1a4d2e; font-size: 1.2rem; font-weight: 700; margin: 30px 0 20px; padding-bottom: 10px; border-bottom: 2px solid #FFD700; }
        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .settings-form { padding: 20px; } }
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
            <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-cog"></i> Site Settings</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="settings-form">
            
            <!-- ========================================= -->
            <!-- SCHOOL INFORMATION SECTION -->
            <!-- ========================================= -->
            <h3 class="section-title"><i class="fas fa-school"></i> School Information</h3>
            
            <div class="form-group">
                <label for="school_name">School Name <span class="required">*</span></label>
                <input type="text" id="school_name" name="settings[school_name]" value="<?= clean($currentSettings['school_name'] ?? '') ?>" required>
                <div class="helper-text"><i class="fas fa-info-circle"></i> This appears in the header, title, and throughout the site.</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="school_logo">School Logo URL</label>
                    <input type="text" id="school_logo" name="settings[school_logo]" 
                           value="<?= clean($currentSettings['school_logo'] ?? '') ?>" 
                           placeholder="assets/images/logo.png">
                    <div class="helper-text">
                        <i class="fas fa-info-circle"></i> Upload your logo to assets/images/logo.png or enter the full URL.
                        Recommended: 200x60px, transparent background (PNG).
                    </div>
                    <?php if (!empty($schoolLogo)): ?>
                        <div class="logo-preview">
                            <span style="font-weight:600;color:#333;">Current Logo:</span>
                            <img src="../<?= clean($schoolLogo) ?>" alt="School Logo">
                        </div>
                    <?php else: ?>
                        <div class="logo-preview">
                            <span class="no-logo"><i class="fas fa-image"></i> No logo uploaded yet</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="favicon">Favicon URL</label>
                    <input type="text" id="favicon" name="settings[favicon]" 
                           value="<?= clean($currentSettings['favicon'] ?? '') ?>" 
                           placeholder="assets/images/favicon.ico">
                    <div class="helper-text">
                        <i class="fas fa-info-circle"></i> Small icon in browser tab. 
                        Recommended: 32x32px (ICO) or 64x64px (PNG).
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="school_phone">Phone Number</label>
                    <input type="text" id="school_phone" name="settings[school_phone]" value="<?= clean($currentSettings['school_phone'] ?? '') ?>" placeholder="+256-700-123456">
                </div>
                <div class="form-group">
                    <label for="school_email">Email Address</label>
                    <input type="email" id="school_email" name="settings[school_email]" value="<?= clean($currentSettings['school_email'] ?? '') ?>" placeholder="info@school.ug">
                </div>
            </div>

            <div class="form-group">
                <label for="school_address">Physical Address</label>
                <input type="text" id="school_address" name="settings[school_address]" value="<?= clean($currentSettings['school_address'] ?? '') ?>" placeholder="P.O. Box 123, Kampala, Uganda">
            </div>

            <hr class="section-divider">

            <!-- ========================================= -->
            <!-- STATISTICS SECTION -->
            <!-- ========================================= -->
            <h3 class="section-title"><i class="fas fa-chart-bar"></i> Statistics & Numbers</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="founded_year">Founded Year</label>
                    <input type="text" id="founded_year" name="settings[founded_year]" value="<?= clean($currentSettings['founded_year'] ?? '1985') ?>" placeholder="1985">
                    <div class="helper-text"><i class="fas fa-info-circle"></i> Displayed in stats bar.</div>
                </div>
                <div class="form-group">
                    <label for="total_students">Total Students</label>
                    <input type="text" id="total_students" name="settings[total_students]" value="<?= clean($currentSettings['total_students'] ?? '1,200') ?>" placeholder="1,200">
                    <div class="helper-text"><i class="fas fa-info-circle"></i> Use numbers with commas (e.g., 1,200).</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_teachers">Total Teachers</label>
                    <input type="text" id="total_teachers" name="settings[total_teachers]" value="<?= clean($currentSettings['total_teachers'] ?? '60') ?>" placeholder="60">
                </div>
                <div class="form-group">
                    <label for="pass_rate">UACE Pass Rate (%)</label>
                    <input type="text" id="pass_rate" name="settings[pass_rate]" value="<?= clean($currentSettings['pass_rate'] ?? '86') ?>" placeholder="86">
                    <div class="helper-text"><i class="fas fa-info-circle"></i> Displayed as percentage in stats bar.</div>
                </div>
            </div>

            <hr class="section-divider">

            <!-- ========================================= -->
            <!-- HERO SECTION -->
            <!-- ========================================= -->
            <h3 class="section-title"><i class="fas fa-image"></i> Hero Section</h3>

            <div class="form-group">
                <label for="hero_subtitle">Hero Subtitle / Tagline</label>
                <input type="text" id="hero_subtitle" name="settings[hero_subtitle]" 
                       value="<?= clean($currentSettings['hero_subtitle'] ?? 'A Center of Academic Excellence and Moral Integrity') ?>"
                       placeholder="A Center of Academic Excellence and Moral Integrity">
                <div class="helper-text"><i class="fas fa-info-circle"></i> This appears below the school name on the homepage hero.</div>
            </div>

            <div class="form-group">
                <label for="hero_images">Hero Slider Images</label>
                <textarea id="hero_images" name="settings[hero_images]" rows="4" 
                          placeholder="assets/images/hero1.jpg, assets/images/hero2.jpg, assets/images/hero3.jpg"><?= clean($currentSettings['hero_images'] ?? '') ?></textarea>
                <div class="helper-text">
                    <i class="fas fa-info-circle"></i> Enter full image URLs separated by commas. 
                    Recommended: 1920x1080px, compressed under 200KB.
                    <br>
                    <i class="fas fa-example"></i> Example: assets/images/school-photo.jpg, assets/images/campus.jpg, assets/images/students.jpg
                </div>
            </div>

            <!-- Hero Images Preview -->
            <?php if (!empty($images)): ?>
                <div class="form-group">
                    <label>Current Hero Images Preview</label>
                    <div class="hero-preview-grid">
                        <?php foreach ($images as $index => $image): 
                            $image = trim($image);
                            if (empty($image)) continue;
                        ?>
                            <div class="hero-preview-item">
                                <img src="../<?= clean($image) ?>" alt="Slide <?= $index + 1 ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22120%22><rect width=%22200%22 height=%22120%22 fill=%22%23e0e0e0%22/><text x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2212%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22>Image not found</text></svg>'">
                                <span class="order">Slide <?= $index + 1 ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="helper-text" style="margin-top:10px;">
                        <i class="fas fa-info-circle"></i> To reorder, change the order of URLs in the textarea above.
                        To remove an image, delete its URL from the list.
                    </div>
                </div>
            <?php else: ?>
                <div class="form-group" style="background:#f8f9fa;padding:20px;border-radius:8px;text-align:center;color:#666;">
                    <i class="fas fa-images" style="font-size:2rem;display:block;margin-bottom:10px;color:#ccc;"></i>
                    No hero images added yet. Add image URLs above or go to <a href="upload-images.php" style="color:#1a4d2e;font-weight:600;">Hero Images</a> to upload.
                </div>
            <?php endif; ?>

            <hr class="section-divider">

            <!-- ========================================= -->
            <!-- SEO SECTION -->
            <!-- ========================================= -->
            <h3 class="section-title"><i class="fas fa-search"></i> SEO & Meta</h3>

            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="settings[meta_description]" rows="3"><?= clean($currentSettings['meta_description'] ?? '') ?></textarea>
                <div class="helper-text">
                    <i class="fas fa-info-circle"></i> This appears in Google search results. Keep under 160 characters.
                    <br>
                    <span id="charCount" style="color:#999;">0 characters</span>
                </div>
            </div>

            <div style="margin-top:30px;padding-top:20px;border-top:2px solid #e0e0e0;display:flex;gap:15px;flex-wrap:wrap;">
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save All Settings</button>
                <a href="../index.php" target="_blank" class="btn-save" style="background:#17a2b8;text-decoration:none;display:inline-block;text-align:center;color:#fff;">
                    <i class="fas fa-eye"></i> View Website
                </a>
            </div>
        </form>
    </main>
</div>

<script>
// Character counter for meta description
document.addEventListener('DOMContentLoaded', function() {
    const metaInput = document.getElementById('meta_description');
    const charCount = document.getElementById('charCount');
    
    if (metaInput && charCount) {
        metaInput.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + ' characters';
            if (count > 160) {
                charCount.style.color = '#dc3545';
            } else {
                charCount.style.color = '#28a745';
            }
        });
        
        // Trigger on load
        const event = new Event('input');
        metaInput.dispatchEvent(event);
    }
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>