<?php
require_once 'database.php';

try {
    $newsItems = $pdo->query("SELECT * FROM news WHERE is_published = 1 ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $newsItems = [];
}
?>
<?php
$currentRoute = 'news';
require_once 'seo-config.php';
$page = $seo[$currentRoute] ?? $seo['home'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page['description']) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($siteUrl) ?>/news.php">
    <meta property="og:title" content="<?= htmlspecialchars($page['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page['description']) ?>">
    <link rel="icon" href="badge.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-container">
            <div class="quick-contact">
                <span><i class="fas fa-phone"></i> +256 772 622 612 / +256 754 465 536</span>
                <span><i class="fas fa-envelope"></i> info@stfrancisborgia.ac.ug</span>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header>
        <div class="header-container">
            <div class="logo-container">
                <img src="naps/logo.jpg" alt="St. Francis Borgia High School Mukono logo" class="logo">
                <div class="school-name">
                    <h1>St. FRANCIS BORGIA HIGH SCHOOL</h1>
                    <p>Called to Shine</p>
                </div>
            </div>
            
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            
            <nav id="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="academics.php">Academics</a></li>
                    <li><a href="admissions.php">Admissions</a></li>
                    <li><a href="facilities.php">Facilities</a></li>
                    <li class="active"><a href="news.php">News</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

<!-- Interior Hero -->
    <section class="interior-hero" style="background-image: url('naps/news.background.webp');">
        <div class="hero-content">
            <h1>News & Campus Events</h1>
            <p>Read the latest updates, achievements, and activities from St. Francis Borgia High School</p>
        </div>
    </section>

    <!-- Latest News Grid -->
    <section class="news-section">
        <div class="section-container">
            <div class="section-title">
                <h2>Latest School News</h2>
            </div>
            
            <div class="grid-3">
                <?php foreach ($newsItems as $item): ?>
    <div class="card">
        <div class="card-image">
            <img src="<?= htmlspecialchars($item['featured_image'] ?: 'naps/merit.svg') ?>" alt="<?= htmlspecialchars($item['title']) ?>">
        </div>
        <div class="card-content">
            <div class="card-date"><i class="far fa-calendar-alt"></i> <?= date('F j, Y', strtotime($item['created_at'])) ?></div>
            <h3><?= htmlspecialchars($item['title']) ?></h3>
            <p><?= htmlspecialchars($item['excerpt']) ?></p>
            <a href="news_detail.php?id=<?= $item['id'] ?>" class="btn btn-secondary">Read More</a>
        </div>
    </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="about" style="background: var(--light-gray);">
        <div class="section-container">
            <div class="section-title">
                <h2>Upcoming Campus Events</h2>
            </div>
            
            <div class="grid-3">
                <div class="card" style="padding: 2rem; border-left: 4px solid var(--sfc-yellow); border-radius: 8px;">
                    <div style="background: var(--sfc-red); color: var(--white); display: inline-block; padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem; margin-bottom: 1rem;">JUL 20, 2026</div>
                    <h3 style="color: var(--sfc-red-dark); font-size: 1.25rem; margin-bottom: 0.8rem;">Parents-Teachers Conference</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 1.5rem;"><i class="fas fa-clock" style="color: var(--sfc-yellow);"></i> 9:00 AM - 3:00 PM<br><i class="fas fa-map-marker-alt" style="color: var(--sfc-yellow);"></i> School Hall</p>
                    <p style="font-size: 0.9rem; color: #555;">Meet one-on-one with subject teachers to discuss mid-term academic performance and DIT skills projects progress.</p>
                </div>
                
                <div class="card" style="padding: 2rem; border-left: 4px solid var(--sfc-yellow); border-radius: 8px;">
                    <div style="background: var(--sfc-red); color: var(--white); display: inline-block; padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem; margin-bottom: 1rem;">AUG 5-7, 2026</div>
                    <h3 style="color: var(--sfc-red-dark); font-size: 1.25rem; margin-bottom: 0.8rem;">MDD Cultural Festival</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 1.5rem;"><i class="fas fa-clock" style="color: var(--sfc-yellow);"></i> 8:30 AM - 5:00 PM Daily<br><i class="fas fa-map-marker-alt" style="color: var(--sfc-yellow);"></i> Campus Grounds</p>
                    <p style="font-size: 0.9rem; color: #555;">Our primary inter-house event celebrating diversity, music heritage, dance, and cultural plays. Parents are highly encouraged to attend.</p>
                </div>
                
                <div class="card" style="padding: 2rem; border-left: 4px solid var(--sfc-yellow); border-radius: 8px;">
                    <div style="background: var(--sfc-red); color: var(--white); display: inline-block; padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem; margin-bottom: 1rem;">OCT 18, 2026</div>
                    <h3 style="color: var(--sfc-red-dark); font-size: 1.25rem; margin-bottom: 0.8rem;">Visitation & Career Day</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 1.5rem;"><i class="fas fa-clock" style="color: var(--sfc-yellow);"></i> 9:00 AM - 5:00 PM<br><i class="fas fa-map-marker-alt" style="color: var(--sfc-yellow);"></i> Campus-Wide</p>
                    <p style="font-size: 0.9rem; color: #555;">Visitation day coupled with career workshops where professionals speak to students about tertiary education and career fields.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Subscription Banner -->
    <section class="newsletter-banner">
        <div class="section-container">
            <h2>Stay Informed</h2>
            <p style="max-width: 600px; margin: 0.5rem auto 1.5rem; font-size: 1.05rem;">Subscribe to our newsletter to receive calendar updates and term newsletters directly in your inbox.</p>
            <form class="newsletter-form" id="newsletterForm">
                <input type="email" placeholder="Your Email Address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- News Archive Section -->
    <section class="about">
        <div class="section-container" style="max-width: 800px;">
            <div class="section-title">
                <h2>News Archive</h2>
            </div>
            
            <div class="leader-card" style="padding: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <ul style="list-style: none; color: #555;">
                    <li style="padding: 1rem 0; border-bottom: 1px solid var(--sfc-silver); display: flex; justify-content: space-between; align-items: center;">
                        <a href="#" style="color: var(--sfc-red); text-decoration: none; font-weight: 600;">Good Samaritan Outreach Club visits Mukono Health Center</a>
                        <span style="font-size: 0.85rem; color: #777;">Dec 2025</span>
                    </li>
                    <li style="padding: 1rem 0; border-bottom: 1px solid var(--sfc-silver); display: flex; justify-content: space-between; align-items: center;">
                        <a href="#" style="color: var(--sfc-red); text-decoration: none; font-weight: 600;">UCE UNEB Examination Results: Outstanding Red Marks</a>
                        <span style="font-size: 0.85rem; color: #777;">Nov 2025</span>
                    </li>
                    <li style="padding: 1rem 0; border-bottom: 1px solid var(--sfc-silver); display: flex; justify-content: space-between; align-items: center;">
                        <a href="#" style="color: var(--sfc-red); text-decoration: none; font-weight: 600;">Nkobazambogo Club pays visit to Buganda Heritage palace</a>
                        <span style="font-size: 0.85rem; color: #777;">Jun 2025</span>
                    </li>
                    <li style="padding: 1rem 0; display: flex; justify-content: space-between; align-items: center;">
                        <a href="#" style="color: var(--sfc-red); text-decoration: none; font-weight: 600;">Inauguration of the new DIT computer laboratory</a>
                        <span style="font-size: 0.85rem; color: #777;">Sep 2024</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Success Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <h3>Subscribed Successfully!</h3>
            <p>Thank you for subscribing to the St. Francis Borgia High School newsletter. You will receive campus updates shortly.</p>
            <button class="btn btn-secondary" id="closeModalBtn">Close Window</button>
        </div>
    </div>
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>St. Francis Borgia</h3>
                    <p>High school mukono dedicated to providing holistic, high-quality secondary education rooted in academic excellence and Christian values.</p>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="academics.php">Academics</a></li>
                        <li><a href="admissions.php">Admissions</a></li>
                        <li><a href="facilities.php">Facilities</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Mukono Town, Uganda</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+256 772 622 612 / +256 754 465 536</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@stfrancisborgia.ac.ug</span>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>School Hours</h3>
                    <ul style="color: var(--sfc-silver); font-size: 0.95rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.5rem;">Monday - Friday: 7:30 AM - 4:30 PM</li>
                        <li style="margin-bottom: 0.5rem;">Saturday: 8:00 AM - 1:00 PM (Study/Clubs)</li>
                        <li>Sunday: Sabbath Worship & Rest</li>
                    </ul>
                    
                    <h3>Follow Us</h3>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> St. FRANCIS BORGIA HIGH SCHOOL MUKONO. All Rights Reserved. | <a href="admin/admin.php" style="color: var(--sfc-yellow); text-decoration: none; font-weight: 600;"><i class="fas fa-user-shield"></i> Admin Dashboard</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.getElementById('main-nav');
        
        if (menuToggle && mainNav) {
            menuToggle.addEventListener('click', () => {
                mainNav.classList.toggle('active');
            });
            
            document.addEventListener('click', (e) => {
                if (!menuToggle.contains(e.target) && !mainNav.contains(e.target)) {
                    mainNav.classList.remove('active');
                }
            });
        }
    </script>
    <!-- Neexa Widget -->
<script>
  window.neexaAsyncInit = function() {
    window.neexa.init({
      agent_id: 'a26358ec-5c33-49b1-bb99-8639edbe3689', mobile_mini_style: 'greeting_only',
    });
  };
</script>
<script async src="https://chat-widget.neexa.ai/main.js?nonce=1785475814179.8662"></script>
<!-- End Neexa Widget -->
</body>
</html>
