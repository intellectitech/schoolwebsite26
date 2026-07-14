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
.staff-hero {
    background: linear-gradient(135deg, #0d2617, #1a4d2e);
    color: #fff;
    padding: 60px 0;
    text-align: center;
}
.staff-hero h1 {
    color: #fff;
    font-size: 2.8rem;
}
.staff-hero p {
    color: rgba(255,255,255,0.8);
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
    background: #e9ecef;
    color: #333;
    transition: all 0.3s;
    font-size: 0.9rem;
}
.staff-filters a:hover,
.staff-filters a.active {
    background: #1a4d2e;
    color: #fff;
}
.staff-section {
    padding: 40px 0 80px;
}
.staff-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}
.staff-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s;
    text-align: center;
}
.staff-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}
.staff-card .photo {
    width: 100%;
    height: 240px;
    object-fit: cover;
    background: #e9ecef;
}
.staff-card .photo-placeholder {
    width: 100%;
    height: 240px;
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
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
    color: #1a4d2e;
    padding: 2px 12px;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 8px;
}
.staff-card .info h4 {
    color: #1a4d2e;
    margin-bottom: 4px;
}
.staff-card .info .role {
    color: #FFD700;
    font-weight: 600;
    font-size: 0.9rem;
}
.staff-card .info .department {
    color: #666;
    font-size: 0.85rem;
    margin-top: 5px;
}
.staff-card .info .subjects {
    color: #888;
    font-size: 0.85rem;
    margin-top: 5px;
}
.staff-card .info .qualification {
    color: #888;
    font-size: 0.85rem;
    margin-top: 5px;
}
@media (max-width: 1200px) {
    .staff-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 992px) {
    .staff-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .staff-grid {
        grid-template-columns: 1fr;
    }
    .staff-hero h1 {
        font-size: 2rem;
    }
}
</style>

<!-- Hero -->
<section class="staff-hero">
    <div class="container">
        <h1><i class="fas fa-users"></i> Staff Directory</h1>
        <p>Meet our dedicated team of educators and professionals</p>
    </div>
</section>

<!-- Filters -->
<section style="padding-top:0;">
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
            <div style="text-align:center;padding:60px 0;">
                <i class="fas fa-users" style="font-size:4rem;color:#ccc;margin-bottom:20px;"></i>
                <h3>No Staff Members Found</h3>
                <p style="color:#666;">Staff directory is being updated.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>