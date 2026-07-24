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
/* ============================================
   ADMISSIONS PAGE SPECIFIC STYLES (Edugrade UI)
   ============================================ */

:root {
    --primary-red: #e91e63;
    --primary-dark-red: #c2185b;
    --navy-dark: #1a1a2e;
    --navy-light: #2a2a4a;
    --light-gray-bg: #f8f9fa;
    --white: #ffffff;
    --text-gray: #666666;
    --shadow-card: 0 4px 20px rgba(0,0,0,0.06);
    --shadow-hover: 0 8px 30px rgba(233, 30, 99, 0.12);
}

/* --- Hero Section --- */
.admissions-hero {
    background: linear-gradient(135deg, var(--navy-dark) 0%, #2d2d54 100%);
    position: relative;
    color: var(--white);
    padding: 100px 0;
    text-align: center;
    overflow: hidden;
}

.admissions-hero .container {
    position: relative;
    z-index: 1;
}

.admissions-hero h1 {
    color: var(--white);
    font-size: 3.2rem;
    font-weight: 700;
    letter-spacing: -1px;
    margin-bottom: 15px;
}

.admissions-hero h1 i {
    color: var(--primary-red);
    margin-right: 10px;
}

.admissions-hero p {
    color: rgba(255,255,255,0.85);
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.15rem;
    line-height: 1.7;
}

.admissions-hero .badge {
    display: inline-block;
    background: rgba(233, 30, 99, 0.2);
    color: var(--primary-red);
    padding: 8px 24px;
    border-radius: 50px;
    font-size: 0.9rem;
    margin-top: 20px;
    border: 1px solid rgba(233, 30, 99, 0.3);
}
.admissions-hero .badge i {
    margin-right: 6px;
}

/* --- Section Wrappers --- */
.admissions-section {
    padding: 80px 0;
}
.admissions-section:nth-child(even) {
    background: var(--light-gray-bg);
}

/* --- Section Titles (Left Aligned as per UI) --- */
.admissions-section .section-title {
    text-align: left;
    margin-bottom: 40px;
}
.admissions-section .section-title h2 {
    display: inline-block;
    position: relative;
    padding-bottom: 12px;
    margin-bottom: 10px;
    color: var(--navy-dark);
}
.admissions-section .section-title h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-red);
}
.admissions-section .section-title p {
    color: var(--text-gray);
    max-width: 600px;
    margin-top: 10px;
}

/* --- Requirements Grid --- */
.requirements-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}
.requirements-list {
    background: var(--white);
    border-radius: 8px;
    padding: 35px;
    box-shadow: var(--shadow-card);
    border-top: 4px solid var(--primary-red);
    transition: var(--transition);
}
.requirements-list:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}
.requirements-list h3 {
    color: var(--navy-dark);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--light-gray-bg);
    display: flex;
    align-items: center;
    gap: 10px;
}
.requirements-list h3 i {
    color: var(--primary-red);
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
    color: var(--navy-dark);
    font-size: 0.95rem;
}
.requirements-list .req-item .desc {
    color: var(--text-gray);
    font-size: 0.9rem;
    margin-top: 4px;
    line-height: 1.5;
}

/* --- Documents Grid --- */
.documents-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}
.doc-card {
    background: var(--white);
    padding: 30px 25px;
    border-radius: 8px;
    box-shadow: var(--shadow-card);
    transition: var(--transition);
    text-align: center;
    border: 1px solid transparent;
}
.doc-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary-red);
}
.doc-card i {
    font-size: 2.8rem;
    color: var(--primary-red);
    margin-bottom: 12px;
}
.doc-card h4 {
    color: var(--navy-dark);
    margin-bottom: 5px;
    font-size: 1.1rem;
}
.doc-card p {
    color: var(--text-gray);
    font-size: 0.9rem;
    line-height: 1.5;
}
.doc-card .btn-download {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 24px;
    background: var(--navy-dark);
    color: var(--white);
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: var(--transition);
}
.doc-card .btn-download:hover {
    background: var(--primary-red);
    color: var(--white);
    transform: translateY(-2px);
}

/* --- Enquiry Form (Modern UI) --- */
.enquiry-form {
    max-width: 800px;
    margin: 0 auto;
    background: var(--white);
    padding: 45px;
    border-radius: 8px;
    box-shadow: var(--shadow-card);
}
.enquiry-form .form-group {
    margin-bottom: 22px;
}
.enquiry-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--navy-dark);
    font-size: 0.9rem;
}
.enquiry-form label .required {
    color: var(--primary-red);
}
.enquiry-form input,
.enquiry-form select,
.enquiry-form textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s;
    font-family: inherit;
    background: var(--light-gray-bg);
    color: var(--navy-dark);
}
.enquiry-form input:focus,
.enquiry-form select:focus,
.enquiry-form textarea:focus {
    outline: none;
    border-color: var(--primary-red);
    background: var(--white);
}
.enquiry-form textarea {
    min-height: 120px;
    resize: vertical;
}
.enquiry-form .btn-submit {
    width: 100%;
    padding: 16px;
    background: var(--primary-red);
    color: var(--white);
    border: none;
    border-radius: 6px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
}
.enquiry-form .btn-submit:hover {
    background: var(--primary-dark-red);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(233, 30, 99, 0.4);
}
.enquiry-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

/* --- Alerts --- */
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 18px 25px;
    border-radius: 6px;
    margin-bottom: 30px;
    border-left: 4px solid #28a745;
    font-weight: 500;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    padding: 18px 25px;
    border-radius: 6px;
    margin-bottom: 30px;
    border-left: 4px solid #dc3545;
    font-weight: 500;
}

/* --- Responsive --- */
@media (max-width: 992px) {
    .requirements-grid { grid-template-columns: 1fr; }
    .documents-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .admissions-hero { padding: 60px 0; }
    .admissions-hero h1 { font-size: 2.2rem; }
    .admissions-hero p { font-size: 1rem; }
    .enquiry-form { padding: 25px; }
    .enquiry-form .form-row { grid-template-columns: 1fr; gap: 0; }
}

@media (max-width: 576px) {
    .admissions-hero h1 { font-size: 1.8rem; }
}
</style>

<!-- Hero -->
<section class="admissions-hero">
    <div class="container">
        <h1><i class="fas fa-graduation-cap"></i> Admissions</h1>
        <p>Join <?= clean(getSetting($pdo, 'school_name', 'our school')) ?> - Where excellence meets opportunity</p>
        <div class="badge">
            <i class="fas fa-calendar-alt"></i> Applications Open for <?= date('Y') . '/' . (date('Y') + 1) ?>
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
<section class="admissions-section" style="background:var(--light-gray-bg);">
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
                        <p style="font-size:0.8rem;color:#999;margin-top:5px;"><?= clean($doc['file_size']) ?></p>
                    <?php endif; ?>
                    <a href="<?= clean($doc['filename']) ?>" class="btn-download" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                    <p style="font-size:0.75rem;color:#adb5bd;margin-top:8px;">
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