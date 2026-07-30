<?php
// news.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'News - ' . getSetting($pdo, 'school_name', 'School');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Category filter
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$categoryCondition = $categoryFilter ? "AND n.category_id = $categoryFilter" : "";

// Count total articles
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM news n 
    WHERE n.is_published = 1 $categoryCondition
");
$countStmt->execute();
$totalArticles = $countStmt->fetchColumn();
$totalPages = ceil($totalArticles / $perPage);

// Fetch articles
$stmt = $pdo->prepare("
    SELECT n.*, nc.name as cat_name, nc.color_code as cat_color
    FROM news n
    LEFT JOIN news_categories nc ON nc.id = n.category_id
    WHERE n.is_published = 1 $categoryCondition
    ORDER BY n.published_at DESC, n.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$articles = $stmt->fetchAll();

// Fetch categories for filter
$categoriesStmt = $pdo->query("SELECT * FROM news_categories ORDER BY name");
$categories = $categoriesStmt->fetchAll();

include 'includes/header.php';
?>

<style>
.news-hero {
    background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
    color: #fff;
    padding: 60px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.news-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 60%;
    height: 150%;
    background: radial-gradient(ellipse, rgba(0, 200, 83, 0.08), transparent 70%);
    animation: heroGlow 8s ease-in-out infinite alternate;
}
.news-hero h1 {
    color: #fff;
    font-size: 2.8rem;
}
.news-hero h1 .highlight {
    color: #00C853;
}
.news-hero p {
    color: rgba(255,255,255,0.7);
    max-width: 600px;
    margin: 15px auto 0;
}

.news-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin: 30px 0;
    justify-content: center;
}
.news-filters a {
    padding: 8px 20px;
    border-radius: 50px;
    background: #fff;
    color: #333;
    transition: all 0.3s;
    font-size: 0.9rem;
    text-decoration: none;
    border: 1px solid #e0e0e0;
}
.news-filters a:hover,
.news-filters a.active {
    background: #00C853;
    color: #fff;
    border-color: #00C853;
}

.news-section {
    padding: 40px 0 80px;
    background: #f5e6d3;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 40px;
}
.pagination a, .pagination span {
    padding: 10px 16px;
    border-radius: 8px;
    background: #fff;
    color: #0a0a0a;
    border: 1px solid #e0e0e0;
    transition: all 0.3s;
    text-decoration: none;
}
.pagination a:hover {
    background: #00C853;
    color: #fff;
    border-color: #00C853;
}
.pagination .active {
    background: #00C853;
    color: #fff;
    border-color: #00C853;
}
.pagination .disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.no-news {
    text-align: center;
    padding: 60px 0;
    color: #8D6E63;
}
.no-news i {
    font-size: 3rem;
    color: #ccc;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .news-hero h1 { font-size: 2rem; }
    .news-filters { gap: 8px; }
    .news-filters a { font-size: 0.8rem; padding: 6px 14px; }
}
</style>

<!-- Hero -->
<section class="news-hero">
    <div class="container">
        <h1><i class="fas fa-newspaper"></i> School <span class="highlight">News</span></h1>
        <p>Stay updated with the latest happenings at <?= clean(getSetting($pdo, 'school_name', 'our school')) ?></p>
    </div>
</section>

<!-- Categories -->
<section style="padding-top:0;background:#f5e6d3;">
    <div class="container">
        <div class="news-filters">
            <a href="news.php" class="<?= $categoryFilter == 0 ? 'active' : '' ?>">All News</a>
            <?php foreach ($categories as $cat): ?>
                <a href="news.php?category=<?= $cat['id'] ?>" class="<?= $categoryFilter == $cat['id'] ? 'active' : '' ?>">
                    <?= clean($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- News Grid -->
<section class="news-section">
    <div class="container">
        <?php if (!empty($articles)): ?>
            <div class="cards-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="card">
                        <?php if (!empty($article['featured_image'])): ?>
                            <img src="<?= clean($article['featured_image']) ?>" alt="<?= clean($article['title']) ?>" class="card-img" loading="lazy">
                        <?php else: ?>
                            <div class="card-img-placeholder"></div>
                        <?php endif; ?>
                        <div class="card-content">
                            <?php if (!empty($article['cat_name'])): ?>
                                <span class="badge" style="background:<?= clean($article['cat_color'] ?? '#00C853') ?>; color:#fff;">
                                    <?= clean($article['cat_name']) ?>
                                </span>
                            <?php endif; ?>
                            <h3><a href="article.php?slug=<?= clean($article['slug']) ?>"><?= clean($article['title']) ?></a></h3>
                            <p><?= clean(truncate($article['excerpt'] ?? $article['body'], 120)) ?></p>
                            <div class="card-meta">
                                <span class="date"><i class="fas fa-calendar-alt"></i> <?= formatDate($article['published_at'] ?? $article['created_at']) ?></span>
                                <a href="article.php?slug=<?= clean($article['slug']) ?>" class="read-more">Read More →</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&category=<?= $categoryFilter ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-left"></i></span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&category=<?= $categoryFilter ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&category=<?= $categoryFilter ?>"><i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="no-news">
                <i class="fas fa-newspaper"></i>
                <h3>No News Articles</h3>
                <p>Check back soon for updates from our school.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>