<?php
// article.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? clean($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: news.php');
    exit;
}

// Fetch article
$stmt = $pdo->prepare("
    SELECT n.*, nc.name as cat_name, nc.color_code as cat_color,
           u.name as author_name
    FROM news n
    LEFT JOIN news_categories nc ON nc.id = n.category_id
    LEFT JOIN admin_users u ON u.id = n.author_id
    WHERE n.slug = ? AND n.is_published = 1
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: news.php');
    exit;
}

// Update view count
$pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$article['id']]);

$pageTitle = $article['title'] . ' - ' . getSetting($pdo, 'school_name', 'School');

// Fetch related articles
$relatedStmt = $pdo->prepare("
    SELECT id, title, slug, featured_image, published_at
    FROM news 
    WHERE is_published = 1 AND id != ? AND category_id = ?
    ORDER BY published_at DESC, created_at DESC
    LIMIT 3
");
$relatedStmt->execute([$article['id'], $article['category_id']]);
$relatedArticles = $relatedStmt->fetchAll();

include 'includes/header.php';
?>

<style>
/* ============================================
   ARTICLE PAGE SPECIFIC STYLES (Edugrade UI)
   ============================================ */

:root {
    --primary-red: #e91e63;
    --primary-dark-red: #c2185b;
    --navy-dark: #1a1a2e;
    --navy-light: #2a2a4a;
    --light-gray-bg: #f8f9fa;
    --white: #ffffff;
    --text-gray: #666666;
    --shadow-card: 0 4px 20px rgba(0,0,0,0.06);
    --shadow-hover: 0 8px 30px rgba(233, 30, 99, 0.12);
}

/* --- Hero Section --- */
.article-hero {
    background: linear-gradient(135deg, var(--navy-dark) 0%, #2d2d54 100%);
    color: var(--white);
    padding: 80px 0 60px;
    position: relative;
    overflow: hidden;
}

.article-hero .container {
    position: relative;
    z-index: 1;
}

.article-hero .category-badge {
    display: inline-block;
    padding: 6px 20px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 18px;
    letter-spacing: 0.5px;
}

.article-hero h1 {
    color: var(--white);
    font-size: 2.8rem;
    max-width: 850px;
    line-height: 1.2;
    letter-spacing: -0.5px;
}

.article-hero .meta {
    color: rgba(255,255,255,0.75);
    font-size: 0.95rem;
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.article-hero .meta span {
    display: flex;
    align-items: center;
    gap: 8px;
}

.article-hero .meta i {
    color: var(--primary-red);
}

/* --- Article Body --- */
.article-body {
    padding: 60px 0 80px;
}

.article-body .content {
    max-width: 850px;
    margin: 0 auto;
}

.article-body .featured-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 40px;
    box-shadow: var(--shadow-card);
}

.article-body .content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 25px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.article-body .content h2, 
.article-body .content h3 {
    color: var(--navy-dark);
    margin-top: 35px;
    margin-bottom: 15px;
}

.article-body .content h2 {
    font-size: 1.8rem;
}
.article-body .content h3 {
    font-size: 1.4rem;
}

.article-body .content p {
    margin-bottom: 20px;
    font-size: 1.08rem;
    color: #444;
    line-height: 1.8;
}

.article-body .content ul, 
.article-body .content ol {
    margin: 20px 0 20px 30px;
}

.article-body .content li {
    margin-bottom: 10px;
    font-size: 1.05rem;
}

.article-body .content blockquote {
    border-left: 4px solid var(--primary-red);
    padding: 15px 25px;
    margin: 30px 0;
    background: var(--light-gray-bg);
    border-radius: 0 8px 8px 0;
    font-style: italic;
    color: #555;
    font-size: 1.1rem;
}

/* --- Share Bar --- */
.share-bar {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.share-bar .share-label {
    font-weight: 600;
    color: var(--navy-dark);
    margin-right: 10px;
}

.share-bar a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: var(--white);
    transition: var(--transition);
    text-decoration: none;
}

.share-bar a:hover {
    transform: translateY(-3px);
}

.share-bar .fb { background: #1877f2; }
.share-bar .tw { background: #1da1f2; }
.share-bar .wa { background: #25d366; }
.share-bar .email { background: var(--navy-dark); }

/* --- Related Articles --- */
.related-section {
    background: var(--light-gray-bg);
    padding: 70px 0;
}

.related-section .section-title {
    text-align: left;
    margin-bottom: 40px;
}
.related-section .section-title h2 {
    display: inline-block;
    position: relative;
    padding-bottom: 12px;
    color: var(--navy-dark);
}
.related-section .section-title h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-red);
}
.related-section .section-title p {
    color: var(--text-gray);
    margin-top: 10px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.related-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: var(--transition);
}

.related-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-hover);
}

.related-card img {
    width: 100%;
    height: 190px;
    object-fit: cover;
}

.related-card .info {
    padding: 25px;
}

.related-card .info h4 {
    font-size: 1.05rem;
    margin-bottom: 10px;
    line-height: 1.4;
}

.related-card .info h4 a {
    color: var(--navy-dark);
    transition: var(--transition);
}

.related-card .info h4 a:hover {
    color: var(--primary-red);
}

.related-card .info .date {
    font-size: 0.85rem;
    color: var(--text-gray);
    display: flex;
    align-items: center;
    gap: 6px;
}

.related-card .info .date i {
    color: var(--primary-red);
}

/* --- Responsive --- */
@media (max-width: 992px) {
    .related-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .article-hero { padding: 60px 0 40px; }
    .article-hero h1 { font-size: 2rem; }
    .article-body .content p { font-size: 1rem; }
    .article-body .content h2 { font-size: 1.5rem; }
    .related-grid { grid-template-columns: 1fr; }
}

@media (max-width: 576px) {
    .article-hero h1 { font-size: 1.6rem; }
    .share-bar { justify-content: center; }
}
</style>

<!-- Hero -->
<section class="article-hero">
    <div class="container">
        <?php if (!empty($article['cat_name'])): ?>
            <span class="category-badge" style="background:<?= clean($article['cat_color'] ?? '#e91e63') ?>; color:#fff;">
                <?= clean($article['cat_name']) ?>
            </span>
        <?php endif; ?>
        <h1><?= clean($article['title']) ?></h1>
        <div class="meta">
            <span><i class="fas fa-calendar-alt"></i> <?= formatDate($article['published_at'] ?? $article['created_at']) ?></span>
            <?php if (!empty($article['author_name'])): ?>
                <span><i class="fas fa-user"></i> <?= clean($article['author_name']) ?></span>
            <?php endif; ?>
            <span><i class="fas fa-eye"></i> <?= number_format($article['views'] ?? 0) ?> views</span>
        </div>
    </div>
</section>

<!-- Article Body -->
<section class="article-body">
    <div class="container">
        <div class="content">
            <?php if (!empty($article['featured_image'])): ?>
                <img src="<?= clean($article['featured_image']) ?>" alt="<?= clean($article['title']) ?>" class="featured-image">
            <?php endif; ?>
            
            <?= $article['body'] ?>
            
            <!-- Share Bar -->
            <div class="share-bar">
                <span class="share-label"><i class="fas fa-share-alt" style="color:var(--primary-red);"></i> Share this article:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="fb" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title']) ?>&url=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="tw" title="Share on Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="wa" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="mailto:?subject=<?= urlencode($article['title']) ?>&body=<?= urlencode('Check out this article: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" class="email" title="Share via Email"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles -->
<?php if (!empty($relatedArticles)): ?>
<section class="related-section">
    <div class="container">
        <div class="section-title">
            <h2>Related Articles</h2>
            <p>You might also find these interesting</p>
        </div>
        <div class="related-grid">
            <?php foreach ($relatedArticles as $related): ?>
                <div class="related-card">
                    <?php if (!empty($related['featured_image'])): ?>
                        <img src="<?= clean($related['featured_image']) ?>" alt="<?= clean($related['title']) ?>" loading="lazy">
                    <?php else: ?>
                        <div style="height:190px;background:#e9ecef;display:flex;align-items:center;justify-content:center;color:#adb5bd;">
                            <i class="fas fa-image" style="font-size:2rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="info">
                        <h4><a href="article.php?slug=<?= clean($related['slug']) ?>"><?= clean($related['title']) ?></a></h4>
                        <div class="date"><i class="fas fa-calendar-alt"></i> <?= formatDate($related['published_at'] ?? $related['created_at']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>