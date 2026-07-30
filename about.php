<?php
require_once 'includes/functions.php';
$pageId = 'about';
$pageTitle = getSetting($pdo, 'school_name') . ' — About Us';

$aboutIntro = getSetting($pdo, 'about_intro');
$vision     = getSetting($pdo, 'vision_text');
$mission    = getSetting($pdo, 'mission_text');
$motto      = getSetting($pdo, 'motto');

$content = $pdo->query("SELECT section, content FROM page_content WHERE page = 'about'")->fetchAll(PDO::FETCH_KEY_PAIR);
$history    = $content['history']    ?? '';
$facilities = $content['facilities'] ?? '';

// A short leadership preview — full directory lives on staff.php
$leaders = $pdo->query(
    "SELECT s.*, d.name AS department_name FROM staff s
     LEFT JOIN departments d ON d.id = s.department_id
     WHERE s.is_active = 1 AND s.is_managemnet = 1
     ORDER BY s.sort_order ASC LIMIT 3"
)->fetchAll();

$testimonials = $pdo->query(
    "SELECT * FROM testimonials WHERE is_published = 1 ORDER BY sort_order ASC LIMIT 3"
)->fetchAll();

require_once 'includes/header.php';
?>
<section id="about" class="page-section active">
    <div class="container">
        <h2 class="section-title">About Us</h2>
        <div class="two-col">
            <img src="assets/images/image4.jpg" alt="School Building">
            <div>
                <p class="intro-text"><?= htmlspecialchars($aboutIntro) ?></p>
                <?php if ($motto): ?><p class="text"><strong>Our Motto:</strong> "<?= htmlspecialchars($motto) ?>"</p><?php endif; ?>
                <h3 class="sub-title">Our Vision</h3>
                <p class="text"><?= htmlspecialchars($vision) ?></p>
                <h3 class="sub-title">Our Mission</h3>
                <p class="text"><?= htmlspecialchars($mission) ?></p>
            </div>
        </div>

        <?php if ($history): ?>
        <div class="two-col" style="margin-top:40px;">
            <div>
                <h3 class="sub-title">Our Story</h3>
                <p class="text"><?= htmlspecialchars($history) ?></p>
            </div>
            <img src="assets/images/image6.jpg" alt="School History">
        </div>
        <?php endif; ?>

        <?php if ($facilities): ?>
        <div class="two-col" style="margin-top:10px;">
            <img src="assets/images/image18.jpg" alt="School Facilities">
            <div>
                <h3 class="sub-title">Our Facilities</h3>
                <p class="text"><?= htmlspecialchars($facilities) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($leaders): ?>
        <h3 class="sub-title" style="text-align:center;margin-top:40px;">School Leadership</h3>
        <div class="staff-grid">
            <?php foreach ($leaders as $s): ?>
            <div class="staff-card">
                <img src="<?= htmlspecialchars($s['photo'] ?: 'assets/images/images.jpg') ?>" alt="<?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>">
                <div class="staff-name"><?= htmlspecialchars($s['title'] . ' ' . $s['first_name'] . ' ' . $s['last_name']) ?></div>
                <div class="staff-role"><?= htmlspecialchars($s['role']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="btn-center"><a href="staff.php" class="btn">Meet The Full Team</a></div>
        <?php endif; ?>

        <?php if ($testimonials): ?>
        <h2 class="section-title" style="margin-top:60px;">What Our Parents Say</h2>
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
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
