<?php
// staff.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Staff Directory - ' . getSetting($pdo, 'school_name', 'School');

$deptFilter = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$deptCondition = $deptFilter ? "AND s.department_id = $deptFilter" : "";

$stmt = $pdo->prepare("
    SELECT s.*, d.name as department_name
    FROM staff s
    LEFT JOIN departments d ON d.id = s.department_id
    WHERE s.is_active = 1 $deptCondition
    ORDER BY s.is_management DESC, s.sort_order, s.last_name
");
$stmt->execute();
$staff = $stmt->fetchAll();

$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<style>
.staff-hero {
    background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
    color: #fff;
    padding: 60px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.staff-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 60%;
    height: 150%;
    background: radial-gradient(ellipse, rgba(0, 200, 83, 0.08), transparent 70%);
    animation: heroGlow 8s ease-in-out infinite alternate;
}
.staff-hero h1 {
    color: #fff;
    font-size: 2.8rem;
}
.staff-hero h1 .highlight {
    color: #00C853;
}
.staff-hero p {
    color: rgba(255,255,255,0.7);
    max-width: 600px;
    margin: 15px auto 0;
}

.staff-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin: 30px 0;
    justify-content: center;
}
.staff-filters a {
    padding: 8px 20px;
    border-radius: 50px;
    background: #fff;
    color: #333;
    transition: all 0.3s;
    font-size: 0.9rem;
    text-decoration: none;
    border: 1px solid #e0e0e0;
}
.staff-filters a:hover,
.staff-filters a.active {
    background: #00C853;
    color: #fff;
    border-color: #00C853;
}

.staff-section {
    padding: 40px 0 80px;
    background: #f5e6d3;
}
.staff-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}
.staff-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 30px rgba(0,0,0,0.08);
    transition: all 0.3s;
    text-align: center;
}
.staff-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 50px rgba(0,0,0,0.12);
}
.staff-card .photo {
    width: 100%;
    height: 240px;
    object-fit: cover;
    background: #e0e0e0;
}
.staff-card .photo-placeholder {
    width: 100%;
    height: 240px;
    background: linear-gradient(135deg, #e0e0e0, #ccc);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 4rem;
}
.staff-card .info {
    padding: 20px;
}
.staff-card .info .management-badge {
    display: inline-block;
    background: #FFD700;
    color: #0a0a0a;
    padding: 2px 12px;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 8px;
}
.staff-card .info h4 {
    color: #0a0a0a;
    margin-bottom: 4px;
}
.staff-card .info .role {
    color: #00C853;
    font-weight: 600;
    font-size: 0.9rem;
}
.staff-card .info .department {
    color: #8D6E63;
    font-size: 0.85rem;
    margin-top: 5px;
}
.staff-card .info .subjects {
    color: #999;
    font-size: 0.85rem;
    margin-top: 5px;
}
.staff-card .info .qualification {
    color: #999;
    font-size: 0.85rem;
    margin-top: 5px;
}

.no-staff {
    text-align: center;
    padding: 60px 0;
    color: #8D6E63;
}
.no-staff i {
    font-size: 3rem;
    color: #ccc;
    margin-bottom: 15px;
}

@media (max-width: 1200px) {
    .staff-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 992px) {
    .staff-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
    .staff-grid { grid-template-columns: 1fr; }
    .staff-hero h1 { font-size: 2rem; }
}
</style>

<!-- Hero -->
<section class="staff-hero">
    <div class="container">
        <h1><i class="fas fa-users"></i> Staff <span class="highlight">Directory</span></h1>
        <p>Meet our dedicated team of educators and professionals</p>
    </div>
</section>

<!-- Filters -->
<section style="padding-top:0;background:#f5e6d3;">
    <div class="container">
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
<section class="staff-section">
    <div class="container">
        <?php if (!empty($staff)): ?>
            <div class="staff-grid">
                <?php foreach ($staff as $member): ?>
                    <div class="staff-card">
                        <?php if (!empty($member['photo'])): ?>
                            <img src="<?= clean($member['photo']) ?>" alt="<?= clean($member['first_name']) ?>" class="photo">
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
            <div class="no-staff">
                <i class="fas fa-users"></i>
                <h3>No Staff Members Found</h3>
                <p>Staff directory is being updated.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>