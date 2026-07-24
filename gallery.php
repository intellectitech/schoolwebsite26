<?php
// gallery.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Gallery - ' . getSetting($pdo, 'school_name', 'School');

// Fetch albums with photos
$albumsStmt = $pdo->query("
    SELECT a.*, 
           (SELECT COUNT(*) FROM gallery_photos WHERE album_id = a.id) as photo_count
    FROM gallery_albums a
    WHERE a.is_published = 1
    ORDER BY a.sort_order, a.created_at DESC
");
$albums = $albumsStmt->fetchAll();

include 'includes/header.php';
?>

<style>
/* ============================================
   GALLERY PAGE SPECIFIC STYLES (Edugrade UI)
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
    --shadow-hover: 0 8px 30px rgba(233, 30, 99, 0.15);
}

/* --- Hero Section --- */
.gallery-hero {
    background: linear-gradient(135deg, var(--navy-dark) 0%, #2d2d54 100%);
    position: relative;
    color: var(--white);
    padding: 80px 0 60px;
    text-align: center;
    overflow: hidden;
}

.gallery-hero .container {
    position: relative;
    z-index: 1;
}

.gallery-hero h1 {
    color: var(--white);
    font-size: 3.2rem;
    font-weight: 700;
    letter-spacing: -1px;
    margin-bottom: 15px;
}

.gallery-hero h1 i {
    color: var(--primary-red);
    margin-right: 12px;
}

.gallery-hero p {
    color: rgba(255,255,255,0.85);
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.15rem;
    line-height: 1.7;
}

/* --- Section Wrappers --- */
.gallery-section {
    padding: 80px 0;
    background: var(--light-gray-bg);
}

/* --- Section Title (Left Aligned) --- */
.gallery-section .section-title {
    text-align: left;
    margin-bottom: 40px;
}
.gallery-section .section-title h2 {
    display: inline-block;
    position: relative;
    padding-bottom: 12px;
    color: var(--navy-dark);
}
.gallery-section .section-title h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-red);
}
.gallery-section .section-title p {
    color: var(--text-gray);
    max-width: 500px;
    margin-top: 10px;
}

/* --- Album Grid --- */
.album-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.album-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.album-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
}

.album-card .cover {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.album-card .cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.album-card:hover .cover img {
    transform: scale(1.08);
}

.album-card .cover .overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px 25px;
    background: linear-gradient(transparent, rgba(26, 26, 46, 0.85));
    color: var(--white);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.album-card .cover .overlay .count {
    font-size: 0.85rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 6px;
}
.album-card .cover .overlay .count i {
    color: var(--primary-red);
}

.album-card .info {
    padding: 25px 25px 30px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 130px;
}

.album-card .info h3 {
    color: var(--navy-dark);
    margin-bottom: 8px;
    font-size: 1.2rem;
}

.album-card .info p {
    color: var(--text-gray);
    font-size: 0.93rem;
    line-height: 1.5;
    flex-grow: 1;
    margin-bottom: 15px;
}

.album-card .btn-view {
    display: inline-block;
    padding: 8px 22px;
    background: var(--navy-dark);
    color: var(--white);
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: var(--transition);
    align-self: flex-start;
    text-decoration: none;
}

.album-card .btn-view:hover {
    background: var(--primary-red);
    color: var(--white);
    transform: translateY(-2px);
}

/* --- Empty State --- */
.no-albums {
    text-align: center;
    padding: 60px 0;
    background: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow-card);
}
.no-albums i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 20px;
}
.no-albums h3 {
    color: var(--text-gray);
}

/* --- Lightbox --- */
.lightbox {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(26, 26, 46, 0.95); /* Navy overlay */
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 40px;
    backdrop-filter: blur(4px);
}

.lightbox.active {
    display: flex;
}

.lightbox .close {
    position: absolute;
    top: 30px;
    right: 45px;
    color: var(--white);
    font-size: 3rem;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
    opacity: 0.8;
}

.lightbox .close:hover {
    transform: rotate(90deg);
    opacity: 1;
    color: var(--primary-red);
}

.lightbox img {
    max-width: 90%;
    max-height: 80vh;
    border-radius: 8px;
    object-fit: contain;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}

.lightbox .caption {
    position: absolute;
    bottom: 50px;
    color: rgba(255,255,255,0.9);
    font-size: 1.2rem;
    text-align: center;
    max-width: 80%;
    background: rgba(0,0,0,0.4);
    padding: 10px 25px;
    border-radius: 50px;
}

.lightbox .nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: var(--white);
    font-size: 3.5rem;
    cursor: pointer;
    padding: 20px;
    transition: all 0.3s ease;
    opacity: 0.5;
    z-index: 10;
    background: rgba(0,0,0,0.2);
    border-radius: 50%;
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox .nav:hover {
    opacity: 1;
    background: var(--primary-red);
}

.lightbox .nav.prev { left: 30px; }
.lightbox .nav.next { right: 30px; }

/* --- Responsive --- */
@media (max-width: 992px) {
    .album-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .gallery-hero { padding: 60px 0; }
    .gallery-hero h1 { font-size: 2.2rem; }
    .gallery-hero p { font-size: 1rem; }
    .lightbox .nav {
        font-size: 2rem;
        width: 50px;
        height: 50px;
    }
    .lightbox .nav.prev { left: 10px; }
    .lightbox .nav.next { right: 10px; }
}

@media (max-width: 576px) {
    .album-grid { grid-template-columns: 1fr; }
    .gallery-hero h1 { font-size: 1.8rem; }
    .album-card .info { min-height: auto; }
}
</style>

<!-- Hero -->
<section class="gallery-hero">
    <div class="container">
        <h1><i class="fas fa-images"></i> Photo Gallery</h1>
        <p>Explore life at <?= clean(getSetting($pdo, 'school_name', 'our school')) ?> through our photos</p>
    </div>
</section>

<!-- Gallery -->
<section class="gallery-section">
    <div class="container">
        <div class="section-title">
            <h2>Our Albums</h2>
            <p>Browse through our memorable moments and events</p>
        </div>

        <?php if (!empty($albums)): ?>
            <div class="album-grid">
                <?php foreach ($albums as $album): 
                    // Get first photo for cover
                    $photoStmt = $pdo->prepare("
                        SELECT image_path FROM gallery_photos 
                        WHERE album_id = ? 
                        ORDER BY sort_order 
                        LIMIT 1
                    ");
                    $photoStmt->execute([$album['id']]);
                    $coverPhoto = $photoStmt->fetch();
                ?>
                    <div class="album-card" data-album="<?= $album['id'] ?>">
                        <div class="cover">
                            <?php if ($coverPhoto): ?>
                                <img src="<?= clean($coverPhoto['image_path']) ?>" alt="<?= clean($album['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <div style="height:100%;background:#e9ecef;display:flex;align-items:center;justify-content:center;color:#adb5bd;font-size:3rem;">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="overlay">
                                <div class="count"><i class="fas fa-camera"></i> <?= $album['photo_count'] ?> photos</div>
                            </div>
                        </div>
                        <div class="info">
                            <div>
                                <h3><?= clean($album['name']) ?></h3>
                                <?php if (!empty($album['description'])): ?>
                                    <p><?= clean($album['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="btn-view">View Album &rarr;</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-albums">
                <i class="fas fa-images"></i>
                <h3>No Albums Available</h3>
                <p style="color:#666;margin-top:5px;">Check back soon for photo updates.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
    <span class="close" onclick="closeLightbox()">&times;</span>
    <span class="nav prev" onclick="changePhoto(-1)">&#10094;</span>
    <span class="nav next" onclick="changePhoto(1)">&#10095;</span>
    <img id="lightbox-img" src="" alt="">
    <div class="caption" id="lightbox-caption"></div>
</div>

<script>
let currentAlbum = null;
let currentPhotos = [];
let currentIndex = 0;

document.querySelectorAll('.album-card').forEach(card => {
    card.addEventListener('click', function(e) {
        // Don't trigger if they clicked the text/button container accidentally, but keep simple
        const albumId = this.dataset.album;
        loadAlbumPhotos(albumId);
    });
});

async function loadAlbumPhotos(albumId) {
    try {
        const response = await fetch(`api/album.php?id=${albumId}`);
        if (!response.ok) throw new Error('Failed to load photos');
        const data = await response.json();
        currentPhotos = data.photos;
        currentAlbum = data.album;
        currentIndex = 0;
        openLightbox();
    } catch (error) {
        console.error('Error loading album:', error);
        alert('Could not load photos. Please try again.');
    }
}

function openLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
    showPhoto(currentIndex);
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
}

function showPhoto(index) {
    if (!currentPhotos || currentPhotos.length === 0) return;
    const photo = currentPhotos[index];
    document.getElementById('lightbox-img').src = photo.image_path;
    document.getElementById('lightbox-caption').textContent = photo.caption || currentAlbum?.name || '';
}

function changePhoto(direction) {
    if (!currentPhotos || currentPhotos.length === 0) return;
    currentIndex = (currentIndex + direction + currentPhotos.length) % currentPhotos.length;
    showPhoto(currentIndex);
}

// Keyboard controls
document.addEventListener('keydown', function(e) {
    if (!document.getElementById('lightbox').classList.contains('active')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') changePhoto(-1);
    if (e.key === 'ArrowRight') changePhoto(1);
});

// Close on click outside
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});
</script>

<?php include 'includes/footer.php'; ?>