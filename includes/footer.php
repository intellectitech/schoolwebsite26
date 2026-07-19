<?php
/**
 * includes/footer.php
 * Shared footer across all pages.
 */
?>
<footer class="site-footer">
  <div class="container footer-grid">

    <div class="footer-col footer-about">
      <img src="<?php echo BASE_URL; ?>assets/images/badge.jpg" alt="Mbuya Parents' School badge" class="footer-badge">
      <h3>Mbuya Parents' School</h3>
      <p><?php echo SITE_MOTTO; ?></p>
      <p class="footer-tagline">A top pre-primary and primary school serving Kampala with holistic, practical, values-driven education.</p>
    </div>

    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul class="footer-links">
        <li><a href="about.php">About Us</a></li>
        <li><a href="academics.php">Academics</a></li>
        <li><a href="admissions.php">Admissions</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="news.php">News &amp; Blog</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Contact Us</h4>
      <ul class="footer-contact">
        <li><?php echo SITE_ADDRESS; ?></li>
        <li><?php echo SITE_POBOX; ?></li>
        <li><?php echo SITE_PHONE_1; ?> / <?php echo SITE_PHONE_2; ?></li>
        <li><?php echo SITE_EMAIL; ?></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Follow Us</h4>
      <a class="footer-social-link" href="<?php echo SITE_INSTAGRAM; ?>" target="_blank" rel="noopener">Instagram</a>
    </div>

  </div>

  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> Mbuya Parents' School. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
