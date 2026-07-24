<?php
// contact.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Contact Us - ' . getSetting($pdo, 'school_name', 'School');

// Handle contact form submission
$contactSuccess = false;
$contactError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $contactError = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactError = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, phone, subject, message, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $name, $email, $phone, $subject, $message, $_SERVER['REMOTE_ADDR']
            ]);
            $contactSuccess = true;
        } catch (Exception $e) {
            $contactError = 'An error occurred. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<style>
/* ============================================
   CONTACT PAGE SPECIFIC STYLES (Edugrade UI)
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
.contact-hero {
    background: linear-gradient(135deg, var(--navy-dark) 0%, #2d2d54 100%);
    position: relative;
    color: var(--white);
    padding: 80px 0 60px;
    text-align: center;
    overflow: hidden;
}

.contact-hero .container {
    position: relative;
    z-index: 1;
}

.contact-hero h1 {
    color: var(--white);
    font-size: 3.2rem;
    font-weight: 700;
    letter-spacing: -1px;
    margin-bottom: 15px;
}

.contact-hero h1 i {
    color: var(--primary-red);
    margin-right: 12px;
}

.contact-hero p {
    color: rgba(255,255,255,0.85);
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.15rem;
    line-height: 1.7;
}

/* --- Section Wrappers --- */
.contact-section {
    padding: 80px 0;
}

/* --- Contact Grid --- */
.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
}

/* --- Section Title (Left Aligned) --- */
.contact-section .section-title {
    text-align: left;
    margin-bottom: 30px;
}
.contact-section .section-title h2 {
    display: inline-block;
    position: relative;
    padding-bottom: 12px;
    color: var(--navy-dark);
}
.contact-section .section-title h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-red);
}
.contact-section .section-title p {
    color: var(--text-gray);
    max-width: 500px;
    margin-top: 10px;
}

/* --- Contact Info Cards --- */
.contact-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.contact-card {
    background: var(--white);
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: var(--shadow-card);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: var(--transition);
    border-left: 4px solid var(--primary-red);
}
.contact-card:hover {
    transform: translateX(6px);
    box-shadow: var(--shadow-hover);
}
.contact-card .icon {
    width: 50px;
    height: 50px;
    min-width: 50px;
    background: var(--navy-dark);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.2rem;
    transition: var(--transition);
}
.contact-card:hover .icon {
    background: var(--primary-red);
}
.contact-card .content h4 {
    color: var(--navy-dark);
    margin-bottom: 4px;
    font-size: 1.05rem;
}
.contact-card .content p {
    color: var(--text-gray);
    font-size: 0.95rem;
}
.contact-card .content a {
    color: var(--navy-dark);
    font-weight: 500;
    transition: var(--transition);
}
.contact-card .content a:hover {
    color: var(--primary-red);
}

/* --- Contact Form (Modern UI) --- */
.contact-form {
    background: var(--white);
    padding: 40px;
    border-radius: 8px;
    box-shadow: var(--shadow-card);
}
.contact-form .form-group {
    margin-bottom: 22px;
}
.contact-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--navy-dark);
    font-size: 0.9rem;
}
.contact-form label .required {
    color: var(--primary-red);
}
.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s;
    font-family: inherit;
    background: var(--light-gray-bg);
    color: var(--navy-dark);
}
.contact-form input:focus,
.contact-form textarea:focus {
    outline: none;
    border-color: var(--primary-red);
    background: var(--white);
}
.contact-form textarea {
    min-height: 140px;
    resize: vertical;
}
.contact-form .btn-submit {
    width: 100%;
    padding: 16px;
    background: var(--primary-red);
    color: var(--white);
    border: none;
    border-radius: 6px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
}
.contact-form .btn-submit:hover {
    background: var(--primary-dark-red);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(233, 30, 99, 0.4);
}

/* --- Map --- */
.map-container {
    margin-top: 50px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
    border: 2px solid var(--light-gray-bg);
}
.map-container iframe {
    width: 100%;
    height: 400px;
    border: none;
}

/* --- Alerts --- */
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 18px 25px;
    border-radius: 6px;
    margin-bottom: 30px;
    border-left: 4px solid #28a745;
    font-weight: 500;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    padding: 18px 25px;
    border-radius: 6px;
    margin-bottom: 30px;
    border-left: 4px solid #dc3545;
    font-weight: 500;
}

/* --- Responsive --- */
@media (max-width: 992px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

@media (max-width: 768px) {
    .contact-hero { padding: 60px 0; }
    .contact-hero h1 { font-size: 2.2rem; }
    .contact-hero p { font-size: 1rem; }
    .contact-form { padding: 25px; }
    .contact-card { padding: 20px; }
    .map-container iframe { height: 250px; }
}

@media (max-width: 576px) {
    .contact-hero h1 { font-size: 1.8rem; }
    .contact-card { flex-direction: column; text-align: center; align-items: center; }
}
</style>

<!-- Hero -->
<section class="contact-hero">
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Contact Us</h1>
        <p>Get in touch with <?= clean(getSetting($pdo, 'school_name', 'our school')) ?></p>
    </div>
</section>

<!-- Contact -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Info -->
            <div>
                <div class="section-title">
                    <h2>Get in Touch</h2>
                    <p>We'd love to hear from you. Reach out to us through any of the channels below.</p>
                </div>
                <div class="contact-info">
                    <div class="contact-card">
                        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="content">
                            <h4>Visit Us</h4>
                            <p><?= clean(getSetting($pdo, 'school_address', 'P.O. Box 123, Kampala, Uganda')) ?></p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="content">
                            <h4>Call Us</h4>
                            <p><a href="tel:<?= clean(getSetting($pdo, 'school_phone', '+256700123456')) ?>">
                                <?= clean(getSetting($pdo, 'school_phone', '+256-700-123456')) ?>
                            </a></p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="icon"><i class="fas fa-envelope"></i></div>
                        <div class="content">
                            <h4>Email Us</h4>
                            <p><a href="mailto:<?= clean(getSetting($pdo, 'school_email', 'info@school.ug')) ?>">
                                <?= clean(getSetting($pdo, 'school_email', 'info@school.ug')) ?>
                            </a></p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <div class="content">
                            <h4>Office Hours</h4>
                            <p>Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 1:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div>
                <div class="section-title">
                    <h2>Send Us a Message</h2>
                    <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                </div>
                
                <?php if ($contactSuccess): ?>
                    <div class="alert-success">
                        <i class="fas fa-check-circle"></i> Thank you! Your message has been sent. We'll get back to you soon.
                    </div>
                <?php endif; ?>

                <?php if ($contactError): ?>
                    <div class="alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= clean($contactError) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="contact-form">
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Your full name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="your@email.com">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="e.g. 0700 123456">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject <span class="required">*</span></label>
                        <input type="text" id="subject" name="subject" required placeholder="What is this about?">
                    </div>
                    <div class="form-group">
                        <label for="message">Message <span class="required">*</span></label>
                        <textarea id="message" name="message" required placeholder="Your message..."></textarea>
                    </div>
                    <button type="submit" name="submit_contact" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>

        <!-- Map -->
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.745596235677!2d32.5829866!3d0.31320359999999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177dbbf6936e5a57%3A0x39324911ffc3af9e!2sKampala%2C%20Uganda!5e0!3m2!1sen!2sus!4v1699999999999!5m2!1sen!2sus" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>