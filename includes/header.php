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
    
    <!-- Fonts (Switched to Poppins for UI match) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- ============================================ -->
    <!-- TOP BAR WITH CONTACT INFO (Dark Navy UI) -->
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
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- NEWS TICKER (Crisp Navy Blue, Red Highlights) -->
    <!-- ============================================ -->
    <div class="ticker-bar">
        <div class="container">
            <div class="ticker-wrapper">
                <div class="ticker-label">
                    <i class="fas fa-bullhorn"></i> Latest News
                </div>
                <div class="ticker-container" id="tickerContainer">
                    <!-- The slide is generated dynamically via JavaScript now -->
                    <div class="ticker-slide" id="tickerSlide">
                        <a href="#" id="tickerLink">Loading updates...</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- NAVIGATION - STICKY (White & Clean UI) -->
    <!-- ============================================ -->
    <nav class="main-nav" id="mainNav" role="navigation">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="index.php">
                        <?php if (!empty($schoolLogo)): ?>
                            <img src="<?= clean($schoolLogo) ?>" alt="<?= clean($schoolName) ?>" class="logo-img">
                        <?php else: ?>
                            <!-- Fallback logo block matching UI's red box style -->
                            <span class="logo-icon"><?= substr(clean($schoolName), 0, 1) ?></span>
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

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Ticker Logic
            if (typeof tickerNewsData !== 'undefined' && tickerNewsData.length > 0) {
                let currentIndex = 0;
                const tickerSlide = document.getElementById('tickerSlide');
                const tickerLink = document.getElementById('tickerLink');

                // Function to update the ticker
                function updateTicker() {
                    const item = tickerNewsData[currentIndex];
                    if (item) {
                        // Set the text
                        tickerLink.textContent = item.title;
                        // Set the link (assuming article.php?slug=...)
                        tickerLink.href = 'article.php?slug=' + encodeURIComponent(item.slug);
                        
                        // Simple fade effect
                        tickerSlide.style.opacity = '0';
                        setTimeout(() => {
                            tickerSlide.style.opacity = '1';
                        }, 300);
                    }
                    // Move to next index
                    currentIndex = (currentIndex + 1) % tickerNewsData.length;
                }

                // Initial load
                if(tickerNewsData.length > 0) {
                    updateTicker();
                } else {
                    tickerLink.textContent = "No updates available";
                }

                // Run update every 5 seconds (5000ms)
                setInterval(updateTicker, 5000);
            } else {
                // Fallback if no data
                const tickerLink = document.getElementById('tickerLink');
                if(tickerLink) {
                    tickerLink.textContent = "No updates available";
                }
            }

            // 2. Mobile Nav Toggle Logic
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.getElementById('navMenu');

            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function() {
                    const isOpen = navMenu.classList.toggle('open');
                    navToggle.classList.toggle('active');
                    navToggle.setAttribute('aria-expanded', isOpen);
                });
            }

            // 3. Sticky Navbar Logic (Adds .scrolled class on scroll)
            const mainNav = document.getElementById('mainNav');
            if (mainNav) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        mainNav.classList.add('scrolled');
                    } else {
                        mainNav.classList.remove('scrolled');
                    }
                });
            }
        });
    </script>
</body>
</html>