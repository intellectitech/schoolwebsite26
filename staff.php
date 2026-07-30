<?php
// ============================================================
//  staff.php — Staff Directory
//  Leadership team first, then teaching staff grouped by department.
// ============================================================
require_once 'includes/functions.php';
$pageId = 'staff';
$pageTitle = getSetting($pdo, 'school_name') . ' — Our Staff';

// Leadership / management first (is_managemnet = the schema's own
// column name — a spelling slip in the supplied dump, kept as-is).
$leaders = $pdo->query(
    "SELECT s.*, d.name AS department_name
     FROM staff s
     LEFT JOIN departments d ON d.id = s.department_id
     WHERE s.is_active = 1 AND s.is_managemnet = 1
     ORDER BY s.sort_order ASC"
)->fetchAll();

// Everyone else, grouped by department
$teachers = $pdo->query(
    "SELECT s.*, d.name AS department_name
     FROM staff s
     LEFT JOIN departments d ON d.id = s.department_id
     WHERE s.is_active = 1 AND s.is_managemnet = 0
     ORDER BY d.name ASC, s.sort_order ASC"
)->fetchAll();

$teachersByDept = [];
foreach ($teachers as $t) {
    $dept = $t['department_name'] ?: 'General';
    $teachersByDept[$dept][] = $t;
}

require_once 'includes/header.php';
?>
<section id="staff" class="page-section active">
    <div class="container">
        <h2 class="section-title">Our Staff</h2>
        <p class="intro-text" style="text-align:center;max-width:750px;margin:-10px auto 10px;">
            Meet the leadership team and teaching staff dedicated to every child's growth at NPPS.
        </p>

        <?php if ($leaders): ?>
        <h3 class="department-heading" style="text-align:center;">School Leadership</h3>
        <div class="staff-grid">
            <?php foreach ($leaders as $s): ?>
            <div class="staff-card">
                <img src="<?= htmlspecialchars($s['photo'] ?: 'assets/images/images.jpg') ?>" alt="<?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>">
                <div class="staff-name"><?= htmlspecialchars($s['title'] . ' ' . $s['first_name'] . ' ' . $s['last_name']) ?></div>
                <div class="staff-role"><?= htmlspecialchars($s['role']) ?></div>
                <div class="staff-qual"><?= htmlspecialchars($s['qualification']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php foreach ($teachersByDept as $deptName => $members): ?>
        <h3 class="department-heading" style="text-align:center;"><?= htmlspecialchars($deptName) ?></h3>
        <div class="staff-grid">
            <?php foreach ($members as $s): ?>
            <div class="staff-card">
                <img src="<?= htmlspecialchars($s['photo'] ?: 'assets/images/images.jpg') ?>" alt="<?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>">
                <div class="staff-name"><?= htmlspecialchars($s['title'] . ' ' . $s['first_name'] . ' ' . $s['last_name']) ?></div>
                <div class="staff-role"><?= htmlspecialchars($s['role']) ?></div>
                <div class="staff-qual"><?= htmlspecialchars($s['qualification']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <?php if (!$leaders && !$teachersByDept): ?>
            <p class="note">Staff profiles are being updated — please check back soon.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
