<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Admission';
$pageDescription = 'Apply for admission to Namugongo Model Primary School. Complete the online registration form and our team will contact you.';
require_once __DIR__ . '/includes/header.php';
?>
        <section class="page-hero">
            <div class="container">
                <h1>Admission Application</h1>
                <p>Parents can now register and apply for admission directly through this website. Please complete the form below and our admissions team will contact you shortly.</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="grid" style="margin-bottom:34px;">
                    <div class="card reveal">
                        <h3>1. Apply Online</h3>
                        <p>Fill in the registration form with your child's and your details.</p>
                    </div>
                    <div class="card reveal">
                        <h3>2. We Review</h3>
                        <p>Our admissions team reviews your application within 2 working days.</p>
                    </div>
                    <div class="card reveal">
                        <h3>3. Confirmation</h3>
                        <p>We contact you by phone or email to confirm placement and next steps.</p>
                    </div>
                </div>

                <div class="form-card reveal">
                    <h3>Parent / Guardian Registration Form</h3>
                    <form action="submit_admission.php" method="POST" id="admissionForm">
                        <label for="parent_name">Parent/Guardian Name</label>
                        <input type="text" id="parent_name" name="parent_name" required>

                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>

                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>

                        <label for="child_name">Child's Name</label>
                        <input type="text" id="child_name" name="child_name" required>

                        <label for="child_age">Child's Age</label>
                        <input type="number" id="child_age" name="child_age" min="3" max="16" required>

                        <label for="grade">Preferred Grade/Class</label>
                        <input type="text" id="grade" name="grade" placeholder="e.g. Primary 1" required>

                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Tell us about your child or any special requests." required></textarea>

                        <button type="submit" class="btn">Submit Admission Request</button>
                    </form>
                </div>

                <div class="highlight-panel reveal">
                    <h3>Need Help With Your Application?</h3>
                    <p>Chat with Nexa, our AI school assistant, in the bottom-right corner — or call us on <?php echo h(SITE_PHONE); ?> for guidance on the admission process.</p>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
