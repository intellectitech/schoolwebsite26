<?php
// about.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'About Us - ' . getSetting($pdo, 'school_name', 'School');

// Fetch leadership team
$leadershipStmt = $pdo->query("
    SELECT * FROM staff 
    WHERE is_management = 1 AND is_active = 1 
    ORDER BY sort_order
");
$leadership = $leadershipStmt->fetchAll();

include 'includes/header.php';
?>

<style>
.about-hero {
    background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
    color: #fff;
    padding: 80px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.about-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 60%;
    height: 150%;
    background: radial-gradient(ellipse, rgba(0, 200, 83, 0.08), transparent 70%);
    animation: heroGlow 8s ease-in-out infinite alternate;
}
.about-hero h1 {
    color: #fff;
    font-size: 3rem;
}
.about-hero h1 .highlight {
    color: #00C853;
}
.about-hero p {
    color: rgba(255,255,255,0.7);
    max-width: 700px;
    margin: 20px auto 0;
}

.about-section {
    padding: 80px 0;
}
.about-section:nth-child(even) {
    background: #f5e6d3;
}

.mission-vision-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}
.mission-box, .vision-box {
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 5px 30px rgba(0,0,0,0.08);
    border-top: 4px solid #00C853;
    transition: transform 0.3s;
}
.mission-box:hover, .vision-box:hover {
    transform: translateY(-5px);
}
.mission-box i, .vision-box i {
    color: #00C853;
    font-size: 2.5rem;
    margin-bottom: 15px;
}
.mission-box h3, .vision-box h3 {
    color: #0a0a0a;
    margin-bottom: 15px;
}
.mission-box p, .vision-box p {
    color: #8D6E63;
    line-height: 1.8;
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
    border-radius: 16px;
    box-shadow: 0 5px 30px rgba(0,0,0,0.08);
    transition: all 0.3s;
    border: 1px solid rgba(0,0,0,0.04);
}
.core-value:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 50px rgba(0,0,0,0.12);
}
.core-value .icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #009624, #00C853);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: #fff;
    font-size: 1.5rem;
}
.core-value h4 {
    color: #0a0a0a;
    margin-bottom: 8px;
}
.core-value p {
    font-size: 0.9rem;
    color: #8D6E63;
}

.leadership-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 30px;
}
.leader-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 30px rgba(0,0,0,0.08);
    text-align: center;
    transition: all 0.3s;
}
.leader-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 50px rgba(0,0,0,0.12);
}
.leader-card .photo {
    width: 100%;
    height: 250px;
    object-fit: cover;
    background: #e0e0e0;
}
.leader-card .info {
    padding: 25px;
}
.leader-card .info h4 {
    color: #0a0a0a;
    margin-bottom: 5px;
}
.leader-card .info .role {
    color: #00C853;
    font-weight: 600;
    font-size: 0.9rem;
}
.leader-card .info .subjects {
    color: #8D6E63;
    font-size: 0.85rem;
    margin-top: 5px;
}

.stats-grid-about {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}
.stat-item-about {
    text-align: center;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 5px 30px rgba(0,0,0,0.08);
}
.stat-item-about .number {
    font-size: 2.5rem;
    font-weight: 900;
    color: #00C853;
    display: block;
}
.stat-item-about .label {
    color: #8D6E63;
    font-size: 0.9rem;
    margin-top: 5px;
}

@media (max-width: 992px) {
    .mission-vision-grid { grid-template-columns: 1fr; }
    .core-values { grid-template-columns: repeat(2, 1fr); }
    .leadership-grid { grid-template-columns: repeat(2, 1fr); }
    .stats-grid-about { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
    .core-values { grid-template-columns: 1fr; }
    .leadership-grid { grid-template-columns: 1fr; }
    .stats-grid-about { grid-template-columns: 1fr; }
    .about-hero h1 { font-size: 2rem; }
}
</style>

<!-- Hero -->
<section class="about-hero">
    <div class="container">
        <h1>About <span class="highlight"><?= clean(getSetting($pdo, 'school_name', 'Our School')) ?></span></h1>
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
<section class="about-section" style="background:#f5e6d3;">
    <div class="container">
        <div class="section-title">
            <span class="subtitle">What We Stand For</span>
            <h2>Our Core <span class="highlight-green">Values</span></h2>
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
            <span class="subtitle">Our Leaders</span>
            <h2>Leadership <span class="highlight-green">Team</span></h2>
            <p>Meet the dedicated leaders guiding our school</p>
        </div>
        <div class="leadership-grid">
            <?php foreach ($leadership as $leader): ?>
                <div class="leader-card">
                    <?php if (!empty($leader['photo'])): ?>
                        <img src="<?= clean($leader['photo']) ?>" alt="<?= clean($leader['first_name']) ?>" class="photo">
                    <?php else: ?>
                        <div class="photo" style="display:flex;align-items:center;justify-content:center;background:#e0e0e0;color:#999;font-size:3rem;">
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
<section class="about-section" style="background:#f5e6d3;">
    <div class="container">
        <div class="section-title">
            <span class="subtitle">By The Numbers</span>
            <h2>School at a <span class="highlight-green">Glance</span></h2>
            <p>Key facts and figures about our institution</p>
        </div>
        <div class="stats-grid-about">
            <div class="stat-item-about">
                <span class="number"><?= clean(getSetting($pdo, 'founded_year', '1985')) ?></span>
                <span class="label">Year Founded</span>
            </div>
            <div class="stat-item-about">
                <span class="number"><?= clean(getSetting($pdo, 'total_students', '1,200')) ?>+</span>
                <span class="label">Students</span>
            </div>
            <div class="stat-item-about">
                <span class="number"><?= clean(getSetting($pdo, 'total_teachers', '60')) ?>+</span>
                <span class="label">Teachers</span>
            </div>
            <div class="stat-item-about">
                <span class="number"><?= clean(getSetting($pdo, 'pass_rate', '86')) ?>%</span>
                <span class="label">UACE Pass Rate</span>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>