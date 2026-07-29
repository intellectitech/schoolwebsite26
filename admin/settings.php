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

$settingsStmt = $pdo->query("SELECT setting_key, setting_value FROM school_info");
$currentSettings = $settingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$heroImages = $currentSettings['hero_images'] ?? '';
$images = $heroImages ? array_map('trim', explode(',', $heroImages)) : [];
$images = array_filter($images);
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
        /* Same Liquid Glass styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0a0a0a; color: #fff; min-height: 100vh; }
        .admin-wrapper { display: flex; min-height: 100vh; }

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

        .admin-content { flex: 1; padding: 30px; background: #0a0a0a; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; padding: 20px 30px; background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .admin-header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; }
        .admin-header h1 i { color: #FF6B00; margin-right: 10px; }

        .settings-form { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border-radius: 20px; padding: 40px; max-width: 900px; border: 1px solid rgba(255,255,255,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: rgba(255,255,255,0.7); font-size: 0.9rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 16px; border: none; border-radius: 14px; font-size: 1rem; transition: all 0.3s ease; font-family: inherit; background: rgba(255,255,255,0.06); color: #fff; border: 1px solid rgba(255,255,255,0.06); box-shadow: inset 0 2px 10px rgba(0,0,0,0.2); }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #FF6B00; background: rgba(255,255,255,0.08); }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn-save { padding: 14px 40px; background: linear-gradient(135deg, #FF6B00, #e85e00); color: #fff; border: none; border-radius: 14px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(255,107,0,0.3); }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(255,107,0,0.4); }

        .alert-success { background: rgba(76,175,80,0.1); color: #4CAF50; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid rgba(76,175,80,0.1); }
        .alert-danger { background: rgba(255,0,0,0.1); color: #ff6b6b; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid rgba(255,0,0,0.1); }

        .section-title { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 30px 0 20px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .section-title i { color: #FF6B00; margin-right: 10px; }
        .helper-text { font-size: 0.85rem; color: rgba(255,255,255,0.3); margin-top: 5px; }
        .logo-preview { display: flex; align-items: center; gap: 20px; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 12px; margin-top: 10px; border: 1px solid rgba(255,255,255,0.06); }
        .logo-preview img { max-height: 60px; max-width: 200px; }
        .logo-preview .no-logo { color: rgba(255,255,255,0.3); font-style: italic; }
        .hero-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 10px; }
        .hero-preview-item { position: relative; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.06); }
        .hero-preview-item img { width: 100%; height: 120px; object-fit: cover; }
        .hero-preview-item .order { position: absolute; bottom: 5px; left: 5px; background: rgba(0,0,0,0.7); color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 0.7rem; }

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

        <?php if ($success): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="settings-form">
            <h3 class="section-title"><i class="fas fa-school"></i> School Information</h3>
            
            <div class="form-group">
                <label for="school_name">School Name <span class="required">*</span></label>
                <input type="text" id="school_name" name="settings[school_name]" value="<?= clean($currentSettings['school_name'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="school_phone">Phone Number</label>
                    <input type="text" id="school_phone" name="settings[school_phone]" value="<?= clean($currentSettings['school_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="school_email">Email Address</label>
                    <input type="email" id="school_email" name="settings[school_email]" value="<?= clean($currentSettings['school_email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="school_address">Address</label>
                <input type="text" id="school_address" name="settings[school_address]" value="<?= clean($currentSettings['school_address'] ?? '') ?>">
            </div>

            <h3 class="section-title"><i class="fas fa-chart-bar"></i> Statistics</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="founded_year">Founded Year</label>
                    <input type="text" id="founded_year" name="settings[founded_year]" value="<?= clean($currentSettings['founded_year'] ?? '1985') ?>">
                </div>
                <div class="form-group">
                    <label for="total_students">Total Students</label>
                    <input type="text" id="total_students" name="settings[total_students]" value="<?= clean($currentSettings['total_students'] ?? '1,200') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_teachers">Total Teachers</label>
                    <input type="text" id="total_teachers" name="settings[total_teachers]" value="<?= clean($currentSettings['total_teachers'] ?? '60') ?>">
                </div>
                <div class="form-group">
                    <label for="pass_rate">UACE Pass Rate (%)</label>
                    <input type="text" id="pass_rate" name="settings[pass_rate]" value="<?= clean($currentSettings['pass_rate'] ?? '86') ?>">
                </div>
            </div>

            <h3 class="section-title"><i class="fas fa-image"></i> Hero Section</h3>

            <div class="form-group">
                <label for="hero_subtitle">Hero Subtitle/Tagline</label>
                <input type="text" id="hero_subtitle" name="settings[hero_subtitle]" 
                       value="<?= clean($currentSettings['hero_subtitle'] ?? 'A Center of Academic Excellence and Moral Integrity') ?>"
                       placeholder="A Center of Academic Excellence and Moral Integrity">
                <div class="helper-text"><i class="fas fa-info-circle"></i> This appears below the school name on the homepage hero.</div>
            </div>

            <div class="form-group">
                <label for="hero_images">Hero Slider Images</label>
                <textarea id="hero_images" name="settings[hero_images]" rows="3" 
                          placeholder="assets/images/hero1.jpg, assets/images/hero2.jpg, assets/images/hero3.jpg"><?= clean($currentSettings['hero_images'] ?? '') ?></textarea>
                <div class="helper-text"><i class="fas fa-info-circle"></i> Enter full image URLs separated by commas.</div>
            </div>

            <?php if (!empty($images)): ?>
                <div class="form-group">
                    <label>Current Hero Images Preview</label>
                    <div class="hero-preview-grid">
                        <?php foreach ($images as $index => $image): 
                            $image = trim($image);
                            if (empty($image)) continue;
                        ?>
                            <div class="hero-preview-item">
                                <img src="../<?= clean($image) ?>" alt="Slide <?= $index + 1 ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22120%22><rect width=%22200%22 height=%22120%22 fill=%22%23222%22/><text x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2212%22 fill=%22%23666%22 text-anchor=%22middle%22 dy=%22.3em%22>Image not found</text></svg>'">
                                <span class="order">Slide <?= $index + 1 ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <h3 class="section-title"><i class="fas fa-search"></i> SEO & Meta</h3>

            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="settings[meta_description]" rows="3"><?= clean($currentSettings['meta_description'] ?? '') ?></textarea>
                <div class="helper-text"><i class="fas fa-info-circle"></i> This appears in Google search results. Keep under 160 characters.</div>
            </div>

            <div style="margin-top:30px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.06);">
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save All Settings</button>
            </div>
        </form>
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>