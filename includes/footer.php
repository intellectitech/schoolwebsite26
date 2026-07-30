<?php
// includes/footer.php
?>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3><?= clean(getSetting($pdo, 'school_name', 'School')) ?></h3>
                    <p><?= clean(getSetting($pdo, 'school_address', 'P.O. Box 123, Kampala, Uganda')) ?></p>
                    <p><i class="fas fa-phone"></i> <?= clean(getSetting($pdo, 'school_phone', '+256-700-123456')) ?></p>
                    <p><i class="fas fa-envelope"></i> <?= clean(getSetting($pdo, 'school_email', 'info@school.ug')) ?></p>
                </div>
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="admissions.php">Admissions</a></li>
                        <li><a href="news.php">News</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="staff.php">Staff Directory</a></li>
                        <li><a href="gallery.php">Photo Gallery</a></li>
                        <li><a href="admissions.php#documents">Download Forms</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Newsletter</h4>
                    <p>Subscribe for updates</p>
                    <form action="subscribe.php" method="POST" class="newsletter-form">
                        <input type="email" name="email" placeholder="Your Email" required>
                        <button type="submit" class="btn btn-small">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= clean(getSetting($pdo, 'school_name', 'School')) ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>