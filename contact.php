<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


// Flash message set by process_contact.php after a submit + redirect
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us · Uganda Martyrs Primary School, Namugongo</title>
  <meta name="description"
    content="Get in touch with Uganda Martyrs Primary School, Namugongo. Find our address, phone, email and directions from Kampala.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <?php include 'includes/header.php'; ?>

  <!-- ============================================================
     PAGE HERO
     ============================================================ -->
  <section class="page-hero">
    <div class="container reveal">
      <p class="breadcrumb"><a href="index.php">Home</a> / Contact Us</p>
      <p class="eyebrow">Get in Touch</p>
      <h1>We are right here in Namugongo</h1>
      <p class="lead">Call, email, or just walk through our gate — our office is open Monday to Friday and we welcome
        visits from prospective families any time during school hours.</p>
    </div>
    <div class="hill-divider" style="color:var(--ink)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,44 C240,4 480,0 720,18 C960,36 1200,40 1440,10 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
  </section>

  <!-- ============================================================
     CONTACT INFO CARDS
     ============================================================ -->
  <section class="contact-cards-section">
    <div class="container reveal">
      <p class="eyebrow">Our Details</p>
      <h2>How to reach us</h2>
      <div class="contact-cards-grid">

        <div class="contact-card">
          <svg class="contact-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 21s7-7.2 7-12a7 7 0 1 0-14 0c0 4.8 7 12 7 12z" />
            <circle cx="12" cy="9" r="2.5" />
          </svg>
          <h3>Address</h3>
          <p>Namugongo, Kira Municipality<br>Wakiso District, Uganda<br><em>Near the Basilica of the Uganda Martyrs</em>
          </p>
        </div>

        <div class="contact-card">
          <svg class="contact-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path
              d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8 9.7a16 16 0 0 0 6 6z" />
          </svg>
          <h3>Phone</h3>
          <p><a href="tel:+256700000000">+256 700 000 000</a><br><a href="tel:+256700000001">+256 700 000 001</a><br>Mon
            – Fri, 8:00 am – 5:00 pm</p>
        </div>

        <div class="contact-card">
          <svg class="contact-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2" />
            <path d="m22 6-10 7L2 6" />
          </svg>
          <h3>Email</h3>
          <p><a href="mailto:info@ugandamartyrsnamugongo.sc.ug">info@ugandamartyrs<br>namugongo.sc.ug</a><br><a
              href="mailto:admissions@ugandamartyrsnamugongo.sc.ug">admissions@ugandamartyrsnamu<br>gongo.sc.ug</a></p>
        </div>

        <div class="contact-card">
          <svg class="contact-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
          </svg>
          <h3>Office Hours</h3>
          <p>Monday – Friday<br>8:00 am – 5:00 pm<br>Saturday tours by appointment</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
     CONTACT FORM  (reuses shared .contact, .contact-grid, .contact-form, .field)
     ============================================================ -->
  <section class="contact">
    <div class="container reveal">
      <p class="eyebrow">Send a Message</p>
      <h2>Write to us directly</h2>
      <div class="contact-grid">

        <div class="info-block">
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <path d="M12 8v4l3 3" />
            </svg>
            <div>
              <h3>Response time</h3>
              <p>We aim to reply to all written enquiries within two working days. Urgent matters are best handled by
                phone.</p>
            </div>
          </div>
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 21s7-7.2 7-12a7 7 0 1 0-14 0c0 4.8 7 12 7 12z" />
              <circle cx="12" cy="9" r="2.5" />
            </svg>
            <div>
              <h3>Visit us in person</h3>
              <p>Parents and prospective families are always welcome during school hours. Ask for the head teacher's
                office when you arrive at the gate.</p>
            </div>
          </div>
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="4" width="20" height="16" rx="2" />
              <path d="m22 6-10 7L2 6" />
            </svg>
            <div>
              <h3>Social media</h3>
              <p>Follow us on Facebook and WhatsApp for term updates, event photos and school announcements.</p>
            </div>
          </div>
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <div>
              <h3>Admissions enquiries</h3>
              <p>For questions specifically about joining the school, please use our dedicated <a
                  href="admissions.php#enquiry" style="color:var(--soil)">admissions enquiry form</a>.</p>
            </div>
          </div>
        </div>

        <form action="process_contact.php" method="POST" class="contact-form" id="contactForm" novalidate>
          <?= csrfField() ?>
          <!-- Honeypot: hidden from real visitors via CSS, bots fill it in and get quietly ignored -->
          <div class="hp-field" aria-hidden="true">
            <label for="c-website">Leave this field blank</label>
            <input id="c-website" name="website" type="text" tabindex="-1" autocomplete="off">
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

          <div class="field">
            <label for="c-name">Full name</label>
            <input id="c-name" name="name" type="text" required placeholder="Your name"
              value="<?= old($flash['old'], 'name') ?>">
          </div>
          <div class="field-row">
            <div class="field">
              <label for="c-email">Email address</label>
              <input id="c-email" name="email" type="email" required placeholder="you@example.com"
                value="<?= old($flash['old'], 'email') ?>">
            </div>
            <div class="field">
              <label for="c-phone">Phone number <span
                  style="text-transform:none;letter-spacing:normal">(optional)</span></label>
              <input id="c-phone" name="phone" type="tel" placeholder="+256 7..."
                value="<?= old($flash['old'], 'phone') ?>">
            </div>
          </div>
          <div class="field">
            <label for="c-subject">Subject</label>
            <select id="c-subject" name="subject" required>
              <option value="" disabled <?= old($flash['old'], 'subject') === '' ? 'selected' : '' ?>>Select a topic
              </option>
              <?php foreach (['General enquiry', 'Admissions', 'School fees', 'Term dates & calendar', 'Pupil welfare', 'Partnership / donation', 'Other'] as $topic): ?>
                <option <?= old($flash['old'], 'subject') === $topic ? 'selected' : '' ?>><?= $topic ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label for="c-message">Message</label>
            <textarea id="c-message" name="message" rows="5" required minlength="10" maxlength="2000"
              placeholder="How can we help?"><?= old($flash['old'], 'message') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>

      </div>
    </div>
  </section>

  <!-- ============================================================
     HOW TO FIND US
     ============================================================ -->
  <section id="find-us" class="directions-section">
    <div class="hill-divider" style="color:var(--leaf-tint)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow eyebrow-light">Directions</p>
      <h2>How to find us from Kampala</h2>
      <div class="directions-grid">

        <div class="directions-copy">
          <p>We are located in Namugongo, approximately 12 km northeast of Kampala city centre along the Namugongo Road.
            The school sits a short walk from the Basilica of the Uganda Martyrs — if you can find the Basilica, you can
            find us.</p>
          <div style="margin-top:1.8em">
            <div class="direction-step">
              <span class="direction-step-num">1</span>
              <div>
                <h4>From Kampala city centre</h4>
                <p>Head northeast on the Northern Bypass towards Kireka and Kira. Pass the Kireka roundabout and
                  continue along Namugongo Road.</p>
              </div>
            </div>
            <div class="direction-step">
              <span class="direction-step-num">2</span>
              <div>
                <h4>From the Northern Bypass</h4>
                <p>Take the Namugongo exit. Follow signs for the Uganda Martyrs Shrine — the school is on the same road,
                  approximately 400 m before the Basilica gate.</p>
              </div>
            </div>
            <div class="direction-step">
              <span class="direction-step-num">3</span>
              <div>
                <h4>By matatu / taxi</h4>
                <p>Board a Namugongo-bound taxi from the Old Park, Kampala or Kireka stage. Alight at the Namugongo
                  Shrine stop. The school is a short walk from the stop.</p>
              </div>
            </div>
            <div class="direction-step">
              <span class="direction-step-num">4</span>
              <div>
                <h4>At the gate</h4>
                <p>Tell the gate attendant you are visiting the school office. Visitor parking is available inside the
                  compound.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="map-illustration" aria-label="Illustrated map showing location near Namugongo Shrine">
          <svg viewBox="0 0 380 300" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <!-- Road grid -->
            <rect width="380" height="300" fill="#2A3A5C" rx="6" />
            <!-- Roads -->
            <rect x="0" y="140" width="380" height="18" fill="#16233D" opacity="0.8" />
            <rect x="190" y="0" width="16" height="300" fill="#16233D" opacity="0.8" />
            <line x1="0" y1="80" x2="380" y2="80" stroke="#16233D" stroke-width="8" opacity="0.5" />
            <!-- Basilica marker -->
            <g transform="translate(280,100)">
              <circle r="22" fill="#A6402E" />
              <polygon points="0,-10 -4,-4 4,-4" fill="#FBF3E6" />
              <rect x="-2" y="-4" width="4" height="8" fill="#FBF3E6" />
              <line x1="-7" y1="-7" x2="-4" y2="-4" stroke="#FBF3E6" stroke-width="1" />
              <line x1="7" y1="-7" x2="4" y2="-4" stroke="#FBF3E6" stroke-width="1" />
            </g>
            <text x="280" y="138" text-anchor="middle" font-family="sans-serif" font-size="8"
              fill="#F8D25C">Basilica</text>
            <!-- School marker -->
            <g transform="translate(200,149)">
              <circle r="18" fill="#F2B705" />
              <rect x="-7" y="-6" width="14" height="10" fill="#16233D" />
              <polygon points="0,-11 -10,-3 10,-3" fill="#16233D" />
            </g>
            <text x="200" y="182" text-anchor="middle" font-family="sans-serif" font-size="8" fill="#F8D25C">UMPS</text>
            <!-- Compass -->
            <g transform="translate(340,30)">
              <circle r="16" fill="none" stroke="#F2B705" stroke-width="1" opacity="0.6" />
              <text x="0" y="-6" text-anchor="middle" font-family="sans-serif" font-size="9" fill="#F2B705"
                font-weight="bold">N</text>
              <line x1="0" y1="-3" x2="0" y2="3" stroke="#F2B705" stroke-width="2" />
            </g>
            <!-- Labels -->
            <text x="20" y="135" font-family="sans-serif" font-size="9" fill="rgba(251,243,230,0.6)">← To Kampala</text>
            <text x="185" y="20" font-family="sans-serif" font-size="9" fill="rgba(251,243,230,0.6)">Namugongo Rd</text>
          </svg>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
     CTA BAND
     ============================================================ -->
  <section class="cta-band">
    <div class="hill-divider" style="color:var(--ink)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <h2>Ready to start the admissions process?</h2>
      <p>Our admissions team is ready to walk you through every step — from the first visit to the first day of school.
      </p>
      <div class="cta-actions">
        <a href="admissions.php" class="btn btn-primary">Admissions Guide</a>
        <a href="admissions.php#enquiry" class="btn btn-on-dark">Send an Enquiry</a>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>

</html>