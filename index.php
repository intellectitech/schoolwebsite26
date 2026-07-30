<?php
// ============================================================
//  index.php — Homepage
// ============================================================
require_once 'includes/functions.php';

$pageId    = 'home';
$pageTitle = getSetting($pdo, 'school_name') . ' — Home';

$heroTitle    = getSetting($pdo, 'hero_title');
$heroSubtitle = getSetting($pdo, 'hero_subtitle');
$foundedYear  = getSetting($pdo, 'founded_year', '25+');
$totalStudents= getSetting($pdo, 'total_students', '1200+');
$passRate     = getSetting($pdo, 'stat_pass_rate', '95%');
$teacherCount = getSetting($pdo, 'stat_teachers', '100+');

// A few published parent/alumni testimonials for the homepage
$testimonials = $pdo->query(
    "SELECT * FROM testimonials WHERE is_published = 1 ORDER BY sort_order ASC LIMIT 3"
)->fetchAll();

// Latest 3 published news articles
$latestNews = $pdo->query(
    "SELECT n.*, nc.name AS cat_name, nc.color AS cat_color
     FROM news n LEFT JOIN news_categories nc ON nc.id = n.category_id
     WHERE n.is_published = 1
     ORDER BY n.published_at DESC LIMIT 2"
)->fetchAll();

// Next 1 upcoming event
$nextEvent = $pdo->query(
    "SELECT * FROM events WHERE is_published = 1 AND event_date >= CURDATE()
     ORDER BY event_date ASC LIMIT 1"
)->fetch();

require_once 'includes/header.php';
?>
<section id="home" class="page-section active">
    <section class="hero">
        <div class="hero-slides">
            <div class="hero-slide" style="background-image: url('assets/images/images.jpg');"></div>
            <div class="hero-slide" style="background-image: url('assets/images/image1.jpg');"></div>
            <div class="hero-slide" style="background-image: url('assets/images/image2.jpg');"></div>
            <div class="hero-slide" style="background-image: url('assets/images/image3.jpg');"></div>
        </div>
        <div class="hero-content">
            <div class="school-badge">
                <img src="assets/images/images.jpg" alt="School Badge">
            </div>
            <h1><?= htmlspecialchars($heroTitle) ?></h1>
            <p><?= htmlspecialchars($heroSubtitle) ?></p>
            <a href="admissions.php" class="btn">Apply Now</a>
        </div>
    </section>

    <div class="container">
        <h2 class="section-title">Why Choose Our School?</h2>
        <div class="features">
            <div class="feature">
                <img src="assets/images/image14.jpg" alt="Academic Excellence">
                <h3>Academic Excellence</h3>
                <p>Dedicated teachers and modern curriculum to ensure your child achieves their full potential.</p>
            </div>
            <div class="feature">
                <img src="assets/images/image20.jpg" alt="Character Development">
                <h3>Character Development</h3>
                <p>We focus on building strong values and character alongside academic achievement.</p>
            </div>
            <div class="feature">
                <img src="assets/images/image12.jpg" alt="Modern Facilities">
                <h3>Modern Facilities</h3>
                <p>State-of-the-art classrooms, libraries, and sports facilities for holistic growth.</p>
            </div>
        </div>
    </div>

    <section class="news-preview-section">
        <div class="news-preview-inner">
            <h2 class="section-title">Latest News & Upcoming Events</h2>
            <p class="section-subtitle">Stay connected with what's happening in our school community</p>

            <div class="features">
                <?php foreach ($latestNews as $article): ?>
                <div class="feature">
                    <img src="<?= htmlspecialchars($article['featured_image'] ?: 'assets/images/images.jpg') ?>"
                         alt="<?= htmlspecialchars($article['title']) ?>">
                    <span class="news-tag"><?= htmlspecialchars($article['cat_name'] ?? 'News') ?></span>
                    <h3><?= htmlspecialchars($article['title']) ?></h3>
                    <p><?= htmlspecialchars(excerpt($article['excerpt'] ?: $article['body'], 130)) ?></p>
                    <p style="color:#6b7280;font-size:14px;margin-bottom:15px;">
                        &#128197; <?= date('F j, Y', strtotime($article['published_at'])) ?>
                    </p>
                </div>
                <?php endforeach; ?>

                <?php if ($nextEvent): ?>
                <div class="feature">
                    <div style="display:flex;align-items:flex-start;gap:15px;margin-bottom:22px;">
                        <div class="event-date-badge">
                            <span class="day"><?= date('d', strtotime($nextEvent['event_date'])) ?></span>
                            <span class="month"><?= date('M', strtotime($nextEvent['event_date'])) ?></span>
                        </div>
                        <div style="text-align:left;flex:1;">
                            <span class="news-tag event-tag"><?= htmlspecialchars('School Event') ?></span>
                            <h3 style="margin-top:12px;text-align:left;font-size:20px;"><?= htmlspecialchars($nextEvent['title']) ?></h3>
                        </div>
                    </div>
                    <p><?= htmlspecialchars(excerpt($nextEvent['description'], 140)) ?></p>
                    <p style="color:#6b7280;font-size:14px;margin-bottom:15px;">
                        &#128337; <?= $nextEvent['start_time'] ? date('g:i A', strtotime($nextEvent['start_time'])) : '' ?>
                        &#128205; <?= htmlspecialchars($nextEvent['location']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            <div class="view-all-btn">
                <a href="news.php" class="btn nav-link">View All News & Events</a>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="stats-grid">
            <div class="stat"><h3><?= htmlspecialchars($foundedYear) ?></h3><p>Years of Excellence</p></div>
            <div class="stat"><h3><?= htmlspecialchars($totalStudents) ?></h3><p>Pupils Enrolled</p></div>
            <div class="stat"><h3><?= htmlspecialchars($passRate) ?></h3><p>PLE Pass Rate</p></div>
            <div class="stat"><h3><?= htmlspecialchars($teacherCount) ?></h3><p>Qualified Teachers</p></div>
        </div>
    </section>

    <?php if ($testimonials): ?>
    <div class="container">
        <h2 class="section-title">What Our Parents Say</h2>
        <div class="testimonial-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <div class="testimonial-stars"><?= str_repeat('&#9733;', (int) $t['rating']) . str_repeat('&#9734;', 5 - (int) $t['rating']) ?></div>
                <p class="testimonial-content">&ldquo;<?= htmlspecialchars($t['content']) ?>&rdquo;</p>
                <p class="testimonial-author"><?= htmlspecialchars($t['author_name']) ?></p>
                <?php if ($t['author_role']): ?><p class="testimonial-role"><?= htmlspecialchars($t['author_role']) ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="newsletter-box">
            <h3>Stay In The Loop</h3>
            <p>Subscribe for termly updates on news, events and admissions at NPPS.</p>
            <?php $flash = getFlash(); ?>
            <?php if ($flash['success']): ?>
                <p style="color:#4ade80;font-weight:700;margin-bottom:16px;"><?= htmlspecialchars($flash['success']) ?></p>
            <?php elseif ($flash['errors']): ?>
                <p style="color:#fca5a5;font-weight:700;margin-bottom:16px;"><?= htmlspecialchars($flash['errors'][0]) ?></p>
            <?php endif; ?>
            <form class="newsletter-form" action="process_newsletter.php" method="POST">
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
                <input type="email" name="email" placeholder="Your email address" required value="<?= old($flash['old'], 'email') ?>">
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
