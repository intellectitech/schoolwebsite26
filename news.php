<?php
// gallery.php
include 'db_connect.php';

// Safe HTML output helper
if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// ---------------------------------------------------------------------
// DYNAMIC COLUMN DETECTION (Prevents Missing Column Errors)
// ---------------------------------------------------------------------
function getColumns(PDO $pdo, string $table): array {
    try {
        $stmt = $pdo->prepare("
            SELECT COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table
        ");
        $stmt->execute([':table' => $table]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    } catch (PDOException $e) {
        return [];
    }
}

$albumCols = getColumns($pdo, 'gallery_albums');
$photoCols = getColumns($pdo, 'gallery_photos');

// Album title column detection
$albumTitleCol = 'title';
foreach (['title', 'album_title', 'album_name', 'name'] as $col) {
    if (in_array($col, $albumCols)) { $albumTitleCol = $col; break; }
}

$hasAlbumDesc = in_array('description', $albumCols);
$hasAlbumDate = in_array('created_at', $albumCols);

// Photo image path column detection
$photoPathCol = 'photo';
if (in_array('image_path', $photoCols)) {
    $photoPathCol = 'image_path';
} elseif (in_array('image', $photoCols)) {
    $photoPathCol = 'image';
}

// ---------------------------------------------------------------------
// FETCH ALBUMS WITH COVER PHOTOS & PHOTO COUNTS
// ---------------------------------------------------------------------
$selectFields = "a.album_id, a.{$albumTitleCol} AS album_title";
if ($hasAlbumDesc) { $selectFields .= ", a.description"; }
if ($hasAlbumDate) { $selectFields .= ", a.created_at"; }

$query = "
    SELECT 
        {$selectFields},
        COUNT(p.photo_id) AS total_photos,
        (SELECT p2.{$photoPathCol} 
         FROM gallery_photos p2 
         WHERE p2.album_id = a.album_id 
         ORDER BY p2.photo_id DESC LIMIT 1) AS cover_photo
    FROM gallery_albums a
    LEFT JOIN gallery_photos p ON a.album_id = p.album_id
    GROUP BY a.album_id
    ORDER BY a.album_id DESC
";

$albums = [];
try {
    $albums = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback if gallery_photos query fails or table is empty
    $albums = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Gallery Showcase | Bbina Islamic Primary School</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header Navigation -->
    <header class="site-header">
        <div class="header-container">
            <a href="index.php" class="brand-logo">
                <div class="logo-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="brand-info">
                    <span class="brand-title">Bbina Islamic</span>
                    <span class="brand-subtitle">Primary School</span>
                </div>
            </a>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="academics.php">Academics</a></li>
                    <li><a href="gallery.php" class="active">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <a href="admissions.php" class="cta-button">Apply Now</a>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="gallery-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="badge">Campus Archives</span>
            <h1>School Life & Event Gallery</h1>
            <p>Browse through structured albums highlighting our academic milestones, co-curricular events, and campus community activities.</p>
        </div>
    </section>

    <!-- Gallery Section Container -->
    <main class="gallery-section">
        <div class="section-header">
            <h2>Explore Gallery Albums</h2>
            <p>Select a card below to view detailed image collections and event highlights.</p>
        </div>

        <!-- Dynamic Cards Grid Layout -->
        <div class="cards-grid">

            <?php if (empty($albums)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem;">
                    <i class="fa-regular fa-images" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                    <p style="font-size: 1.1rem; color: var(--text-muted);">No gallery albums available yet. Check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach ($albums as $album): ?>
                    <?php 
                        // Cover Image Logic
                        $coverImg = !empty($album['cover_photo']) 
                            ? $album['cover_photo'] 
                            : 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=800'; // Fallback image

                        // Date Formatting Logic
                        $dateDisplay = !empty($album['created_at']) 
                            ? date('M d, Y', strtotime($album['created_at'])) 
                            : 'Recent Album';
                    ?>
                    <article class="gallery-card">
                        <div class="card-media">
                            <img src="<?= h($coverImg); ?>" alt="<?= h($album['album_title']); ?>" onerror="this.src='https://via.placeholder.com/800x600?text=No+Cover+Image';">
                            <span class="card-count">
                                <i class="fa-solid fa-image"></i> <?= (int)$album['total_photos']; ?> Photos
                            </span>
                            <span class="card-tag">Campus Life</span>
                        </div>
                        <div class="card-body">
                            <h3><?= h($album['album_title']); ?></h3>
                            <p class="card-description">
                                <?= h($album['description'] ?? 'Explore photo updates from this event and school collection.'); ?>
                            </p>
                            <div class="card-footer">
                                <span class="card-date"><i class="fa-regular fa-calendar"></i> <?= $dateDisplay; ?></span>
                                <a href="album_details.php?id=<?= (int)$album['album_id']; ?>" class="card-btn">
                                    View Album <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-bottom">
            <p>&copy; 2026 Bbina Islamic Primary School. All rights reserved.</p>
        </div>
    </footer>

<style>

/* ==========================================
   GLOBAL VARIABLES & RESET
   ========================================== */
:root {
    --primary: #0b4f2c;
    --primary-dark: #052c18;
    --accent: #d97706;
    --bg-light: #f1f5f9;
    --surface: #ffffff;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --radius-card: 12px;
    --shadow-card: 0 4px 12px rgba(0, 0, 0, 0.05);
    --shadow-hover: 0 12px 24px rgba(0, 0, 0, 0.12);
    --transition: all 0.3s ease;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

body {
    background-color: var(--bg-light);
    color: var(--text-dark);
    line-height: 1.6;
}

a {
    text-decoration: none;
    color: inherit;
}

/* ==========================================
   HEADER & NAVIGATION
   ========================================== */
.site-header {
    background: var(--surface);
    border-bottom: 1px solid var(--border-color);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.brand-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: var(--primary);
    color: #fff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.brand-title {
    display: block;
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--primary-dark);
    line-height: 1.1;
}

.brand-subtitle {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
}

.main-nav ul {
    display: flex;
    list-style: none;
    gap: 1.75rem;
}

.main-nav a {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-dark);
}

.main-nav a:hover,
.main-nav a.active {
    color: var(--primary);
}

.cta-button {
    background: var(--primary);
    color: #fff;
    padding: 0.55rem 1.2rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
}

/* ==========================================
   HERO BANNER
   ========================================== */
.gallery-hero {
    position: relative;
    background-image: url('https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1600');
    background-size: cover;
    background-position: center;
    padding: 4.5rem 1.5rem;
    text-align: center;
    color: #fff;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(11, 79, 44, 0.88) 0%, rgba(15, 23, 42, 0.9) 100%);
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 650px;
    margin: 0 auto;
}

.badge {
    display: inline-block;
    background: rgba(217, 119, 6, 0.2);
    border: 1px solid var(--accent);
    color: #fef3c7;
    padding: 0.2rem 0.75rem;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
}

.hero-content h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.75rem;
}

.hero-content p {
    font-size: 1rem;
    color: #cbd5e1;
}

/* ==========================================
   GALLERY SECTION & CARDS GRID
   ========================================== */
.gallery-section {
    max-width: 1200px;
    margin: 3rem auto;
    padding: 0 1.5rem;
}

.section-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.section-header h2 {
    font-size: 1.8rem;
    color: var(--primary-dark);
    margin-bottom: 0.5rem;
}

.section-header p {
    color: var(--text-muted);
    font-size: 0.95rem;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 2rem;
}

/* INDIVIDUAL GALLERY CARD */
.gallery-card {
    background: var(--surface);
    border-radius: var(--radius-card);
    overflow: hidden;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-card);
    display: flex;
    flex-direction: column;
    transition: var(--transition);
}

.gallery-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-hover);
}

.card-media {
    position: relative;
    height: 210px;
    overflow: hidden;
}

.card-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.gallery-card:hover .card-media img {
    transform: scale(1.06);
}

.card-count {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(15, 23, 42, 0.75);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.3rem 0.65rem;
    border-radius: 50px;
    backdrop-filter: blur(4px);
}

.card-tag {
    position: absolute;
    bottom: 12px;
    left: 12px;
    background: var(--primary);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.25rem 0.6rem;
    border-radius: 4px;
    text-transform: uppercase;
}

/* CARD CONTENT BODY */
.card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.card-body h3 {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.card-description {
    font-size: 0.88rem;
    color: var(--text-muted);
    line-height: 1.55;
    margin-bottom: 1.25rem;
    flex-grow: 1;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--border-color);
    padding-top: 0.85rem;
}

.card-date {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 500;
}

.card-btn {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: var(--transition);
}

.card-btn:hover {
    color: var(--accent);
}

/* ==========================================
   FOOTER
   ========================================== */
.site-footer {
    background: var(--primary-dark);
    color: #94a3b8;
    margin-top: 4rem;
    padding: 1.5rem 0;
    text-align: center;
    font-size: 0.85rem;
}

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        gap: 1rem;
    }
    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>

</body>
</html>