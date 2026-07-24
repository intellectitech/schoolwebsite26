<?php
// staff.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Staff Directory - ' . getSetting($pdo, 'school_name', 'School');

// Get filter
$deptFilter = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$deptCondition = $deptFilter ? "AND s.department_id = $deptFilter" : "";

// Fetch all staff
$stmt = $pdo->prepare("
    SELECT s.*, d.name as department_name
    FROM staff s
    LEFT JOIN departments d ON d.id = s.department_id
    WHERE s.is_active = 1 $deptCondition
    ORDER BY s.is_management DESC, s.sort_order, s.last_name
");
$stmt->execute();
$staff = $stmt->fetchAll();

// Fetch departments for filter
$deptsStmt = $pdo->query("SELECT * FROM departments ORDER BY name");
$departments = $deptsStmt->fetchAll();

include 'includes/header.php';
?>

<style>
/* ============================================
   STAFF PAGE SPECIFIC STYLES (Edugrade UI)
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
.staff-hero {
    background: linear-gradient(135deg, var(--navy-dark) 0%, #2d2d54 100%);
    position: relative;
    color: var(--white);
    padding: 80px 0 60px;
    text-align: center;
    overflow: hidden;
}

.staff-hero .container {
    position: relative;
    z-index: 1;
}

.staff-hero h1 {
    color: var(--white);
    font-size: 3.2rem;
    font-weight: 700;
    letter-spacing: -1px;
    margin-bottom: 15px;
}

.staff-hero h1 i {
    color: var(--primary-red);
    margin-right: 12px;
}

.staff-hero p {
    color: rgba(255,255,255,0.85);
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.15rem;
    line-height: 1.7;
}

/* --- Section Wrappers --- */
.staff-section {
    padding: 40px 0 80px;
    background: var(--light-gray-bg);
}

/* --- Section Title (Left Aligned) --- */
.staff-section .section-title {
    text-align: left;
    margin-bottom: 40px;
}
.staff-section .section-title h2 {
    display: inline-block;
    position: relative;
    padding-bottom: 12px;
    color: var(--navy-dark);
}
.staff-section .section-title h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-red);
}
.staff-section .section-title p {
    color: var(--text-gray);
    max-width: 500px;
    margin-top: 10px;
}

/* --- Department Filters --- */
.staff-filters {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin: 40px 0 30px;
    justify-content: flex-start;
}
.staff-filters a {
    padding: 10px 24px;
    border-radius: 50px;
    background: var(--white);
    color: var(--navy-dark);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 500;
    box-shadow: var(--shadow-card);
}
.staff-filters a:hover {
    background: var(--light-gray-bg);
    transform: translateY(-2px);
}
.staff-filters a.active {
    background: var(--primary-red);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
}
.staff-filters a.active:hover {
    background: var(--primary-dark-red);
}

/* --- Staff Grid --- */
.staff-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.staff-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: all 0.3s ease;
    text-align: center;
    border: 1px solid transparent;
}

.staff-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary-red);
}

.staff-card .photo {
    width: 100%;
    height: 260px;
    object-fit: cover;
    background: #e9ecef;
}

.staff-card .photo-placeholder {
    width: 100%;
    height: 260px;
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 4rem;
}

.staff-card .info {
    padding: 25px 20px 30px;
    position: relative;
}

.staff-card .info .management-badge {
    display: inline-block;
    background: var(--primary-red);
    color: var(--white);
    padding: 4px 16px;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.staff-card .info h4 {
    color: var(--navy-dark);
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.staff-card .info .role {
    color: var(--primary-red);
    font-weight: 600;
    font-size: 0.9rem;
}

.staff-card .info .department {
    color: var(--text-gray);
    font-size: 0.85rem;
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.staff-card .info .department i {
    color: var(--primary-red);
}

.staff-card .info .subjects {
    color: var(--text-gray);
    font-size: 0.85rem;
    margin-top: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.staff-card .info .subjects i {
    color: var(--primary-red);
}

.staff-card .info .qualification {
    color: var(--text-gray);
    font-size: 0.85rem;
    margin-top: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.staff-card .info .qualification i {
    color: var(--primary-red);
}

/* --- Empty State --- */
.empty-state {
    text-align: center;
    padding: 60px 0;
    background: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow-card);
}
.empty-state i {
    font-size: 4rem;
    color: #ced4da;
    margin-bottom: 20px;
}
.empty-state h3 {
    color: var(--text-gray);
}

/* --- Responsive --- */
@media (max-width: 1200px) {
    .staff-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 992px) {
    .staff-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .staff-hero { padding: 60px 0; }
    .staff-hero h1 { font-size: 2.2rem; }
    .staff-hero p { font-size: 1rem; }
    .staff-filters { justify-content: center; }
}

@media (max-width: 576px) {
    .staff-grid { grid-template-columns: 1fr; }
    .staff-hero h1 { font-size: 1.8rem; }
    .staff-filters a { padding: 8px 18px; font-size: 0.8rem; }
}
</style>

<!-- Hero -->
<section class="staff-hero">
    <div class="container">
        <h1><i class="fas fa-users"></i> Staff Directory</h1>
        <p>Meet our dedicated team of educators and professionals</p>
    </div>
</section>

<!-- Filters + Section Title -->
<section class="staff-section" style="padding-bottom:0; background:var(--light-gray-bg);">
    <div class="container">
        <div class="section-title">
            <h2>Our Team</h2>
            <p>Browse through our talented faculty and administrative staff by department.</p>
        </div>
        
        <div class="staff-filters">
            <a href="staff.php" class="<?= $deptFilter == 0 ? 'active' : '' ?>">All Departments</a>
            <?php foreach ($departments as $dept): ?>
                <a href="staff.php?department=<?= $dept['id'] ?>" class="<?= $deptFilter == $dept['id'] ? 'active' : '' ?>">
                    <?= clean($dept['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Staff Grid -->
<section class="staff-section" style="padding-top:20px;">
    <div class="container">
        <?php if (!empty($staff)): ?>
            <div class="staff-grid">
                <?php foreach ($staff as $member): ?>
                    <div class="staff-card">
                        <?php if (!empty($member['photo'])): ?>
                            <img src="<?= clean($member['photo']) ?>" alt="<?= clean($member['first_name']) ?>" class="photo" loading="lazy">
                        <?php else: ?>
                            <div class="photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <div class="info">
                            <?php if ($member['is_management']): ?>
                                <div class="management-badge"><i class="fas fa-star"></i> Leadership</div>
                            <?php endif; ?>
                            <h4><?= clean($member['title'] ?? '') ?> <?= clean($member['first_name']) ?> <?= clean($member['last_name']) ?></h4>
                            <div class="role"><?= clean($member['role']) ?></div>
                            <?php if (!empty($member['department_name'])): ?>
                                <div class="department"><i class="fas fa-building"></i> <?= clean($member['department_name']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($member['subjects'])): ?>
                                <div class="subjects"><i class="fas fa-book"></i> <?= clean($member['subjects']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($member['qualification'])): ?>
                                <div class="qualification"><i class="fas fa-graduation-cap"></i> <?= clean($member['qualification']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No Staff Members Found</h3>
                <p style="color:#666;margin-top:5px;">Staff directory is being updated.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>