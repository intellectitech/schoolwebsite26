<?php
$currentRoute = 'about';
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
    <link rel="canonical" href="<?= htmlspecialchars($siteUrl) ?>/about.php">
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
                    <li class="active"><a href="about.php">About</a></li>
                    <li><a href="academics.php">Academics</a></li>
                    <li><a href="admissions.php">Admissions</a></li>
                    <li><a href="facilities.php">Facilities</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

<!-- Interior Hero -->
    <section class="interior-hero" style="background-image: url('naps/about.1.webp');">
        <div class="hero-content">
            <h1>Our Story & Leadership</h1>
            <p>Discover the values, history, and team that define St. Francis Borgia High School</p>
        </div>
    </section>

    <!-- History Section -->
    <section class="about">
        <div class="section-container">
            <div class="about-content">
                <div class="about-text">
                    <h3>Our Rich History</h3>
                    <p>Founded in 2015, St. Francis Borgia High School Mukono began with a vision to deliver premium, affordable secondary education focused on holistic human development. Starting with just a handful of students and a dedicated band of educators, the school has steadily expanded its campus infrastructure and academic offerings.</p>
                    <p>Over the past decade, we have established ourselves as one of the premier educational institutions in Mukono District. We are fully registered and classified by the Ministry of Education and Sports, offering both UNEB curriculum and DIT vocational skills.</p>
                    <p>Today, our campus serves a diverse community of over 1,000 students. Our graduates have transitioned successfully to major national and international universities, embodying our motto: "Called to Shine."</p>
                </div>
                <div class="about-image">
                    <img src="naps/about.building.svg" alt="St. Francis Borgia Campus Building">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="about" style="background: var(--white); padding: 4rem 0;">
        <div class="section-container">
            <div class="mv-container">
                <div class="mv-card">
                    <div class="mv-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To provide top quality education that prepares students for a quality life, spirit of enterprise, community-based services, and competent leadership.</p>
                </div>
                
                <div class="mv-card">
                    <div class="mv-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To be a model secondary school in the provision of quality education and produce God-fearing, innovative, and responsible citizens.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="about" style="background: var(--sfc-red-dark); color: var(--white);">
        <div class="section-container">
            <div class="section-title">
                <h2 style="color: var(--sfc-yellow);">Our Core Values</h2>
            </div>
            
            <div class="values-grid">
                <div class="value-card">
                    <h3>Excellence</h3>
                    <p>We pursue the highest standards in academics, administration, and personal conduct, challenging every student to excel.</p>
                </div>
                <div class="value-card">
                    <h3>Integrity</h3>
                    <p>We cultivate moral uprightness, transparency, and honesty in all individual and institutional relationships.</p>
                </div>
                <div class="value-card">
                    <h3>Faith</h3>
                    <p>We believe in nurturing spiritual growth, respect for religious diversity, and strong moral principles.</p>
                </div>
                <div class="value-card">
                    <h3>Innovation</h3>
                    <p>We encourage creative thinking, entrepreneurial spirits, and the integration of technical vocational skills.</p>
                </div>
                <div class="value-card">
                    <h3>Community</h3>
                    <p>We build a supportive, inclusive, and loving family environment where respect and team spirit thrive.</p>
                </div>
                <div class="value-card">
                    <h3>Service</h3>
                    <p>We instil a sense of duty to serve the community, preparing students to be active citizens who solve societal problems.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Section -->
    <section class="about">
        <div class="section-container">
            <div class="section-title">
                <h2>School Administration</h2>
            </div>
            
            <div class="leader-grid">
                <div class="leader-card">
                    <div class="leader-image">
                        <img src="naps/Chairman Board of Governors (1).svg" alt="Director">
                    </div>
                    <div class="leader-info">
                        <h3>Dr. Lugya Benon</h3>
                        <p class="leader-position">Chairman Board of Governors</p>
                        <p>Providing strategic and visionary oversight to make St. Francis Borgia a model school in Uganda.</p>
                    </div>
                </div>
                
                <div class="leader-card">
                    <div class="leader-image">
                        <img src="naps/headteacher.svg" alt="Headteacher">
                    </div>
                    <div class="leader-info">
                        <h3>Mrs. Henry Ninda </h3>
                        <p class="leader-position">Headteacher</p>
                        <p>Responsible for daily school administration, academic supervision, and student welfare implementation.</p>
                    </div>
                </div>
                
                <div class="leader-card">
                    <div class="leader-image">
                        <img src="naps/xul preacher.svg" alt="DOS">
                    </div>
                    <div class="leader-info">
                        <h3>Mr. Rev. Fr. Dr Gerald Bwenvu</h3>
                        <p class="leader-position">Education Secretary</p>
                        <p>Manages all the services conducted in the School.</p>
                    </div>
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
