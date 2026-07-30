<?php
// index.php - Updated hero section with settings
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = getSetting($pdo, 'school_name', 'School') . ' - Home';

// Fetch data
$newsStmt = $pdo->query("
    SELECT n.*, nc.name as cat_name, nc.color_code as cat_color
    FROM news n
    LEFT JOIN news_categories nc ON nc.id = n.category_id
    WHERE n.is_published = 1
    ORDER BY n.published_at DESC, n.created_at DESC
    LIMIT 3
");
$latestNews = $newsStmt->fetchAll();

$eventsStmt = $pdo->query("
    SELECT * FROM events
    WHERE is_published = 1 AND event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT 5
");
$upcomingEvents = $eventsStmt->fetchAll();

$testimonialsStmt = $pdo->query("
    SELECT * FROM testimonials
    WHERE is_published = 1
    ORDER BY sort_order
    LIMIT 3
");
$testimonials = $testimonialsStmt->fetchAll();

// Get hero images from settings
$heroImagesSetting = getSetting($pdo, 'hero_images', '');
$heroImages = $heroImagesSetting ? array_map('trim', explode(',', $heroImagesSetting)) : [];

// If no images are set, use defaults
if (empty($heroImages) || (count($heroImages) === 1 && empty($heroImages[0]))) {
    $heroImages = [
        'assets/images/hero1.jpg',
        'assets/images/hero2.jpg',
        'assets/images/hero3.jpg'
    ];
}

// Get hero subtitle
$heroSubtitle = getSetting($pdo, 'hero_subtitle', 'A Center of Academic Excellence and Moral Integrity');

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="hero-slider">
        <?php foreach ($heroImages as $index => $img): 
            $imagePath = !empty($img) ? trim($img) : 'assets/images/placeholder.jpg';
        ?>
            <img src="<?= clean($imagePath) ?>" 
                 alt="School Image <?= $index + 1 ?>" 
                 loading="lazy" 
                 class="<?= $index === 0 ? 'active' : '' ?>"
                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%221920%22 height=%221080%22><rect width=%221920%22 height=%221080%22 fill=%22%231a4d2e%22/><text x=%2250%25%22 y=%2250%25%22 font-family=%22Arial%22 font-size=%2236%22 fill=%22%23FFD700%22 text-anchor=%22middle%22 dy=%22.3em%22>School Image</text></svg>'">
        <?php endforeach; ?>
    </div>
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge"><i class="fas fa-star"></i> Excellence in Education</span>
            <h1><?= clean(getSetting($pdo, 'school_name', 'St. Mary\'s High School')) ?></h1>
            <div class="typewriter-wrapper">
                <span class="typewriter-text"><span class="cursor">|</span></span>
            </div>
            <p class="hero-description"><?= clean($heroSubtitle) ?></p>
            <div class="hero-buttons">
                <a href="admissions.php" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Apply Now</a>
                <a href="about.php" class="btn btn-outline"><i class="fas fa-info-circle"></i> Learn More</a>
            </div>
        </div>
    </div>
    <div class="hero-dots">
        <?php foreach ($heroImages as $index => $img): 
            if (empty(trim($img))) continue;
        ?>
            <button class="dot <?= $index === 0 ? 'active' : '' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
</section>

<!-- Stats Bar -->
<section class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number"><?= clean(getSetting($pdo, 'founded_year', '1985')) ?></span>
                <span class="stat-label">UCE </span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= clean(getSetting($pdo, 'total_students', '1,200')) ?>+</span>
                <span class="stat-label">Students</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= clean(getSetting($pdo, 'total_teachers', '60')) ?>+</span>
                <span class="stat-label">Teachers</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= clean(getSetting($pdo, 'pass_rate', '86')) ?>%</span>
                <span class="stat-label">UACE Pass Rate</span>
            </div>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="news-section" id="news">
    <div class="container">
        <div class="section-title">
            <h2>Latest News</h2>
            <p>Stay updated with what's happening at our school</p>
        </div>
        <div class="cards-grid" id="news-feed">
            <?php if (!empty($latestNews)): ?>
                <?php foreach ($latestNews as $news): ?>
                    <div class="card">
                        <?php if (!empty($news['featured_image'])): ?>
                            <img src="<?= clean($news['featured_image']) ?>" alt="<?= clean($news['title']) ?>" class="card-img" loading="lazy">
                        <?php else: ?>
                            <div class="card-img-placeholder"></div>
                        <?php endif; ?>
                        <div class="card-content">
                            <?php if (!empty($news['cat_name'])): ?>
                                <span class="badge" style="background:<?= clean($news['cat_color'] ?? '#2d7a4a') ?>; color:#fff;">
                                    <?= clean($news['cat_name']) ?>
                                </span>
                            <?php endif; ?>
                            <h3><a href="article.php?slug=<?= clean($news['slug']) ?>"><?= clean($news['title']) ?></a></h3>
                            <p><?= clean(truncate($news['excerpt'] ?? $news['body'], 120)) ?></p>
                            <div class="card-meta">
                                <span class="date"><i class="fas fa-calendar-alt"></i> <?= formatDate($news['published_at'] ?? $news['created_at']) ?></span>
                                <a href="article.php?slug=<?= clean($news['slug']) ?>" class="read-more">Read More →</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">No news articles yet. Check back soon!</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Events Section -->
<?php if (!empty($upcomingEvents)): ?>
<section class="events-section" id="events">
    <div class="container">
        <div class="section-title">
            <h2>Upcoming Events</h2>
            <p>Don't miss out on our school activities</p>
        </div>
        <div class="events-list">
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="event-item">
                    <div class="event-date-box">
                        <span class="day"><?= date('d', strtotime($event['event_date'])) ?></span>
                        <span class="month"><?= date('M', strtotime($event['event_date'])) ?></span>
                    </div>
                    <div class="event-body">
                        <h4><?= clean($event['title']) ?></h4>
                        <p><?= clean($event['description']) ?></p>
                        <span class="event-location"><i class="fas fa-map-marker-alt"></i> <?= clean($event['location'] ?? 'School Campus') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Why Choose Us Section -->
<section class="why-section" id="why">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose Us</h2>
            <p>Discover what makes our school the right choice for your child</p>
        </div>
        <div class="why-grid">
            <div class="why-item">
                <div class="icon"><i class="fas fa-graduation-cap"></i></div>
                <h4>Academic Excellence</h4>
                <p>Consistent top performance with an 86% UACE pass rate and numerous university admissions.</p>
            </div>
            <div class="why-item">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h4>Experienced Staff</h4>
                <p>Over 60 qualified teachers with years of experience dedicated to student success.</p>
            </div>
            <div class="why-item">
                <div class="icon"><i class="fas fa-futbol"></i></div>
                <h4>Sports & Activities</h4>
                <p>Active sports programs, clubs, and societies that develop well-rounded students.</p>
            </div>
            <div class="why-item">
                <div class="icon"><i class="fas fa-flask"></i></div>
                <h4>Modern Facilities</h4>
                <p>Well-equipped laboratories, libraries, and classrooms that enhance learning.</p>
            </div>
            <div class="why-item">
                <div class="icon"><i class="fas fa-shield-alt"></i></div>
                <h4>Safe Environment</h4>
                <p>A secure campus with boarding facilities for both boys and girls.</p>
            </div>
            <div class="why-item">
                <div class="icon"><i class="fas fa-handshake"></i></div>
                <h4>Community Values</h4>
                <p>Strong moral values, discipline, and community engagement.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<?php if (!empty($testimonials)): ?>
<section class="testimonials-section" id="testimonials">
    <div class="container">
        <div class="section-title">
            <h2>What Parents & Students Say</h2>
            <p>Real stories from our school community</p>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-item">
                    <div class="quote"><?= clean($testimonial['content']) ?></div>
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= ($testimonial['rating'] ?? 5) ? '' : 'far' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <div class="author">
                        <div class="author-info">
                            <div class="name"><?= clean($testimonial['author_name']) ?></div>
                            <div class="role"><?= clean($testimonial['author_role'] ?? 'Parent') ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Join Us?</h2>
            <p>Applications for <?= date('Y') . '/' . (date('Y') + 1) ?> are now open for S1 and S5 entry.<br>
            <strong style="color: var(--gold);">Only 40 S1 places available!</strong></p>
            <a href="admissions.php" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Apply Now</a>
        </div>
    </div>
</section>

<!-- Neexa Widget -->
<script>
  window.neexaAsyncInit = function() {
    window.neexa.init({
      agent_id: 'a24dd7ec-8092-448d-abe6-b4f34dba983e', mobile_mini_style: 'greeting_only',
    });
  };
</script>
<script async src="https://chat-widget.neexa.ai/main.js?nonce=1784552208885.7986"></script>
<!-- End Neexa Widget -->


<?php include 'includes/footer.php'; ?>