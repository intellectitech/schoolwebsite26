<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/datastore.php';

$slug = $_GET['slug'] ?? null;

// ---------- Single post view ----------
if ($slug) {
    $allNews = ds_read('news');
    $post = null;
    foreach ($allNews as $p) {
        if ($p['slug'] === $slug && !empty($p['is_published'])) { $post = $p; break; }
    }

    $page_title = $post ? $post['title'] . " | " . SITE_NAME : "News &amp; Blog | " . SITE_NAME;
    $active_page = 'news';
    require_once __DIR__ . '/includes/header.php';

    if (!$post): ?>
        <section><div class="container text-center">
            <h2>Post Not Found</h2>
            <p>Sorry, we couldn't find that article.</p>
            <a href="news.php" class="btn btn-blue">Back to News &amp; Blog</a>
        </div></section>
    <?php else: ?>
        <div class="page-hero">
          <div class="container">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="breadcrumb"><a href="index.php">Home</a> / <a href="news.php">News &amp; Blog</a> / <?php echo htmlspecialchars($post['title']); ?></div>
          </div>
        </div>
        <section>
          <div class="container" style="max-width:820px;">
            <img src="<?php echo htmlspecialchars($post['cover_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width:100%; border-radius:14px; margin-bottom:26px;">
            <div class="news-meta"><?php echo htmlspecialchars($post['category'] ?? 'News'); ?><span class="news-date"><?php echo date('d F Y', strtotime($post['published_at'])); ?></span></div>
            <p style="font-size:1.05rem; line-height:1.8;"><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
            <a href="news.php" class="btn btn-blue" style="margin-top:20px;">&larr; Back to News &amp; Blog</a>
          </div>
        </section>
    <?php endif;

    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ---------- Listing view ----------
$page_title = "News & Blog | " . SITE_NAME;
$active_page = 'news';

$posts = ds_read('news');
$posts = array_values(array_filter($posts, function ($p) { return !empty($p['is_published']); }));
usort($posts, function ($a, $b) { return strtotime($b['published_at']) - strtotime($a['published_at']); });

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <h1>News &amp; Blog</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / News &amp; Blog</div>
  </div>
</div>

<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Stay Informed</span>
      <h2>Latest News &amp; Articles</h2>
      <p>School updates, events, academic highlights and tips for parents.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($posts as $post):
          $category = $post['category'] ?? 'News';
          $date = date('d M Y', strtotime($post['published_at']));
      ?>
      <div class="card">
        <img src="<?php echo htmlspecialchars($post['cover_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="card-img">
        <div class="card-body">
          <div class="news-meta"><?php echo htmlspecialchars($category); ?><span class="news-date"><?php echo $date; ?></span></div>
          <h3><?php echo htmlspecialchars($post['title']); ?></h3>
          <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
          <a href="news.php?slug=<?php echo urlencode($post['slug']); ?>" class="read-more">Read More &rarr;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
