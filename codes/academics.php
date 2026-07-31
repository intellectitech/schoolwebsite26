<?php
$currentRoute = 'academics';
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
    <link rel="canonical" href="<?= htmlspecialchars($siteUrl) ?>/academics.php">
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
                    <li class="active"><a href="academics.php">Academics</a></li>
                    <li><a href="admissions.php">Admissions</a></li>
                    <li><a href="facilities.php">Facilities</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

<!-- Interior Hero -->
    <section class="interior-hero" style="background-image: url('naps/academic.background.webp');">
        <div class="hero-content">
            <h1>Academic Excellence</h1>
            <p>Empowering minds with curriculum rigor, vocational skills, and creative instruction</p>
        </div>
    </section>

    <!-- Programs Tab Section -->
    <section class="about">
        <div class="section-container">
            <h2 class="section-title">Academic & Vocational Tracks</h2>
            
            <div class="program-tabs">
                <button class="tab-btn active" data-tab="ordinary-level">Ordinary Level (O-Level)</button>
                <button class="tab-btn" data-tab="advanced-level">Advanced Level (A-Level)</button>
                <button class="tab-btn" data-tab="vocational">Vocational Studies (DIT)</button>
            </div>
            
            <!-- O-Level -->
            <div class="tab-content active" id="ordinary-level">
                <div class="leader-grid">
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-book-open"></i> Core Curriculum</h3>
                        <p style="color: #666; font-size: 0.95rem; margin-bottom: 1.5rem;">All students study core compulsory subjects in line with the Uganda National Curriculum guidelines: English, Mathematics, Biology, Chemistry, Physics, History, Geography, and Christian Religious Education (CRE).</p>
                    </div>
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-layer-group"></i> Elective Options</h3>
                        <p style="color: #666; font-size: 0.95rem; margin-bottom: 1.5rem;">Students choose supplementary areas based on interests: Literature in English, Computer Studies, Agriculture, Commerce, Fine Art, and Entrepreneurship.</p>
                    </div>
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-certificate"></i> Practical Integration</h3>
                        <p style="color: #666; font-size: 0.95rem; margin-bottom: 1.5rem;">To supplement the standard curriculum, students are introduced to DIT certified projects starting Senior One to establish a strong technical foundation.</p>
                    </div>
                </div>
            </div>
            
            <!-- A-Level -->
            <div class="tab-content" id="advanced-level">
                <div class="leader-grid">
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-flask"></i> Science Combinations</h3>
                        <p style="color: #666; font-size: 0.95rem;">Preparing future medical professionals, engineers, and tech researchers. Combinations include: PCM (Physics, Chemistry, Math), PCB (Physics, Chemistry, Biology), BCM (Biology, Chemistry, Math), with Subsidiary ICT and Sub-Math.</p>
                    </div>
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-globe"></i> Arts & Humanities</h3>
                        <p style="color: #666; font-size: 0.95rem;">Preparing students for legal, leadership, media, and social work fields. Combinations include: HEG (History, Economics, Geography), HEL (History, Economics, Literature), Divinity combos, with Subsidiary ICT/Sub-Math.</p>
                    </div>
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-graduation-cap"></i> Career Counseling</h3>
                        <p style="color: #666; font-size: 0.95rem;">Comprehensive guidance systems assisting candidates with career pathways, university entry applications, and personal statement portfolios.</p>
                    </div>
                </div>
            </div>
            
            <!-- Vocational -->
            <div class="tab-content" id="vocational">
                <div class="leader-grid">
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-laptop-code"></i> ICT & Computer Science</h3>
                        <p style="color: #666; font-size: 0.95rem;">Certified DIT training in hardware assembly, basic network systems, software utilities, web development, and digital graphic design principles.</p>
                    </div>
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-utensils"></i> Bakery & Culinary Arts</h3>
                        <p style="color: #666; font-size: 0.95rem;">Hands-on training in commercial bakery, recipe preparation, food handling, hygiene standards, and confectionery entrepreneurship.</p>
                    </div>
                    <div class="leader-card" style="text-align: left; padding: 2rem;">
                        <h3 style="color: var(--sfc-red); margin-bottom: 1rem;"><i class="fas fa-tractor"></i> Modern Agribusiness</h3>
                        <p style="color: #666; font-size: 0.95rem;">Practical experience on the school farm focusing on high-yield crop cultivation, animal husbandry basics, poultry setup, and budget management.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Curriculum Section -->
    <section class="about" style="background: var(--light-gray); padding: 4rem 0;">
        <div class="section-container">
            <h2 class="section-title">Academic Enrichment Programs</h2>
            <p style="text-align: center; max-width: 800px; margin: 0 auto 3rem; color: #666;">We believe in augmenting classwork with skill validation. Our curriculum is integrated with enrichment initiatives overseen by DIT:</p>
            
            <div class="leader-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
                <div class="achievement-item" style="text-align: left; border-bottom: none; border-left: 3px solid var(--sfc-yellow);">
                    <h4 style="font-size: 1.1rem; color: var(--sfc-red); margin-bottom: 0.5rem;">UNEB Curriculum</h4>
                    <p style="font-size: 0.9rem; color: #666;">Full compliance with national grading syllabi for both UCE and UACE.</p>
                </div>
                <div class="achievement-item" style="text-align: left; border-bottom: none; border-left: 3px solid var(--sfc-yellow);">
                    <h4 style="font-size: 1.1rem; color: var(--sfc-red); margin-bottom: 0.5rem;">Entrepreneurship</h4>
                    <p style="font-size: 0.9rem; color: #666;">Practical business planning projects, marketing exercises, and expo days.</p>
                </div>
                <div class="achievement-item" style="text-align: left; border-bottom: none; border-left: 3px solid var(--sfc-yellow);">
                    <h4 style="font-size: 1.1rem; color: var(--sfc-red); margin-bottom: 0.5rem;">Digital Integration</h4>
                    <p style="font-size: 0.9rem; color: #666;">Integrating multimedia presentation techniques and e-learning resources across subjects.</p>
                </div>
                <div class="achievement-item" style="text-align: left; border-bottom: none; border-left: 3px solid var(--sfc-yellow);">
                    <h4 style="font-size: 1.1rem; color: var(--sfc-red); margin-bottom: 0.5rem;">Community Projects</h4>
                    <p style="font-size: 0.9rem; color: #666;">Regular social projects in Mukono town to build service mentalities and empathy.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Academic Performance Section -->
    <section class="about" style="background: var(--white); padding: 4rem 0;">
        <div class="section-container">
            <div class="section-title">
                <h2>Previous UNEB Performance</h2>
            </div>
            <p style="text-align: center; max-width: 800px; margin: -2rem auto 3rem; color: #666;">Our students consistently achieve outstanding results in the Uganda National Examinations. Below is our performance summary for the past four years:</p>

            <div class="mv-container" style="gap: 3rem; margin-bottom: 0;">
                <!-- UCE (O-Level) Performance -->
                <div class="mv-card" style="text-align: left; padding: 2.5rem; border-top-color: var(--sfc-yellow);">
                    <h3 style="color: var(--sfc-red-dark); margin-bottom: 1.5rem; text-align: center;"><i class="fas fa-file-alt" style="color: var(--sfc-red); margin-right: 0.8rem;"></i> UCE (O-Level) - Past 4 Years</h3>
                    <div class="table-responsive">
                        <table class="sfc-table" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>No. of Candidates</th>
                                    <th>Division 1</th>
                                    <th>Division 2</th>
                                    <th>Division 3 & 4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>2025</strong></td>
                                    <td>104</td>
                                    <td>68</td>
                                    <td>28</td>
                                    <td>8</td>
                                </tr>
                                <tr>
                                    <td><strong>2024</strong></td>
                                    <td>98</td>
                                    <td>59</td>
                                    <td>29</td>
                                    <td>10</td>
                                </tr>
                                <tr>
                                    <td><strong>2023</strong></td>
                                    <td>91</td>
                                    <td>54</td>
                                    <td>27</td>
                                    <td>10</td>
                                </tr>
                                <tr>
                                    <td><strong>2022</strong></td>
                                    <td>85</td>
                                    <td>48</td>
                                    <td>25</td>
                                    <td>12</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- UACE (A-Level) Performance -->
                <div class="mv-card" style="text-align: left; padding: 2.5rem; border-top-color: var(--sfc-yellow);">
                    <h3 style="color: var(--sfc-red-dark); margin-bottom: 1.5rem; text-align: center;"><i class="fas fa-chart-line" style="color: var(--sfc-red); margin-right: 0.8rem;"></i> UACE (A-Level) - Past 4 Years</h3>
                    <div class="table-responsive">
                        <table class="sfc-table" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>No. of Candidates</th>
                                    <th>3 PPs</th>
                                    <th>2 PPs</th>
                                    <th>Others</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>2025</strong></td>
                                    <td>88</td>
                                    <td>52</td>
                                    <td>24</td>
                                    <td>12</td>
                                </tr>
                                <tr>
                                    <td><strong>2024</strong></td>
                                    <td>81</td>
                                    <td>47</td>
                                    <td>22</td>
                                    <td>12</td>
                                </tr>
                                <tr>
                                    <td><strong>2023</strong></td>
                                    <td>76</td>
                                    <td>42</td>
                                    <td>21</td>
                                    <td>13</td>
                                </tr>
                                <tr>
                                    <td><strong>2022</strong></td>
                                    <td>70</td>
                                    <td>38</td>
                                    <td>20</td>
                                    <td>12</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculty Section -->
    <section class="about">
        <div class="section-container">
            <h2 class="section-title">Distinguished Faculty</h2>
            <p style="text-align: center; max-width: 800px; margin: 0 auto 3rem; color: #666;">Meet the department heads driving academic quality and instructional success at St. Francis Borgia High School:</p>
            
            <div class="leader-grid">
                <div class="leader-card">
                    <div class="leader-image" style="height: 250px;">
                        <img src="" alt="Mr Senfuka Abdul">
                    </div>
                    <div class="leader-info">
                        <h3>Mr. Senfuka Abdul</h3>
                        <p class="leader-position" style="font-size: 0.8rem; margin-bottom: 0.2rem;">Head of Science Department</p>
                        <p style="font-size: 0.85rem; color: #777;">MSc in Chemistry Education (Makerere University). 12 years of lab pedagogy experience.</p>
                    </div>
                </div>
                
                <div class="leader-card">
                    <div class="leader-image" style="height: 250px;">
                        <img src="" alt="Mr. Serugya Julius">
                    </div>
                    <div class="leader-info">
                        <h3>Mr. Serugya Julius</h3>
                        <p class="leader-position" style="font-size: 0.8rem; margin-bottom: 0.2rem;">Head of Humanities Department</p>
                        <p style="font-size: 0.85rem; color: #777;">MA in Education (Kyambogo University). Specializes in History & Divinity instruction.</p>
                    </div>
                </div>
                
                <div class="leader-card">
                    <div class="leader-image" style="height: 250px;">
                        <img src="" alt="Mr. Musa Sekiziyivu">
                    </div>
                    <div class="leader-info">
                        <h3>Mr. Musa Sekiziyivu</h3>
                        <p class="leader-position" style="font-size: 0.8rem; margin-bottom: 0.2rem;">Vocational Studies Coordinator</p>
                        <p style="font-size: 0.85rem; color: #777;">BSc in Agricultural & Mechanical Engineering. Oversees all DIT certificate courses.</p>
                    </div>
                </div>
                
                <div class="leader-card">
                    <div class="leader-image" style="height: 250px;">
                        <img src="" alt="Mr Kabunga Hameem">
                    </div>
                    <div class="leader-info">
                        <h3>Mr. Kabunga Hameem</h3>
                        <p class="leader-position" style="font-size: 0.8rem; margin-bottom: 0.2rem;">Senior Mathematics Lead</p>
                        <p style="font-size: 0.85rem; color: #777;">BSc in Education (Math/Physics). Passionate about algebra, computational logic, and calculus.</p>
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
                <p>&copy; <?php echo date('Y'); ?> St. FRANCIS BORGIA HIGH SCHOOL MUKONO. All Rights Reserved.| <a href="admin/admin.php" style="color: var(--sfc-yellow); text-decoration: none; font-weight: 600;"><i class="fas fa-user-shield"></i> Admin Dashboard</a></p>
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
