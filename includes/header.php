<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';

// Get logo from settings
$schoolLogo = getSetting($pdo, 'school_logo', '');
$schoolName = getSetting($pdo, 'school_name', 'School');
$favicon = getSetting($pdo, 'favicon', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($pageTitle ?? $schoolName) ?></title>
    <meta name="description" content="<?= clean(getSetting($pdo, 'meta_description', 'Welcome to our school')) ?>">
    
    <!-- Favicon -->
    <?php if (!empty($favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?= clean($favicon) ?>">
        <link rel="shortcut icon" href="<?= clean($favicon) ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏫</text></svg>">
    <?php endif; ?>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="container">
                <div class="top-bar-content">
                    <div class="top-bar-info">
                        <span><i class="fas fa-phone"></i> <?= clean(getSetting($pdo, 'school_phone', '+256-700-123456')) ?></span>
                        <span><i class="fas fa-envelope"></i> <?= clean(getSetting($pdo, 'school_email', 'info@school.ug')) ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= clean(getSetting($pdo, 'school_address', 'P.O. Box 123, Kampala, Uganda')) ?></span>
                    </div>
                    <div class="top-bar-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="main-nav" role="navigation">
            <div class="container">
                <div class="nav-wrapper">
                    <div class="logo">
                        <a href="index.php">
                            <?php if (!empty($schoolLogo)): ?>
                                <img src="<?= clean($schoolLogo) ?>" alt="<?= clean($schoolName) ?>" class="logo-img" style="max-height:50px;width:auto;">
                            <?php else: ?>
                                <i class="fas fa-graduation-cap"></i>
                                <span><?= clean($schoolName) ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <button class="nav-toggle" aria-label="Toggle Navigation" aria-expanded="false">
                        <span class="hamburger"></span>
                        <span class="hamburger"></span>
                        <span class="hamburger"></span>
                    </button>
                    <ul class="nav-menu">
                        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a></li>
                        <li><a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">About</a></li>
                        <li><a href="news.php" class="<?= basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : '' ?>">News</a></li>
                        <li><a href="admissions.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admissions.php' ? 'active' : '' ?>">Admissions</a></li>
                        <li><a href="staff.php" class="<?= basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : '' ?>">Staff</a></li>
                        <li><a href="gallery.php" class="<?= basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : '' ?>">Gallery</a></li>
                        <li><a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="admin/dashboard.php"><i class="fas fa-user-cog"></i> Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>