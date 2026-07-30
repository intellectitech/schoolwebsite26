<?php
// ============================================================
//  includes/header.php — <head>, sliding background & nav
//  Include at the TOP of every public page. The page must set
//  $pageTitle (and optionally $pageId for nav highlighting)
//  before including this.
// ============================================================
require_once __DIR__ . '/functions.php';

$schoolName  = getSetting($pdo, 'school_name', "Namugongo Parents' School");
$schoolPhone = getSetting($pdo, 'school_phone');
$schoolEmail = getSetting($pdo, 'school_email');

// Detect current page for active nav highlight.
// Pages can override this by setting $pageId before including this file.
$currentPage = $pageId ?? basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $schoolName) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Global Sliding Background -->
    <div class="page-background">
        <div class="bg-slide" style="background-image: url('assets/images/images.jpg');"></div>
        <div class="bg-slide" style="background-image: url('assets/images/image1.jpg');"></div>
        <div class="bg-slide" style="background-image: url('assets/images/image2.jpg');"></div>
        <div class="bg-slide" style="background-image: url('assets/images/image3.jpg');"></div>
    </div>
    <div class="bg-overlay"></div>

    <!-- Header & Navigation -->
    <header>
        <nav>
            <div class="logo">
                <div class="logo-badge-small">
                    <img src="assets/images/images.jpg" alt="School Badge">
                </div>
                <?= htmlspecialchars($schoolName) ?>
            </div>
            <ul>
                <?php
                $navLinks = [
                    'home'       => ['Home',           'index.php'],
                    'about'      => ['About Us',       'about.php'],
                    'staff'      => ['Our Staff',      'staff.php'],
                    'admissions' => ['Admissions',     'admissions.php'],
                    'fees'       => ['School Fees',    'fees.php'],
                    'news'       => ['News & Events',  'news.php'],
                    'gallery'    => ['Gallery',        'gallery.php'],
                    'contact'    => ['Contact',        'contact.php'],
                ];
                foreach ($navLinks as $key => [$label, $href]):
                    $active = ($currentPage === $key) ? 'active' : '';
                ?>
                <li><a href="<?= $href ?>" class="nav-link <?= $active ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
                <li class="nav-item">
    <a href="admin/dashboard.php" class="nav-link admin-dashboard-link">
        <i class="fa fa-tachometer-alt"></i>🔒
    </a>
</li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
