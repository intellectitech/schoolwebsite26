<?php
require_once 'database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT n.*, c.name AS category_name, c.color AS category_color, u.name AS author_name
                                FROM news n
                                LEFT JOIN news_categories c ON n.category_id = c.id
                                LEFT JOIN admin_users u ON n.author_id = u.id
                                WHERE n.id = ? AND n.is_published = 1");
        $stmt->execute([$id]);
        $article = $stmt->fetch();

        if ($article) {
            $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$id]);
        }
    } catch (PDOException $e) {
        $article = null;
    }
}

// Fetch a few other recent articles for the sidebar
try {
    $moreStmt = $pdo->prepare("SELECT id, title, featured_image, created_at FROM news WHERE is_published = 1 AND id != ? ORDER BY created_at DESC LIMIT 4");
    $moreStmt->execute([$id]);
    $moreNews = $moreStmt->fetchAll();
} catch (PDOException $e) {
    $moreNews = [];
}
?>
<?php require_once 'seo-config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $article ? htmlspecialchars($article['title']) . ' | St. FRANCIS BORGIA HIGH SCHOOL' : 'Article Not Found | St. FRANCIS BORGIA HIGH SCHOOL' ?></title>
    <?php if ($article): ?>
        <meta name="description" content="<?= htmlspecialchars($article['excerpt'] ?: mb_substr(strip_tags($article['body']), 0, 155)) ?>">
        <link rel="canonical" href="<?= htmlspecialchars($siteUrl) ?>/news_detail.php?id=<?= (int)$article['id'] ?>">
        <meta property="og:title" content="<?= htmlspecialchars($article['title']) ?>">
        <meta property="og:description" content="<?= htmlspecialchars($article['excerpt'] ?: mb_substr(strip_tags($article['body']), 0, 155)) ?>">
        <meta property="og:type" content="article">
        <?php if ($article['featured_image']): ?>
            <meta property="og:image" content="<?= htmlspecialchars($siteUrl) ?>/<?= htmlspecialchars($article['featured_image']) ?>">
        <?php endif; ?>
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Article",
            "headline": <?= json_encode($article['title']) ?>,
            "datePublished": "<?= date('c', strtotime($article['published_at'] ?: $article['created_at'])) ?>",
            "publisher": { "@type": "Organization", "name": "St. Francis Borgia High School Mukono" }
        }
        </script>
    <?php else: ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <link rel="icon" href="badge.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-container">
            <div class="quick-contact">
                <span><i class="fas fa-phone"></i> +256 772 622 612 / +256 754 465 536</span>
                <span><i class="fas fa-envelope"></i> info@stfrancisborgia.ac.ug</span>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header>
        <div class="header-container">
            <div class="logo-container">
                <img src="naps/logo.jpg" alt="" class="logo">
                <div class="school-name">
                    <h1>St. FRANCIS BORGIA HIGH SCHOOL</h1>
                    <p>Called to Shine</p>
                </div>
            </div>

            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>

            <nav id="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="academics.php">Academics</a></li>
                    <li><a href="admissions.php">Admissions</a></li>
                    <li><a href="facilities.php">Facilities</a></li>
                    <li class="active"><a href="news.php">News</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php if ($article): ?>

        <!-- Article Hero -->
        <section class="interior-hero" style="background-image: url('<?= htmlspecialchars($article['featured_image'] ?: 'naps/merit.svg') ?>');">
            <div class="hero-content">
                <?php if ($article['category_name']): ?>
                    <span style="background: <?= htmlspecialchars($article['category_color'] ?: '#D01116') ?>; color: #fff; padding: 0.3rem 0.9rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 1rem;">
                        <?= htmlspecialchars($article['category_name']) ?>
                    </span>
                <?php endif; ?>
                <h1><?= htmlspecialchars($article['title']) ?></h1>
                <p>
                    <i class="far fa-calendar-alt"></i> <?= date('F j, Y', strtotime($article['published_at'] ?: $article['created_at'])) ?>
                    <?php if ($article['author_name']): ?> &bull; By <?= htmlspecialchars($article['author_name']) ?><?php endif; ?>
                    &bull; <i class="far fa-eye"></i> <?= (int)$article['views'] ?> views
                </p>
            </div>
        </section>

        <!-- Article Body -->
        <section class="about">
            <div class="section-container" style="max-width: 850px;">
                <a href="news.php" style="display: inline-block; margin-bottom: 2rem; color: var(--sfc-red); text-decoration: none; font-weight: 600;"><i class="fas fa-arrow-left"></i> Back to All News</a>

                <style>
                    .article-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 1rem 0; display: block; }
                </style>
                <div class="article-body" style="font-size: 1.05rem; line-height: 1.9; color: #333;">
                    <?= $article['body'] ?>
                </div>
            </div>
        </section>

        <?php if (!empty($moreNews)): ?>
        <!-- More News -->
        <section class="about" style="background: var(--light-gray);">
            <div class="section-container">
                <div class="section-title">
                    <h2>More News & Highlights</h2>
                </div>
                <div class="grid-3">
                    <?php foreach ($moreNews as $more): ?>
                        <div class="card">
                            <div class="card-image">
                                <img src="<?= htmlspecialchars($more['featured_image'] ?: 'naps/merit.svg') ?>" alt="<?= htmlspecialchars($more['title']) ?>">
                            </div>
                            <div class="card-content">
                                <div class="card-date"><i class="far fa-calendar-alt"></i> <?= date('F j, Y', strtotime($more['created_at'])) ?></div>
                                <h3><?= htmlspecialchars($more['title']) ?></h3>
                                <a href="news_detail.php?id=<?= $more['id'] ?>" class="btn btn-secondary">Read More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

    <?php else: ?>

        <!-- Not Found -->
        <section class="about" style="text-align: center; padding: 5rem 1.5rem;">
            <i class="fas fa-newspaper" style="font-size: 3rem; color: var(--sfc-red); margin-bottom: 1.5rem;"></i>
            <h2>Article Not Found</h2>
            <p style="color: #666; margin: 1rem 0 2rem;">This article may have been removed, unpublished, or the link is incorrect.</p>
            <a href="news.php" class="btn">Browse All News</a>
        </section>

    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>St. Francis Borgia</h3>
                    <p>High school mukono dedicated to providing holistic, high-quality secondary education rooted in academic excellence and Christian values.</p>
                </div>

                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="academics.php">Academics</a></li>
                        <li><a href="admissions.php">Admissions</a></li>
                        <li><a href="facilities.php">Facilities</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Mukono Town, Uganda</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+256 772 622 612 / +256 754 465 536</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@stfrancisborgia.ac.ug</span>
                    </div>
                </div>

                <div class="footer-col">
                    <h3>School Hours</h3>
                    <ul style="color: var(--sfc-silver); font-size: 0.95rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.5rem;">Monday - Friday: 7:30 AM - 4:30 PM</li>
                        <li style="margin-bottom: 0.5rem;">Saturday: 8:00 AM - 1:00 PM (Study/Clubs)</li>
                        <li>Sunday: Sabbath Worship & Rest</li>
                    </ul>

                    <h3>Follow Us</h3>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> St. FRANCIS BORGIA HIGH SCHOOL MUKONO. All Rights Reserved. | <a href="admin/admin.php" style="color: var(--sfc-yellow); text-decoration: none; font-weight: 600;"><i class="fas fa-user-shield"></i> Admin Dashboard</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.getElementById('main-nav');

        if (menuToggle && mainNav) {
            menuToggle.addEventListener('click', () => {
                mainNav.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!menuToggle.contains(e.target) && !mainNav.contains(e.target)) {
                    mainNav.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>
