<?php
require_once 'includes/functions.php';

$slug = clean($_GET['slug'] ?? '');
$stmt = $pdo->prepare(
    "SELECT n.*, nc.name AS cat_name, nc.color AS cat_color, nc.slug AS cat_slug, a.name AS author_name
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     LEFT JOIN admin_users a ON a.id = n.author_id
     WHERE n.slug = ? AND n.is_published = 1"
);
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
} else {
    // Count a read every time the article is opened (best-effort, not unique-visitor tracking)
    $pdo->prepare('UPDATE news SET views = views + 1 WHERE id = ?')->execute([$article['id']]);
}

// A few related stories from the same category
$related = [];
if ($article && $article['category_id']) {
    $rStmt = $pdo->prepare(
        'SELECT title, slug, excerpt, featured_image, published_at
         FROM news
         WHERE is_published = 1 AND category_id = ? AND id != ?
         ORDER BY published_at DESC LIMIT 3'
    );
    $rStmt->execute([$article['category_id'], $article['id']]);
    $related = $rStmt->fetchAll();
}

$pageId    = 'news';
$pageTitle = $article
    ? $article['title'] . ' — ' . getSetting($pdo, 'school_name')
    : 'Article Not Found';

require_once 'includes/header.php';
?>
<section class="page-section active">
    <div class="container" style="max-width:800px">
        <?php if (!$article): ?>
            <h2 class="section-title">Article Not Found</h2>
            <p style="text-align:center">
                <a href="news.php" class="btn">&larr; Back to News</a>
            </p>
        <?php else: ?>
            <span class="news-tag" style="background:<?= htmlspecialchars($article['cat_color'] ?? '#1565C0') ?>">
                <?= htmlspecialchars($article['cat_name'] ?? 'News') ?>
            </span>
            <h2 class="section-title" style="text-align:left;margin-top:10px">
                <?= htmlspecialchars($article['title']) ?>
            </h2>
            <div class="news-meta" style="margin-bottom:20px">
                <span>&#128197; <?= date('F j, Y', strtotime($article['published_at'])) ?></span>
                <span>&#128100; <?= htmlspecialchars($article['author_name'] ?? 'Admin') ?></span>
            </div>
            <?php if ($article['featured_image']): ?>
            <img src="<?= htmlspecialchars($article['featured_image']) ?>"
                 alt="<?= htmlspecialchars($article['title']) ?>"
                 style="width:100%;border-radius:12px;margin-bottom:20px">
            <?php endif; ?>
            <p class="text" style="white-space:pre-line"><?= nl2br(htmlspecialchars($article['body'])) ?></p>

            <?php if ($related): ?>
            <h3 class="sub-title" style="margin-top:40px;">Related Stories</h3>
            <div class="news-grid">
                <?php foreach ($related as $r): ?>
                <div class="news-card">
                    <img src="<?= htmlspecialchars($r['featured_image'] ?: 'assets/images/images.jpg') ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="news-card-img">
                    <div class="news-card-body">
                        <h3><?= htmlspecialchars($r['title']) ?></h3>
                        <p><?= htmlspecialchars(excerpt($r['excerpt'], 100)) ?></p>
                        <a href="article.php?slug=<?= urlencode($r['slug']) ?>" class="news-readmore">Read More &rarr;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <p style="margin-top:30px">
                <a href="news.php" class="btn">&larr; Back to News</a>
            </p>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
