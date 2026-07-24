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
/* ============================================
   ABOUT PAGE SPECIFIC STYLES (Edugrade UI)
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
.about-hero {
    /* Replaces the old green gradient with the UI's Navy overlay */
    background: linear-gradient(135deg, var(--navy-dark) 0%, #2d2d54 100%);
    position: relative;
    color: var(--white);
    padding: 100px 0;
    text-align: center;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: url('assets/images/pattern-dots.png') center/cover no-repeat; /* Optional: Add texture */
    opacity: 0.05;
    z-index: 0;
}

.about-hero .container {
    position: relative;
    z-index: 1;
}

.about-hero h1 {
    color: var(--white);
    font-size: 3.2rem;
    font-weight: 700;
    letter-spacing: -1px;
    margin-bottom: 15px;
}

.about-hero h1 span {
    color: var(--primary-red);
}

.about-hero p {
    color: rgba(255,255,255,0.85);
    max-width: 650px;
    margin: 0 auto;
    font-size: 1.15rem;
    line-height: 1.7;
}

/* --- Section Wrappers --- */
.about-section {
    padding: 80px 0;
}
.about-section:nth-child(even) {
    background: var(--light-gray-bg);
}

/* --- Section Titles (Left Aligned as per UI) --- */
.about-section .section-title {
    text-align: left;
    margin-bottom: 40px;
}
.about-section .section-title h2 {
    display: inline-block;
    position: relative;
    padding-bottom: 12px;
    margin-bottom: 10px;
    color: var(--navy-dark);
}
.about-section .section-title h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-red);
}
.about-section .section-title p {
    color: var(--text-gray);
    max-width: 600px;
    margin-top: 10px;
}

/* --- Mission & Vision Grid --- */
.mission-vision-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}
.mission-box, .vision-box {
    background: var(--white);
    padding: 40px 35px;
    border-radius: 8px;
    box-shadow: var(--shadow-card);
    border-top: 4px solid var(--primary-red);
    transition: var(--transition);
}
.mission-box:hover, .vision-box:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}
.mission-box h3, .vision-box h3 {
    color: var(--navy-dark);
    margin-bottom: 15px;
    font-size: 1.4rem;
}
.mission-box i, .vision-box i {
    color: var(--primary-red);
    font-size: 2rem;
    margin-bottom: 15px;
}
.mission-box p, .vision-box p {
    color: var(--text-gray);
    line-height: 1.7;
}

/* --- Core Values --- */
.core-values {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
    margin-top: 10px;
}
.core-value {
    text-align: center;
    padding: 35px 20px;
    background: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow-card);
    transition: var(--transition);
}
.core-value:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
}
.core-value .icon {
    width: 65px;
    height: 65px;
    background: var(--navy-dark);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: var(--white);
    font-size: 1.6rem;
    transition: var(--transition);
}
.core-value:hover .icon {
    background: var(--primary-red);
}
.core-value h4 {
    color: var(--navy-dark);
    margin-bottom: 8px;
    font-size: 1.1rem;
}
.core-value p {
    font-size: 0.9rem;
    color: var(--text-gray);
    line-height: 1.5;
}

/* --- Leadership Team --- */
.leadership-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 10px;
}
.leader-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    text-align: center;
    transition: var(--transition);
}
.leader-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
}
.leader-card .photo {
    width: 100%;
    height: 260px;
    object-fit: cover;
    background: #e9ecef;
}
.leader-card .info {
    padding: 25px 20px 30px;
}
.leader-card .info h4 {
    color: var(--navy-dark);
    margin-bottom: 5px;
    font-size: 1.15rem;
}
.leader-card .info .role {
    color: var(--primary-red);
    font-weight: 600;
    font-size: 0.9rem;
}
.leader-card .info .subjects {
    color: var(--text-gray);
    font-size: 0.85rem;
    margin-top: 8px;
}

/* --- School Stats (UI Matched Stats Bar) --- */
.stats-wrapper {
    background: var(--navy-dark);
    padding: 50px 0;
    border-top: 4px solid var(--primary-red);
}
.stats-grid-about {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    text-align: center;
}
.stat-item-about {
    color: var(--white);
    border-right: 1px solid rgba(255,255,255,0.1);
    padding: 0 15px;
}
.stat-item-about:last-child {
    border-right: none;
}
.stat-number-about {
    font-size: 2.8rem;
    font-weight: 800;
    color: var(--white);
    display: block;
    margin-bottom: 5px;
}
.stat-label-about {
    font-size: 1rem;
    opacity: 0.8;
    font-weight: 300;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* --- Responsive --- */
@media (max-width: 992px) {
    .mission-vision-grid { grid-template-columns: 1fr; }
    .core-values { grid-template-columns: repeat(2, 1fr); }
    .leadership-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .about-hero { padding: 60px 0; }
    .about-hero h1 { font-size: 2.2rem; }
    .about-hero p { font-size: 1rem; }
    .stats-grid-about { grid-template-columns: repeat(2, 1fr); }
    .stat-item-about { border-right: none; margin-bottom: 20px; }
}

@media (max-width: 576px) {
    .core-values { grid-template-columns: 1fr; }
    .leadership-grid { grid-template-columns: 1fr; }
    .stats-grid-about { grid-template-columns: 1fr; }
    .about-hero h1 { font-size: 1.8rem; }
}
</style>

<!-- Hero -->
<section class="about-hero">
    <div class="container">
        <h1>About <span><?= clean(getSetting($pdo, 'school_name', 'Our School')) ?></span></h1>
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
                        <div class="photo" style="display:flex;align-items:center;justify-content:center;background:#e9ecef;color:#adb5bd;font-size:3rem;">
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

<!-- School Stats (Now using the Dark Navy Bar) -->
<section class="stats-wrapper">
    <div class="container">
        <div class="stats-grid-about">
            <div class="stat-item-about">
                <span class="stat-number-about"><?= clean(getSetting($pdo, 'founded_year', '1985')) ?></span>
                <span class="stat-label-about">Year Founded</span>
            </div>
            <div class="stat-item-about">
                <span class="stat-number-about"><?= clean(getSetting($pdo, 'total_students', '1,200')) ?>+</span>
                <span class="stat-label-about">Students</span>
            </div>
            <div class="stat-item-about">
                <span class="stat-number-about"><?= clean(getSetting($pdo, 'total_teachers', '60')) ?>+</span>
                <span class="stat-label-about">Teachers</span>
            </div>
            <div class="stat-item-about">
                <span class="stat-number-about"><?= clean(getSetting($pdo, 'pass_rate', '86')) ?>%</span>
                <span class="stat-label-about">UACE Pass Rate</span>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>