<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


$deptHeads = $pdo->query(
  'SELECT * FROM vw_staff_directory WHERE is_active = 1 ORDER BY is_management DESC, sort_order ASC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Staff · Uganda Martyrs Primary School, Namugongo</title>
  <meta name="description"
    content="Meet the teachers and staff of Uganda Martyrs Primary School, Namugongo — the people who make the school what it is.">
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

        <div class="staff-card">
          <div class="staff-card-photo" aria-hidden="true">
            <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
              <rect width="400" height="200" fill="#16233D" />
              <circle cx="200" cy="72" r="48" fill="#2A3A5C" />
              <circle cx="200" cy="62" r="28" fill="#F3E6D2" opacity="0.5" />
              <path d="M120,170 C120,128 156,104 200,104 C244,104 280,128 280,170" fill="#F3E6D2" opacity="0.35" />
              <circle cx="200" cy="62" r="16" fill="#A6402E" opacity="0.4" />
            </svg>
          </div>
          <div class="staff-card-body">
            <h3>Head Teacher</h3>
            <span class="staff-role-label">School Leadership</span>
            <p class="staff-bio">Oversees the school's academic direction, pastoral care, staff development and
              relationship with the parish, parents and Diocese of Kampala. <em>Name to be added by school office.</em>
            </p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-card-photo" aria-hidden="true">
            <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
              <rect width="400" height="200" fill="#2F6B4F" />
              <circle cx="200" cy="72" r="48" fill="#204B37" />
              <circle cx="200" cy="62" r="28" fill="#E9F1E7" opacity="0.5" />
              <path d="M120,170 C120,128 156,104 200,104 C244,104 280,128 280,170" fill="#E9F1E7" opacity="0.35" />
              <circle cx="200" cy="62" r="16" fill="#F2B705" opacity="0.5" />
            </svg>
          </div>
          <div class="staff-card-body">
            <h3>Deputy Head Teacher — Academics</h3>
            <span class="staff-role-label">Curriculum & Examinations</span>
            <p class="staff-bio">Manages the timetable, streaming, exam preparation and academic standards from Primary
              One to Seven. Coordinates the PLE programme and UNEB registration.</p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-card-photo" aria-hidden="true">
            <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
              <rect width="400" height="200" fill="#A6402E" />
              <circle cx="200" cy="72" r="48" fill="#832F21" />
              <circle cx="200" cy="62" r="28" fill="#FBF3E6" opacity="0.4" />
              <path d="M120,170 C120,128 156,104 200,104 C244,104 280,128 280,170" fill="#FBF3E6" opacity="0.3" />
              <circle cx="200" cy="62" r="16" fill="#F2B705" opacity="0.5" />
            </svg>
          </div>
          <div class="staff-card-body">
            <h3>Deputy Head Teacher — Welfare</h3>
            <span class="staff-role-label">Pupil Welfare & Discipline</span>
            <p class="staff-bio">Looks after pupil wellbeing, attendance, discipline and the day-to-day running of the
              compound. First point of contact for welfare concerns from parents.</p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-card-photo" aria-hidden="true">
            <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
              <rect width="400" height="200" fill="#F2B705" />
              <circle cx="200" cy="72" r="48" fill="#F8D25C" />
              <circle cx="200" cy="62" r="28" fill="#16233D" opacity="0.25" />
              <path d="M120,170 C120,128 156,104 200,104 C244,104 280,128 280,170" fill="#16233D" opacity="0.2" />
              <circle cx="200" cy="62" r="16" fill="#A6402E" opacity="0.4" />
            </svg>
          </div>
          <div class="staff-card-body">
            <h3>Director of Studies</h3>
            <span class="staff-role-label">Lesson Planning & Teacher Development</span>
            <p class="staff-bio">Coordinates lesson planning, teacher appraisals and in-service training across all
              subjects and grades. Ensures fidelity to the NCDC curriculum.</p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-card-photo" aria-hidden="true">
            <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
              <rect width="400" height="200" fill="#204B37" />
              <circle cx="200" cy="72" r="48" fill="#2F6B4F" />
              <circle cx="200" cy="62" r="28" fill="#E9F1E7" opacity="0.4" />
              <path d="M120,170 C120,128 156,104 200,104 C244,104 280,128 280,170" fill="#E9F1E7" opacity="0.3" />
              <circle cx="200" cy="62" r="16" fill="#F2B705" opacity="0.5" />
            </svg>
          </div>
          <div class="staff-card-body">
            <h3>Senior Woman Teacher</h3>
            <span class="staff-role-label">Pastoral Care — Girls</span>
            <p class="staff-bio">First point of contact for female pupils on welfare, guidance and counselling matters.
              Coordinates girls' activities and liaises with parents on sensitive issues.</p>
          </div>
        </div>

        <div class="staff-card">
          <div class="staff-card-photo" aria-hidden="true">
            <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
              <rect width="400" height="200" fill="#2A3A5C" />
              <circle cx="200" cy="72" r="48" fill="#16233D" />
              <circle cx="200" cy="62" r="28" fill="#FBF3E6" opacity="0.35" />
              <path d="M120,170 C120,128 156,104 200,104 C244,104 280,128 280,170" fill="#FBF3E6" opacity="0.25" />
              <!-- cross icon -->
              <g stroke="#F2B705" stroke-width="4" stroke-linecap="round" opacity="0.7">
                <line x1="200" y1="54" x2="200" y2="70" />
                <line x1="192" y1="58" x2="208" y2="58" />
              </g>
            </svg>
          </div>
          <div class="staff-card-body">
            <h3>School Chaplain</h3>
            <span class="staff-role-label">Faith Life</span>
            <p class="staff-bio">Leads morning prayer, co-ordinates weekday Mass, guides the chapel choir and maintains
              the school's close relationship with the Namugongo parish and shrines.</p>
          </div>
        </div>

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

        <?php
        $teachers = [
          ['grade' => 'Primary One', 'subjects' => ['Literacy', 'Numeracy', 'RE'], 'bg' => '#16233D', 'accent' => '#F2B705'],
          ['grade' => 'Primary Two', 'subjects' => ['English', 'Mathematics', 'RE'], 'bg' => '#2F6B4F', 'accent' => '#F2B705'],
          ['grade' => 'Primary Three', 'subjects' => ['English', 'Mathematics', 'Science'], 'bg' => '#A6402E', 'accent' => '#FBF3E6'],
          ['grade' => 'Primary Four', 'subjects' => ['English', 'Mathematics', 'SST'], 'bg' => '#204B37', 'accent' => '#F8D25C'],
          ['grade' => 'Primary Five', 'subjects' => ['English', 'Mathematics', 'Science', 'SST'], 'bg' => '#832F21', 'accent' => '#F8D25C'],
          ['grade' => 'Primary Six', 'subjects' => ['English', 'Mathematics', 'Science', 'SST', 'RE'], 'bg' => '#2A3A5C', 'accent' => '#F2B705'],
          ['grade' => 'Primary Seven A', 'subjects' => ['English', 'Mathematics', 'Science', 'SST'], 'bg' => '#16233D', 'accent' => '#A6402E'],
          ['grade' => 'Primary Seven B', 'subjects' => ['English', 'Mathematics', 'Science', 'SST'], 'bg' => '#2F6B4F', 'accent' => '#F2B705'],
        ];
        foreach ($teachers as $t): ?>
          <div class="staff-card">
            <div class="staff-card-photo" aria-hidden="true">
              <svg viewBox="0 0 300 160" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                <rect width="300" height="160" fill="<?= $t['bg'] ?>" />
                <circle cx="150" cy="56" r="34" fill="rgba(0,0,0,0.2)" />
                <circle cx="150" cy="48" r="20" fill="<?= $t['accent'] ?>" opacity="0.4" />
                <path d="M90,130 C90,100 116,84 150,84 C184,84 210,100 210,130" fill="<?= $t['accent'] ?>"
                  opacity="0.25" />
              </svg>
            </div>
            <div class="staff-card-body">
              <h3><?= $t['grade'] ?> Teacher</h3>
              <span class="staff-role-label"><?= $t['grade'] ?></span>
              <div><?php foreach ($t['subjects'] as $s): ?><span
                    class="staff-subject-tag"><?= $s ?></span><?php endforeach; ?></div>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </section>

  <?php if ($deptHeads): ?>
    <!-- ============================================================
     DEPARTMENT HEADS — real records from the staff table
     ============================================================ -->
    <section class="staff-leadership-section">
      <div class="container reveal">
        <p class="eyebrow">From Our Records</p>
        <h2>Department heads</h2>
        <div class="staff-lead-grid">
          <?php foreach ($deptHeads as $person): ?>
            <?php $hasRealPhoto = $person['photo'] && file_exists(__DIR__ . '/' . $person['photo']); ?>
            <div class="staff-card">
              <div class="staff-card-photo" aria-hidden="<?= $hasRealPhoto ? 'false' : 'true' ?>">
                <?php if ($hasRealPhoto): ?>
                  <img src="<?= htmlspecialchars($person['photo']) ?>" alt="<?= htmlspecialchars($person['full_name']) ?>"
                    loading="lazy">
                <?php else: ?>
                  <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                    <rect width="400" height="200" fill="#16233D" />
                    <circle cx="200" cy="72" r="48" fill="#2A3A5C" />
                    <circle cx="200" cy="60" r="26" fill="#F2B705" opacity="0.35" />
                    <path d="M120,190 C120,150 156,128 200,128 C244,128 280,150 280,190" fill="#F2B705" opacity="0.2" />
                  </svg>
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
        </div>
      </div>
    </section>
  <?php endif; ?>

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
        <a href="contact.php" class="btn btn-on-soil">Send your CV to the school office →</a>
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
        <a href="admissions.php" class="btn btn-primary">Begin Admissions</a>
        <a href="contact.php" class="btn btn-on-dark">Plan a Visit</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>

</html>