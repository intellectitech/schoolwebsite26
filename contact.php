<?php
// 1. DATABASE CONNECTION
$host     = '127.0.0.1';
$db       = 'school_website_db';
$user     = 'root';
$pass     = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

// 2. INITIALIZE FORM VARIABLES
$messageStatus = '';
$statusClass   = '';

$name       = '';
$email      = '';
$phone      = '';
$subject    = '';
$msgContent = '';

// 3. PROCESS FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and sanitize inputs
    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $subject    = trim($_POST['subject'] ?? '');
    $msgContent = trim($_POST['message'] ?? '');

    // Validate Required Fields
    if (empty($name) || empty($email) || empty($subject) || empty($msgContent)) {
        $messageStatus = "Please fill in all required fields marked with *.";
        $statusClass   = "alert-danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageStatus = "Please enter a valid email address.";
        $statusClass   = "alert-danger";
    } else {
        try {
            // Get Client IP Address
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_address = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            }

            // Insert Record into `contact_messages` Table
            $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, is_read, replied_at, created_at) 
                    VALUES (:name, :email, :phone, :subject, :message, :ip_address, 0, NULL, NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name'       => $name,
                ':email'      => $email,
                ':phone'      => $phone,
                ':subject'    => $subject,
                ':message'    => $msgContent,
                ':ip_address' => $ip_address
            ]);

            // Set Success Response & Reset Form Inputs
            $messageStatus = "Thank you! Your message has been sent successfully.";
            $statusClass   = "alert-success";

            $name = $email = $phone = $subject = $msgContent = '';

        } catch (\PDOException $e) {
            $messageStatus = "An error occurred while saving your message. Please try again later.";
            $statusClass   = "alert-danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - St. Henry’s College Namugongo</title>

    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        /* Alert message styling */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .alert-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

    <!-- Unified Header Engine Container -->
    <div class="header-container" id="mainHeader">
        <div class="utility-bar">
            <div>
                <span>Call: <b>+256 700 380950</b></span>
                <span>Mail: <b>sthenryscollegenamugongo2@gmail.com</b></span>
            </div>
        </div>
        <header class="main-navbar">
            <div class="navbar-brand-pane">
                <img class="header-logo" src="assets/images/logoo.svg" alt="College Logo">
                <h2>St. Henry’s College Namugongo</h2>  
            </div>
        </header>
    </div>

    <!-- Minimal Overlay Panel Drawer Nav -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="app-sidebar" id="sidebarPanel">
        <button class="close-sidebar-btn" id="menuCloseBtn">&times;</button>
        <nav class="navigation-menu">
            <img class="sidebar-logo" src="assets/images/logoo.svg" alt="College Logo">
            <a href="index.html">Overview Home</a>
            <a href="about.html">About us</a>
            <a href="admissions.html">Admissions Panel</a>
            <a href="contact.php" class="active">Contact us</a>
        </nav>
    </aside> 

    <!-- MAIN CONTENT CARDS WRAPPER -->
    <div class="cards-wrapper">

        <!-- Info Card (Left Side) -->
        <div class="info-card">
            <div>
                <div class="info-header">
                    <h2>Contact Information</h2>
                    <p>Find us on campus or reach out through our official digital channels.</p>
                </div>

                <div class="info-body">
                    <!-- Contact Item 1 -->
                    <div class="info-item">
                        <div class="icon-wrapper">
                            <i class='bx bx-map-pin'></i>
                        </div>
                        <div>
                            <h4>Our College</h4>
                            <p>St. Henry’s College Namugongo</p>
                        </div>
                    </div>

                    <!-- Contact Item 2 -->
                    <div class="info-item">
                        <div class="icon-wrapper">
                            <i class='bx bx-phone'></i>
                        </div>
                        <div>
                            <h4>Call Center</h4>
                            <p>+256 700 380950</p>
                        </div>
                    </div>

                    <!-- Contact Item 3 -->
                    <div class="info-item">
                        <div class="icon-wrapper">
                            <i class='bx bx-envelope'></i>
                        </div>
                        <div>
                            <h4>Email Desk</h4>
                            <p>sthenryscollegenamugongo2@gmail.com</p>
                        </div>
                    </div>
                </div>

                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.6587635292333!2d32.65171717424364!3d0.3837130639908483!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177db9907f1396a5%3A0x6280436d4bc3f139!2sSt.%20Henry&#39;s%20College%20Namugongo!5e0!3m2!1sen!2sug!4v1710000000000!5m2!1sen!2sug" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <!-- Social Handles Section -->
            <div class="social-section">
                <h4>Connect With Us</h4>
                <div class="social-grid">
                    <a href="https://x.com" class="social-link" target="_blank" aria-label="X">
                        <i class='bx bxl-twitter'></i>
                    </a>
                    <a href="https://facebook.com" class="social-link" target="_blank" aria-label="Facebook">
                        <i class='bx bxl-facebook'></i>
                    </a>
                    <a href="https://instagram.com" class="social-link" target="_blank" aria-label="Instagram">
                        <i class='bx bxl-instagram'></i>
                    </a>
                    <a href="https://linkedin.com" class="social-link" target="_blank" aria-label="LinkedIn">
                        <i class='bx bxl-linkedin'></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Contact Card (Right Side Form) -->
        <div class="contact-card">
            <div class="contact-header">
                <h2>Get in Touch</h2>
                <p>Have questions? Drop us a message and we'll reply as soon as possible.</p>
            </div>

            <?php if (!empty($messageStatus)): ?>
                <div class="alert <?= $statusClass ?>">
                    <?= htmlspecialchars($messageStatus) ?>
                </div>
            <?php endif; ?>

            <form action="contact.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" placeholder="your name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" placeholder="email" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>" placeholder="+256...">
                    </div>

                    <div class="form-group full-width">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($subject ?? '') ?>" placeholder="What is this regarding?" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" class="form-control" placeholder="Write your details here..." required><?= htmlspecialchars($msgContent ?? '') ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>

    </div>
    <br> <br>





    <footer class="shcn-modern-footer">
        <div class="footer-top-branding">
            <div class="branding-wrapper">
                <div class="college-logo-badge"></div>
                <img style="width: 60px;height: 60px;border-radius: 500%;box-shadow: 0 4px 6px -1px rgb(0, 0, 0);" src="assets/images/logoo.svg" alt="">
                <div class="college-title-block">
                    <h2>ST. HENRY’S COLLEGE NAMUGONGO</h2>
                    <p class="motto-tagline">"FOR GREATER HORIZONS"</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert <?= htmlspecialchars($statusClass) ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="newsletter-inline-bo">
                        <input type="text" id="name" placeholder="Name" aria-label="Name subscription" name="name"><br>
                        <input type="email" id="email" placeholder="(email) Subscribe to College Newsletter" aria-label="Email subscription" name="email"><br>
                        <button type="submit" class="btn-submit">Join</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer-grid-body">
            <div class="grid-column">
                <h3>Academic Portals</h3>
                <nav class="nav-links-stack">
                    <a href="#curriculum">O & A Level Curriculum</a>
                    <a href="#admissions">Admissions & Fees Structure</a>
                    <a href="#e-learning">Digital Student Portal</a>
                    <a href="#library">E-Library Resources</a>
                </nav>
            </div>

            <div class="grid-column">
                <h3>School Life</h3>
                <nav class="nav-links-stack">
                    <a href="#houses">Residential Houses & Dorms</a>
                    <a href="#sports">Sports & Co-curricular Activities</a>
                    <a href="#clubs">Clubs, Music & Drama Societies</a>
                    <a href="#chapel">Spiritual Life & Chaplaincy</a>
                </nav>
            </div>

            <div class="grid-column">
                <h3>The Community</h3>
                <nav class="nav-links-stack">
                    <a href="gallery.php">Gallery</a>
                    <a href="#pta">Parents & Teachers Association (PTA)</a>
                    <a href="news.php">Latest College News & Announcements</a>
                    <a href="gallery.php">Campus Photo Records</a>
                </nav>
            </div>

            <div class="grid-column contact-card-column">
                <div class="contact-card-box">
                    <h3>Reach Us</h3>
                    <p><strong>Location:</strong> Namugongo, Wakiso District, Uganda</p>
                    <p><strong>Hotlines:</strong> +256 414 000 000 | +256 701 000 000</p>
                    <p><strong>Email:</strong> sthenryscollegenamugongo2@gmail.com</p>
                    <div class="social-icon-row">
                        <a href="#" aria-label="X Platform">𝕏</a>
                        <a href="#" aria-label="Facebook Platform">f</a>
                        <a href="#" aria-label="YouTube Channel">▶</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-base-legal">
            <div class="legal-container">
                <p>&copy; 2026 St. Henry’s College Namugongo. All Rights Reserved.</p>
                <div class="legal-sub-links">
                    <a href="#privacy">Privacy Policy</a>
                    <a href="#terms">Terms & services</a>
                </div>
            </div>
        </div>
    </footer>













<script>
document.addEventListener('DOMContentLoaded', () => {

    // -------------------------------------------------------------
    // 1. MOBILE SIDEBAR & NAVBAR SCROLL HANDLERS
    // -------------------------------------------------------------
    const menuOpenBtn = document.getElementById('menuOpenBtn');
    const menuCloseBtn = document.getElementById('menuCloseBtn');
    const bodyElement = document.body;
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (menuOpenBtn) menuOpenBtn.addEventListener('click', () => bodyElement.classList.add('sidebar-open'));
    if (menuCloseBtn) menuCloseBtn.addEventListener('click', () => bodyElement.classList.remove('sidebar-open'));
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', () => bodyElement.classList.remove('sidebar-open'));

    const mainHeader = document.getElementById('mainHeader');
    window.addEventListener('scroll', () => {
        if (mainHeader) {
            if (window.scrollY > 50) {
                mainHeader.classList.add('scrolled');
            } else {
                mainHeader.classList.remove('scrolled');
            }
        }
    });

    // -------------------------------------------------------------
    // 2. SCROLL REVEAL ANIMATIONS FOR CONTACT CARDS & SECTIONS
    // -------------------------------------------------------------
    const revealElements = document.querySelectorAll('.info-card, .contact-card, .map-container, .social-section, .shcn-modern-footer');
    
    revealElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(25px)';
        el.style.transition = 'opacity 0.6s cubic-bezier(0.165, 0.84, 0.44, 1), transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1)';
    });

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(el => revealObserver.observe(el));

    // -------------------------------------------------------------
    // 3. CURSOR HOVER 3D TILT EFFECT FOR CONTACT & INFO CARDS
    // -------------------------------------------------------------
    const tiltCards = document.querySelectorAll('.info-card, .contact-card');
    tiltCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -3;
            const rotateY = ((x - centerX) / centerX) * 3;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-3px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
        });
    });

    // -------------------------------------------------------------
    // 4. INTERACTIVE FORM ENHANCEMENTS & FEEDBACK
    // -------------------------------------------------------------
    const messageArea = document.getElementById('message');
    if (messageArea) {
        // Dynamic Character Counter for Message Area
        const charCounter = document.createElement('small');
        charCounter.style.cssText = 'display:block; text-align:right; color:#64748b; font-size:11px; margin-top:4px;';
        charCounter.innerText = `${messageArea.value.length} characters`;
        messageArea.parentNode.appendChild(charCounter);

        messageArea.addEventListener('input', () => {
            charCounter.innerText = `${messageArea.value.length} characters`;
        });
    }

    // Add loading indicator on form submit button
    const contactForm = document.querySelector('.contact-card form');
    if (contactForm) {
        contactForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('.btn-submit');
            if (submitBtn) {
                submitBtn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Sending...";
                submitBtn.style.opacity = '0.8';
                submitBtn.style.pointerEvents = 'none';
            }
        });
    }
});
</script>

<style>
/* Smooth focus styling for form inputs */
.form-control:focus {
    border-color: #1d4ed8 !important;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15) !important;
    outline: none;
    transition: all 0.2s ease;
}

/* 3D Transform Smoothness */
.info-card, .contact-card {
    transition: transform 0.2s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.2s ease;
    will-change: transform;
}
</style>










</body>
</html>