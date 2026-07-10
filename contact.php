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
.contact-hero {
    background: linear-gradient(135deg, #0d2617, #1a4d2e);
    color: #fff;
    padding: 60px 0;
    text-align: center;
}
.contact-hero h1 {
    color: #fff;
    font-size: 2.8rem;
}
.contact-hero p {
    color: rgba(255,255,255,0.8);
    max-width: 600px;
    margin: 15px auto 0;
}
.contact-section {
    padding: 60px 0;
}
.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
}
.contact-info {
    display: flex;
    flex-direction: column;
    gap: 25px;
}
.contact-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    align-items: flex-start;
    gap: 20px;
    transition: transform 0.3s;
}
.contact-card:hover {
    transform: translateX(5px);
}
.contact-card .icon {
    width: 50px;
    height: 50px;
    min-width: 50px;
    background: #1a4d2e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFD700;
    font-size: 1.2rem;
}
.contact-card .content h4 {
    color: #1a4d2e;
    margin-bottom: 5px;
}
.contact-card .content p {
    color: #666;
}
.contact-card .content a {
    color: #1a4d2e;
}
.contact-card .content a:hover {
    color: #FFD700;
}
.contact-form {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.contact-form .form-group {
    margin-bottom: 20px;
}
.contact-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #333;
    font-size: 0.9rem;
}
.contact-form label .required {
    color: #dc3545;
}
.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
    font-family: inherit;
}
.contact-form input:focus,
.contact-form textarea:focus {
    outline: none;
    border-color: #1a4d2e;
}
.contact-form textarea {
    min-height: 140px;
    resize: vertical;
}
.contact-form .btn-submit {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #FFD700, #f5c842);
    color: #1a4d2e;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}
.contact-form .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255,215,0,0.4);
}
.map-container {
    margin-top: 40px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.map-container iframe {
    width: 100%;
    height: 400px;
    border: none;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}
@media (max-width: 992px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
}
@media (max-width: 768px) {
    .contact-hero h1 {
        font-size: 2rem;
    }
    .contact-form {
        padding: 25px;
    }
    .contact-card {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
    .map-container iframe {
        height: 250px;
    }
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
                <h2 style="color:#1a4d2e;margin-bottom:20px;">Get in Touch</h2>
                <p style="color:#666;margin-bottom:30px;">
                    We'd love to hear from you. Reach out to us through any of the channels below or fill out the contact form.
                </p>
                <div class="contact-info">
                    <div class="contact-card">
                        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="content">
                            <h4>Visit Us</h4>
                            <p><?= clean(getSetting($pdo, 'school_address', 'P.O. Box 123, Kampala, Uganda')) ?></p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="icon"><i class="fas fa-phone"></i></div>
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
                <h2 style="color:#1a4d2e;margin-bottom:20px;">Send Us a Message</h2>
                
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