<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'About Us';
$pageDescription = 'Learn about the mission, vision and core values of Namugongo Model Primary School in Kampala, Uganda.';
require_once __DIR__ . '/includes/header.php';
?>
        <section class="page-hero">
            <div class="container">
                <h1>About Our School</h1>
                <p>For over a decade, our school has shaped young learners through strong academics, purposeful character formation, and warm community partnerships.</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="grid">
                    <div class="image-card reveal">
                        <img src="images/girl guides.jpg" alt="Classroom learning">
                        <h3>Our Mission</h3>
                        <p>To provide excellent and inclusive education that develops learners into confident, disciplined, and responsible citizens.</p>
                    </div>
                    <div class="image-card reveal">
                        <img src="images/pipils.jpg" alt="Students and teacher">
                        <h3>Our Vision</h3>
                        <p>To be a leading model school known for academic excellence, character formation, and community service.</p>
                    </div>
                    <div class="image-card reveal">
                        <img src="images/Compound.jpg" alt="School environment">
                        <h3>Core Values</h3>
                        <p>Integrity, respect, curiosity, teamwork, and excellence guide our daily school life.</p>
                    </div>
                </div>

                <div class="stats">
                    <div class="stat reveal"><h3 class="stat-number" data-target="12" data-suffix="+">0</h3><p>Academic Clubs</p></div>
                    <div class="stat reveal"><h3 class="stat-number" data-target="18" data-suffix="+">0</h3><p>Dedicated Staff</p></div>
                    <div class="stat reveal"><h3 class="stat-number" data-target="96" data-suffix="%">0</h3><p>Parent Satisfaction</p></div>
                    <div class="stat reveal"><h3 class="stat-number" data-target="7" data-suffix="/7">0</h3><p>Student Care</p></div>
                </div>

                <div class="highlight-panel reveal">
                    <h3>Why Parents Trust Us</h3>
                    <p>We combine academic strength, moral guidance, and a welcoming environment so every child can thrive.</p>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
