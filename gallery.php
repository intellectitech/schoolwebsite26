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
.gallery-hero {
    background: linear-gradient(135deg, #0d2617, #1a4d2e);
    color: #fff;
    padding: 60px 0;
    text-align: center;
}
.gallery-hero h1 {
    color: #fff;
    font-size: 2.8rem;
}
.gallery-hero p {
    color: rgba(255,255,255,0.8);
    max-width: 600px;
    margin: 15px auto 0;
}
.gallery-section {
    padding: 60px 0 80px;
}
.album-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}
.album-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s;
    cursor: pointer;
}
.album-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}
.album-card .cover {
    position: relative;
    height: 240px;
    overflow: hidden;
}
.album-card .cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.album-card:hover .cover img {
    transform: scale(1.05);
}
.album-card .cover .overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: #fff;
}
.album-card .cover .overlay .count {
    font-size: 0.85rem;
    opacity: 0.8;
}
.album-card .info {
    padding: 20px;
}
.album-card .info h3 {
    color: #1a4d2e;
    margin-bottom: 5px;
}
.album-card .info p {
    color: #666;
    font-size: 0.93rem;
}
.no-albums {
    text-align: center;
    padding: 60px 0;
}
.no-albums i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 20px;
}
.no-albums h3 {
    color: #666;
}
.lightbox {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 40px;
}
.lightbox.active {
    display: flex;
}
.lightbox .close {
    position: absolute;
    top: 20px;
    right: 40px;
    color: #fff;
    font-size: 3rem;
    cursor: pointer;
    transition: all 0.3s;
}
.lightbox .close:hover {
    transform: rotate(90deg);
}
.lightbox img {
    max-width: 90%;
    max-height: 80vh;
    border-radius: 8px;
    object-fit: contain;
}
.lightbox .caption {
    position: absolute;
    bottom: 40px;
    color: #fff;
    font-size: 1.1rem;
    text-align: center;
    max-width: 80%;
}
.lightbox .nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    font-size: 3rem;
    cursor: pointer;
    padding: 20px;
    transition: all 0.3s;
    opacity: 0.5;
}
.lightbox .nav:hover {
    opacity: 1;
}
.lightbox .nav.prev { left: 20px; }
.lightbox .nav.next { right: 20px; }
@media (max-width: 992px) {
    .album-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .album-grid {
        grid-template-columns: 1fr;
    }
    .gallery-hero h1 {
        font-size: 2rem;
    }
    .lightbox .nav {
        font-size: 2rem;
        padding: 10px;
    }
    .lightbox .nav.prev { left: 5px; }
    .lightbox .nav.next { right: 5px; }
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
                                <div style="height:100%;background:#e9ecef;display:flex;align-items:center;justify-content:center;color:#999;font-size:3rem;">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="overlay">
                                <div class="count"><i class="fas fa-camera"></i> <?= $album['photo_count'] ?> photos</div>
                            </div>
                        </div>
                        <div class="info">
                            <h3><?= clean($album['name']) ?></h3>
                            <?php if (!empty($album['description'])): ?>
                                <p><?= clean($album['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-albums">
                <i class="fas fa-images"></i>
                <h3>No Albums Available</h3>
                <p style="color:#666;">Check back soon for photo updates.</p>
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
    card.addEventListener('click', function() {
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