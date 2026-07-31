<?php
if (!isset($pdo)) {
    require_once '../config/database.php';
}
require_once __DIR__ . '/functions.php';

// All of these keys exist in the school_info table in the supplied
// school_website_db.sql — a few (school_address, hero_title, etc.)
// only exist because the dump already has the schema fixes merged
// in; see README.md §5 for the history of what changed and why.
$schoolName = getSetting($pdo, 'school_name') ?: 'Uganda Martyrs Primary School';
$schoolPhone = getSetting($pdo, 'contact_phone');
$schoolEmail = getSetting($pdo, 'contact_email');

// Detect current page for active nav highlight
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

?>

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
    <a href="admissions.php" class="btn btn-primary">Apply</a>
    <a href="admin/login.php" class="btn btn-primary">🔒 Admin Login</a>
</div>