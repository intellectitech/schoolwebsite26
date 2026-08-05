<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Home';
$pageDescription = SITE_DESCRIPTION;
require_once __DIR__ . '/includes/header.php';
?>
        <section id="home" class="hero reveal">
            <div class="container hero-grid">
                <div class="hero-content">
                    <h1>Education Is My Future</h1>
                    <p>Namugongo Model Primary School provides a safe, caring and inspiring learning environment where every child can grow academically, socially and morally.</p>
                    <div class="hero-buttons">
                        <a class="btn" href="about.php">Learn About Us</a>
                        <a class="btn btn-outline" href="admission.php">Apply for Admission</a>
                    </div>
                </div>
                <div>
                    <img class="hero-image" src="images/pipils.jpg" alt="Pupils at Namugongo Model Primary School">
                </div>
            </div>
        </section>

        <section id="about" class="section reveal">
            <div class="container">
                <h2>About the School</h2>
                <p class="section-intro">For over a decade, our school has shaped young learners through strong academics, purposeful character formation, and warm community partnerships.</p>
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
            </div>
        </section>

        <section id="news" class="section reveal">
            <div class="container">
                <h2>Latest News</h2>
                <p class="section-intro">Keep up with school activities, student achievements, and important announcements that keep our community moving forward.</p>
                <div class="grid">
                    <div class="news-item reveal">
                        <img src="images/news.jpg" alt="Sports event">
                        <h3>Inter-House Sports</h3>
                        <p>Students competed with great energy and sportsmanship across all houses in a successful athletic day.</p>
                    </div>
                    <div class="news-item reveal">
                        <img src="images/admissions.jpg" alt="Science projects">
                        <h3>Science Fair</h3>
                        <p>Our learners presented creative projects that highlighted their curiosity and investigative skills.</p>
                    </div>
                    <div class="news-item reveal">
                        <img src="images/speech.jpg" alt="Parent meeting">
                        <h3>Parent Meeting</h3>
                        <p>Teachers and parents worked together to review progress and set goals for the upcoming term.</p>
                    </div>
                </div>
                <p style="margin-top:24px;"><a class="btn" href="news.php">View All News</a></p>
            </div>
        </section>

        <section id="gallery" class="section reveal">
            <div class="container">
                <h2>School Gallery</h2>
                <p class="section-intro">A glimpse at our vibrant campus life, learning moments, and special school events.</p>
                <div class="gallery">
                    <img src="images/band.jpg" alt="School band">
                    <img src="images/marching.jpg" alt="Marching students">
                    <img src="images/speech.jpg" alt="Student speech">
                    <img src="images/swimming.jpg" alt="Swimming activity">
                    <img src="images/top graduation.jpg" alt="Graduation ceremony">
                    <img src="images/PE.jpg" alt="PE class">
                </div>
                <p style="margin-top:24px;"><a class="btn" href="gallery.php">View Full Gallery</a></p>
            </div>
        </section>

        <section id="contact" class="section reveal">
            <div class="container">
                <h2>Contact Us</h2>
                <p class="section-intro">Have a question or want to book a school visit? Send us a message and our admissions team will respond promptly.</p>
                <div class="grid">
                    <div class="card reveal">
                        <h3>Contact Information</h3>
                        <p><strong>Phone:</strong> <?php echo h(SITE_PHONE); ?></p>
                        <p><strong>Email:</strong> <?php echo h(SITE_EMAIL); ?></p>
                        <p><strong>Location:</strong> <?php echo h(SITE_ADDRESS); ?></p>
                    </div>
                    <div class="form-card reveal">
                        <h3>Send a Message</h3>
                        <form id="contactForm" action="submit_contact.php" method="POST">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                            <label for="message">Message</label>
                            <textarea id="message" name="message" required></textarea>
                            <button type="submit" class="btn">Send Message</button>
                        </form>
                        <p id="messageOutput"></p>
                    </div>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
