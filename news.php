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
/* ============================================
   NEWS PAGE SPECIFIC STYLES (Edugrade UI)
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
.news-hero {
    background: linear-gradient(135deg, var(--navy-dark) 0%, #2d2d54 100%);
    position: relative;
    color: var(--white);
    padding: 80px 0 60px;
    text-align: center;
    overflow: hidden;
}

.news-hero .container {
    position: relative;
    z-index: 1;
}

.news-hero h1 {
    color: var(--white);
    font-size: 3.2rem;
    font-weight: 700;
    letter-spacing: -1px;
    margin-bottom: 15px;
}

.news-hero h1 i {
    color: var(--primary-red);
    margin-right: 12px;
}

.news-hero p {
    color: rgba(255,255,255,0.85);
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.15rem;
    line-height: 1.7;
}

/* --- Section Wrappers --- */
.news-section {
    padding: 60px 0 80px;
    background: var(--light-gray-bg);
}

/* --- Section Title (Left Aligned) --- */
.news-section .section-title {
    text-align: left;
    margin-bottom: 40px;
}
.news-section .section-title h2 {
    display: inline-block;
    position: relative;
    padding-bottom: 12px;
    color: var(--navy-dark);
}
.news-section .section-title h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-red);
}
.news-section .section-title p {
    color: var(--text-gray);
    max-width: 500px;
    margin-top: 10px;
}

/* --- Category Filters --- */
.news-filters {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin: 40px 0 30px;
    justify-content: flex-start;
}
.news-filters a {
    padding: 10px 24px;
    border-radius: 50px;
    background: var(--white);
    color: var(--navy-dark);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 500;
    box-shadow: var(--shadow-card);
}
.news-filters a:hover {
    background: var(--light-gray-bg);
    transform: translateY(-2px);
}
.news-filters a.active {
    background: var(--primary-red);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
}
.news-filters a.active:hover {
    background: var(--primary-dark-red);
}

/* --- News Card Grid (Overriding base cards-grid) --- */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: var(--transition);
    border: 1px solid transparent;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary-red);
}

.card-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.card-img-placeholder {
    height: 220px;
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 3rem;
}

.card-content {
    padding: 25px 28px 30px;
}

.card .badge {
    display: inline-block;
    padding: 5px 16px;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 15px;
}

.card h3 {
    font-size: 1.1rem;
    margin-bottom: 12px;
    line-height: 1.4;
}

.card h3 a {
    color: var(--navy-dark);
    transition: var(--transition);
}

.card h3 a:hover {
    color: var(--primary-red);
}

.card p {
    font-size: 0.93rem;
    color: var(--text-gray);
    margin-bottom: 18px;
    line-height: 1.6;
}

.card .card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
    color: var(--text-gray);
    border-top: 1px solid var(--light-gray-bg);
    padding-top: 15px;
}

.card .card-meta .date {
    display: flex;
    align-items: center;
    gap: 6px;
}
.card .card-meta .date i {
    color: var(--primary-red);
}

.card .card-meta .read-more {
    color: var(--primary-red);
    font-weight: 600;
}

.card .card-meta .read-more:hover {
    color: var(--primary-dark-red);
    transform: translateX(4px);
}

/* --- Pagination --- */
.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 50px;
}
.pagination a, .pagination span {
    padding: 12px 18px;
    border-radius: 6px;
    background: var(--white);
    color: var(--navy-dark);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    font-weight: 500;
    box-shadow: var(--shadow-card);
}
.pagination a:hover {
    background: var(--navy-dark);
    color: var(--white);
    transform: translateY(-2px);
}
.pagination .active {
    background: var(--primary-red);
    color: var(--white);
    border-color: var(--primary-red);
    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
}
.pagination .disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f1f3f5;
}

/* --- Empty State --- */
.empty-state {
    text-align: center;
    padding: 60px 0;
    background: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow-card);
}
.empty-state i {
    font-size: 4rem;
    color: #ced4da;
    margin-bottom: 20px;
}
.empty-state h3 {
    color: var(--text-gray);
}

/* --- Responsive --- */
@media (max-width: 992px) {
    .cards-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .news-hero { padding: 60px 0; }
    .news-hero h1 { font-size: 2.2rem; }
    .news-hero p { font-size: 1rem; }
    .cards-grid { grid-template-columns: 1fr; }
    .news-filters { justify-content: center; }
}

@media (max-width: 576px) {
    .news-hero h1 { font-size: 1.8rem; }
    .news-filters a { padding: 8px 18px; font-size: 0.8rem; }
}
</style>

<!-- Hero -->
<section class="news-hero">
    <div class="container">
        <h1><i class="fas fa-newspaper"></i> School News</h1>
        <p>Stay updated with the latest happenings at <?= clean(getSetting($pdo, 'school_name', 'our school')) ?></p>
    </div>
</section>

<!-- Categories -->
<section class="news-section" style="padding-bottom:0; background:var(--light-gray-bg);">
    <div class="container">
        <div class="section-title">
            <h2>Latest Articles</h2>
            <p>Browse our latest news, events, and stories from the school community</p>
        </div>
        
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
<section class="news-section" style="padding-top:20px;">
    <div class="container">
        <?php if (!empty($articles)): ?>
            <div class="cards-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="card">
                        <?php if (!empty($article['featured_image'])): ?>
                            <img src="<?= clean($article['featured_image']) ?>" alt="<?= clean($article['title']) ?>" class="card-img" loading="lazy">
                        <?php else: ?>
                            <div class="card-img-placeholder"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                        <div class="card-content">
                            <?php if (!empty($article['cat_name'])): ?>
                                <span class="badge" style="background:<?= clean($article['cat_color'] ?? '#e91e63') ?>; color:#fff;">
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
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>No News Articles Found</h3>
                <p style="color:#666;margin-top:5px;">Check back soon for updates from our school.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>