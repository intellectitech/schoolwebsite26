<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/datastore.php';

$page_title = "About Us | " . SITE_NAME;
$active_page = 'about';

$staff = ds_read('staff');
usort($staff, function ($a, $b) { return ($a['display_order'] ?? 0) - ($b['display_order'] ?? 0); });

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <h1>About Mbuya Parents' School</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / About Us</div>
  </div>
</div>

<section>
  <div class="container split">
    <div class="split-copy">
      <span class="eyebrow">Our Story</span>
      <h2>A Top Pre-Primary &amp; Primary School in Kampala</h2>
      <p>Mbuya Parents' School is a mixed day and boarding Primary and Pre-primary school located at Plot 22-25 Nadiope Lane, off Ismael Road, Mbuya II Parish Zone 1, Nakawa Division, Kampala District. The school boasts state-of-the-art infrastructure and facilities including conducive classrooms, an ICT Laboratory, a Library, two swimming pools, and other sports facilities, all within a spacious and conducive environment that favours academic excellence.</p>
      <p>This is complemented by a team of professional, highly motivated staff who work to offer quality education to our learners. We pride ourselves in providing holistic and practical education that enables pupils to develop their unique, God-given talents and abilities alongside excellent academic grades.</p>
    </div>
    <div class="split-art">
    <svg viewBox="0 0 400 320" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="320" rx="16" fill="#ffffff"/>

    <image 
      href="assets/images/school.jpg"
      x="20"
      y="20"
      width="360"
      height="280"
      preserveAspectRatio="xMidYMid slice"
      clip-path="inset(0 round 16px)"
    />
  </svg>
</div>
  </div>
</section>

<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Head Teacher's Message</span>
      <h2>Educating for Excellence and Character</h2>
    </div>
    <div class="message-block">
      I bring you all warm greetings from the Mbuya Parents' School community, and I thank you for visiting our website. The global icon Nelson Mandela once remarked that "Education is the Most Powerful Weapon One Can Use to Change the World." It is with similar conviction that Mbuya Parents' School devotedly and professionally nurtures all its pupils into virtuous and competent persons who will provide transformative leadership to our country and the world. We employ a holistic education model, with a curriculum that emphasises the development of each learner's unique talents and abilities &mdash; preparing them to thrive in today's fast-changing world.
    </div>
    <p style="text-align:center; color:var(--text-light);">With a proven track record of excellence, our learners have consistently stood out as top performers in the Primary Leaving Examinations (PLE), not only in Kampala District but across the country &mdash; going on to excel in secondary schools and tertiary institutions.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Our Values</span>
      <h2>What We Stand For</h2>
    </div>
    <div class="grid grid-4">
      <div class="card"><div class="card-body"><h3>Excellence</h3><p>Consistently outstanding results at every level, from Kindergarten to Primary Seven.</p></div></div>
      <div class="card"><div class="card-body"><h3>Discipline</h3><p>Instilling hard work, discipline and confidence in every learner.</p></div></div>
      <div class="card"><div class="card-body"><h3>Creativity</h3><p>Nurturing each child's unique, God-given talents and abilities.</p></div></div>
      <div class="card"><div class="card-body"><h3>Holistic Growth</h3><p>Practical, well-rounded education preparing pupils for a changing world.</p></div></div>
    </div>
  </div>
</section>

<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Our Team</span>
      <h2>School Leadership</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($staff as $member): ?>
      <div class="card">
        <img src="<?php echo htmlspecialchars($member['photo_path'] ?? 'assets/images/staff/headteacher.svg'); ?>" alt="<?php echo htmlspecialchars($member['full_name']); ?>" class="card-img" style="aspect-ratio:1/1;">
        <div class="card-body">
          <h3><?php echo htmlspecialchars($member['full_name']); ?></h3>
          <p style="color:var(--blue); font-weight:700; font-size:0.85rem; text-transform:uppercase;"><?php echo htmlspecialchars($member['role_title']); ?></p>
          <p><?php echo htmlspecialchars($member['bio']); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section>
  <div class="cta-banner">
    <h2>Come and See Mbuya Parents' School for Yourself</h2>
    <p>We welcome you to visit our campus and experience our learning environment firsthand.</p>
    <a href="contact.php" class="btn btn-primary">Get In Touch</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
