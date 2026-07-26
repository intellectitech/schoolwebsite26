<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Flash message set by process_admissions.php after a submit + redirect
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admissions · Uganda Martyrs Primary School, Namugongo</title>
  <meta name="description" content="Apply to Uganda Martyrs Primary School, Namugongo. Learn about our admission process, requirements, fees, and how to enrol your child in Primary One through Primary Seven.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- ============================================================
     PAGE HERO
     ============================================================ -->
<section class="adm-hero">
  <div class="container reveal">
    <p class="breadcrumb"><a href="index.php">Home</a> / Admissions</p>
    <p class="eyebrow">Admissions 2025 / 2026</p>
    <h1>A place for every willing learner</h1>
    <p class="lead">Uganda Martyrs Primary School welcomes pupils from Primary One through Primary Seven. Whether you are enrolling a child straight from nursery or transferring mid-stream, here is everything you need to know about joining our school.</p>
    <div class="hero-actions" style="justify-content:flex-start; margin-top:1.6em;">
      <a href="#how-to-apply" class="btn btn-primary">How to Apply</a>
      <a href="#enquiry" class="btn btn-ghost">Send an Enquiry</a>
    </div>
  </div>
  <div class="hill-divider" style="color:var(--ink);">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none"><path d="M0,44 C240,4 480,0 720,18 C960,36 1200,40 1440,10 L1440,44 L0,44 Z" fill="currentColor"/></svg>
  </div>
</section>

<!-- ============================================================
     WHY CHOOSE UMPS – highlight cards
     ============================================================ -->
<section class="adm-highlights">
  <div class="container reveal">
    <p class="eyebrow">Why Uganda Martyrs</p>
    <h2>What makes us the right choice</h2>
    <div class="highlights-grid">
      <div class="highlight-card">
        <span class="highlight-num">P1–P7</span>
        <h3>Complete primary journey</h3>
        <p>Your child need not transfer schools. We take them from their very first day of primary school through to their PLE send-off.</p>
      </div>
      <div class="highlight-card">
        <span class="highlight-num">7</span>
        <h3>Core NCDC subjects</h3>
        <p>English, Mathematics, Science, Social Studies, Religious Education, Luganda, and Creative Arts — all taught to the national curriculum.</p>
      </div>
      <div class="highlight-card">
        <span class="highlight-num">3 Jun</span>
        <h3>Heritage on the doorstep</h3>
        <p>Minutes from the Basilica and Anglican Shrine. Our pupils study history on the very ground it happened, every year on Martyrs' Day.</p>
      </div>
      <div class="highlight-card">
        <span class="highlight-num">UNEB</span>
        <h3>Rigorous PLE preparation</h3>
        <p>Structured exam preparation begins in Primary Five, with mock assessments in Primary Six and intensive revision throughout Primary Seven.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     HOW TO APPLY – 4-step process
     ============================================================ -->
<section class="how-to-apply" id="how-to-apply">
  <div class="hill-divider" style="color:var(--cream);">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none"><path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor"/></svg>
  </div>
  <div class="container reveal">
    <p class="eyebrow">The Process</p>
    <h2>Four steps to a place at Uganda Martyrs</h2>
    <div class="apply-grid">

      <div class="steps-light">
        <div class="step-light">
          <span class="step-no">1</span>
          <div>
            <h3>Make an enquiry</h3>
            <p>Call the school office or fill in the enquiry form at the bottom of this page. Our admissions office is open Monday to Friday, 8am – 4pm. You can also visit us in person at Namugongo, Kira Municipality.</p>
          </div>
        </div>
        <div class="step-light">
          <span class="step-no">2</span>
          <div>
            <h3>Tour the school & assessment</h3>
            <p>We invite every prospective pupil for a guided walk of the compound and a short, friendly placement check. This is not a competitive entrance test — it helps us place your child in the right stream and identify any extra support they may need.</p>
          </div>
        </div>
        <div class="step-light">
          <span class="step-no">3</span>
          <div>
            <h3>Submit your documents</h3>
            <p>Bring the required documents to the school office. Our admissions team will verify them and prepare a place offer, usually within two working days.</p>
          </div>
        </div>
        <div class="step-light">
          <span class="step-no">4</span>
          <div>
            <h3>Collect your welcome pack</h3>
            <p>Once fees are settled, collect the uniform list, term calendar and reading materials. You will also meet your child's class teacher before the first day of school.</p>
          </div>
        </div>
      </div>

      <div class="apply-aside">
        <div class="apply-aside-card">
          <h3>Documents to bring</h3>
          <ul>
            <li>Birth certificate or birth notification extract</li>
            <li>Immunisation card (yellow card)</li>
            <li>Most recent school report (for transfers)</li>
            <li>Transfer letter from previous school (for transfers)</li>
            <li>Two recent passport-size photographs</li>
            <li>Parent or guardian's contact details</li>
          </ul>
        </div>
        <div class="apply-aside-card">
          <h3>Intake periods</h3>
          <p>We accept applications year-round subject to availability. The heaviest Primary One intake falls in the weeks before Term One opens each February. Transfer pupils may join at the start of any term.</p>
        </div>
        <div class="apply-aside-card">
          <h3>Office hours</h3>
          <p>Monday – Friday: 8:00 am – 5:00 pm<br>Saturday tours by prior appointment only.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     ENTRY REQUIREMENTS BY GRADE
     ============================================================ -->
<section class="grade-reqs">
  <div class="hill-divider" style="color:var(--leaf-tint);">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none"><path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor"/></svg>
  </div>
  <div class="container reveal">
    <p class="eyebrow">Entry Requirements</p>
    <h2>What we look for at each stage</h2>
    <p class="section-lead">There are no competitive entrance exams. Every child who meets the age and readiness criteria below is considered for a place.</p>
    <div class="grade-req-grid">

      <div class="grade-req-card">
        <span class="grade-badge">Primary One</span>
        <h3>New starters</h3>
        <ul>
          <li>Age 5 – 7 years by the start of the school year</li>
          <li>Nursery school completion preferred but not mandatory</li>
          <li>Able to follow simple spoken instructions in English or Luganda</li>
          <li>Birth certificate and immunisation card</li>
        </ul>
      </div>

      <div class="grade-req-card">
        <span class="grade-badge">Primary 2 – 4</span>
        <h3>Lower primary transfers</h3>
        <ul>
          <li>Most recent school report card</li>
          <li>Transfer letter from the previous school</li>
          <li>Short placement check in Literacy and Numeracy</li>
          <li>Birth certificate and immunisation card</li>
          <li>Passed the preceding grade</li>
        </ul>
      </div>

      <div class="grade-req-card">
        <span class="grade-badge">Primary 5 – 7</span>
        <h3>Upper primary transfers</h3>
        <ul>
          <li>Most recent school report card</li>
          <li>Transfer letter from the previous school</li>
          <li>Placement check in English, Mathematics and Science</li>
          <li>Birth certificate</li>
          <li>Passed the preceding grade with satisfactory marks</li>
          <li>Note: P7 transfers accepted in Term One only</li>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     FEES OVERVIEW
     ============================================================ -->
<section class="fees-section">
  <div class="hill-divider" style="color:var(--cream);">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none"><path d="M0,10 C240,42 480,40 720,20 C960,0 1200,4 1440,24 L1440,44 L0,44 Z" fill="currentColor"/></svg>
  </div>
  <div class="container reveal">
    <p class="eyebrow eyebrow-light">School Fees</p>
    <h2>Fees overview</h2>
    <p class="section-lead-light">The figures below are a guide. Please contact the school office for the current term's fee schedule. All fees are payable in Ugandan Shillings (UGX).</p>
    <div class="fees-grid">
      <div class="fee-card">
        <h3>Primary One – Three</h3>
        <span class="fee-amount">UGX 450,000</span>
        <span class="fee-period">Per term</span>
        <p>Includes tuition, meals, sports, Religious Education activities and standard stationery pack.</p>
      </div>
      <div class="fee-card">
        <h3>Primary Four – Six</h3>
        <span class="fee-amount">UGX 520,000</span>
        <span class="fee-period">Per term</span>
        <p>Includes tuition, meals, sports, science practicals, RE activities and stationery.</p>
      </div>
      <div class="fee-card">
        <h3>Primary Seven</h3>
        <span class="fee-amount">UGX 620,000</span>
        <span class="fee-period">Per term</span>
        <p>Includes tuition, meals, PLE revision materials, mock exams, UNEB registration and send-off celebrations.</p>
      </div>
    </div>
    <p class="fees-note"><strong>Note:</strong> These are indicative figures for planning purposes only. Actual fees are confirmed at time of enrolment. The school offers a payment plan option — speak to the bursar's office. Fees are reviewed each year at the start of Term One.</p>
  </div>
</section>

<!-- ============================================================
     FAQ ACCORDION
     ============================================================ -->
<section class="faq-section">
  <div class="hill-divider" style="color:var(--ink);">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none"><path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor"/></svg>
  </div>
  <div class="container reveal">
    <p class="eyebrow">Common Questions</p>
    <h2>Frequently asked questions</h2>
    <div class="faq-list">

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Is Uganda Martyrs Primary School a government or private school?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>We are a privately owned, Catholic-founded primary school operating under the Uganda Ministry of Education and Sports guidelines. We follow the national curriculum (NCDC) and our Primary Seven pupils sit the Uganda National Examinations Board (UNEB) Primary Leaving Examinations.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          What age should my child be to start Primary One?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>We generally admit children aged 5 – 7 years into Primary One. Children who have completed nursery school (Baby, Middle and Top class) are well prepared for the transition, though nursery completion is not a strict requirement for bright and ready younger children.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Can my child join mid-year or mid-term?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>Transfer pupils may join at the start of any term, subject to available places. Mid-term transfers are considered case by case and generally only if the child would miss minimal instructional time. Primary Seven transfers are accepted in Term One only to allow the full year for PLE preparation.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Is there a boarding option?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>Uganda Martyrs Primary School is a day school. All pupils go home at the end of each school day. We do, however, offer an after-school care programme for pupils whose parents or guardians cannot collect them at the regular closing time — please ask the office for details.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          What is included in the school fees?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>Term fees cover tuition, a hot midday meal, sports activities, Religious Education activities and a stationery pack. UNEB registration fees for Primary Seven pupils are included in that year's fee structure. Uniform, extra-curricular trips and optional activities are charged separately and always communicated in advance.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Does the school cater for pupils of faiths other than Catholic?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>Yes. While our school was founded on Catholic values and the daily rhythm includes morning prayer and Religious Education, we warmly welcome pupils from all faith backgrounds. Religious Education lessons cover both Catholic and broad Christian content in line with the NCDC syllabus. Pupils are respected as individuals, whatever their family's faith tradition.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Where exactly is the school located?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>We are located in Namugongo, Kira Municipality, Wakiso District — approximately 12 km northeast of Kampala city centre, a short walk from the Basilica of the Uganda Martyrs and the Anglican Uganda Martyrs Shrine. The school sits along the main Namugongo road and is accessible by matatu from the Northern Bypass.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Can we visit the school before applying?
          <span class="faq-icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-answer" role="region">
          <div class="faq-answer-inner">
            <p>Absolutely — we encourage it. Weekday visits during school hours give you a chance to see the compound, sit in briefly on a lesson, and meet the head teacher or admissions team. Saturday morning visits can be arranged by appointment. Use the enquiry form below or call our office to book a suitable time.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     ENQUIRY FORM
     ============================================================ -->
<section class="enquiry-section" id="enquiry">
  <div class="hill-divider" style="color:var(--leaf-tint);">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none"><path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor"/></svg>
  </div>
  <div class="container reveal">
    <p class="eyebrow">Get in Touch</p>
    <h2>Send an admissions enquiry</h2>
    <div class="enquiry-grid">

      <div class="enquiry-info">
        <p>Fill in the form and our admissions office will respond within two working days. You are welcome to call or visit us directly if you prefer to speak with someone straight away.</p>
        <div class="enquiry-contact-list">
          <div class="enq-contact-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8 9.7a16 16 0 0 0 6 6z"/>
            </svg>
            <div>
              <h4>Phone</h4>
              <p>+256 700 000 000<br><small>Monday – Friday, 8am – 5pm</small></p>
            </div>
          </div>
          <div class="enq-contact-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="2"/>
              <path d="m22 6-10 7L2 6"/>
            </svg>
            <div>
              <h4>Email</h4>
              <p>admissions@ugandamartyrsnamugongo.sc.ug</p>
            </div>
          </div>
          <div class="enq-contact-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 21s7-7.2 7-12a7 7 0 1 0-14 0c0 4.8 7 12 7 12z"/>
              <circle cx="12" cy="9" r="2.5"/>
            </svg>
            <div>
              <h4>Address</h4>
              <p>Namugongo, Kira Municipality<br>Wakiso District, Uganda</p>
            </div>
          </div>
        </div>
      </div>

      <form class="enquiry-form" id="enquiryForm" action="process_admissions.php" method="POST" novalidate>
        <?= csrfField() ?>
        <div class="hp-field" aria-hidden="true">
          <label for="a-website">Leave this field blank</label>
          <input id="a-website" name="website" type="text" tabindex="-1" autocomplete="off">
        </div>

        <?php if ($flash['success']): ?>
          <div class="alert alert-success"><?= htmlspecialchars($flash['success']) ?></div>
        <?php endif; ?>
        <?php if ($flash['errors']): ?>
          <div class="alert alert-error">
            <ul>
              <?php foreach ($flash['errors'] as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="field-row">
          <div class="field">
            <label for="parent-name">Parent / Guardian name</label>
            <input id="parent-name" name="parent_name" type="text" required placeholder="Full name" value="<?= old($flash['old'], 'parent_name') ?>">
          </div>
          <div class="field">
            <label for="child-name">Child's name</label>
            <input id="child-name" name="child_name" type="text" required placeholder="Full name" value="<?= old($flash['old'], 'child_name') ?>">
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label for="phone">Phone number</label>
            <input id="phone" name="phone" type="tel" required placeholder="+256 7..." value="<?= old($flash['old'], 'phone') ?>">
          </div>
          <div class="field">
            <label for="email">Email address <span style="text-transform:none;letter-spacing:normal">(optional)</span></label>
            <input id="email" name="email" type="email" placeholder="you@example.com" value="<?= old($flash['old'], 'email') ?>">
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label for="grade">Grade applying for</label>
            <?php $selectedGrade = old($flash['old'], 'grade'); ?>
            <select id="grade" name="grade" required>
              <option value="" disabled <?= $selectedGrade === '' ? 'selected' : '' ?>>Select grade</option>
              <option value="P1" <?= $selectedGrade === 'P1' ? 'selected' : '' ?>>Primary One (P1)</option>
              <option value="P2" <?= $selectedGrade === 'P2' ? 'selected' : '' ?>>Primary Two (P2)</option>
              <option value="P3" <?= $selectedGrade === 'P3' ? 'selected' : '' ?>>Primary Three (P3)</option>
              <option value="P4" <?= $selectedGrade === 'P4' ? 'selected' : '' ?>>Primary Four (P4)</option>
              <option value="P5" <?= $selectedGrade === 'P5' ? 'selected' : '' ?>>Primary Five (P5)</option>
              <option value="P6" <?= $selectedGrade === 'P6' ? 'selected' : '' ?>>Primary Six (P6)</option>
              <option value="P7" <?= $selectedGrade === 'P7' ? 'selected' : '' ?>>Primary Seven (P7)</option>
            </select>
          </div>
          <div class="field">
            <label for="term">Preferred intake term</label>
            <?php $selectedTerm = old($flash['old'], 'term'); ?>
            <select id="term" name="term">
              <option value="" disabled <?= $selectedTerm === '' ? 'selected' : '' ?>>Select term</option>
              <option value="term1" <?= $selectedTerm === 'term1' ? 'selected' : '' ?>>Term One (February)</option>
              <option value="term2" <?= $selectedTerm === 'term2' ? 'selected' : '' ?>>Term Two (June)</option>
              <option value="term3" <?= $selectedTerm === 'term3' ? 'selected' : '' ?>>Term Three (September)</option>
            </select>
          </div>
        </div>
        <div class="field">
          <label for="current-school">Current / previous school <span style="text-transform:none;letter-spacing:normal">(if transferring)</span></label>
          <input id="current-school" name="current_school" type="text" placeholder="Name of current school" value="<?= old($flash['old'], 'current_school') ?>">
        </div>
        <div class="field">
          <label for="message">Additional information</label>
          <textarea id="message" name="message" rows="4" placeholder="Any background about your child, special needs, or questions for the admissions team"><?= old($flash['old'], 'message') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Enquiry</button>
      </form>

    </div>
  </div>
</section>

<!-- ============================================================
     CTA BAND
     ============================================================ -->
<section class="cta-band">
  <div class="hill-divider" style="color:var(--cream);">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none"><path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor"/></svg>
  </div>
  <div class="container reveal">
    <h2>The best way to know us is to come and see us</h2>
    <p>Walk the compound on a school morning, listen to assembly hymns, and meet the teachers who will shape your child's primary years.</p>
    <div class="cta-actions">
      <a href="#enquiry" class="btn btn-primary">Send an Enquiry</a>
      <a href="contact.php#find-us" class="btn btn-on-dark">Get Directions</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
