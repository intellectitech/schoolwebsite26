<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_settings.php

$msg = $_GET['msg'] ?? '';

// Handle Bulk Settings Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $updatedSettings = $_POST['settings'] ?? [];
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE setting_value = ?");
                               
        foreach ($updatedSettings as $key => $val) {
            $stmt->execute([$key, trim($val), trim($val)]);
        }
        
        $pdo->commit();
        log_audit_action('update', 'school_info', 0, "Updated system settings parameters");
        
        header("Location: admin.php?page=settings&msg=updated");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errorMsg = "Database error: " . $e->getMessage();
    }
}

// Fetch settings from db
$settings = [];
try {
    $rows = $pdo->query("SELECT setting_key, setting_value FROM school_info")->fetchAll();
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    // Fail silently
}

// Helper to echo setting or default
function setting_val($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? $settings[$key] : $default;
}
?>

<div class="dashboard-card" style="text-align: left;">
    <div class="card-header-flex" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 2rem;">
        <h3>School Global Settings</h3>
        <div>
            <?php if ($msg === 'updated'): ?>
                <span style="color: var(--accent-green); font-size: 0.85rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Settings updated successfully!</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($errorMsg)): ?>
        <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
            <i class="fas fa-exclamation-triangle"></i> <?= e($errorMsg) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="admin.php?page=settings" style="display: flex; flex-direction: column; gap: 2rem;">
        <input type="hidden" name="save_settings" value="1">

        <!-- 1. Brand Identity -->
        <div>
            <h4 style="color: var(--accent-yellow); font-size: 1.05rem; border-left: 3px solid var(--accent-yellow); padding-left: 0.6rem; margin-bottom: 1.2rem;">School Identity</h4>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Official School Name</label>
                    <input type="text" name="settings[school_name]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('school_name', 'St. FRANCIS BORGIA HIGH SCHOOL')) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">School Slogan / Motto</label>
                    <input type="text" name="settings[motto]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('motto', 'Called to Shine')) ?>">
                </div>
            </div>
        </div>

        <!-- 2. Contact Details -->
        <div>
            <h4 style="color: var(--accent-yellow); font-size: 1.05rem; border-left: 3px solid var(--accent-yellow); padding-left: 0.6rem; margin-bottom: 1.2rem;">Contact Information</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.2rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">School Phone 1</label>
                    <input type="text" name="settings[phone_1]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('phone_1', '+256 772 622 612')) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">School Phone 2</label>
                    <input type="text" name="settings[phone_2]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('phone_2', '+256 754 465 536')) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">Official Email Address</label>
                    <input type="email" name="settings[email]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('email', 'info@stfrancisborgia.ac.ug')) ?>">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;">School Location Address</label>
                <input type="text" name="settings[address]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('address', 'Mukono, Central Uganda')) ?>">
            </div>
        </div>

        <!-- 3. Social Networks Links -->
        <div>
            <h4 style="color: var(--accent-yellow); font-size: 1.05rem; border-left: 3px solid var(--accent-yellow); padding-left: 0.6rem; margin-bottom: 1.2rem;">Social Media Links</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;"><i class="fab fa-facebook-f"></i> Facebook URL</label>
                    <input type="text" name="settings[facebook_url]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('facebook_url', '#')) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;"><i class="fab fa-twitter"></i> Twitter / X URL</label>
                    <input type="text" name="settings[twitter_url]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('twitter_url', '#')) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;"><i class="fab fa-instagram"></i> Instagram URL</label>
                    <input type="text" name="settings[instagram_url]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('instagram_url', '#')) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.4rem; display: block;"><i class="fab fa-youtube"></i> YouTube Channel URL</label>
                    <input type="text" name="settings[youtube_url]" class="form-control" style="padding-left: 1rem;" value="<?= e(setting_val('youtube_url', '#')) ?>">
                </div>
            </div>
        </div>

        <button type="submit" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.8rem; border-radius: 10px; cursor: pointer; font-size: 0.95rem; text-align: center; width: 100%;"><i class="fas fa-save"></i> Save System Settings</button>
    </form>
</div>
