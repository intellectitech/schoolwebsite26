<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/datastore.php';

$page_title = "Gallery | " . SITE_NAME;
$active_page = 'gallery';

$images = ds_read('gallery');
usort($images, function ($a, $b) { return strtotime($b['uploaded_at'] ?? 'now') - strtotime($a['uploaded_at'] ?? 'now'); });

// Build unique category list for the filter bar
$categories = [];
foreach ($images as $img) {
    $cat = $img['category'] ?? 'General';
    if (!in_array($cat, $categories)) { $categories[] = $cat; }
}

require_once __DIR__ . '/includes/header.php';

function slugify($text) {
    return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $text), '-'));
}
?>

<div class="page-hero">
  <div class="container">
    <h1>Photo Gallery</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / Gallery</div>
  </div>
</div>

<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Life at Mbuya Parents' School</span>
      <h2>Explore Our Gallery</h2>
      <p>Academics, sports, ICT, and celebrations &mdash; moments from around our campus.</p>
    </div>

    <div class="filter-bar">
      <button class="filter-btn active" data-filter="all">All</button>
      <?php foreach ($categories as $cat): ?>
        <button class="filter-btn" data-filter="<?php echo slugify($cat); ?>"><?php echo htmlspecialchars($cat); ?></button>
      <?php endforeach; ?>
    </div>

    <div class="gallery-grid">
      <?php foreach ($images as $img):
          $cat = $img['category'] ?? 'General';
      ?>
      <div class="gallery-item" data-category="<?php echo slugify($cat); ?>">
        <img src="<?php echo htmlspecialchars($img['file_path']); ?>" alt="<?php echo htmlspecialchars($img['title']); ?>">
        <div class="gallery-caption"><?php echo htmlspecialchars($img['title']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
  <span class="lightbox-close">&times;</span>
  <div>
    <img id="lightboxImg" src="" alt="">
    <div class="lightbox-cap" id="lightboxCap"></div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
