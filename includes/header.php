<?php
/**
 * includes/header.php
 * Shared header, meta tags and responsive navigation bar.
 * Expects (optional): $page_title, $active_page, $page_description
 */
if (!isset($page_title)) { $page_title = SITE_NAME; }
if (!isset($page_description)) { $page_description = "Mbuya Parents' School — a top pre-primary and primary school in Kampala, Uganda. " . SITE_MOTTO . "."; }
if (!isset($active_page)) { $active_page = ''; }
require_once __DIR__ . '/seo-config.php';

$currentSEO = $seo[$active_page] ?? $seo['home'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($currentSEO['title']); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($currentSEO['description']); ?>">
<link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>assets/images/badge.jpg">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

<!-- Top info bar -->
<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-contact">
      <span>&#9742; <?php echo SITE_PHONE_1; ?> / <?php echo SITE_PHONE_2; ?></span>
      <span>&#9993; <?php echo SITE_EMAIL; ?></span>
    </div>
    <div class="topbar-social">
      <a href="<?php echo SITE_INSTAGRAM; ?>" target="_blank" rel="noopener">Instagram</a>
    </div>
  </div>
</div>

<!-- Main navigation -->
<header class="site-header">
  <div class="container header-inner">
    <a href="index.php" class="brand">
      <img src="<?php echo BASE_URL; ?>assets/images/badge.jpg" alt="Mbuya Parents' School badge" class="brand-badge">
      <span class="brand-text">
        <span class="brand-name">Mbuya Parents' School</span>
        <span class="brand-motto"><?php echo SITE_MOTTO; ?></span>
      </span>
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <nav class="main-nav" id="mainNav">
      <ul>
        <li><a href="index.php" class="<?php echo $active_page === 'home' ? 'active' : ''; ?>">Home</a></li>
        <li><a href="about.php" class="<?php echo $active_page === 'about' ? 'active' : ''; ?>">About Us</a></li>
        <li><a href="academics.php" class="<?php echo $active_page === 'academics' ? 'active' : ''; ?>">Academics</a></li>
        <li><a href="events.php" class="<?php echo $active_page === 'events' ? 'active' : ''; ?>">Events</a></li>
        <li><a href="admissions.php" class="<?php echo $active_page === 'admissions' ? 'active' : ''; ?>">Admissions</a></li>
        <li><a href="gallery.php" class="<?php echo $active_page === 'gallery' ? 'active' : ''; ?>">Gallery</a></li>
        <li><a href="news.php" class="<?php echo $active_page === 'news' ? 'active' : ''; ?>">News &amp; Blog</a></li>
        <li><a href="contact.php" class="<?php echo $active_page === 'contact' ? 'active' : ''; ?>">Contact</a></li>
      </ul>
    </nav>
  </div>
</header>
