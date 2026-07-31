<?php
$currentRoute = 'facilities';
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
    <link rel="canonical" href="<?= htmlspecialchars($siteUrl) ?>/facilities.php">
    <meta property="og:title" content="<?= htmlspecialchars($page['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page['description']) ?>">
    <link rel="icon" href="badge.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="academics.php">Academics</a></li>
                    <li><a href="admissions.php">Admissions</a></li>
                    <li class="active"><a href="facilities.php">Facilities</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

<!-- Interior Hero -->
    <section class="interior-hero" style="background-image: url('naps/facilities.background.webp');">
        <div class="hero-content">
            <h1>Our Campus Facilities</h1>
            <p>Providing a highly equipped, comfortable, and modern learning ecosystem in Mukono</p>
        </div>
    </section>

    <!-- Academic Facilities -->
    <section class="about">
        <div class="section-container">
            <div class="section-title">
                <h2>Academic Infrastructure</h2>
            </div>
            
            <div class="grid-3">
                <div class="card">
                    <div class="card-image">
                        <img src="naps/modern classroom.svg" alt="Modern Classrooms">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-chalkboard" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> Modern Classrooms</h3>
                        <p>Spacious, well-ventilated classrooms with ergonomic desks, writing boards, and natural lighting to maximize student concentration.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-digital resource.svg" alt="School Library">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-book-reader" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> Digital Resource Center</h3>
                        <p>A library housing thousands of printed textbooks, study guides, and research desks coupled with computer research stations.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-science lab.svg" alt="Science Laboratory">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-flask" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> Science Laboratories</h3>
                        <p>Fully certified, specialized laboratories for Chemistry, Biology, and Physics setups to support UNEB practical exams.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technology & Sports -->
    <section class="about" style="background: var(--light-gray);">
        <div class="section-container">
            <div class="section-title">
                <h2>Co-Curricular & Tech Hubs</h2>
            </div>
            
            <div class="grid-3">
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-ict $ lab.svg" alt="Computer Lab">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-laptop" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> ICT & DIT Lab</h3>
                        <p>Modern workstation terminals, hardware modules, high-speed fiber internet, and dedicated project workspaces for technical courses.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-sports $ recreation.svg" alt="Sports Grounds">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-running" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> Sports & Recreation</h3>
                        <p>A full-sized football pitch, basketball court, athletic track lanes, and an indoor game center for chess and mental sports training.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-creative arts studio.svg" alt="Creative Arts">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-palette" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> Creative Arts Studio</h3>
                        <p>Dedicated zones for painting, carpentry design, tailoring crafts, and musical performance practice rooms with instruments.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Residential/Boarding Facilities -->
    <section class="about">
        <div class="section-container">
            <div class="section-title">
                <h2>Boarding & Welfare</h2>
            </div>
            
            <div class="grid-3">
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-dormitories.svg" alt="Dormitories">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-bed" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> Secure Dormitories</h3>
                        <p>Comfortable residential spaces with individual wardrobes, iron beds, and clean restrooms. Strictly separated for boys and girls.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-dining hall.svg" alt="Dining Hall">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-utensils" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> Dining Hall & Kitchen</h3>
                        <p>Clean commercial kitchen serving healthy termly meals (posho, beans, rice, and matooke) in an organized 500-seat cafeteria.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="naps/facilities-dispensary.svg" alt="Health Clinic">
                    </div>
                    <div class="card-content">
                        <h3><i class="fas fa-clinic-medical" style="color: var(--sfc-red); margin-right: 0.5rem;"></i> College Dispensary</h3>
                        <p>Fully stocked first aid dispensary with a full-time resident nurse and standby ambulance for medical emergencies.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Campus Gallery -->
    <section class="about" style="background: var(--light-gray);">
        <div class="section-container">
            <div class="section-title">
                <h2>Campus Life Gallery</h2>
            </div>
            
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="naps/facilities-interactive learning.svg" alt="Interactive Learning">
                    <div class="gallery-overlay">Interactive Learning</div>
                </div>
                <div class="gallery-item">
                    <img src="naps/facilities-scientific inquiry.svg" alt="Scientific Inquiry">
                    <div class="gallery-overlay">Scientific Inquiry</div>
                </div>
                <div class="gallery-item">
                    <img src="naps/facilities-athletics competition.svg" alt="Athletics Competition">
                    <div class="gallery-overlay">Athletics Competition</div>
                </div>
                <div class="gallery-item">
                    <img src="naps/facilities-quiet library.svg" alt="Study Time">
                    <div class="gallery-overlay">Quiet Library Study</div>
                </div>
                <div class="gallery-item">
                    <img src="naps/facilities-creative expression.svg" alt="Creative Arts">
                    <div class="gallery-overlay">Creative Expression</div>
                </div>
                <div class="gallery-item">
                    <img src="naps/facilities-ivocational bakery.svg" alt="Technical Skills">
                    <div class="gallery-overlay">Vocational Bakery</div>
                </div>
            </div>
        </div>
    </section>
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
                <p>&copy; <?php echo date('Y'); ?> St. FRANCIS BORGIA HIGH SCHOOL MUKONO. All Rights Reserved. | <a href="admin/admin.php" style="color: var(--sfc-yellow); text-decoration: none; font-weight: 600;"><i class="fas fa-user-shield"></i> Admin Dashboard</a></p>
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
