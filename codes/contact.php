<?php
$currentRoute = 'contact';
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
    <link rel="canonical" href="<?= htmlspecialchars($siteUrl) ?>/contact.php">
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
                    <li><a href="facilities.php">Facilities</a></li>
                    <li><a href="news.php">News</a></li>
                    <li class="active"><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

<!-- Interior Hero -->
    <section class="interior-hero" style="background-image: url('naps/contact.background.webp');">
        <div class="hero-content">
            <h1>Contact Our Administration</h1>
            <p>Have questions about admissions, fees, or events? Get in touch with our team today</p>
        </div>
    </section>

    <!-- Contact Container -->
    <section class="about">
        <div class="section-container">
            <div class="about-content" style="align-items: start; grid-template-columns: 1fr 1.2fr;">
                <!-- Contact Info Column -->
                <div class="about-text">
                    <h3>Reach Out Directly</h3>
                    <p style="color: #666; margin-bottom: 2rem;">Our administrative staff is ready to assist you. You can call, email, or visit our campus in Mukono, Uganda.</p>
                    
                    <div style="margin-bottom: 2rem;">
                        <div class="footer-contact-item" style="color: inherit; margin-bottom: 1.5rem;">
                            <div class="achievement-icon" style="margin: 0 1rem 0 0; font-size: 1.5rem; color: var(--sfc-red);"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h4 style="color: var(--sfc-red-dark); font-weight: 700; margin-bottom: 0.2rem;">Location</h4>
                                <p style="color: #666; font-size: 0.95rem;">Mukono, Central Uganda<br>P.O. Box 6738, Mukono, Uganda</p>
                            </div>
                        </div>
                        
                        <div class="footer-contact-item" style="color: inherit; margin-bottom: 1.5rem;">
                            <div class="achievement-icon" style="margin: 0 1rem 0 0; font-size: 1.5rem; color: var(--sfc-red);"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <h4 style="color: var(--sfc-red-dark); font-weight: 700; margin-bottom: 0.2rem;">Phone Numbers</h4>
                                <p style="color: #666; font-size: 0.95rem;">Main Reception: +256 772 622 612<br>Admissions Office: +256 754 465 536</p>
                            </div>
                        </div>
                        
                        <div class="footer-contact-item" style="color: inherit; margin-bottom: 1.5rem;">
                            <div class="achievement-icon" style="margin: 0 1rem 0 0; font-size: 1.5rem; color: var(--sfc-red);"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h4 style="color: var(--sfc-red-dark); font-weight: 700; margin-bottom: 0.2rem;">Email Addresses</h4>
                                <p style="color: #666; font-size: 0.95rem;">General Info: info@stfrancisborgia.ac.ug<br>Admissions Desk: admissions@stfrancisborgia.ac.ug</p>
                            </div>
                        </div>
                        
                        <div class="footer-contact-item" style="color: inherit;">
                            <div class="achievement-icon" style="margin: 0 1rem 0 0; font-size: 1.5rem; color: var(--sfc-red);"><i class="fas fa-clock"></i></div>
                            <div>
                                <h4 style="color: var(--sfc-red-dark); font-weight: 700; margin-bottom: 0.2rem;">Administrative Hours</h4>
                                <p style="color: #666; font-size: 0.95rem;">Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 1:00 PM<br>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Map Widget -->
                    <div style="border-radius: 8px; overflow: hidden; height: 350px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1.5px solid var(--sfc-silver-dark);">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15958.948293796338!2d32.7522!3d0.3549!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177db8b2ef8508eb%3A0xe5a3de51c08e5c1!2sMukono!5e0!3m2!1sen!2sug!4v1743680000000!5m2!1sen!2sug" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
                
                <!-- Contact Form Column -->
                <div class="form-card" style="margin: 0; width: 100%; max-width: 100%; border-top: 5px solid var(--sfc-red);">
                    <h3 style="color: var(--sfc-red); font-size: 1.8rem; margin-bottom: 1rem;">Send Us a Message</h3>
                    <p style="color: #666; margin-bottom: 2rem;">If you have a quick enquiry, please fill out the form below. We will respond to your registered email address within 24 hours.</p>
                    
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" class="form-control" required placeholder="e.g. Samuel Mukasa">
                        </div>
                        
                        <div class="form-group">
                            <label for="emailAddress">Email Address</label>
                            <input type="email" id="emailAddress" class="form-control" required placeholder="email@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="tel" id="phoneNumber" class="form-control" placeholder="e.g. +256 772 622 612">
                        </div>
                        
                        <div class="form-group">
                            <label for="messageSubject">Subject</label>
                            <select id="messageSubject" class="form-control" required>
                                <option value="">Select subject</option>
                                <option value="admissions">Admissions Inquiry</option>
                                <option value="academics">Academic Combinations</option>
                                <option value="vocational">Vocational Skills (DIT)</option>
                                <option value="visitation">School Tour/Visit</option>
                                <option value="other">Other Inquiry</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="messageBody">Your Message</label>
                            <textarea id="messageBody" class="form-control" rows="5" required placeholder="Describe your question or feedback in detail..."></textarea>
                        </div>
                        
                        <div style="text-align: center; margin-top: 2rem;">
                            <button type="submit" class="btn" style="padding: 1rem 3rem; width: 100%; border-radius: 6px;">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Specific Departments Section -->
    <section class="about" style="background: var(--sfc-red); color: var(--white); padding: 5rem 0;">
        <div class="section-container">
            <div class="section-title">
                <h2 style="color: var(--sfc-yellow);">Contact Specific Departments</h2>
            </div>
            
            <div class="grid-3">
                <div class="value-card" style="border-left-color: var(--sfc-yellow); background: rgba(255,255,255,0.08);">
                    <h3>Admissions Office</h3>
                    <p style="font-size: 0.9rem; color: var(--sfc-silver); margin-top: 0.5rem;">Email: admissions@stfrancisborgia.ac.ug<br>Phone: +256 754 465 536</p>
                </div>
                
                <div class="value-card" style="border-left-color: var(--sfc-yellow); background: rgba(255,255,255,0.08);">
                    <h3>Academics & DOS</h3>
                    <p style="font-size: 0.9rem; color: var(--sfc-silver); margin-top: 0.5rem;">Email: academics@stfrancisborgia.ac.ug<br>Phone: +256 772 622 612</p>
                </div>
                
                <div class="value-card" style="border-left-color: var(--sfc-yellow); background: rgba(255,255,255,0.08);">
                    <h3>Finance Department</h3>
                    <p style="font-size: 0.9rem; color: var(--sfc-silver); margin-top: 0.5rem;">Email: finance@stfrancisborgia.ac.ug<br>Phone: +256 772 622 612</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Message Sent!</h3>
            <p>Thank you for reaching out. A copy of your inquiry has been logged. Our administrative team will get back to you shortly.</p>
            <button class="btn btn-secondary" id="closeModalBtn">Close Window</button>
        </div>
    </div>
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
