<?php
if (!isset($pdo)) {
    require_once '../config/database.php';
}
require_once __DIR__ . '/functions.php';

// These keys exist in the school_info table straight out of the
// supplied dump (see database_patch.sql for the ones that don't).
$schoolName = getSetting($pdo, 'school_name') ?: 'Uganda Martyrs Primary School';
$schoolPhone = getSetting($pdo, 'contact_phone');
$schoolEmail = getSetting($pdo, 'contact_email');

// Detect current page for active nav highlight
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

?>

<head>
    <link rel="shortcut icon" href="assets/images/ESD_69e8c39b15887.webp" type="image/x-icon">
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <!-- Neexa Widget -->
    <script>
        window.neexaAsyncInit = function () {
            window.neexa.init({
                agent_id: 'a2518be7-a25d-4235-8c87-19302452120b', mobile_mini_style: 'greeting_only',
            });
        };
    </script>
    <script async src="https://chat-widget.neexa.ai/main.js?nonce=1784711297610.682"></script>
    <!-- End Neexa Widget -->
</head>


<header class="site-header">
    <div class="header-inner">
        <a href="index.php#top" class="brand">
            <img src="assets/images/ESD_69e8c39b15887.webp" alt="<?= htmlspecialchars($schoolName) ?> logo"
                class="brand-mark" />
            <span class="brand-text">
                <strong><?= htmlspecialchars(explode(' Primary', $schoolName)[0]) ?></strong>
                <small>Primary School · Namugongo</small>
            </span>
        </a>
        <?php
        $navLinks = [
            'index.php' => 'HOME',
            'about.php' => 'ABOUT',
            'news.php' => 'NEWS',
            'admissions.php' => 'ADMISSIONS',
            'staff.php' => 'STAFF',
            'gallery.php' => 'GALLERY',
            'contact.php' => 'CONTACT',
        ];
        ?>
        <nav class="main-nav">
            <?php foreach ($navLinks as $href => $label): ?>
                <a href="<?= $href ?>" <?= $currentPage . '.php' === $href ? ' class="current"' : '' ?>><?= $label ?></a>
            <?php endforeach; ?>
        </nav>
        <a href="admissions.php" class="btn btn-primary header-cta">Apply</a>
        <a href="admin/login.php" class="btn btn-primary header-cta">🔒</a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
<div class="mobile-nav" id="mobileNav">
    <button class="mobile-nav-close" id="mobileNavClose" aria-label="Close menu">
        &times;
    </button>
    <?php foreach ($navLinks as $href => $label): ?>
        <a href="<?= $href ?>" <?= $currentPage . '.php' === $href ? ' class="current"' : '' ?>><?= $label ?></a>
    <?php endforeach; ?>
</div>