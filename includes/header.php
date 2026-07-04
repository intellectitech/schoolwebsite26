<?php
// ============================================================
//  includes/header.php — Navigation & <head>
//  Include at the top of EVERY public page.
//  The page must set $pageTitle before including this.
// ============================================================

// Load DB + functions if not already loaded
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}
require_once __DIR__ . '/../includes/functions.php';

$schoolName  = getSetting($pdo, 'school_name');
$schoolPhone = getSetting($pdo, 'school_phone');
$schoolEmail = getSetting($pdo, 'school_email');

// Detect current page for active nav highlight
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $schoolName) ?></title>
    <link rel="stylesheet" href="/schoolwebsite26/assets/css/style.css">
</head>
<body>

<!-- ── TOP BAR ── -->
<div class="top-bar">
    <div class="container top-bar-inner">
        <span>📞 <?= htmlspecialchars($schoolPhone) ?></span>
        <span>✉ <?= htmlspecialchars($schoolEmail) ?></span>
        <a href="/schoolwebsite26/admin/login.php" class="admin-link">Admin Login</a>
    </div>
</div>

<!-- ── SITE HEADER ── -->
<header class="site-header">
    <div class="container header-inner">
        <a href="/schoolwebsite26/" class="logo">
            <span class="logo-name"><?= htmlspecialchars($schoolName) ?></span>
            <span class="logo-tagline">Excellence in Education</span>
        </a>

        <!-- Hamburger button — shown only on mobile -->
        <button class="burger" id="burger" aria-label="Open menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <!-- Main navigation -->
        <nav class="main-nav" id="main-nav" role="navigation" aria-label="Main menu">
            <?php
            $navLinks = [
                'index'      => ['Home',       '/schoolwebsite26/'],
                'about'      => ['About',      '/schoolwebsite26/about.php'],
                'news'       => ['News',        '/schoolwebsite26/news.php'],
                'admissions' => ['Admissions',  '/schoolwebsite26/admissions.php'],
                'staff'      => ['Staff',       '/schoolwebsite26/staff.php'],
                'gallery'    => ['Gallery',     '/schoolwebsite26/gallery.php'],
                'contact'    => ['Contact',     '/schoolwebsite26/contact.php'],
            ];
            foreach ($navLinks as $key => [$label, $href]):
                $active = ($currentPage === $key) ? 'active' : '';
            ?>
            <a href="<?= $href ?>" class="nav-link <?= $active ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
