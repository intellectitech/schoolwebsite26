<?php
// ============================================================
//  news.php — News Listing Page
//  Open at: /news (rewritten via .htaccess to news.php)
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'News & Announcements';

// ── PAGINATION ────────────────────────────────────────────────
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

// ── OPTIONAL CATEGORY FILTER ─────────────────────────────────
$catSlug = clean($_GET['category'] ?? '');
$catWhere = '';
$catParams = [];
if ($catSlug) {
  $catWhere = 'AND nc.slug = ?';
  $catParams = [$catSlug];
}

// Total count for pagination
$countSql = "SELECT COUNT(*) FROM news n
             LEFT JOIN news_categories nc ON nc.id = n.category_id
             WHERE n.is_published = 1 $catWhere";
$total = $pdo->prepare($countSql);
$total->execute($catParams);
$totalRows = (int) $total->fetchColumn();
$totalPages = (int) ceil($totalRows / $perPage);

// Articles
$sql = "SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image,
               n.published_at, n.views,
               nc.name AS cat_name, nc.color AS cat_color
        FROM news n
        LEFT JOIN news_categories nc ON nc.id = n.category_id
        WHERE n.is_published = 1 $catWhere
        ORDER BY n.published_at DESC
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($catParams);
$articles = $stmt->fetchAll();

// All categories for filter tabs
$categories = $pdo->query('SELECT * FROM news_categories ORDER BY name')->fetchAll();

// One featured article for the hero card at the top of the page —
// falls back to the most recent article if nothing is flagged featured.
$featured = $pdo->query(
  'SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image,
            n.published_at,
            nc.name  AS cat_name,
            nc.color AS cat_color
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     WHERE n.is_published = 1
     ORDER BY n.is_featured DESC, n.published_at DESC
     LIMIT 1'
)->fetch();

// Flash message set by process_newsletter.php after a submit + redirect
$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php renderSeoTags('news', null, null, 'news'); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <?php include 'includes/head-meta.php'; ?>
</head>

<body>

  <?php include 'includes/header.php'; ?>

  <!-- ============================================================
     PAGE HERO
     ============================================================ -->
  <section class="page-hero">
    <div class="container reveal">
      <p class="breadcrumb"><a href="index.php">Home</a> / News</p>
      <p class="eyebrow">News & Announcements</p>
      <h1>What's happening at Uganda Martyrs</h1>
      <p class="lead">Term updates, pupil achievements, upcoming events and school news — everything that matters to our
        community, in one place.</p>
    </div>
    <div class="hill-divider" style="color:var(--ink)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,44 C240,4 480,0 720,18 C960,36 1200,40 1440,10 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
  </section>

  <!-- ============================================================
     FEATURED ARTICLE
     ============================================================ -->
  <?php if ($featured): ?>
    <section class="news-featured-section">
      <div class="container reveal">
        <p class="eyebrow">Latest Story</p>
        <h2>Featured</h2>
        <div class="news-featured-card">
          <div class="news-feat-img" aria-hidden="true">
            <?php if ($featured['featured_image'] && file_exists(__DIR__ . '/' . $featured['featured_image'])): ?>
              <img src="<?= htmlspecialchars($featured['featured_image']) ?>"
                alt="<?= htmlspecialchars($featured['title']) ?>" loading="lazy">
            <?php else: ?>
              <svg viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                <rect width="600" height="400" fill="#16233D" />
                <!-- hill layers -->
                <path d="M0,280 C120,230 280,220 400,260 C500,288 560,270 600,260 L600,400 L0,400 Z" fill="#2F6B4F"
                  opacity="0.7" />
                <path d="M0,320 C180,270 340,290 500,310 C560,318 590,315 600,312 L600,400 L0,400 Z" fill="#A6402E"
                  opacity="0.75" />
                <!-- sun -->
                <circle cx="300" cy="130" r="54" fill="#F2B705" />
                <g stroke="#F2B705" stroke-width="4" stroke-linecap="round" opacity="0.5">
                  <line x1="300" y1="56" x2="300" y2="40" />
                  <line x1="300" y1="220" x2="300" y2="204" />
                  <line x1="218" y1="130" x2="202" y2="130" />
                  <line x1="398" y1="130" x2="382" y2="130" />
                  <line x1="242" y1="72" x2="231" y2="61" />
                  <line x1="369" y1="199" x2="358" y2="188" />
                  <line x1="358" y1="72" x2="369" y2="61" />
                  <line x1="231" y1="199" x2="242" y2="188" />
                </g>
                <!-- banner text placeholder -->
                <rect x="60" y="220" width="480" height="3" fill="#F2B705" opacity="0.3" />
              </svg>
            <?php endif; ?>
          </div>
          <div class="news-feat-body">
            <span class="news-tag news-tag-gold"
              style="background:<?= htmlspecialchars($featured['cat_color'] ?? '#F2B705') ?>"><?= htmlspecialchars($featured['cat_name'] ?? 'News') ?></span>
            <p class="news-meta"><?= date('j F Y', strtotime($featured['published_at'])) ?></p>
            <h3><?= htmlspecialchars($featured['title']) ?></h3>
            <p class="news-excerpt"><?= htmlspecialchars(excerpt($featured['excerpt'] ?: '', 220)) ?></p>
            <a href="<?= urlencode($featured['slug']) ?>" class="btn btn-primary"
              style="align-self:flex-start">Read the full story →</a>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- ============================================================
     NEWS ARCHIVE GRID
     ============================================================ -->
  <section class="news-archive-section">
    <div class="container reveal">
      <p class="eyebrow">All Stories</p>
      <h2>Latest news</h2>

      <!-- Category filter tabs -->
      <div class="news-cat-tabs" role="group" aria-label="Filter news by category">
        <a href="news" class="cat-tab <?= !$catSlug ? 'active' : '' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
          <a href="news?category=<?= urlencode($cat['slug']) ?>"
            class="cat-tab <?= $catSlug === $cat['slug'] ? 'active' : '' ?>"
            style="--tab-color:<?= htmlspecialchars($cat['color']) ?>">
            <?= htmlspecialchars($cat['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if ($articles): ?>
        <div class="news-grid-3">
          <?php foreach ($articles as $article): ?>
            <div class="news-card">
              <div class="news-card-img">
                <?php if ($article['featured_image'] && file_exists(__DIR__ . '/' . $article['featured_image'])): ?>
                  <img class="card-img" src="<?= htmlspecialchars($article['featured_image']) ?>"
                    alt="<?= htmlspecialchars($article['title']) ?>" loading="lazy">
                <?php else: ?>
                  <svg viewBox="0 0 400 240" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                    <rect width="400" height="240" fill="<?= htmlspecialchars($article['cat_color'] ?? '#16233D') ?>" />
                    <path d="M0,180 C100,150 260,150 400,175 L400,240 L0,240 Z" fill="#FBF3E6" opacity="0.15" />
                  </svg>
                <?php endif; ?>
              </div>

              <div class="news-card-body">
                <span class="news-tag news-tag-leaf"
                  style="background:<?= htmlspecialchars($article['cat_color'] ?? '#1565C0') ?>">
                  <?= htmlspecialchars($article['cat_name'] ?? 'News') ?>
                </span>
                <h3><?= htmlspecialchars($article['title']) ?></h3>
                <p><?= htmlspecialchars(excerpt($article['excerpt'] ?? '', 120)) ?></p>
              </div>

              <div class="card-footer">
                <a href="<?= urlencode($article['slug']) ?>" class="news-read-more">Read more →</a>
                <span><?= date('d M Y', strtotime($article['published_at'])) ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
          <div class="news-pagination">
            <?php if ($page > 1): ?>
              <a class="pagination-btn"
                href="news?page=<?= $page - 1 ?><?= $catSlug ? '&category=' . urlencode($catSlug) : '' ?>"
                aria-label="Previous page">←</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a class="pagination-btn <?= $i === $page ? 'active' : '' ?>"
                href="news?page=<?= $i ?><?= $catSlug ? '&category=' . urlencode($catSlug) : '' ?>" <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
              <a class="pagination-btn"
                href="news?page=<?= $page + 1 ?><?= $catSlug ? '&category=' . urlencode($catSlug) : '' ?>"
                aria-label="Next page">→</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <p style="color:var(--ink-soft);padding:2rem 0">No news articles found<?= $catSlug ? ' in this category' : '' ?>
          yet.</p>
      <?php endif; ?>

    </div>
  </section>

  <!-- ============================================================
     NEWSLETTER SIGN-UP
     ============================================================ -->
  <section class="newsletter-section" id="newsletter">
    <div class="hill-divider" style="color:var(--cream)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow">Stay Informed</p>
      <h2>Get school news by email</h2>
      <p>We send a short newsletter at the end of each term — results, upcoming events, and a word from the head
        teacher. No spam, unsubscribe any time.</p>

      <?php if ($flash['success']): ?>
        <div class="alert alert-success" style="margin-top:1.2em;max-width:52ch">
          <?= htmlspecialchars($flash['success']) ?></div>
      <?php endif; ?>
      <?php if ($flash['errors']): ?>
        <div class="alert alert-error" style="margin-top:1.2em;max-width:52ch">
          <?php foreach ($flash['errors'] as $err): ?>
            <p><?= htmlspecialchars($err) ?></p><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="newsletter-form" id="newsletterForm" action="process_newsletter.php" method="POST">
        <?= csrfField() ?>
        <div class="hp-field" aria-hidden="true">
          <label for="n-website">Leave this field blank</label>
          <input id="n-website" name="website" type="text" tabindex="-1" autocomplete="off">
        </div>
        <input type="email" name="newsletter_email" placeholder="your@email.com" required aria-label="Email address">
        <button type="submit" class="btn btn-primary">Subscribe</button>
      </form>
    </div>
  </section>

  <!-- ============================================================
     CTA BAND
     ============================================================ -->
  <section class="cta-band">
    <div class="hill-divider" style="color:var(--leaf-tint)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <h2>Want to see the school for yourself?</h2>
      <p>Reading about us is a start — but nothing beats a morning in the compound, hearing the choir and meeting the
        teachers.</p>
      <div class="cta-actions">
        <a href="admissions" class="btn btn-primary">Begin Admissions</a>
        <a href="contact#find-us" class="btn btn-on-dark">Plan a Visit</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>

</html>