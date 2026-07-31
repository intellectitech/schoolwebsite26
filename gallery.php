<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// ── PHOTOS ─────────────────────────────────────────────────
// Pull every published album, then only the photos in it whose
// file actually exists on disk. This keeps the page honest: if
// an admin hasn't uploaded anything into an album yet, that
// album simply doesn't produce any tiles below (rather than
// showing a broken image icon for a database row with no file
// behind it).
$albums = $pdo->query(
    'SELECT * FROM gallery_albums WHERE is_published = 1 ORDER BY sort_order ASC, id ASC'
)->fetchAll();

$photoStmt = $pdo->prepare(
    'SELECT * FROM gallery_photos WHERE album_id = ? ORDER BY sort_order ASC, id ASC'
);

$galleryItems = [];   // flat list of real photos to render as tiles
$filters = [];        // album_slug => album_name, only for albums with >= 1 real photo

foreach ($albums as $album) {
    $slug = slugify($album['name']);
    $photoStmt->execute([$album['id']]);
    foreach ($photoStmt->fetchAll() as $photo) {
        if ($photo['filename'] && file_exists(__DIR__ . '/' . $photo['filename'])) {
            $galleryItems[] = [
                'slug' => $slug,
                'album_name' => $album['name'],
                'caption' => $photo['caption'] !== '' ? $photo['caption'] : $album['name'],
                'filename' => $photo['filename'],
            ];
            $filters[$slug] = $album['name'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery · Uganda Martyrs Primary School, Namugongo</title>
  <meta name="description"
    content="Photos of school life at Uganda Martyrs Primary School, Namugongo — classrooms, sports, ceremonies, events and more.">
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
      <p class="breadcrumb"><a href="index.php">Home</a> / Gallery</p>
      <p class="eyebrow">Photo Gallery</p>
      <h1>Life at Uganda Martyrs, in pictures</h1>
      <p class="lead">From classroom mornings to the June pilgrimage, from inter-house sports days to the Primary Seven
        send-off Mass — a glimpse of what it looks like to be part of our school.</p>
    </div>
    <div class="hill-divider" style="color:var(--ink)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,44 C240,4 480,0 720,18 C960,36 1200,40 1440,10 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
  </section>

  <!-- ============================================================
     GALLERY  (filter + masonry grid)
     ============================================================ -->
  <section class="gallery-section">
    <div class="container">

      <?php if ($galleryItems): ?>
        <div class="reveal">
          <p class="eyebrow">Browse by album</p>
          <h2>School life through the lens</h2>

          <div class="gallery-filter-bar" role="group" aria-label="Filter gallery by album">
            <button class="filter-btn active" data-filter="all">All photos</button>
            <?php foreach ($filters as $slug => $albumName): ?>
              <button class="filter-btn" data-filter="<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($albumName) ?></button>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="gallery-grid reveal">
          <?php foreach ($galleryItems as $item): ?>
            <div class="gallery-item" data-category="<?= htmlspecialchars($item['slug']) ?>"
              data-caption="<?= htmlspecialchars($item['caption']) ?>">
              <div class="gallery-item-inner">
                <img class="gallery-media" src="<?= htmlspecialchars($item['filename']) ?>"
                  alt="<?= htmlspecialchars($item['caption']) ?>" loading="lazy">
                <div class="gallery-overlay">
                  <span class="gallery-cap-text"><?= htmlspecialchars($item['caption']) ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="gallery-empty reveal">
          <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="6" y="14" width="52" height="38" rx="4" stroke="currentColor" stroke-width="3" />
            <circle cx="20" cy="26" r="5" stroke="currentColor" stroke-width="3" />
            <path d="M10 46 L24 32 L34 42 L42 34 L54 46" stroke="currentColor" stroke-width="3" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
          <h2>Photos are on their way</h2>
          <p>We're still building this gallery — check back soon for pictures from classrooms, sports days, and school
            events.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============================================================
     LIGHTBOX (hidden until a gallery-item is clicked)
     ============================================================ -->
  <div class="lightbox-overlay" id="galleryLightbox" role="dialog" aria-modal="true" aria-label="Photo lightbox">
    <div class="lightbox-content">
      <button class="lightbox-close" aria-label="Close photo">&times;</button>
      <div class="lightbox-img-wrap" id="lightboxSvgWrap"></div>
      <p class="lightbox-caption" id="lightboxCaption"></p>
    </div>
  </div>

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
      <h2>Ready to make your own memories here?</h2>
      <p>The best way to see the school is to walk through the gate on a school morning. Come and say hello.</p>
      <div class="cta-actions">
        <a href="admissions.php" class="btn btn-primary">Begin Admissions</a>
        <a href="contact.php" class="btn btn-on-dark">Plan a Visit</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>

</html>
