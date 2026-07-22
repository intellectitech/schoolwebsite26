<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';

$schoolLogo = getSetting($pdo, 'school_logo', '');
$schoolName = getSetting($pdo, 'school_name', 'School');

// Fetch news for ticker
$tickerStmt = $pdo->query("
    SELECT title, slug, created_at 
    FROM news 
    WHERE is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 10
");
$tickerNews = $tickerStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($pageTitle ?? $schoolName) ?></title>
    <meta name="description" content="<?= clean(getSetting($pdo, 'meta_description', 'Welcome to our school')) ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- ============================================ -->
    <!-- TOP BAR WITH CONTACT INFO -->
    <!-- ============================================ -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-bar-info">
                    <span><i class="fas fa-phone"></i> <?= clean(getSetting($pdo, 'school_phone', '+256-700-123456')) ?></span>
                    <span><i class="fas fa-envelope"></i> <?= clean(getSetting($pdo, 'school_email', 'info@school.ug')) ?></span>
                    <span><i class="fas fa-map-marker-alt"></i> <?= clean(getSetting($pdo, 'school_address', 'P.O. Box 123, Kampala, Uganda')) ?></span>
                </div>
                <div class="top-bar-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- NEWS TICKER - Between Top Bar and Navigation -->
    <!-- ============================================ -->
    <div class="ticker-bar">
        <div class="container">
            <div class="ticker-wrapper">
                <div class="ticker-label">
                    <i class="fas fa-bullhorn"></i> Latest News
                </div>
                <div class="ticker-container" id="tickerContainer">
                    <div class="ticker-slide" id="tickerSlide">
                        <a href="#" id="tickerLink">
                            <?php if (!empty($tickerNews)): ?>
                                <?= clean($tickerNews[0]['title']) ?>
                            <?php else: ?>
                                No updates available
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- NAVIGATION - STICKY -->
    <!-- ============================================ -->
    <nav class="main-nav" id="mainNav" role="navigation">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="index.php">
                        <?php if (!empty($schoolLogo)): ?>
                            <img src="<?= clean($schoolLogo) ?>" alt="<?= clean($schoolName) ?>" class="logo-img">
                        <?php else: ?>
                            <i class="fas fa-graduation-cap"></i>
                            <span><?= clean($schoolName) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <button class="nav-toggle" id="navToggle" aria-label="Toggle Navigation" aria-expanded="false">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
                <ul class="nav-menu" id="navMenu">
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

    <!-- ============================================ -->
    <!-- PASS PHP DATA TO JAVASCRIPT -->
    <!-- ============================================ -->
    <script>
        // Pass news data to JavaScript
        var tickerNewsData = <?php echo json_encode($tickerNews); ?>;
        console.log('Ticker loaded with ' + tickerNewsData.length + ' items');
    </script>