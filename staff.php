<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


$deptHeads = $pdo->query(
  'SELECT * FROM vw_staff_directory WHERE is_active = 1 ORDER BY is_management DESC, sort_order ASC'
)->fetchAll();

// Split into leadership (management) and general teaching staff so each
// section on the page pulls real records - and real photos - from the database.
$leadership = [];
$teachingStaff = [];
foreach ($deptHeads as $person) {
  if ($person['is_management']) {
    $leadership[] = $person;
  } else {
    $teachingStaff[] = $person;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php renderSeoTags('staff', null, null, 'staff'); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <?php include 'includes/head-meta.php'; ?>
</head>

<body>

  <?php include 'includes/header.php'; ?>


  <!-- ============================================================
     PAGE HERO
     ============================================================ -->
  <section class="page-hero">
    <div class="container reveal">
      <p class="breadcrumb"><a href="index.php">Home</a> / Staff</p>
      <p class="eyebrow">Our People</p>
      <h1>The teachers behind every lesson</h1>
      <p class="lead">Uganda Martyrs Primary School is its people as much as its buildings. Meet the staff who plan the
        lessons, mark the books, coach the teams and hold the school together term after term.</p>
    </div>
    <div class="hill-divider" style="color:var(--ink)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,44 C240,4 480,0 720,18 C960,36 1200,40 1440,10 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
  </section>

  <!-- ============================================================
     SCHOOL LEADERSHIP
     ============================================================ -->
  <section class="staff-leadership-section">
    <div class="container reveal">
      <p class="eyebrow">School Leadership</p>
      <h2>Senior management team</h2>
      <div class="staff-lead-grid">
        <?php if ($leadership): ?>
          <?php foreach ($leadership as $person): ?>
            <?php $hasPhoto = $person['photo'] && file_exists(__DIR__ . '/' . $person['photo']); ?>
            <div class="staff-card">
              <div class="staff-card-photo" aria-hidden="<?= $hasPhoto ? 'false' : 'true' ?>">
                <?php if ($hasPhoto): ?>
                  <img src="<?= htmlspecialchars($person['photo']) ?>" alt="<?= htmlspecialchars($person['full_name']) ?>"
                    loading="lazy">
                <?php else: ?>
                  <?= staffPlaceholderPhoto() ?>
                <?php endif; ?>
              </div>
              <div class="staff-card-body">
                <h3><?= htmlspecialchars($person['full_name']) ?></h3>
                <span
                  class="staff-role-label"><?= htmlspecialchars($person['role']) ?><?= $person['department_name'] ? ' · ' . htmlspecialchars($person['department_name']) : '' ?></span>
                <?php if ($person['bio']): ?>
                  <p class="staff-bio"><?= htmlspecialchars($person['bio']) ?></p><?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="staff-empty">Leadership team profiles will appear here once they are added from the admin dashboard.</p>
        <?php endif; ?>

      </div>
    </div>
  </section>

  <!-- ============================================================
     TEACHING STAFF
     ============================================================ -->
  <section class="staff-teachers-section">
    <div class="hill-divider" style="color:var(--leaf-tint)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow">Class Teachers</p>
      <h2>Teaching staff</h2>
      <div class="staff-grid-4">

        <?php if ($teachingStaff): ?>
          <?php foreach ($teachingStaff as $person): ?>
            <?php $hasPhoto = $person['photo'] && file_exists(__DIR__ . '/' . $person['photo']); ?>
            <?php $subjectList = $person['subjects'] ? array_filter(array_map('trim', explode(',', $person['subjects']))) : []; ?>
            <div class="staff-card">
              <div class="staff-card-photo" aria-hidden="<?= $hasPhoto ? 'false' : 'true' ?>">
                <?php if ($hasPhoto): ?>
                  <img src="<?= htmlspecialchars($person['photo']) ?>" alt="<?= htmlspecialchars($person['full_name']) ?>"
                    loading="lazy">
                <?php else: ?>
                  <?= staffPlaceholderPhoto() ?>
                <?php endif; ?>
              </div>
              <div class="staff-card-body">
                <h3><?= htmlspecialchars($person['full_name']) ?></h3>
                <span
                  class="staff-role-label"><?= htmlspecialchars($person['role']) ?><?= $person['department_name'] ? ' · ' . htmlspecialchars($person['department_name']) : '' ?></span>
                <?php if ($subjectList): ?>
                  <div><?php foreach ($subjectList as $subj): ?><span
                        class="staff-subject-tag"><?= htmlspecialchars($subj) ?></span><?php endforeach; ?></div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="staff-empty">Teaching staff profiles will appear here once they are added from the admin dashboard.</p>
        <?php endif; ?>

      </div>
    </div>
  </section>

  <!-- ============================================================
     CLASS TEACHERS
     ============================================================ -->
  <section class="staff-support-section">
    <div class="hill-divider" style="color:var(--cream)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow">Behind the Scenes</p>
      <h2>Support staff</h2>
      <div class="support-grid">

        <div class="support-card">
          <h3>School Bursar</h3>
          <span class="staff-role-label">Finance & Administration</span>
        </div>
        <div class="support-card">
          <h3>School Nurse</h3>
          <span class="staff-role-label">Health & First Aid</span>
        </div>
        <div class="support-card">
          <h3>Head Cook</h3>
          <span class="staff-role-label">School Meals</span>
        </div>
        <div class="support-card">
          <h3>Librarian</h3>
          <span class="staff-role-label">Library & Reading Resources</span>
        </div>
        <div class="support-card">
          <h3>Sports Coach</h3>
          <span class="staff-role-label">Physical Education & Athletics</span>
        </div>
        <div class="support-card">
          <h3>Gate & Compound</h3>
          <span class="staff-role-label">Security & Grounds</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
     VACANCIES
     ============================================================ -->
  <section class="vacancies-section">
    <div class="hill-divider" style="color:var(--cream)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,10 C240,42 480,40 720,20 C960,0 1200,4 1440,24 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow eyebrow-light">Work With Us</p>
      <h2>Join our teaching team</h2>
      <p>We hire teachers who believe that patience, subject knowledge and genuine care for children are more important
        than any credential. If that is you, we would like to hear from you.</p>
      <div class="vacancies-grid">
        <div class="vacancy-card">
          <h3>Class Teacher — Primary Three</h3>
          <p>We are seeking a qualified and experienced primary teacher for Primary Three. The successful candidate will
            teach Literacy, Numeracy, Science and SST.</p>
          <span class="vacancy-type">Full-time · Permanent · Term One 2027 start</span>
        </div>
        <div class="vacancy-card">
          <h3>Primary Seven Mathematics & Science Specialist</h3>
          <p>An opportunity for a strong upper-primary teacher with a track record of excellent PLE results in
            Mathematics and Science.</p>
          <span class="vacancy-type">Full-time · Permanent · Immediate start</span>
        </div>
        <div class="vacancy-card">
          <h3>School Nurse (Part-time)</h3>
          <p>A registered nurse to staff the sick bay, Monday to Friday during school hours. Experience with children
            preferred.</p>
          <span class="vacancy-type">Part-time · Renewable contract</span>
        </div>
        <div class="vacancy-card">
          <h3>Volunteer & Teaching Practice Positions</h3>
          <p>We welcome student-teachers from accredited institutions for teaching practice placements, subject to
            availability and prior arrangement.</p>
          <span class="vacancy-type">Voluntary / Teaching Practice</span>
        </div>
      </div>
      <div style="margin-top:36px">
        <a href="contact" class="btn btn-on-soil">Send your CV to the school office →</a>
      </div>
    </div>
  </section>

  <!-- ============================================================
     CTA BAND
     ============================================================ -->
  <section class="cta-band">
    <div class="hill-divider" style="color:var(--soil-deep)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <h2>Come and meet the team in person</h2>
      <p>The best impression of any school is the one you get by walking through the gate on a school morning and
        watching the people who run it.</p>
      <div class="cta-actions">
        <a href="admissions" class="btn btn-primary">Begin Admissions</a>
        <a href="contact" class="btn btn-on-dark">Plan a Visit</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>

</html>