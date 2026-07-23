<?php
//-Session first-
session_start();

//-Load database & helpers-
require_once 'config/database.php';
require_once 'includes/functions.php';

//Set Page Title-
$pageTitle = getSettings($pdo, 'school_name') . '-Home';

//Fetch All Data (before any HTML output)-
$latestNews = $pdo->query(
    "SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image, n.published_at, nc.name AS cat_name, nc.color AS cat_color
    FROM news n
    LEFT JOIN news_categories nc ON nc.id = n.category_id
    WHERE n.published = 1
    ORDER BY n.published_at DESC
    LIMIT 3"
)->fetchAll();

$events = $pdo->query(
    "SELECT * FROM events
    WHERE is_published = 1 AND event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT 3"
)->fetchAll();

$testimonials = $pdo->query(
    "SELECT * FROM testimonials
    WHERE is_published = 1
    LIMIT 3"
)->fetchAll();

$heroTitle = getSettings($pdo, 'hero_title');
$heroSubtitle = getSettings($pdo, 'hero_subtitle');
?>

<!--Include Header-->
<?php require_once 'includes/header.php'; ?>

<!--HOME PAGE SECTIONS -->
<!--Sections 1-7 go here, built below -->

<section class= "hero">
    <div class="container">
        <h1><?= htmlspecialchars(getSettings($pdo, 'school_name')) ?></h1>
        <p><?= htmlspecialchars(getSettings($pdo, 'hero_subtitle')) ?></p>
</div class="hero-btns">
        <a href="admissions.php" class="btn btn-primary">Apply Now</a>
        <a href="contact.php" class="btn btn-secondary">Learn More</a>
      </div>
    </div>
</section>


<section class = "stats-bar">
    <div class="container stats-grid">
        <?php
        $stats = [
            ['Founded', getSettings($pdo, 'founded_year')],
            ['Students', getSettings($pdo, 'student_count')],
            ['Teachers', '60+'],
            ['Subjects', '20+'],
        ];
        foreach ($stats as [$label, $value]): ?>
           <div>
            <div class= "stat-num"><?= htmlspecialchars($value) ?></div>
            <div class="stat-label"><?= htmlspecialchars($label) ?></div>
           </div>
        <?php endforeach; ?>
    </div>
</section>


<section class="latest-news">
    <div class="container">
        <h2 class="section-title">Latest News</h2>
        <div class="cards-grid">
            <?php foreach ($latestNews as $a): ?>
                <div class="card">
                    <span class="badge" style="background:<?= htmlspecialchars($a['cat_color']) ?>"><?= htmlspecialchars($a['cat_name']) ?></span>
                        <h3><?= htmlspecialchars($a['title']) ?></h3>
                        <p><?= excerpt($a['excerpt'], 120) ?></p>
                        <a href="article.php?slug=<?= urlencode($a['slug']) ?>" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<section class="section section-alt">
    <div class="container">
        <h2 class="section-title">Upcoming Events</h2>
        <?php foreach ($events as $ev): ?>
            <div class="event-item">
                <div class="date-box">
                    <div class="date"><?= date('d', strtotime($ev['event_date'])) ?></div>
                    <div class="month"><?= date('M', strtotime($ev['event_date'])) ?></div>
                </div>      
                <div class="event-details">
                    <h3><?= htmlspecialchars($ev['event_name']) ?></h3>
                    <p><?= htmlspecialchars($ev['event_location']) ?></p>
                </div>  
            </div>   
        <?php endforeach; ?>
         </div>
</section>


<section class="section">
    <div class="container">
        <h2 class="section-title">Why Choose Us</h2>
        <div class="cards-grid-6">
            <div class="reason">
                <strong>Qualified Teachers</strong>
                <p>Our teachers are highly qualified and experienced in their respective fields.</p></div>
            <div class="reason">
                <strong>Modern Facilities</strong>
                <p>We provide state-of-the-art facilities to enhance the learning experience.</p></div>
            <div class="reason">
                <strong>Proven Results</strong>
                <p>80% PASS RATE in both UCE and UACE.</p></div>
            <div class="reason">
                <strong>Safe Environment</strong>
                <p>We provide 24-hr security, CCTV and structured daily routines for our students.</p></div>
            <div class="reason">
                <strong>Sports and Clubs</strong>
                <p>We offer a wide range of extracurricular activities with more 14 active clubs and teams.</p></div>
        </div>
    </div>
</section>


<section class="section section-alt">
    <div class="container">
        <?php foreach ($testimonials as $t): ?>
            <blockquote>
                "<?= htmlspecialchars($t['quote']) ?>"
                <footer>- <?= htmlspecialchars($t['author']) ?></footer>
            </blockquote>
        <?php endforeach; ?>
    </div>
</section>


<section class="section section-alt" style="text-align:center">
    <div class="container">
        <h2>Ready to Join Us?</h2>
        <p>Applications for 2026/2027 are open for S1 and S5 entry.</p>
        <a href="admissions.php" class="btn btn-primary">Apply Now</a>
    </div>
</section>

<!--INCLUDE FOOTER-->
<?php require_once 'includes/footer.php'; ?>