<?php
require_once 'includes/functions.php';
$pageTitle = getSetting($pdo, 'school_name') . ' — News & Events';

$newsList = $pdo->query(
    "SELECT n.*, nc.name AS cat_name, nc.color AS cat_color, a.name AS author_name
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     LEFT JOIN admin_users a ON a.id = n.author_id
     WHERE n.is_published = 1
     ORDER BY n.published_at DESC"
)->fetchAll();

$events = $pdo->query(
    "SELECT * FROM events WHERE is_published = 1
     ORDER BY event_date ASC"
)->fetchAll();

require_once 'includes/header.php';
?>
<section id="news" class="page-section active">
    <div class="news-events-wrapper">
        <h2 class="section-title">News & Upcoming Events</h2>
        <p class="section-subtitle">Stay updated with the latest happenings at <?= htmlspecialchars(getSetting($pdo,'school_name')) ?></p>

        <div class="news-tabs">
            <button class="news-tab-btn active" data-tab="news-spa">Latest News</button>
            <button class="news-tab-btn" data-tab="events-spa">Upcoming Events</button>
        </div>

        <div id="news-spa-tab" class="tab-content active">
            <div class="news-grid">
                <?php if (!$newsList): ?>
                    <p style="color:#6b7280">No news articles published yet.</p>
                <?php endif; ?>
                <?php foreach ($newsList as $article): ?>
                <div class="news-card">
                    <img src="<?= htmlspecialchars($article['featured_image'] ?: 'assets/images/images.jpg') ?>"
                         alt="<?= htmlspecialchars($article['title']) ?>" class="news-card-img">
                    <div class="news-card-body">
                        <span class="news-tag" style="background:<?= htmlspecialchars($article['cat_color'] ?? '#1565C0') ?>">
                            <?= htmlspecialchars($article['cat_name'] ?? 'News') ?>
                        </span>
                        <h3><?= htmlspecialchars($article['title']) ?></h3>
                        <div class="news-meta">
                            <span>&#128197; <?= date('F j, Y', strtotime($article['published_at'])) ?></span>
                            <span>&#128100; <?= htmlspecialchars($article['author_name'] ?? 'Admin') ?></span>
                        </div>
                        <p><?= htmlspecialchars(excerpt($article['excerpt'] ?: $article['body'], 160)) ?></p>
                        <a href="article.php?slug=<?= urlencode($article['slug']) ?>" class="news-readmore">Read More &rarr;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="events-spa-tab" class="tab-content">
            <div class="events-grid">
                <?php if (!$events): ?>
                    <p style="color:#6b7280">No events published yet.</p>
                <?php endif; ?>
                <?php foreach ($events as $ev):
                    $isPast = strtotime($ev['event_date']) < strtotime(date('Y-m-d'));
                    $img = $ev['featured_img'] ?? '';
                ?>
                <div class="event-card">
                    <img src="<?= htmlspecialchars($img ?: 'assets/images/images.jpg') ?>"
                         alt="<?= htmlspecialchars($ev['title']) ?>" class="event-card-img">
                    <div class="event-card-body">
                        <span class="news-tag event-tag">School Event</span>
                        <div class="event-header">
                            <div class="event-date-badge">
                                <span class="day"><?= date('d', strtotime($ev['event_date'])) ?></span>
                                <span class="month"><?= date('M', strtotime($ev['event_date'])) ?></span>
                            </div>
                            <div class="event-header-info">
                                <h3><?= htmlspecialchars($ev['title']) ?></h3>
                                <p class="event-location">&#128205; <?= htmlspecialchars($ev['location']) ?></p>
                            </div>
                            <span class="event-status <?= $isPast ? 'past' : 'upcoming' ?>"><?= $isPast ? 'Past' : 'Upcoming' ?></span>
                        </div>
                        <div class="event-meta">
                            <span>&#128337;
                                <?= $ev['start_time'] ? date('g:i A', strtotime($ev['start_time'])) : '' ?>
                                <?= $ev['end_time'] ? ' - ' . date('g:i A', strtotime($ev['end_time'])) : '' ?>
                            </span>
                        </div>
                        <p><?= htmlspecialchars(excerpt($ev['description'], 160)) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

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
<script>
document.querySelectorAll('.news-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.news-tab-btn').forEach(function(b){ b.classList.remove('active'); });
        document.querySelectorAll('.tab-content').forEach(function(t){ t.classList.remove('active'); });
        btn.classList.add('active');
        var tab = document.getElementById(btn.getAttribute('data-tab') + '-tab');
        if (tab) tab.classList.add('active');
    });
});
</script>
<?php require_once 'includes/footer.php'; ?>
