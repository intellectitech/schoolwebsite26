<?php
require_once 'includes/functions.php';
$pageId = 'contact';
$pageTitle = getSetting($pdo, 'school_name') . ' — Contact Us';

$schoolAddress = getSetting($pdo, 'school_address');
$schoolPhone   = getSetting($pdo, 'school_phone');
$schoolEmail   = getSetting($pdo, 'school_email');
$officeHours   = getSetting($pdo, 'office_hours');

$flash = getFlash();

// FAQs relevant to general enquiries
$faqStmt = $pdo->prepare("SELECT * FROM faqs WHERE category = 'general' AND is_published = 1 ORDER BY sort_order ASC");
$faqStmt->execute();
$faqs = $faqStmt->fetchAll();

require_once 'includes/header.php';
?>
<section id="contact" class="page-section active">
    <div class="container">
        <h2 class="section-title">Contact Us</h2>
        <div class="three-col">
            <img src="assets/images/image10.jpg" alt="Contact Us">
            <div>
                <h3 class="sub-title">Get In Touch</h3>
                <p class="contact-item"><strong>Address:</strong> <?= htmlspecialchars($schoolAddress) ?></p>
                <p class="contact-item"><strong>Phone:</strong> <?= htmlspecialchars($schoolPhone) ?></p>
                <p class="contact-item"><strong>Email:</strong> <?= htmlspecialchars($schoolEmail) ?></p>
                <p class="contact-item"><strong>Office Hours:</strong> <?= htmlspecialchars($officeHours) ?></p>
            </div>
            <form class="contact-form" method="POST" action="process_contact.php">
                <?php if ($flash['success']): ?>
                    <p class="success"><?= htmlspecialchars($flash['success']) ?></p>
                <?php elseif ($flash['errors']): ?>
                    <div class="error-list"><ul>
                        <?php foreach ($flash['errors'] as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
                    </ul></div>
                <?php endif; ?>

                <!-- Honeypot: hidden from real visitors, only bots fill this in -->
                <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">

                <input type="text" name="name" placeholder="Your Name" required value="<?= old($flash['old'], 'name') ?>">
                <input type="email" name="email" placeholder="Your Email" required value="<?= old($flash['old'], 'email') ?>">
                <input type="text" name="phone" placeholder="Your Phone (optional)" value="<?= old($flash['old'], 'phone') ?>">
                <input type="text" name="subject" placeholder="Subject" required value="<?= old($flash['old'], 'subject') ?>">
                <textarea name="message" rows="5" placeholder="Your Message" required><?= old($flash['old'], 'message') ?></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>

        <?php if ($faqs): ?>
        <h2 class="section-title" style="margin-top:60px;">Frequently Asked Questions</h2>
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
