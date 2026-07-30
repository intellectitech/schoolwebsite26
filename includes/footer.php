<?php
$schoolName    = getSetting($pdo, 'school_name', "Namugongo Parents' School");
$schoolAddress = getSetting($pdo, 'school_address');
$schoolPhone   = getSetting($pdo, 'school_phone');
$schoolEmail   = getSetting($pdo, 'school_email');
$facebookUrl   = getSetting($pdo, 'facebook_url', '#');
$twitterUrl    = getSetting($pdo, 'twitter_url', '#');
$instagramUrl  = getSetting($pdo, 'instagram_url', '#');
?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="about.php" class="nav-link">About Us</a></li>
                    <li><a href="staff.php" class="nav-link">Our Staff</a></li>
                    <li><a href="admissions.php" class="nav-link">Admissions</a></li>
                    <li><a href="fees.php" class="nav-link">School Fees</a></li>
                    <li><a href="news.php" class="nav-link">News & Events</a></li>
                    <li><a href="gallery.php" class="nav-link">Gallery</a></li>
                    <li><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul>
                    <li><?= htmlspecialchars($schoolAddress) ?></li>
                    <li><?= htmlspecialchars($schoolPhone) ?></li>
                    <li><?= htmlspecialchars($schoolEmail) ?></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <ul>
                    <li><a href="<?= htmlspecialchars($facebookUrl) ?>">Facebook</a></li>
                    <li><a href="<?= htmlspecialchars($twitterUrl) ?>">Twitter</a></li>
                    <li><a href="<?= htmlspecialchars($instagramUrl) ?>">Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($schoolName) ?>. All Rights Reserved.
               &nbsp;|&nbsp; <a href="admin/login.php" style="color:inherit">Admin Login</a></p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
