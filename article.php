<?php
// ============================================================
//  article.php — Single News Article
//  Open at: article.php?slug=some-article-slug
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$slug = clean($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: news.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT n.*, nc.name AS cat_name, nc.color AS cat_color, nc.slug AS cat_slug
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     WHERE n.slug = ? AND n.is_published = 1
     LIMIT 1'
);
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
}

// Count a view every time the article is opened (best-effort, not unique-visitor tracking)
if ($article) {
    $pdo->prepare('UPDATE news SET views = views + 1 WHERE id = ?')->execute([$article['id']]);
}

// A few related stories from the same category
$related = [];
if ($article) {
    $rStmt = $pdo->prepare(
        'SELECT title, slug, excerpt, featured_image, published_at
         FROM news
         WHERE is_published = 1 AND category_id = ? AND id != ?
         ORDER BY published_at DESC
         LIMIT 3'
    );
    $rStmt->execute([$article['category_id'], $article['id']]);
    $related = $rStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $article ? htmlspecialchars($article['title']) . ' · ' : 'Story Not Found · ' ?>Uganda Martyrs Primary School, Namugongo</title>
  <?php if ($article): ?>
  <meta name="description" content="<?= htmlspecialchars(excerpt($article['excerpt'] ?: strip_tags($article['body']), 160)) ?>">
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<?php if (!$article): ?>

  <section class="page-hero">
    <div class="container reveal">
      <p class="eyebrow">News</p>
      <h1>Story not found</h1>
      <p class="lead">This article may have been moved or unpublished.</p>
      <div class="hero-actions"><a href="news.php" class="btn btn-primary">← Back to all news</a></div>
    </div>
  </section>

<?php else: ?>

  <section class="page-hero">
    <div class="container reveal">
      <p class="eyebrow"><a href="news.php?category=<?= urlencode($article['cat_slug'] ?? '') ?>" style="color:inherit"><?= htmlspecialchars($article['cat_name'] ?? 'News') ?></a></p>
      <h1><?= htmlspecialchars($article['title']) ?></h1>
      <p class="lead">
        <?= date('l, d F Y', strtotime($article['published_at'])) ?>
        · <?= (int) $article['views'] ?> view<?= (int) $article['views'] === 1 ? '' : 's' ?>
      </p>
    </div>
  </section>

  <section class="article-section">
    <div class="container reveal" style="max-width:760px">
      <?php if ($article['featured_image'] && file_exists(__DIR__ . '/' . $article['featured_image'])): ?>
        <img class="article-hero-img" src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
      <?php else: ?>
      <svg class="article-hero-img" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
        <rect width="800" height="400" fill="<?= htmlspecialchars($article['cat_color'] ?? '#16233D') ?>"/>
        <path d="M0,300 C160,250 340,240 500,270 C620,292 720,280 800,270 L800,400 L0,400 Z" fill="#2F6B4F" opacity="0.55"/>
        <path d="M0,340 C220,300 420,315 600,330 L800,320 L800,400 L0,400 Z" fill="#A6402E" opacity="0.6"/>
        <circle cx="640" cy="110" r="46" fill="#F2B705"/>
      </svg>
      <?php endif; ?>

      <div class="article-body">
        <?= nl2br(htmlspecialchars($article['body'])) ?>
      </div>

      <p class="article-back"><a href="news.php" class="btn btn-ghost">← Back to all news</a></p>
    </div>
  </section>

  <?php if ($related): ?>
  <section class="news-archive-section">
    <div class="container reveal">
      <p class="eyebrow">Related</p>
      <h2>More from <?= htmlspecialchars($article['cat_name'] ?? 'News') ?></h2>
      <div class="news-grid-3">
        <?php foreach ($related as $r): ?>
          <div class="news-card">
            <div class="news-card-img">
              <?php if ($r['featured_image'] && file_exists(__DIR__ . '/' . $r['featured_image'])): ?>
                <img class="card-img" src="<?= htmlspecialchars($r['featured_image']) ?>" alt="<?= htmlspecialchars($r['title']) ?>">
              <?php else: ?>
                <svg viewBox="0 0 400 240" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                  <rect width="400" height="240" fill="<?= htmlspecialchars($article['cat_color'] ?? '#16233D') ?>"/>
                </svg>
              <?php endif; ?>
            </div>
            <div class="news-card-body">
              <h3><?= htmlspecialchars($r['title']) ?></h3>
              <p><?= htmlspecialchars(excerpt($r['excerpt'] ?? '', 100)) ?></p>
            </div>
            <div class="card-footer">
              <a href="article.php?slug=<?= urlencode($r['slug']) ?>" class="news-read-more">Read more →</a>
              <span><?= date('d M Y', strtotime($r['published_at'])) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>

</body>
</html>
