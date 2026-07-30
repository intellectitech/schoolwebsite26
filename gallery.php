<?php
require_once 'includes/functions.php';
$pageTitle = getSetting($pdo, 'school_name') . ' — Gallery';

// school_website_db: gallery_photos + gallery_albums
$images = $pdo->query(
    "SELECT p.*, a.name AS album_name
     FROM gallery_photos p
     LEFT JOIN gallery_albums a ON a.id = p.album_id
     WHERE a.is_published = 1 OR a.id IS NULL
     ORDER BY p.sort_order ASC, p.id ASC"
)->fetchAll();

require_once 'includes/header.php';
?>
<section id="gallery" class="page-section active">
    <div class="container">
        <h2 class="section-title">Photo Gallery</h2>
        <div class="gallery-grid">
            <?php if (!$images): ?>
                <p style="color:#6b7280">No photos in the gallery yet.</p>
            <?php endif; ?>
            <?php foreach ($images as $img):
                $src = $img['filename'];
                // Support both full paths and bare filenames
                if ($src && strpos($src, 'assets/') !== 0 && strpos($src, 'http') !== 0) {
                    $src = 'assets/images/' . ltrim($src, '/');
                }
            ?>
            <img src="<?= htmlspecialchars($src) ?>"
                 alt="<?= htmlspecialchars($img['caption'] ?: 'School Photo') ?>">
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
