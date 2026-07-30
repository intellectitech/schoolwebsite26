<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'settings';

$fields = [
    'school_name'    => 'School Name',
    'motto'          => 'School Motto',
    'school_phone'   => 'Phone Number',
    'school_email'   => 'Email Address',
    'school_address' => 'Address',
    'office_hours'   => 'Office Hours',
    'hero_title'     => 'Homepage Hero Title',
    'hero_subtitle'  => 'Homepage Hero Subtitle',
    'founded_year'   => 'Stat: Years of Excellence',
    'total_students' => 'Stat: Pupils Enrolled',
    'stat_pass_rate' => 'Stat: PLE Pass Rate',
    'stat_teachers'  => 'Stat: Qualified Teachers',
    'about_intro'    => 'About Page: Intro Paragraph',
    'vision_text'    => 'About Page: Our Vision',
    'mission_text'   => 'About Page: Our Mission',
    'facebook_url'   => 'Facebook URL',
    'twitter_url'    => 'Twitter / X URL',
    'instagram_url'  => 'Instagram URL',
];

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminId = (int) ($_SESSION['admin_id'] ?? 0);
    foreach ($fields as $key => $label) {
        setSetting($pdo, $key, trim($_POST[$key] ?? ''), $adminId);
    }
    header('Location: settings.php?msg=saved');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Site Settings — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Site Settings</h1>
    <p class="admin-sub">Content shown across the public site — home page, about page, and footer.</p>

    <?php if ($notice): ?><div class="admin-alert admin-alert-error"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg'])): ?><div class="admin-alert admin-alert-success">Settings saved.</div><?php endif; ?>

    <div class="admin-card">
        <form method="POST" class="admin-form">
            <?php foreach ($fields as $key => $label): ?>
                <label><?= htmlspecialchars($label) ?></label>
                <?php if (in_array($key, ['hero_subtitle','about_intro','vision_text','mission_text'])): ?>
                    <textarea name="<?= $key ?>" rows="3"><?= htmlspecialchars(getSetting($pdo, $key)) ?></textarea>
                <?php else: ?>
                    <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars(getSetting($pdo, $key)) ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <div style="margin-top:20px">
                <button type="submit" class="admin-btn admin-btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
