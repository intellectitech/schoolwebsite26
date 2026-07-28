<?php
// about.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'About Us - ' . getSetting($pdo, 'school_name', 'School');

// Fetch leadership team (staff with is_management = 1)
$leadershipStmt = $pdo->query("
    SELECT * FROM staff 
    WHERE is_management = 1 AND is_active = 1 
    ORDER BY sort_order
");
$leadership = $leadershipStmt->fetchAll();

// Fetch staff count by department for stats
$deptStats = $pdo->query("
    SELECT d.name, COUNT(s.id) as count 
    FROM departments d
    LEFT JOIN staff s ON s.department_id = d.id AND s.is_active = 1
    GROUP BY d.id
    ORDER BY d.name
")->fetchAll();

include 'includes/header.php';
?>

<style>
.about-hero {
    background: linear-gradient(135deg, #0d2617, #1a4d2e);
    color: #fff;
    padding: 80px 0;
    text-align: center;
}
.about-hero h1 {
    color: #fff;
    font-size: 3rem;
}
.about-hero p {
    color: rgba(255,255,255,0.8);
    max-width: 700px;
    margin: 20px auto 0;
}
.about-section {
    padding: 80px 0;
}
.about-section:nth-child(even) {
    background: #f8f9fa;
}
.mission-vision-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}
.mission-box, .vision-box {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-top: 4px solid #FFD700;
}
.mission-box h3, .vision-box h3 {
    color: #1a4d2e;
    margin-bottom: 15px;
}
.mission-box i, .vision-box i {
    color: #FFD700;
    font-size: 2rem;
    margin-bottom: 15px;
}
.core-values {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 30px;
}
.core-value {
    text-align: center;
    padding: 30px 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}
.core-value:hover {
    transform: translateY(-5px);
}
.core-value .icon {
    width: 60px;
    height: 60px;
    background: #1a4d2e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: #FFD700;
    font-size: 1.5rem;
}
.core-value h4 {
    color: #1a4d2e;
    margin-bottom: 8px;
}
.core-value p {
    font-size: 0.9rem;
    color: #666;
}
.leadership-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 30px;
}
.leader-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s;
}
.leader-card:hover {
    transform: translateY(-5px);
}
.leader-card .photo {
    width: 100%;
    height: 250px;
    object-fit: cover;
    background: #e9ecef;
}
.leader-card .info {
    padding: 25px;
}
.leader-card .info h4 {
    color: #1a4d2e;
    margin-bottom: 5px;
}
.leader-card .info .role {
    color: #FFD700;
    font-weight: 600;
    font-size: 0.9rem;
}
.leader-card .info .subjects {
    color: #666;
    font-size: 0.85rem;
    margin-top: 5px;
}
@media (max-width: 992px) {
    .mission-vision-grid {
        grid-template-columns: 1fr;
    }
    .core-values {
        grid-template-columns: repeat(2, 1fr);
    }
    .leadership-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .core-values {
        grid-template-columns: 1fr;
    }
    .leadership-grid {
        grid-template-columns: 1fr;
    }
    .about-hero h1 {
        font-size: 2rem;
    }
}
</style>

<!-- Hero -->
<section class="about-hero">
    <div class="container">
        <h1>About <?= clean(getSetting($pdo, 'school_name', 'Our School')) ?></h1>
        <p>Discover our rich history, mission, and commitment to excellence in education since <?= clean(getSetting($pdo, 'founded_year', '1985')) ?>.</p>
    </div>
</section>

<!-- Mission & Vision -->
<section class="about-section">
    <div class="container">
        <div class="mission-vision-grid">
            <div class="mission-box">
                <i class="fas fa-bullseye"></i>
                <h3>Our Mission</h3>
                <p>To provide holistic, quality education that empowers students to become responsible, disciplined, and productive citizens who contribute positively to society.</p>
            </div>
            <div class="vision-box">
                <i class="fas fa-eye"></i>
                <h3>Our Vision</h3>
                <p>To be a center of academic excellence and moral integrity, producing well-rounded individuals who excel in their chosen fields and serve as leaders in their communities.</p>
            </div>
        </div>
    </div>
</section>

<!-- Core Values -->
<section class="about-section" style="background:#f8f9fa;">
    <div class="container">
        <div class="section-title">
            <h2>Our Core Values</h2>
            <p>The principles that guide everything we do</p>
        </div>
        <div class="core-values">
            <div class="core-value">
                <div class="icon"><i class="fas fa-star"></i></div>
                <h4>Excellence</h4>
                <p>Striving for the highest standards in academics, sports, and character development.</p>
            </div>
            <div class="core-value">
                <div class="icon"><i class="fas fa-handshake"></i></div>
                <h4>Integrity</h4>
                <p>Building character through honesty, transparency, and ethical behavior.</p>
            </div>
            <div class="core-value">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h4>Community</h4>
                <p>Fostering a supportive environment where every student belongs and thrives.</p>
            </div>
            <div class="core-value">
                <div class="icon"><i class="fas fa-lightbulb"></i></div>
                <h4>Innovation</h4>
                <p>Embracing modern teaching methods and technology to enhance learning.</p>
            </div>
        </div>
    </div>
</section>

<!-- Leadership Team -->
<?php if (!empty($leadership)): ?>
<section class="about-section">
    <div class="container">
        <div class="section-title">
            <h2>Leadership Team</h2>
            <p>Meet the dedicated leaders guiding our school</p>
        </div>
        <div class="leadership-grid">
            <?php foreach ($leadership as $leader): ?>
                <div class="leader-card">
                    <?php if (!empty($leader['photo'])): ?>
                        <img src="<?= clean($leader['photo']) ?>" alt="<?= clean($leader['first_name']) ?>" class="photo">
                    <?php else: ?>
                        <div class="photo" style="display:flex;align-items:center;justify-content:center;background:#e9ecef;color:#999;font-size:3rem;">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <div class="info">
                        <h4><?= clean($leader['title'] ?? '') ?> <?= clean($leader['first_name']) ?> <?= clean($leader['last_name']) ?></h4>
                        <div class="role"><?= clean($leader['role']) ?></div>
                        <?php if (!empty($leader['qualification'])): ?>
                            <div class="subjects"><?= clean($leader['qualification']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- School Stats -->
<section class="about-section" style="background:#f8f9fa;">
    <div class="container">
        <div class="section-title">
            <h2>School at a Glance</h2>
            <p>Key facts and figures about our institution</p>
        </div>
        <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
            <div class="stat-item" style="text-align:center;background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <span class="stat-number" style="color:#1a4d2e;font-size:2.5rem;"><?= clean(getSetting($pdo, 'founded_year', '1985')) ?></span>
                <span class="stat-label" style="color:#666;text-transform:none;letter-spacing:0;">Year Founded</span>
            </div>
            <div class="stat-item" style="text-align:center;background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <span class="stat-number" style="color:#1a4d2e;font-size:2.5rem;"><?= clean(getSetting($pdo, 'total_students', '1,200')) ?>+</span>
                <span class="stat-label" style="color:#666;text-transform:none;letter-spacing:0;">Students</span>
            </div>
            <div class="stat-item" style="text-align:center;background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <span class="stat-number" style="color:#1a4d2e;font-size:2.5rem;"><?= clean(getSetting($pdo, 'total_teachers', '60')) ?>+</span>
                <span class="stat-label" style="color:#666;text-transform:none;letter-spacing:0;">Teachers</span>
            </div>
            <div class="stat-item" style="text-align:center;background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <span class="stat-number" style="color:#1a4d2e;font-size:2.5rem;"><?= clean(getSetting($pdo, 'pass_rate', '86')) ?>%</span>
                <span class="stat-label" style="color:#666;text-transform:none;letter-spacing:0;">UACE Pass Rate</span>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>