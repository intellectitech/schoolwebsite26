<?php
require_once 'includes/functions.php';
$pageId = 'fees';
$pageTitle = getSetting($pdo, 'school_name') . ' — School Fees';

$faqStmt = $pdo->prepare("SELECT * FROM faqs WHERE category = 'fees' AND is_published = 1 ORDER BY sort_order ASC");
$faqStmt->execute();
$faqs = $faqStmt->fetchAll();

require_once 'includes/header.php';
?>
<section id="fees" class="page-section active">
    <div class="container">
        <h2 class="section-title">School Fees</h2>
        <div class="two-col">
            <img src="assets/images/image9.jpg" alt="School Fees">
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Class/Level</th>
                            <th>Term 1</th>
                            <th>Term 2</th>
                            <th>Term 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Nursery</td><td>UGX 300,000</td><td>UGX 300,000</td><td>UGX 300,000</td></tr>
                        <tr><td>Primary 1-3</td><td>UGX 400,000</td><td>UGX 400,000</td><td>UGX 400,000</td></tr>
                        <tr><td>Primary 4-7</td><td>UGX 500,000</td><td>UGX 500,000</td><td>UGX 500,000</td></tr>
                    </tbody>
                </table>
                <p class="note">Note: Fees are subject to change. Contact us for the latest information.</p>
            </div>
        </div>

        <?php if ($faqs): ?>
        <h2 class="section-title" style="margin-top:60px;">Fees FAQs</h2>
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
