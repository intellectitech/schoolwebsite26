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
.article-hero {
    background: linear-gradient(135deg, #0d2617, #1a4d2e);
    color: #fff;
    padding: 60px 0 40px;
}
.article-hero .category-badge {
    display: inline-block;
    padding: 4px 16px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 15px;
}
.article-hero h1 {
    color: #fff;
    font-size: 2.5rem;
    max-width: 800px;
}
.article-hero .meta {
    color: rgba(255,255,255,0.7);
    font-size: 0.95rem;
    margin-top: 15px;
}
.article-hero .meta span {
    margin-right: 20px;
}
.article-body {
    padding: 50px 0 80px;
}
.article-body .content {
    max-width: 820px;
    margin: 0 auto;
}
.article-body .content img {
    max-width: 100%;
    border-radius: 12px;
    margin: 20px 0;
}
.article-body .content h2, 
.article-body .content h3 {
    color: #1a4d2e;
    margin-top: 30px;
}
.article-body .content p {
    margin-bottom: 18px;
    font-size: 1.05rem;
}
.article-body .content ul, 
.article-body .content ol {
    margin: 20px 0 20px 25px;
}
.article-body .content li {
    margin-bottom: 8px;
}
.article-body .featured-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 30px;
}
.related-section {
    background: #f8f9fa;
    padding: 60px 0;
}
.related-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}
.related-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}
.related-card:hover {
    transform: translateY(-5px);
}
.related-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.related-card .info {
    padding: 20px;
}
.related-card .info h4 {
    font-size: 1rem;
    margin-bottom: 8px;
}
.related-card .info h4 a {
    color: #1a1a1a;
}
.related-card .info h4 a:hover {
    color: #1a4d2e;
}
.related-card .info .date {
    font-size: 0.85rem;
    color: #999;
}
@media (max-width: 992px) {
    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 768px) {
    .article-hero h1 {
        font-size: 1.8rem;
    }
    .related-grid {
        grid-template-columns: 1fr;
    }
    .article-body .content p {
        font-size: 1rem;
    }
}
</style>

<!-- Hero -->
<section class="article-hero">
    <div class="container">
        <?php if (!empty($article['cat_name'])): ?>
            <span class="category-badge" style="background:<?= clean($article['cat_color'] ?? '#2d7a4a') ?>; color:#fff;">
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
            
            <div style="margin-top:40px;padding-top:30px;border-top:1px solid #e0e0e0;">
                <p style="font-size:0.9rem;color:#999;">
                    <i class="fas fa-tag"></i> 
                    <?php if (!empty($article['cat_name'])): ?>
                        Category: <?= clean($article['cat_name']) ?>
                    <?php else: ?>
                        Uncategorized
                    <?php endif; ?>
                </p>
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
                        <div style="height:180px;background:#e9ecef;display:flex;align-items:center;justify-content:center;color:#999;">
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