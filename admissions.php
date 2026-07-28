<?php
// admissions.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Admissions - ' . getSetting($pdo, 'school_name', 'School');

// Fetch requirements
$reqStmt = $pdo->query("
    SELECT * FROM admissions_requirements 
    ORDER BY level, sort_order
");
$requirements = $reqStmt->fetchAll();

// Fetch documents
$docStmt = $pdo->query("
    SELECT * FROM admissions_documents 
    WHERE is_active = 1
    ORDER BY sort_order
");
$documents = $docStmt->fetchAll();

// Handle enquiry submission
$enquirySuccess = false;
$enquiryError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enquiry'])) {
    $parent_name = clean($_POST['parent_name'] ?? '');
    $parent_phone = clean($_POST['parent_phone'] ?? '');
    $parent_email = clean($_POST['parent_email'] ?? '');
    $student_name = clean($_POST['student_name'] ?? '');
    $entry_level = clean($_POST['entry_level'] ?? '');
    $current_school = clean($_POST['current_school'] ?? '');
    $ple_aggregate = isset($_POST['ple_aggregate']) ? (int)$_POST['ple_aggregate'] : null;
    $message = clean($_POST['message'] ?? '');
    
    if (empty($parent_name) || empty($parent_phone) || empty($student_name) || empty($entry_level)) {
        $enquiryError = 'Please fill in all required fields.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO admissions_enquiries 
                (parent_name, parent_phone, parent_email, student_name, entry_level, 
                 current_school, ple_aggregate, message, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new')
            ");
            $stmt->execute([
                $parent_name, $parent_phone, $parent_email, $student_name,
                $entry_level, $current_school, $ple_aggregate, $message
            ]);
            $enquirySuccess = true;
        } catch (Exception $e) {
            $enquiryError = 'An error occurred. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<style>
.admissions-hero {
    background: linear-gradient(135deg, #0d2617, #1a4d2e);
    color: #fff;
    padding: 60px 0;
    text-align: center;
}
.admissions-hero h1 {
    color: #fff;
    font-size: 2.8rem;
}
.admissions-hero p {
    color: rgba(255,255,255,0.8);
    max-width: 600px;
    margin: 15px auto 0;
}
.admissions-section {
    padding: 60px 0;
}
.admissions-section:nth-child(even) {
    background: #f8f9fa;
}
.requirements-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}
.requirements-list {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.requirements-list h3 {
    color: #1a4d2e;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 3px solid #FFD700;
}
.requirements-list .req-item {
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}
.requirements-list .req-item:last-child {
    border-bottom: none;
}
.requirements-list .req-item .title {
    font-weight: 600;
    color: #1a4d2e;
}
.requirements-list .req-item .desc {
    color: #666;
    font-size: 0.9rem;
    margin-top: 3px;
}
.documents-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.doc-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s;
    text-align: center;
    border: 2px solid transparent;
}
.doc-card:hover {
    transform: translateY(-3px);
    border-color: #FFD700;
}
.doc-card i {
    font-size: 2.5rem;
    color: #1a4d2e;
    margin-bottom: 10px;
}
.doc-card h4 {
    color: #1a4d2e;
    margin-bottom: 5px;
}
.doc-card p {
    color: #666;
    font-size: 0.9rem;
}
.doc-card .btn-download {
    display: inline-block;
    margin-top: 12px;
    padding: 8px 20px;
    background: #1a4d2e;
    color: #fff;
    border-radius: 50px;
    font-size: 0.85rem;
    transition: all 0.3s;
}
.doc-card .btn-download:hover {
    background: #2d7a4a;
    color: #fff;
}
.enquiry-form {
    max-width: 700px;
    margin: 0 auto;
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.enquiry-form .form-group {
    margin-bottom: 20px;
}
.enquiry-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #333;
    font-size: 0.9rem;
}
.enquiry-form label .required {
    color: #dc3545;
}
.enquiry-form input,
.enquiry-form select,
.enquiry-form textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
    font-family: inherit;
}
.enquiry-form input:focus,
.enquiry-form select:focus,
.enquiry-form textarea:focus {
    outline: none;
    border-color: #1a4d2e;
}
.enquiry-form textarea {
    min-height: 120px;
    resize: vertical;
}
.enquiry-form .btn-submit {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #FFD700, #f5c842);
    color: #1a4d2e;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}
.enquiry-form .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255,215,0,0.4);
}
.enquiry-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}
@media (max-width: 992px) {
    .requirements-grid {
        grid-template-columns: 1fr;
    }
    .documents-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 768px) {
    .admissions-hero h1 {
        font-size: 2rem;
    }
    .enquiry-form {
        padding: 25px;
    }
    .enquiry-form .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Hero -->
<section class="admissions-hero">
    <div class="container">
        <h1><i class="fas fa-graduation-cap"></i> Admissions</h1>
        <p>Join <?= clean(getSetting($pdo, 'school_name', 'our school')) ?> - Where excellence meets opportunity</p>
        <div style="margin-top:20px;">
            <span style="background:rgba(255,215,0,0.2);color:#FFD700;padding:8px 20px;border-radius:50px;font-size:0.9rem;">
                <i class="fas fa-calendar-alt"></i> Applications Open for <?= date('Y') . '/' . (date('Y') + 1) ?>
            </span>
        </div>
    </div>
</section>

<!-- Requirements -->
<section class="admissions-section">
    <div class="container">
        <div class="section-title">
            <h2>Entry Requirements</h2>
            <p>What you need to join <?= clean(getSetting($pdo, 'school_name', 'our school')) ?></p>
        </div>
        <div class="requirements-grid">
            <?php
            $s1Reqs = array_filter($requirements, fn($r) => $r['level'] === 'S1');
            $s5Reqs = array_filter($requirements, fn($r) => $r['level'] === 'S5');
            ?>
            <div class="requirements-list">
                <h3><i class="fas fa-child"></i> S1 Entry</h3>
                <?php if (!empty($s1Reqs)): ?>
                    <?php foreach ($s1Reqs as $req): ?>
                        <div class="req-item">
                            <div class="title"><?= clean($req['title']) ?></div>
                            <div class="desc"><?= clean($req['description']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#999;">Requirements coming soon.</p>
                <?php endif; ?>
            </div>
            <div class="requirements-list">
                <h3><i class="fas fa-user-graduate"></i> S5 Entry</h3>
                <?php if (!empty($s5Reqs)): ?>
                    <?php foreach ($s5Reqs as $req): ?>
                        <div class="req-item">
                            <div class="title"><?= clean($req['title']) ?></div>
                            <div class="desc"><?= clean($req['description']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#999;">Requirements coming soon.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Documents -->
<?php if (!empty($documents)): ?>
<section class="admissions-section" style="background:#f8f9fa;">
    <div class="container">
        <div class="section-title">
            <h2>Downloadable Forms</h2>
            <p>Download and fill out the necessary application forms</p>
        </div>
        <div class="documents-grid">
            <?php foreach ($documents as $doc): ?>
                <div class="doc-card">
                    <i class="fas fa-file-pdf"></i>
                    <h4><?= clean($doc['title']) ?></h4>
                    <p><?= clean($doc['description'] ?? '') ?></p>
                    <?php if (!empty($doc['file_size'])): ?>
                        <p style="font-size:0.8rem;color:#999;"><?= clean($doc['file_size']) ?></p>
                    <?php endif; ?>
                    <a href="<?= clean($doc['filename']) ?>" class="btn-download" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                    <p style="font-size:0.75rem;color:#999;margin-top:8px;">
                        <?= number_format($doc['downloads'] ?? 0) ?> downloads
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Enquiry Form -->
<section class="admissions-section">
    <div class="container">
        <div class="section-title">
            <h2>Request More Information</h2>
            <p>Fill out the form below and our admissions team will get back to you</p>
        </div>

        <?php if ($enquirySuccess): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Thank you! Your enquiry has been submitted. We will contact you shortly.
            </div>
        <?php endif; ?>

        <?php if ($enquiryError): ?>
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= clean($enquiryError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="enquiry-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="parent_name">Parent/Guardian Name <span class="required">*</span></label>
                    <input type="text" id="parent_name" name="parent_name" required placeholder="Full name">
                </div>
                <div class="form-group">
                    <label for="parent_phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="parent_phone" name="parent_phone" required placeholder="e.g. 0700 123456">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="parent_email">Email Address</label>
                    <input type="email" id="parent_email" name="parent_email" placeholder="email@example.com">
                </div>
                <div class="form-group">
                    <label for="student_name">Student Name <span class="required">*</span></label>
                    <input type="text" id="student_name" name="student_name" required placeholder="Full name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="entry_level">Entry Level <span class="required">*</span></label>
                    <select id="entry_level" name="entry_level" required>
                        <option value="">Select Level</option>
                        <option value="S1">S1 (Senior One)</option>
                        <option value="S5">S5 (Senior Five)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="current_school">Current School</label>
                    <input type="text" id="current_school" name="current_school" placeholder="Current school name">
                </div>
            </div>
            <div class="form-group">
                <label for="ple_aggregate">PLE Aggregate (if applying for S1)</label>
                <input type="number" id="ple_aggregate" name="ple_aggregate" min="0" max="60" placeholder="e.g. 20">
            </div>
            <div class="form-group">
                <label for="message">Additional Message</label>
                <textarea id="message" name="message" placeholder="Any special requirements or questions..."></textarea>
            </div>
            <button type="submit" name="submit_enquiry" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Submit Enquiry
            </button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>