<?php
require_once 'database.php';

try {
    $newsItems = $pdo->query("SELECT * FROM news WHERE is_published = 1 ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $newsItems = [];
}

try {
    $testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_published = 1 ORDER BY sort_order ASC")->fetchAll();
} catch (PDOException $e) {
    $testimonials = [];
}
?>
<?php
$currentRoute = 'home';
require_once 'seo-config.php';
$page = $seo[$currentRoute] ?? $seo['home'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page['description']) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($siteUrl) ?>/index.php">
    <meta property="og:title" content="<?= htmlspecialchars($page['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page['description']) ?>">
    <meta property="og:type" content="website">
    <link rel="icon" href="badge.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "School",
        "name": "St. Francis Borgia High School Mukono",
        "alternateName": "St. FRANCIS BORGIA HIGH SCHOOL MUKONO",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Mukono",
            "addressCountry": "UG"
        },
        "telephone": "+256772622612",
        "email": "info@stfrancisborgia.ac.ug",
        "url": "<?= htmlspecialchars($siteUrl) ?>"
    }
    </script>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-container">
            <div class="quick-contact">
                <span><i class="fas fa-phone"></i> +256 772 622 612 / +256 754 465 536</span>
                <span><i class="fas fa-envelope"></i> info@stfrancisborgia.ac.ug</span>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header>
        <div class="header-container">
            <div class="logo-container">
                <img src="naps/logo.jpg" alt="St. Francis Borgia High School Mukono logo" class="logo">
                <div class="school-name">
                    <h1>St. FRANCIS BORGIA HIGH SCHOOL</h1>
                    <p>Called to Shine</p>
                </div>
            </div>
            
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            
            <nav id="main-nav">
                <ul>
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="academics.php">Academics</a></li>
                    <li><a href="admissions.php">Admissions</a></li>
                    <li><a href="facilities.php">Facilities</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

<!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-slideshow">
                <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=80');"></div>
                <div class="slide" style="background-image: url('naps/background1.webp');"></div>
                <div class="slide" style="background-image: url('naps/background2.webp');"></div>
                <div class="slide" style="background-image: url('naps/background3.webp');"></div>
                <div class="slide" style="background-image: url('naps/background4.webp');"></div>
            </div>
            <div class="hero-content">
                <h1>Nurturing Minds, Nurturing Souls</h1>
                <p>Welcome to St. Francis Borgia High School Mukono, where academic rigor merges with technical expertise and strong spiritual principles under our motto: "Called to Shine".</p>
                <div class="hero-buttons">
                    <a href="admissions.php" class="btn">Apply Now</a>
                    <a href="tour.php" class="btn btn-outline">Schedule a Tour</a>
                </div>
            </div>
            <!-- Navigation Dots -->
            <div class="slideshow-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
                <span class="dot" data-slide="3"></span>
                <span class="dot" data-slide="4"></span>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <div class="section-container">
            <div class="section-title">
                <h2>Welcome to Our High School</h2>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <h3>Empowering Generations Since 2015</h3>
                    <p>Located in Mukono, Uganda, St. Francis Borgia High School has built a formidable reputation as a hub for secondary education. Our mission is to mold young minds into competent, responsible, and god-fearing citizens who can contribute positively to a globalized society.</p>
                    <p>With an emphasis on academic diligence, moral uprightness, and active co-curricular participation, we cultivate a vibrant community where every learner is guided to realize their full potential.</p>

                    <div class="achievements">
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h4>98% Pass Rate</h4>
                            <p>UNEB Examinations</p>
                        </div>
                        
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <h4>DIT Certified</h4>
                            <p>Vocational Skills training</p>
                        </div>
                        
                        <div class="achievement-item">
                            <div class="achievement-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4>1,000+ Students</h4>
                            <p>Thriving Community</p>
                        </div>
                    </div>
                </div>
                
                <div class="about-image">
                    <img src="naps/empowering (1).svg" alt="St. Francis Borgia High School Campus">
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="programs">
        <div class="section-container">
            <div class="section-title">
                <h2>Our Academic Programs</h2>
            </div>
            
            <div class="grid-3">
                <div class="card">
                    <div class="card-image">
                        <img src="naps/sci $ math.svg" alt="Science Program">
                    </div>
                    <div class="card-content">
                        <h3>Sciences & Mathematics</h3>
                        <p>A rigorous curriculum in Physics, Chemistry, Biology, and Mathematics backed by modern laboratories to encourage scientific inquiry.</p>
                        <a href="academics.php" class="btn btn-secondary">Explore Program</a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/arts $ huma.svg" alt="Arts Program">
                    </div>
                    <div class="card-content">
                        <h3>Arts & Humanities</h3>
                        <p>Explore language, literature, history, geography, and divinity to cultivate critical thinking, communication, and empathy.</p>
                        <a href="academics.php" class="btn btn-secondary">Explore Program</a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/voca.svg" alt="Vocational Studies">
                    </div>
                    <div class="card-content">
                        <h3>Vocational & Technical</h3>
                        <p>Certified DIT courses in bakery, computer science, and agricultural entrepreneurship to equip students with practical skills.</p>
                        <a href="academics.php" class="btn btn-secondary">Explore Program</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News & Events Section -->
    <section class="news-section" style="background: var(--light-gray);">
        <div class="section-container">
            <div class="section-title">
                <h2>Latest News & Highlights</h2>
            </div>
            
            <div class="grid-3">
                <?php foreach ($newsItems as $item): ?>
    <div class="card">
        <div class="card-image">
            <img src="<?= htmlspecialchars($item['featured_image'] ?: 'naps/merit.svg') ?>" alt="<?= htmlspecialchars($item['title']) ?>">
        </div>
        <div class="card-content">
            <div class="card-date"><i class="far fa-calendar-alt"></i> <?= date('F j, Y', strtotime($item['created_at'])) ?></div>
            <h3><?= htmlspecialchars($item['title']) ?></h3>
            <p><?= htmlspecialchars($item['excerpt']) ?></p>
            <a href="news_detail.php?id=<?= $item['id'] ?>" class="btn btn-secondary">Read More</a>
        </div>
    </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($testimonials)): ?>
    <!-- Testimonials Section -->
    <section class="testimonials-section" style="background: var(--sfc-red); padding: 5rem 0;">
        <div class="section-container">
            <div class="section-title">
                <h2 style="color: var(--white);">What Parents & Students Say</h2>
            </div>

            <div class="grid-3">
                <?php foreach ($testimonials as $t): ?>
                    <div class="card" style="padding: 2rem; text-align: center;">
                        <img src="<?= htmlspecialchars($t['photo'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80') ?>" alt="<?= htmlspecialchars($t['author_name']) ?>" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; margin: 0 auto 1rem; display: block;">
                        <div style="color: var(--sfc-yellow); margin-bottom: 0.8rem; font-size: 0.9rem;">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="<?= $i < (int)$t['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p style="font-style: italic; color: #555; margin-bottom: 1.2rem; line-height: 1.6;">&ldquo;<?= htmlspecialchars($t['content']) ?>&rdquo;</p>
                        <h4 style="color: var(--sfc-red-dark); margin-bottom: 0.2rem;"><?= htmlspecialchars($t['author_name']) ?></h4>
                        <?php if (!empty($t['author_role'])): ?>
                            <p style="font-size: 0.85rem; color: #888;"><?= htmlspecialchars($t['author_role']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <script>
        // Hero Slideshow
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        if (slides.length > 0) {
            let currentSlide = 0;
            let slideInterval;

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                    if (dots[i]) {
                        dots[i].classList.toggle('active', i === index);
                    }
                });
                currentSlide = index;
            }

            function showNextSlide() {
                const nextSlide = (currentSlide + 1) % slides.length;
                showSlide(nextSlide);
            }

            function startSlideshow() {
                slideInterval = setInterval(showNextSlide, 5000);
            }

            function resetSlideshow() {
                clearInterval(slideInterval);
                startSlideshow();
            }

            startSlideshow();

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    resetSlideshow();
                });
            });
        }
    </script>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>St. Francis Borgia</h3>
                    <p>High school mukono dedicated to providing holistic, high-quality secondary education rooted in academic excellence and Christian values.</p>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="academics.php">Academics</a></li>
                        <li><a href="admissions.php">Admissions</a></li>
                        <li><a href="facilities.php">Facilities</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Mukono Town, Uganda</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+256 772 622 612 / +256 754 465 536</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@stfrancisborgia.ac.ug</span>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>School Hours</h3>
                    <ul style="color: var(--sfc-silver); font-size: 0.95rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.5rem;">Monday - Friday: 7:30 AM - 4:30 PM</li>
                        <li style="margin-bottom: 0.5rem;">Saturday: 8:00 AM - 1:00 PM (Study/Clubs)</li>
                        <li>Sunday: Sabbath Worship & Rest</li>
                    </ul>
                    
                    <h3>Follow Us</h3>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> St. FRANCIS BORGIA HIGH SCHOOL MUKONO. All Rights Reserved.| <a href="admin/admin.php" style="color: var(--sfc-yellow); text-decoration: none; font-weight: 600;"><i class="fas fa-user-shield"></i> Admin Dashboard</a> </p> 
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.getElementById('main-nav');
        
        if (menuToggle && mainNav) {
            menuToggle.addEventListener('click', () => {
                mainNav.classList.toggle('active');
            });
            
            document.addEventListener('click', (e) => {
                if (!menuToggle.contains(e.target) && !mainNav.contains(e.target)) {
                    mainNav.classList.remove('active');
                }
            });
        }

        // Hero Slideshow
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        if (slides.length > 0) {
            let currentSlide = 0;
            let slideInterval;

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                    if (dots[i]) {
                        dots[i].classList.toggle('active', i === index);
                    }
                });
                currentSlide = index;
            }

            function showNextSlide() {
                const nextSlide = (currentSlide + 1) % slides.length;
                showSlide(nextSlide);
            }

            function startSlideshow() {
                slideInterval = setInterval(showNextSlide, 5000);
            }

            function resetSlideshow() {
                clearInterval(slideInterval);
                startSlideshow();
            }

            startSlideshow();

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    resetSlideshow();
                });
            });
        }
    </script>
    <!-- Neexa Widget -->
<script>
  window.neexaAsyncInit = function() {
    window.neexa.init({
      agent_id: 'a26358ec-5c33-49b1-bb99-8639edbe3689', mobile_mini_style: 'greeting_only',
    });
  };
</script>
<script async src="https://chat-widget.neexa.ai/main.js?nonce=1785475814179.8662"></script>
<!-- End Neexa Widget -->
</body>
</html>
