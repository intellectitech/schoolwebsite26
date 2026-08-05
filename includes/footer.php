    <footer>
        <div class="container footer-grid">
            <div>
                <div class="brand-wrap">
                    <div class="logo-badge"><img src="images/logo.png" alt="School logo"></div>
                    <div class="brand">Namugongo Model<br>Primary School</div>
                </div>
                <p class="footer-tagline">Education Is My Future</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="admission.php">Admission</a></li>
                    <li><a href="news.php">News</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul class="footer-links">
                    <li>📍 <?php echo h(SITE_ADDRESS); ?></li>
                    <li>📞 <?php echo h(SITE_PHONE); ?></li>
                    <li>✉ <?php echo h(SITE_EMAIL); ?></li>
                </ul>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Namugongo Model Primary School. All rights reserved.</p>
        </div>
    </footer>
    </main>

    <!-- Nexa AI School Assistant -->
    <div id="nexaAiRoot"></div>

    <script src="script.js"></script>
    <script src="assets/js/nexa-ai.js"></script>
</body>
</html>
