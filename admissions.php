<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/datastore.php';

$page_title = "Admissions | " . SITE_NAME;
$active_page = 'admissions';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    $parent_name   = trim($_POST['parent_name'] ?? '');
    $parent_email  = trim($_POST['parent_email'] ?? '');
    $parent_phone  = trim($_POST['parent_phone'] ?? '');
    $child_name    = trim($_POST['child_name'] ?? '');
    $desired_class = trim($_POST['desired_class'] ?? '');
    $message       = trim($_POST['message'] ?? '');

    if ($parent_name === '' || $parent_email === '' || $parent_phone === '' || $child_name === '' || $desired_class === '') {
        $errors[] = "Please fill in all required fields.";
    }
    if (!filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($errors)) {
        $inquiries = ds_read('admissions');
        $inquiries[] = [
            'id' => ds_next_id($inquiries),
            'parent_name' => $parent_name,
            'parent_email' => $parent_email,
            'parent_phone' => $parent_phone,
            'child_name' => $child_name,
            'desired_class' => $desired_class,
            'message' => $message,
            'submitted_at' => date('Y-m-d H:i:s'),
            'status' => 'new',
        ];
        if (ds_write('admissions', $inquiries)) {
            $success = true;
        } else {
            $errors[] = "Sorry, something went wrong saving your inquiry. Please try again or call us directly.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <h1>Admissions</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / Admissions</div>
  </div>
</div>

<section>
  <div class="container split">
    <div class="split-copy">
      <span class="eyebrow">Join Our Family</span>
      <h2>Admissions Information</h2>
      <p>Mbuya Parents' School welcomes applications for Kindergarten through Primary Seven, on both a Day and Boarding basis. We admit learners throughout the year, subject to space availability.</p>
      <ul class="checklist">
        <li>Kindergarten to Primary Seven (Day &amp; Boarding)</li>
        <li>Rolling admissions, subject to availability</li>
        <li>Entrance assessment for the relevant class</li>
        <li>Copy of previous school report (where applicable)</li>
        <li>Copy of the child's birth certificate</li>
        <li>Passport photographs of the child</li>
      </ul>
      <p>For the current fees structure and a campus tour, please contact our admissions office using the details on our <a href="contact.php" style="color:var(--blue); font-weight:700;">Contact page</a>.</p>
    </div>
      </svg>
    </div>
  </div>
</section>

<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Apply Online</span>
      <h2>Admission Inquiry Form</h2>
      <p>Fill in the form below and our admissions team will get back to you.</p>
    </div>

    <div class="form-card" style="max-width:760px; margin:0 auto;">
      <?php if ($success): ?>
        <div class="alert-success">Thank you! Your admission inquiry has been received. Our admissions team will contact you shortly.</div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="form-note">
          <?php foreach ($errors as $err) { echo htmlspecialchars($err) . "<br>"; } ?>
        </div>
      <?php endif; ?>

      <form action="admissions.php" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label for="parent_name">Parent / Guardian Name *</label>
            <input type="text" id="parent_name" name="parent_name" required value="<?php echo htmlspecialchars($_POST['parent_name'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="parent_phone">Phone Number *</label>
            <input type="text" id="parent_phone" name="parent_phone" required value="<?php echo htmlspecialchars($_POST['parent_phone'] ?? ''); ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="parent_email">Email Address *</label>
            <input type="email" id="parent_email" name="parent_email" required value="<?php echo htmlspecialchars($_POST['parent_email'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="child_name">Child's Name *</label>
            <input type="text" id="child_name" name="child_name" required value="<?php echo htmlspecialchars($_POST['child_name'] ?? ''); ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="desired_class">Desired Class *</label>
          <select id="desired_class" name="desired_class" required>
            <option value="">Select a class</option>
            <?php
            $classes = ['Kindergarten (Baby / Middle / Top Class)','Primary One','Primary Two','Primary Three','Primary Four','Primary Five','Primary Six','Primary Seven'];
            $selectedClass = $_POST['desired_class'] ?? '';
            foreach ($classes as $c) {
                $sel = ($selectedClass === $c) ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($c) . "\" $sel>" . htmlspecialchars($c) . "</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="message">Additional Message</label>
          <textarea id="message" name="message" rows="4" placeholder="Tell us anything else we should know..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="submit_inquiry" class="btn btn-primary" style="width:100%;">Submit Inquiry</button>
      </form>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
