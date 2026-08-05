<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with Namugongo Model Primary School for admissions, school visits or general enquiries.';
require_once __DIR__ . '/includes/header.php';
?>
        <section class="page-hero">
            <div class="container">
                <h1>Contact Us</h1>
                <p>Have a question or want to book a school visit? Send us a message and our admissions team will respond promptly.</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="grid">
                    <div class="card contact-card reveal">
                        <h3>Contact Information</h3>
                        <p><strong>Phone:</strong> <?php echo h(SITE_PHONE); ?></p>
                        <p><strong>Email:</strong> <?php echo h(SITE_EMAIL); ?></p>
                        <p><strong>Address:</strong> <?php echo h(SITE_ADDRESS); ?></p>
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

                <div class="highlight-panel reveal">
                    <h3>We are ready to hear from you</h3>
                    <p>Reach out for admissions, school visits, or general information about our programs and activities.</p>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
