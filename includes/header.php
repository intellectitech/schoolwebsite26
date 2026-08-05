<?php
/**
 * includes/header.php
 * Shared <head> + navigation for every page.
 * Expects (optionally) before include:
 *   $pageTitle        string  e.g. "Admission"
 *   $pageDescription  string  meta description for this specific page
 *   $pageImage        string  path to a representative image for social sharing
 */
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/config.php';
}

$pageTitle       = $pageTitle ?? 'Home';
$pageDescription = $pageDescription ?? SITE_DESCRIPTION;
$pageImage       = $pageImage ?? 'images/logo.png';
$fullTitle       = $pageTitle === 'Home' ? SITE_NAME . ' | ' . SITE_TAGLINE : $pageTitle . ' | ' . SITE_NAME;
$canonical       = SITE_URL . '/' . current_page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($fullTitle); ?></title>

    <!-- SEO -->
    <meta name="description" content="<?php echo h($pageDescription); ?>">
    <meta name="keywords" content="Namugongo Model Primary School, primary school Uganda, Namugongo school, Kampala primary school, best primary school Namugongo, school admissions Uganda">
    <meta name="author" content="Namugongo Model Primary School">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo h($canonical); ?>">

    <!-- Open Graph / Social -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo h(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo h($fullTitle); ?>">
    <meta property="og:description" content="<?php echo h($pageDescription); ?>">
    <meta property="og:image" content="<?php echo h($pageImage); ?>">
    <meta property="og:url" content="<?php echo h($canonical); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo h($fullTitle); ?>">
    <meta name="twitter:description" content="<?php echo h($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo h($pageImage); ?>">

    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="styles.css">

    <!-- Structured data for search engines -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "School",
        "name": "<?php echo SITE_NAME; ?>",
        "description": "<?php echo SITE_DESCRIPTION; ?>",
        "url": "<?php echo SITE_URL; ?>",
        "telephone": "<?php echo SITE_PHONE; ?>",
        "email": "<?php echo SITE_EMAIL; ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Namugongo, Kampala",
            "addressCountry": "UG"
        }
    }
    </script>
</head>
<body>
    <button class="sidebar-toggler" aria-label="Open navigation">☰</button>
    <aside class="sidebar">
        <div class="container navbar">
            <div class="brand-wrap">
                <div class="logo-badge">
                    <img src="images/logo.png" alt="Namugongo Model Primary School logo">
                </div>
                <div class="brand">Namugongo Model<br>Primary School</div>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="<?php echo current_page() === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="about.php" class="<?php echo current_page() === 'about.php' ? 'active' : ''; ?>">About</a></li>
                    <li><a href="news.php" class="<?php echo current_page() === 'news.php' ? 'active' : ''; ?>">News</a></li>
                    <li><a href="gallery.php" class="<?php echo current_page() === 'gallery.php' ? 'active' : ''; ?>">Gallery</a></li>
                    <li><a href="contact.php" class="<?php echo current_page() === 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                    <li><a href="admission.php" class="<?php echo current_page() === 'admission.php' ? 'active' : ''; ?>">Admission</a></li>
                    <li><a href="dashboard.php" class="<?php echo current_page() === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                </ul>
            </nav>
            <div class="sidebar-contact">
                <p>📞 <?php echo h(SITE_PHONE); ?></p>
                <p>✉ <?php echo h(SITE_EMAIL); ?></p>
            </div>
        </div>
    </aside>

    <main class="main-content">
