<?php
// ============================================================
//  index.php — Homepage
//  school-website/index.php
//  Open at: http://localhost/schoolwebsite26/
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = getSetting($pdo, 'school_name') . ' — Home';

// ── FETCH DATA FROM DATABASE ──────────────────────────────────

// Latest 3 published news articles
$stmt = $pdo->query(
    'SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image,
            n.published_at,
            nc.name  AS cat_name,
            nc.color AS cat_color
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     WHERE n.is_published = 1
     ORDER BY n.published_at DESC
     LIMIT 3'
);
$latestNews = $stmt->fetchAll();

// Upcoming events (today or future, max 4)
$events = $pdo->query(
    'SELECT * FROM events
     WHERE is_published = 1
       AND event_date >= CURDATE()
     ORDER BY event_date ASC
     LIMIT 4'
)->fetchAll();

// Published testimonials
$testimonials = $pdo->query(
    'SELECT * FROM testimonials
     WHERE is_published = 1
     ORDER BY sort_order ASC
     LIMIT 3'
)->fetchAll();

// Page content blocks
$heroTitle    = getSetting($pdo, 'hero_title');
$heroSubtitle = getSetting($pdo, 'hero_subtitle');
$foundedYear   = getSetting($pdo, 'founded_year');
$totalStudents = getSetting($pdo, 'total_students');
$totalTeachers = getSetting($pdo, 'total_teachers');
$totalSubjects = getSetting($pdo, 'total_subjects');
?>
<?php require_once 'includes/header.php'; ?>

<!-- ── HERO SLIDER ── -->
<section class="hero hero-slider" id="heroSlider">

    <div class="hero-slide active"
         style="background-image: url('assets/images/school-hero.svg');">

        <div class="hero-overlay"></div>

        <div class="container hero-container">
            <div class="hero-content">

                <span class="hero-badge">
                    Admissions Open 2026/27
                </span>

                <h1>
                    <?= htmlspecialchars(
                        $heroTitle ?: getSetting($pdo, 'school_name')
                    ) ?>
                </h1>

                <p>
                    <?= htmlspecialchars(
                        $heroSubtitle ?: 'Shaping tomorrow\'s leaders through quality education.'
                    ) ?>
                </p>

                <div class="hero-btns">
                    <a href="admissions.php" class="btn btn-primary">
                        Apply for Admission
                    </a>

                    <a href="about.php" class="btn btn-outline">
                        Discover Our School
                    </a>
                </div>

            </div>

            <div class="hero-highlight">
                <div class="highlight-icon">★</div>

                <div>
                    <span>Our Commitment</span>
                    <strong>Excellence, Discipline & Faith</strong>
                </div>
            </div>
        </div>
    </div>


    <!-- SLIDE 2 -->
    <div class="hero-slide"
         style="background-image: url('assets/images/admissions-hero.svg');">

        <div class="hero-overlay"></div>

        <div class="container hero-container">
            <div class="hero-content">

                <span class="hero-badge">
                    Join Kalinabiri
                </span>

                <h1>
                    Admissions Open for 2026/27
                </h1>

                <p>
                    Begin your child's journey in an environment built on
                    academic excellence, discipline and strong values.
                </p>

                <div class="hero-btns">
                    <a href="admissions.php" class="btn btn-primary">
                        Apply Now
                    </a>

                    <a href="admissions.php" class="btn btn-outline">
                        Admission Requirements
                    </a>
                </div>

            </div>

            <div class="hero-highlight">
                <div class="highlight-icon">✓</div>

                <div>
                    <span>Now Enrolling</span>
                    <strong>Senior One & Senior Five</strong>
                </div>
            </div>
        </div>
    </div>


    <!-- SLIDE 3 -->
    <div class="hero-slide"
         style="background-image: url('assets/images/excellence-hero.svg');">

        <div class="hero-overlay"></div>

        <div class="container hero-container">
            <div class="hero-content">

                <span class="hero-badge">
                    Discover Excellence
                </span>

                <h1>
                    Building Knowledge, Character & Purpose
                </h1>

                <p>
                    We nurture confident learners through quality education,
                    discipline, faith and a commitment to excellence.
                </p>

                <div class="hero-btns">
                    <a href="about.php" class="btn btn-primary">
                        Discover Our School
                    </a>

                    <a href="contact.php" class="btn btn-outline">
                        Contact Us
                    </a>
                </div>

            </div>

            <div class="hero-highlight">
                <div class="highlight-icon">★</div>

                <div>
                    <span>Our Vision</span>
                    <strong>Preparing Learners for a Better Future</strong>
                </div>
            </div>
        </div>
    </div>


    <!-- SLIDER ARROWS -->
    <button class="hero-arrow hero-prev"
            type="button"
            aria-label="Previous slide">
        &#10094;
    </button>

    <button class="hero-arrow hero-next"
            type="button"
            aria-label="Next slide">
        &#10095;
    </button>


    <!-- SLIDER DOTS -->
    <div class="hero-dots" aria-label="Hero slider navigation">
        <button class="hero-dot active" type="button" data-slide="0" aria-label="Go to slide 1"></button>
        <button class="hero-dot" type="button" data-slide="1" aria-label="Go to slide 2"></button>
        <button class="hero-dot" type="button" data-slide="2" aria-label="Go to slide 3"></button>
    </div>

</section>
<!-- ── PREMIUM STATS SECTION ── -->
<section class="stats-bar">
    <div class="container stats-grid">

        <div class="stat-item">
            <div class="stat-icon">◆</div>

            <div class="stat-content">
                <div class="stat-num">
                    <?= htmlspecialchars($foundedYear ?: '1984') ?>
                </div>

                <div class="stat-label">
                    Established
                </div>
            </div>
        </div>


        <div class="stat-item">
            <div class="stat-icon">♟</div>

            <div class="stat-content">
                <div
                    class="stat-num stat-counter"
                    data-target="<?= htmlspecialchars($totalStudents ?: '1200') ?>"
                    data-suffix="+"
                >
                    0
                </div>

                <div class="stat-label">
                    Active Students
                </div>
            </div>
        </div>


        <div class="stat-item">
            <div class="stat-icon">✦</div>

            <div class="stat-content">
                <div
                    class="stat-num stat-counter"
                    data-target="60"
                    data-suffix="+"
                >
                    0
                </div>

                <div class="stat-label">
                    Dedicated Teachers
                </div>
            </div>
        </div>


        <div class="stat-item">
            <div class="stat-icon">▣</div>

            <div class="stat-content">
                <div
                    class="stat-num stat-counter"
                    data-target="18"
                    data-suffix="+"
                >
                    0
                </div>

                <div class="stat-label">
                    Subjects Offered
                </div>
            </div>
        </div>

    </div>
</section>
<!-- ── PREMIUM LATEST NEWS ── -->
<section class="news-section">

    <div class="container">

        <div class="news-heading">

            <div class="news-heading-content">

                <span class="section-eyebrow">
                    School Updates
                </span>

                <h2>
                    Latest News & Stories
                </h2>

                <p>
                    Discover the latest achievements, activities and important
                    updates from the Kalinabiri Secondary School community.
                </p>

            </div>

            <a href="news.php" class="news-view-all">
                View All News
                <span>→</span>
            </a>

        </div>


        <?php if ($latestNews): ?>

            <div class="news-grid">

                <?php foreach ($latestNews as $index => $article): ?>

                    <article class="news-card <?= $index === 0 ? 'news-card-featured' : '' ?>">

                        <a
                            href="article.php?slug=<?= urlencode($article['slug']) ?>"
                            class="news-image"
                        >

                            <?php if (!empty($article['featured_image'])): ?>

                                <img
                                    src="<?= htmlspecialchars($article['featured_image']) ?>"
                                    alt="<?= htmlspecialchars($article['title']) ?>"
                                >

                            <?php else: ?>

                                <div class="news-image-placeholder">
                                    <span>KSS</span>
                                </div>

                            <?php endif; ?>


                            <span
                                class="news-category"
                                style="--category-color: <?= htmlspecialchars($article['cat_color'] ?? '#F9A825') ?>"
                            >
                                <?= htmlspecialchars($article['cat_name'] ?? 'School News') ?>
                            </span>

                        </a>


                        <div class="news-card-content">

                            <div class="news-meta">

                                <span class="news-date">
                                    <?= date('d M Y', strtotime($article['published_at'])) ?>
                                </span>

                                <span class="news-meta-dot"></span>

                                <span>
                                    Kalinabiri Secondary School
                                </span>

                            </div>


                            <h3>

                                <a href="article.php?slug=<?= urlencode($article['slug']) ?>">

                                    <?= htmlspecialchars($article['title']) ?>

                                </a>

                            </h3>


                            <p>

                                <?= htmlspecialchars(
                                    excerpt($article['excerpt'] ?? '', 145)
                                ) ?>

                            </p>


                            <a
                                href="article.php?slug=<?= urlencode($article['slug']) ?>"
                                class="news-read-more"
                            >

                                Read Full Story

                                <span>→</span>

                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>


        <?php else: ?>

            <div class="news-empty">

                <div class="news-empty-icon">
                    ✦
                </div>

                <h3>
                    Stories Are Coming Soon
                </h3>

                <p>
                    News, achievements and school updates will appear here
                    as they are published.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>
<!-- ── WHY KALINABIRI ── -->
<section class="why-school">

    <div class="container why-school-grid">

        <div class="why-school-visual">

            <div class="why-image-frame">
                <img
                    src="assets/images/why-kalinabiri.svg"
                    alt="Students learning at Kalinabiri Secondary School"
                >
            </div>

            <div class="why-experience-card">
                <strong><?= htmlspecialchars($foundedYear ?: '1984') ?></strong>
                <span>Years of Excellence</span>
            </div>

        </div>


        <div class="why-school-content">

            <span class="section-eyebrow">
                Why Kalinabiri
            </span>

            <h2>
                Education That Builds
                <span>Knowledge & Character.</span>
            </h2>

            <p class="why-intro">
                At Kalinabiri Secondary School, we nurture confident,
                disciplined and purpose-driven learners through quality
                education, strong values and a supportive school community.
            </p>


            <div class="why-features">

                <div class="why-feature">
                    <div class="why-feature-icon">✦</div>

                    <div>
                        <h3>Academic Excellence</h3>
                        <p>
                            A focused learning environment that inspires
                            students to achieve their academic potential.
                        </p>
                    </div>
                </div>


                <div class="why-feature">
                    <div class="why-feature-icon">◆</div>

                    <div>
                        <h3>Discipline & Character</h3>
                        <p>
                            We develop responsible learners guided by
                            discipline, integrity and respect.
                        </p>
                    </div>
                </div>


                <div class="why-feature">
                    <div class="why-feature-icon">★</div>

                    <div>
                        <h3>Faith & Values</h3>
                        <p>
                            Our school community promotes faith, moral
                            values and purposeful living.
                        </p>
                    </div>
                </div>


                <div class="why-feature">
                    <div class="why-feature-icon">▣</div>

                    <div>
                        <h3>Student Development</h3>
                        <p>
                            Learners are encouraged to grow through
                            leadership, sports and co-curricular activities.
                        </p>
                    </div>
                </div>

            </div>


            <a href="about.php" class="why-link">
                Discover More About Our School
                <span>→</span>
            </a>

        </div>

    </div>

</section>

<!-- ── UPCOMING EVENTS ── -->
<?php if ($events): ?>
<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">Upcoming Events</h2>
        <p class="section-sub">Mark your calendar — don't miss these important dates.</p>

        <?php foreach ($events as $ev): ?>
        <div class="event-item">
            <div class="event-date">
                <div class="day"><?= date('d', strtotime($ev['event_date'])) ?></div>
                <div class="month"><?= date('M Y', strtotime($ev['event_date'])) ?></div>
            </div>
            <div class="event-body">
                <strong><?= htmlspecialchars($ev['title']) ?></strong>
                <small>
                    <?php if ($ev['location']): ?>
                    📍 <?= htmlspecialchars($ev['location']) ?>
                    <?php endif; ?>
                    <?php if ($ev['start_time']): ?>
                    &nbsp;·&nbsp; 🕗 <?= date('g:i A', strtotime($ev['start_time'])) ?>
                    <?php endif; ?>
                </small>
                <?php if ($ev['description']): ?>
                <p style="margin-top:.3rem;color:var(--muted);font-size:.9rem">
                    <?= htmlspecialchars(excerpt($ev['description'], 100)) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ── TESTIMONIALS ── -->
<?php if ($testimonials): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">What People Say</h2>
        <p class="section-sub">Hear from our students and parents.</p>

        <div class="cards-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <p><?= htmlspecialchars($t['content']) ?></p>
                <div class="testimonial-author"><?= htmlspecialchars($t['author_name']) ?></div>
                <div class="testimonial-role"><?= htmlspecialchars($t['author_role'] ?? '') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── CALL TO ACTION ── -->
<section class="section section-alt">
    <div class="container" style="text-align:center">
        <h2 class="section-title">Ready to Join Us?</h2>
        <p class="section-sub" style="margin-bottom:1.5rem">
            Applications for 2026/27 are now open for S1 and S5 entry.
        </p>
        <a href="admissions.php" class="btn btn-primary" style="font-size:1.1rem;padding:.9rem 2.5rem">
            Start Your Application
        </a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
