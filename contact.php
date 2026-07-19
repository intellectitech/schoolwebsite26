<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/datastore.php';

$page_title = "Contact Us | " . SITE_NAME;
$active_page = 'contact';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $subject   = trim($_POST['subject'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    if ($full_name === '' || $email === '' || $message === '') {
        $errors[] = "Please fill in your name, email and message.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($errors)) {
        $messages = ds_read('messages');
        $messages[] = [
            'id' => ds_next_id($messages),
            'full_name' => $full_name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'submitted_at' => date('Y-m-d H:i:s'),
            'is_read' => false,
        ];
        if (ds_write('messages', $messages)) {
            $success = true;
        } else {
            $errors[] = "Sorry, something went wrong sending your message. Please try again or call us directly.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <h1>Contact Us</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / Contact</div>
  </div>
</div>

<section>
  <div class="container split">
    <div class="split-copy">
      <span class="eyebrow">Get In Touch</span>
      <h2>We'd Love to Hear From You</h2>
      <p>Whether you have a question about admissions, our academic programme, or anything else, our team is ready to help.</p>
      <ul class="footer-links" style="margin-top:24px;">
        <li style="margin-bottom:16px; color:var(--text);"><strong>Address:</strong><br><?php echo SITE_ADDRESS; ?><br><?php echo SITE_POBOX; ?></li>
        <li style="margin-bottom:16px; color:var(--text);"><strong>Phone:</strong><br><?php echo SITE_PHONE_1; ?> / <?php echo SITE_PHONE_2; ?></li>
        <li style="margin-bottom:16px; color:var(--text);"><strong>Email:</strong><br><?php echo SITE_EMAIL; ?></li>
         <li style="color:var(--text);"><strong>Instagram:</strong><br><a href="<?php echo SITE_INSTAGRAM; ?>" target="_blank" rel="noopener" style="color:var(--blue); font-weight:700;">Mbuya Parents' School</a></li>
      </ul>
      </ul>
    </div>
    <div class="form-card">
      <?php if ($success): ?>
        <div class="alert-success">Thank you for reaching out! We have received your message and will respond soon.</div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="form-note"><?php foreach ($errors as $err) { echo htmlspecialchars($err) . "<br>"; } ?></div>
      <?php endif; ?>
      <form action="contact.php" method="POST">
        <div class="form-group">
          <label for="full_name">Full Name *</label>
          <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="email">Email Address *</label>
          <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="message">Message *</label>
          <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="submit_contact" class="btn btn-primary" style="width:100%;">Send Message</button>
      </form>
    </div>
  </div>
</section>

<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Find Us</span>
      <h2>Our Location</h2>
      <p>Plot 22-25 Nadiope Lane, off Ismael Road, Mbuya II Parish, Nakawa Division, Kampala.</p>
    </div>
    <div class="split-art" style="max-width:900px; margin:0 auto;">
      <iframe
        src="https://www.google.com/maps?q=Mbuya%20Parents%20School%2C%20Nadiope%20Lane%2C%20Kampala&output=embed"
        width="100%" height="360" style="border:0; border-radius:10px;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade" title="Mbuya Parents' School Location">
      </iframe>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
