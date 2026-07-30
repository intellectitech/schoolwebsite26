<?php
require_once 'includes/functions.php';
$pageId = 'admissions';
$pageTitle = getSetting($pdo, 'school_name') . ' — Admissions';

$flash = getFlash();

$welcomeNote = $pdo->prepare("SELECT content FROM page_content WHERE page = 'admissions' AND section = 'welcome_note' LIMIT 1");
$welcomeNote->execute();
$welcomeNote = $welcomeNote->fetchColumn();

$requirements = $pdo->query("SELECT * FROM admission_requirements ORDER BY sort_order ASC")->fetchAll();
$documents    = $pdo->query("SELECT * FROM admission_documents WHERE is_active = 1")->fetchAll();

$faqStmt = $pdo->prepare("SELECT * FROM faqs WHERE category = 'admissions' AND is_published = 1 ORDER BY sort_order ASC");
$faqStmt->execute();
$faqs = $faqStmt->fetchAll();

$entryLevels = ['Baby Class','Middle Class','Top Class','P1','P2','P3','P4','P5','P6','P7'];

require_once 'includes/header.php';
?>
<section id="admissions" class="page-section active">
    <div class="container">
        <h2 class="section-title">Admissions</h2>
        <?php if ($welcomeNote): ?>
            <p class="intro-text" style="text-align:center;max-width:800px;margin:-10px auto 40px;"><?= htmlspecialchars($welcomeNote) ?></p>
        <?php endif; ?>

        <div class="two-col">
            <img src="assets/images/image13.jpg" alt="Admissions">
            <div>
                <h3 class="sub-title">How to Apply</h3>
                <ol class="list">
                    <li>Submit an enquiry using the form below, or visit our school office</li>
                    <li>Collect and complete the application form with all required information</li>
                    <li>Submit the form along with the required documents listed below</li>
                    <li>Attend the admission assessment/interview</li>
                    <li>Receive your admission decision from our admissions office</li>
                </ol>

                <?php if ($requirements): ?>
                <h3 class="sub-title">Required Documents</h3>
                <ul class="list">
                    <?php foreach ($requirements as $r): ?>
                        <li><strong><?= htmlspecialchars($r['title']) ?>:</strong> <?= htmlspecialchars($r['description']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($documents): ?>
        <h3 class="sub-title" style="text-align:center;margin-top:50px;">Downloadable Resources</h3>
        <ul class="doc-list" style="max-width:700px;margin:20px auto 0;">
            <?php foreach ($documents as $d): ?>
            <li class="doc-item">
                <div>
                    <div class="doc-title"><?= htmlspecialchars($d['title']) ?></div>
                    <div class="doc-meta"><?= htmlspecialchars($d['description']) ?></div>
                </div>
                <span class="doc-meta"><?= htmlspecialchars($d['file_size']) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <p class="note">Ask our front office for a printed or emailed copy of any of the documents above.</p>
        <?php endif; ?>

        <h3 class="sub-title" style="text-align:center;margin-top:60px;">Send Us An Admissions Enquiry</h3>
        <p class="intro-text" style="text-align:center;">Fill in the form below and our admissions office will get back to you.</p>
        <form class="contact-form enquiry-form" method="POST" action="process_admissions.php" style="margin:0 auto;">
            <?php if ($flash['success']): ?>
                <p class="success"><?= htmlspecialchars($flash['success']) ?></p>
            <?php elseif ($flash['errors']): ?>
                <div class="error-list"><ul>
                    <?php foreach ($flash['errors'] as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
                </ul></div>
            <?php endif; ?>

            <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">

            <label class="field-label" for="parent_name">Parent / Guardian Name</label>
            <input type="text" id="parent_name" name="parent_name" placeholder="Your full name" required value="<?= old($flash['old'], 'parent_name') ?>">

            <label class="field-label" for="parent_phone">Phone Number</label>
            <input type="text" id="parent_phone" name="parent_phone" placeholder="e.g. 0772 345 678" required value="<?= old($flash['old'], 'parent_phone') ?>">

            <label class="field-label" for="parent_email">Email Address</label>
            <input type="email" id="parent_email" name="parent_email" placeholder="you@example.com" required value="<?= old($flash['old'], 'parent_email') ?>">

            <label class="field-label" for="student_name">Child's Name</label>
            <input type="text" id="student_name" name="student_name" placeholder="Child's full name" required value="<?= old($flash['old'], 'student_name') ?>">

            <label class="field-label" for="entry_level">Class Applying For</label>
            <select id="entry_level" name="entry_level" required>
                <option value="">Select a class...</option>
                <?php $oldLevel = $flash['old']['entry_level'] ?? ''; ?>
                <?php foreach ($entryLevels as $lvl): ?>
                    <option value="<?= htmlspecialchars($lvl) ?>" <?= $oldLevel === $lvl ? 'selected' : '' ?>><?= htmlspecialchars($lvl) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="field-label" for="current_school">Current School (if transferring)</label>
            <input type="text" id="current_school" name="current_school" placeholder="Optional" value="<?= old($flash['old'], 'current_school') ?>">

            <label class="field-label" for="enq_message">Message</label>
            <textarea id="enq_message" name="message" rows="4" placeholder="Anything else we should know?"><?= old($flash['old'], 'message') ?></textarea>

            <button type="submit">Submit Enquiry</button>
        </form>

        <?php if ($faqs): ?>
        <h2 class="section-title" style="margin-top:60px;">Admissions FAQs</h2>
        <div class="faq-list">
            <?php foreach ($faqs as $f): ?>
            <div class="faq-item">
                <button type="button" class="faq-question">
                    <span><?= htmlspecialchars($f['question']) ?></span>
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer"><p><?= htmlspecialchars($f['answer']) ?></p></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
