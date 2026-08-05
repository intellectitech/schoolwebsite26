<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'School News';
$pageDescription = 'Read the latest news, achievements and announcements from Namugongo Model Primary School.';

$newsPosts = [];
if ($pdo instanceof PDO) {
    try {
        $newsPosts = $pdo->query('SELECT title, content, created_at FROM news_posts ORDER BY created_at DESC')->fetchAll();
    } catch (PDOException $e) {
        $newsPosts = [];
    }
}

// Fallback static content shown if the database has no rows yet (e.g. fresh install)
$fallbackNews = [
    ['title' => 'Inter-House Sports', 'content' => 'The school successfully held its annual sports day with great participation from all houses.', 'image' => 'images/news.jpg'],
    ['title' => 'Science Fair', 'content' => 'Students showcased exciting projects that demonstrated creativity and problem-solving skills.', 'image' => 'images/admissions.jpg'],
    ['title' => 'Parent Meeting', 'content' => 'Teachers and parents discussed school performance and strategies to improve student outcomes.', 'image' => 'images/speech.jpg'],
];

require_once __DIR__ . '/includes/header.php';
?>
        <section class="page-hero">
            <div class="container">
                <h1>School News</h1>
                <p>Keep up with the latest school activities, student successes, and announcements that keep our community moving forward.</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <?php if (!empty($dbError)) : ?>
                    <p class="notice notice-error"><?php echo h($dbError); ?> Showing recent highlights below.</p>
                <?php endif; ?>

                <div class="grid">
                <?php if (!empty($newsPosts)) : ?>
                    <?php foreach ($newsPosts as $post) : ?>
                        <div class="news-item reveal">
                            <img src="images/news.jpg" alt="<?php echo h($post['title']); ?>">
                            <h3><?php echo h($post['title']); ?></h3>
                            <p><?php echo h($post['content']); ?></p>
                            <p style="color:var(--muted); font-size:0.75rem; margin-top:8px;">
                                <?php echo h(date('d M Y', strtotime($post['created_at']))); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <?php foreach ($fallbackNews as $post) : ?>
                        <div class="news-item reveal">
                            <img src="<?php echo h($post['image']); ?>" alt="<?php echo h($post['title']); ?>">
                            <h3><?php echo h($post['title']); ?></h3>
                            <p><?php echo h($post['content']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>

                <div class="highlight-panel reveal">
                    <h3>Stay Connected</h3>
                    <p>Follow the latest achievements, student events, and school announcements through our community updates, or ask Nexa AI for a quick summary.</p>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
