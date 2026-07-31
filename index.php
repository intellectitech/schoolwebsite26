   <?php
// Include your existing database connection file
require_once 'db_connect.php';

// Normalize the database connection variable ($pdo from db_connect.php)
$conn = $conn ?? $pdo ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bbina Islamic Primary School | Strive for Excellence</title>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Link to external CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Top Utility Contacts Bar -->
    <div class="top-bar">
        <div class="top-bar-contact">
            <span><i class="fa-solid fa-location-dot"></i> Kampala, Uganda</span>
            <span><i class="fa-solid fa-envelope"></i> infob@bbina.ac.ug</span>
            <span><i class="fa-solid fa-phone"></i> +256 772605229</span>
        </div>
        <div class="top-bar-social">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-youtube"></i></a>
        </div>
    </div>

    <!-- Main Header & Navigation -->
    <header>
        <a href="#" class="logo-container">
            <div class="logo-placeholder">
                <img class="logo-placeholder" src="images/bina.png" alt="Bbina Islamic Primary School Logo">
            </div>
            <div class="logo-text">
                <h1>Bbina Islamic Primary School</h1>
                <span>Primary</span>
            </div>
        </a>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="admmissions.php">Admissions</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="news.php">News & Gallery</a></li>
                <li><a href="admmissions.php" class="btn-apply">Apply Now</a></li>
            </ul>
        </nav>
    </header>

    <section style="background-image: url('images/hero5.jpg');" class="hero-banner">
        <div class="hero-content">
            <h2>WELCOME TO BBINA ISLAMIC PRIMARY SCHOOL</h2>
            <p>Bbina Islamic Primary School is a leading educational institution dedicated to providing comprehensive and high-quality learning experiences in Uganda.</p>
            <a href="#" class="btn-explore">Explore Our Programs <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </section>

    <!-- Student and Parent Portal Badges -->
    <div class="portal-bar">
        <a href="#" class="portal-item"><i class="fa-solid fa-user-graduate"></i> Student Portal Login</a>
        <a href="#" class="portal-item"><i class="fa-solid fa-users"></i> Parent Portal (SchoolPay)</a>
        <a href="#" class="portal-item"><i class="fa-solid fa-book-open"></i> E-Learning Platform</a>
    </div>

    <!-- Main Container Content Layout Split -->
    <div class="main-layout">
        
        <!-- Left Column: Primary Content Block -->
        <main>
            <section class="welcome-box">
                <h3 class="section-title">ABOUT US</h3>
                <p>Since our establishment, Bbina Islamic Primary School has consistently stood as a beacon of scholastic distinction and holistic growth within Central Uganda. We embrace a student-centered educational philosophy that empowers young minds to thrive in an ever-changing global climate.</p>
                <p>We provide a rich learning setting for standard UNEB evaluation models, ensuring every child achieves premium performance outcomes.</p>
                <a href="#" class="btn-inline">Read Message from the Headteacher <i class="fa-solid fa-angles-right"></i></a>
            </section>

            <section>
                <h3 class="section-title">Core Institutional Pillars</h3>
                <div class="grid-2">
                    <div class="value-card">
                        <h4><i class="fa-solid fa-star" style="color: var(--accent-color);"></i> Academic Excellence</h4>
                        <p>Consistently ranking among top division earners nationwide through rigorous instructional methodologies.</p>
                    </div>
                    <div class="value-card">
                        <h4><i class="fa-solid fa-shield-halved"></i> Character & Integrity</h4>
                        <p>Instilling firm moral values, discipline, and community accountability into our learners.</p>
                    </div>
                    <div class="value-card">
                        <h4><i class="fa-solid fa-volleyball"></i> Holistic Development</h4>
                        <p>Thriving sports clubs, award-winning drama setups, and practical computer programming boot camps.</p>
                    </div>
                    <div class="value-card">
                        <h4><i class="fa-solid fa-seedling"></i> Modern Facilities</h4>
                        <p>Fully equipped scientific laboratories, standard boarding amenities, and safe transportation loops.</p>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="section-title">Latest News & Announcements</h3>
                
                <div class="news-card">
                    <div class="news-img" style="background-image: url('https://unsplash.com');"></div>
                    <div class="news-info">
                        <h4><a href="#">Term One Online Registration Framework Activated</a></h4>
                        <p class="news-date"><i class="fa-regular fa-calendar"></i> January 10, 2026</p>
                    </div>
                </div>

                <div class="news-card">
                    <div class="news-img" style="background-image: url('https://unsplash.com');"></div>
                    <div class="news-info">
                        <h4><a href="#">Bbina Islamic Primary School Dominates Regional Inter-School Sports Gala</a></h4>
                        <p class="news-date"><i class="fa-regular fa-calendar"></i> November 14, 2025</p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Right Column: Sidebar Component Blocks -->
        <aside>
            <div class="sidebar-widget">
                <h4 class="sidebar-title">Upcoming Events</h4>
                
                <?php
                if (!$conn) {
                    echo "<p style='color: #d9534f; font-size: 0.9rem;'>Unable to connect to the database.</p>";
                } else {
                    try {
                        // Query fetching upcoming events using your exact table columns
                        $sql = "SELECT title, event_date, location FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5";
                        
                        if ($conn instanceof PDO) {
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            $result = $conn->query($sql);
                            $events = ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
                        }

                        if (!empty($events)) {
                            foreach ($events as $row) {
                                $event_timestamp = strtotime($row['event_date']);
                                $day = date('d', $event_timestamp);
                                $month = date('M', $event_timestamp);
                                $title = htmlspecialchars($row['title']);
                                $location = !empty($row['location']) ? htmlspecialchars($row['location']) : '';
                                ?>
                                <div class="event-item">
                                    <div class="event-date-box">
                                        <span class="event-day"><?php echo $day; ?></span>
                                        <span class="event-month"><?php echo $month; ?></span>
                                    </div>
                                    <div class="event-title">
                                        <?php echo $title; ?>
                                        <?php if ($location): ?>
                                            <br><small style="color: #777; font-size: 0.8rem;"><i class="fa-solid fa-location-dot"></i> <?php echo $location; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p class='no-events' style='font-size: 0.9rem; color: #666;'>No upcoming events scheduled at the moment.</p>";
                        }
                    } catch (Exception $e) {
                        echo "<p style='color: #d9534f; font-size: 0.9rem;'>Database query error: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                }
                ?>
            </div>

            <div class="sidebar-widget">
                <h4 class="sidebar-title">Quick Links</h4>
                <ul class="links-list">
                    <li><a href="#"><i class="fa-solid fa-file-pdf"></i> Download Fees Structure 2026</a></li>
                    <li><a href="#"><i class="fa-solid fa-file-arrow-download"></i> Admissions Application Form</a></li>
                    <li><a href="#"><i class="fa-solid fa-graduation-cap"></i> Recent UNEB Performance Results</a></li>
                    <li><a href="#"><i class="fa-solid fa-clipboard-list"></i> School Requirement Checklist</a></li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h4 class="sidebar-title">Contact Us</h4>
                <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Thank you for reaching out! We will contact you soon.');">
                    <div class="form-group">
                        <label>Your Full Name</label>
                        <input type="text" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" required placeholder="johndoe@gmail.com">
                    </div>
                    <div class="form-group">
                        <label>Message Subject</label>
                        <input type="text" class="form-control" required placeholder="Admission Inquiry">
                    </div>
                    <div class="form-group">
                        <label>Your Message</label>
                        <textarea class="form-control" rows="3" required placeholder="Enter your message here..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit" style="margin-top: 10px;">Send Message</button>
                </form>
            </div>
        </aside>

    </div>

    <!-- PREMIUM ARCHITECTURE FOOTER COMPONENT -->
    <footer class="premium-footer">
        <div class="footer-matrix-grid">
            
            <!-- Column One: School Profile & Social Media -->
            <div class="footer-matrix-col school-profile-summary">
                <div class="footer-brand-logo">
                    <div class="brand-crest-mini">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3>Bbina Islamic Primary School</h3>
                </div>
                <p class="school-motto-statement">"Strive For Excellence"</p>
                <p class="school-description">A premier standard-setting educational hub in Uganda dedicated to fostering technical competency networks, analytical thinking skillsets, and empathetic leadership values.</p>
                <div class="social-icon-networks">
                    <a href="#" class="network-badge facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="network-badge x-twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="network-badge youtube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="network-badge linkedin"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Column Two: Quick Portals Links -->
            <div class="footer-matrix-col">
                <h4 class="footer-col-title">Quick Portals</h4>
                <ul class="footer-links-list">
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Digital Student Portal</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> E-Learning Framework</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> UNEB Center Details</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Alumni Resource Circle</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Vacancies & Staff Hiring</a></li>
                </ul>
            </div>

            <!-- Column Three: Ministry Registration & Fees Channels -->
            <div class="footer-matrix-col">
                <h4 class="footer-col-title">Verification & Fees</h4>
                <p class="verification-meta">Fully registered by the Ministry of Education and Sports (MoES).</p>
                <p class="verification-meta"><strong>EMIS Number:</strong> 5558/130119</p>
                
                <h5 class="payment-title-heading">Supported Payment Channels</h5>
                <div class="payment-platforms-wrapper">
                    <div class="payment-badge-node" title="SchoolPay Framework Enabled">
                        <i class="fa-solid fa-wallet"></i> <span>SchoolPay Channel</span>
                    </div>
                    <div class="payment-badge-node" title="Mobile Money Routes Approved">
                        <i class="fa-solid fa-mobile-screen-button"></i> <span>Mobile Money Pay</span>
                    </div>
                </div>
            </div>

            <!-- Column Four: Physical Address & Coordination Details -->
            <div class="footer-matrix-col">
                <h4 class="footer-col-title">Contact & Location</h4>
                <ul class="footer-contact-coordinates">
                    <li>
                        <i class="fa-solid fa-location-dot coordinate-icon"></i>
                        <span>P.O box 76 Kampala,<br>Butabika road, Bbina, Nakawa Division</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope-open coordinate-icon"></i>
                        <span>admissions@bbina.ac.ug</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-headset coordinate-icon"></i>
                        <span>+256 772605229</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright & Legal Row Component -->
        <div class="footer-bottom-legal-row">
            <p>&copy; 2026 Bbina Islamic Primary School. All Rights Reserved. Authoritative Platform Dashboard. Developed by Edtech</p>
            <div class="legal-utilities-sub-links">
                <a href="#">Privacy Framework</a>
                <a href="#">Terms of Use</a>
                <a href="#">Web Portal Sitemap</a>
            </div>
        </div>
    </footer>

</body>
</html>