<?php
// ============================================================
//  includes/footer.php — Footer & closing HTML
//  Include at the BOTTOM of every public page.
// ============================================================
$schoolName    = isset($pdo) ? (getSetting($pdo, 'school_name')    ?: 'Uganda Martyrs Primary School') : 'Uganda Martyrs Primary School';
$schoolAddress = isset($pdo) ? (getSetting($pdo, 'school_address') ?: 'Namugongo, Kira Municipality, Wakiso District') : '';
$schoolPhone   = isset($pdo) ? (getSetting($pdo, 'contact_phone')  ?: '+256 700 000 000') : '';
$schoolEmail   = isset($pdo) ? (getSetting($pdo, 'contact_email')  ?: 'info@ugandamartyrsnamugongo.sc.ug') : '';
?>

 <footer class="site-footer">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-about">
            <div class="footer-brand">
              <svg
                width="34"
                height="34"
                viewBox="0 0 60 60"
                aria-hidden="true"
              >
                <circle cx="30" cy="30" r="14" fill="#F2B705" />
              </svg>
              <strong>Uganda Martyrs Primary School</strong>
            </div>
            <p>
              Namugongo, Kira Municipality, Wakiso District — teaching Primary
              One through Primary Seven within sight of the shrine spires since
              our founding generation.
            </p>
          </div>
          <div class="footer-col">
            <h4>Explore</h4>
            <ul>
              <li><a href="index.php#about">About Us</a></li>
              <li><a href="index.php#journey">Academics</a></li>
              <li><a href="index.php#life">School Life</a></li>
              <li><a href="index.php#admissions">Admissions</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>School</h4>
            <ul>
              <li><a href="index.php#contact">Contact & Visits</a></li>
              <li><a href="index.php#voices">Parent Voices</a></li>
              <li><a href="index.php#top">Term Calendar</a></li>
              <li><a href="index.php#top">Uniform List</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>Connect</h4>
            <ul>
              <li><a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $schoolPhone)) ?>"><?= htmlspecialchars($schoolPhone) ?></a></li>
              <li><a href="mailto:<?= htmlspecialchars($schoolEmail) ?>"><?= htmlspecialchars($schoolEmail) ?></a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <div class="container">
            <span>© <?= date('Y') ?> <?= htmlspecialchars($schoolName)?>, Namugongo.All rights reserved.
          </span>
           
          </div>
          <span>Courage to Learn, Faith to Rise. <br> Developed by Intellectitech
          </span>
        </div>
      </div>
    </footer>
    <script src="assets/js/script.js"></script>