<?php
// Include database connection
require_once "database.php";

// Fetch ONLY the 3 most recent articles
$result = $conn->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 3");
?>


<?php
// 1. Database Connection & Configuration
$host     = '127.0.0.1';
$db       = 'school_website_db'; 
$user     = 'root';              
$password = '';                  
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// 2. Fetch Latest News (Limit strict to 3)
$newsQuery = "SELECT n.title, n.slug, n.excerpt, n.featured_image, n.published_at, c.name AS category_name, c.color AS category_color 
              FROM news n 
              LEFT JOIN news_categories c ON n.category_id = c.id 
              WHERE n.is_published = 1 
              ORDER BY n.published_at DESC, n.id DESC 
              LIMIT 3";

try {
    $newsStmt = $pdo->query($newsQuery);
    $newsList = $newsStmt->fetchAll();
} catch (\PDOException $e) {
    $newsList = [];
}

// 3. Process Newsletter Form Submission
$message = '';
$statusClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $statusClass = "alert-error";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, is_confirmed FROM newsletter_subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                if ($existing['is_confirmed'] == 1) {
                    $message = "This email is already subscribed to our newsletter!";
                    $statusClass = "alert-info";
                } else {
                    $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET is_confirmed = 1, name = ?, unsubscribed_at = NULL WHERE id = ?");
                    $stmt->execute([$name ?: 'Anonymous', $existing['id']]);
                    $message = "Welcome back! Your subscription has been reactivated.";
                    $statusClass = "alert-success";
                }
            } else {
                $confirmToken = bin2hex(random_bytes(16));
                $subscribedAt = date('Y-m-d H:i:s');
                
                $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (name, email, is_confirmed, confirm_token, subscribed_at) VALUES (?, ?, 1, ?, ?)");
                $stmt->execute([$name ?: 'Anonymous', $email, $confirmToken, $subscribedAt]);
                
                $message = "Thank you! You have successfully subscribed to our newsletter.";
                $statusClass = "alert-success";
            }
        } catch (\PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $statusClass = "alert-error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOMEPAGE</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admissions.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

    <!-- Unified Header Engine Container -->
    <div class="header-container" id="mainHeader">
        <div class="utility-bar">
            <div>
                <span>Call: <b>+256 700 380950</b></span>
                <span>Mail: <b>sthenryscollegenamugongo2@gmail.com</b></span>
            </div>
        </div>
        <header style="height: 95px;" class="main-navbar">
            <div class="navbar-brand-pane">
                <button class="menu-toggle-btn" id="menuOpenBtn"> <img width="40px" class="menu-toggle-btn" src="assets/images/menubutton.svg" alt=""></button>
                <img style="width: 80px;border-radius: 500%;background-color: white;height: 80px;margin-left: 30px; box-shadow: 0 10px 30px rgba(0, 2, 10, 0.754);" src="assets/images/logoo.svg" alt="">
                <h2>St. Henry’s College Namugongo</h2>  
                <a href="gallery.php">Gallery</a>
                <a href="contact.php">Contact us</a>
                <a href="about.html">About us</a>
                <a href="admissions.php">Admissions</a>
                <a href="news-detail.php">News</a>
            </div>

            <style> 
                header a{
                    text-decoration: none;  
                    font-size: 15px;
                    color: rgba(2, 6, 16, 0.98);
                }
            </style>
        </header>
    </div>

    <!-- Minimal Overlay Panel Drawer Nav -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="app-sidebar" id="sidebarPanel">
        <button class="close-sidebar-btn" id="menuCloseBtn">&times;</button>
        <nav class="navigation-menu">
            <img style="width: 100px;border-radius: 500%;background-color: white;height: 100px;margin-left: 30px;" src="assets/images/logoo.svg" alt="">
            <a href="index.php" class="active">Overview Home</a>
            <a href="about.html">About us</a>
            <a href="admissions.php">Admissions Panel</a>
            <a href="contact.html">Contact us</a>
        </nav>
    </aside> 

    <!-- Cinematic Hero Showcase -->
    <section style="margin-top: 115px;" class="hero-banner">
        <div class="hero-slideshow-container">
            <div class="slide-layer active" style="background-image: url('assets/images/New\ Project.svg');"></div>
            <div class="slide-layer" style="background-image: url('assets/images/image2.svg');"></div>
            <div class="slide-layer" style="background-image: url('assets/images/image3.svg');"></div>
            <div class="slide-layer" style="background-image: url('assets/images/image4.svg');"></div>
        </div>
        <div class="hero-content">
            <br>
            <h1>Where Academic Brilliance Meets Moral Integrity</h1>
            <p>Cultivating academic success while simultaneously building strong character, self-esteem, and positive decision-making skills.</p>
            <div class="hero-action-buttons">
                <div style="display: flex;">
                    <a href="admissions.php">
                        <button class="btn-prime">Apply For Admission <i class="bx bx-file"></i></button>
                    </a>
                </div> 
                <a href="gallery.php"><button class="btn-secondary">Virtual Campus Tour</button></a>
            </div>
        </div>
    </section>

    <div class="banner-container">
        <div class="banner-track">
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
        </div>
    </div>

    <br><br><br>

    <!-- Professional Metrics Showcase Block -->
    <section class="school-metrics">
        <div class="metric-block">
            <img width="20px" src="assets/images/metrics1.svg" alt="">
            <h3 class="count-engine" data-target="98">0</h3>
            <p>School Admission Rate</p>
        </div>
        <div class="metric-block">
            <img width="20px" src="assets/images/metrics2.svg" alt="">
            <h3>1:12</h3>
            <p>Teacher:Student Ratio</p>
        </div>
        <div class="metric-block">
            <img width="20px" src="assets/images/metrics3.svg" alt="">
            <h3 class="count-engine" data-target="35">0</h3>
            <p>Extracurricular Clubs</p>
        </div>
        <div class="metric-block">
            <img width="20px" src="assets/images/tubeg.svg" alt="">
            <h3 class="count-engine" data-target="100">0</h3>
            <p>Digital Lab Connectivity</p>
        </div>
    </section>

    <!-- Core Values Column Layout Components -->
    <main class="cardarrange">
        <div class="card">
            <img src="assets/images/WhatsApp Image 2026-07-07 at 7.17.28 PM.svg" alt="Sports">
            <div class="card-content">
                <h1>Sports <i class="bx bx-football"></i></h1>
                <p>Advanced curriculum architecture emphasizing data literacy, strategic thinking, and computational foundational mechanics.</p>
            </div>
        </div>

        <div class="card">
            <img src="assets/images/WhatsApp Image 2026-07-07 at 7.17.26 PM.svg" alt="Faculty Profile">
            <div class="card-content">
                <h1>Expert Educators <i class="bx bx-group"></i></h1>
                <p>Guided mentorship by leading technical practitioners dedicated to real-world capability development.</p>
            </div>
        </div>

        <div class="card">
            <img src="assets/images/1783360660660.svg" alt="Infrastructures">
            <div class="card-content">
                <h1>Elite Infrastructures <i class="bx bx-buildings"></i></h1>
                <p>High-end digital research facilities, robust computing centers, and premium environments for personal development.</p>
            </div>
        </div>
    </main>





<!-- ================= ARTICLES PREVIEW WIDGET ================= -->
<div class="articles-preview-container">
    <div class="preview-header">
        <div class="header-title">
            <i class='bx bx-news'></i>
            <h3>Latest Articles</h3>
        </div>
        <span class="preview-badge">Recent 3</span>
    </div>

    <div class="preview-feed">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <article class="preview-card">
                    <div class="card-header-row">
                        <h4 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h4>
                    </div>
                    
                    <div class="card-meta">
                        <span class="meta-author">
                            <i class='bx bx-user-circle'></i>
                            <?php echo htmlspecialchars($row['author']); ?>
                        </span>
                        <span class="meta-date">
                            <i class='bx bx-time-five'></i>
                            <?php echo date("M j, Y • g:i a", strtotime($row['created_at'])); ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <?php echo htmlspecialchars($row['content']); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-preview">
                <i class='bx bx-folder-open'></i>
                <p>No articles published yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>



















































































    <h1 style="text-align: center; margin-top: 40px;">SCHOOL UPDATES</h1> <br>

    <!-- FEATURED EDITORIAL DISPLAY (STRICT LIMIT OF 3 ARTICLES) -->
    <section class="news-updates-wrapper">
        <div class="news-updates-container">
            <?php if (!empty($newsList)): ?>
                <?php 
                    // 1st Article: Takes the Big Featured Spotlight Card
                    $featuredNews  = $newsList[0];
                    $featuredImage = !empty($featuredNews['featured_image']) ? $featuredNews['featured_image'] : 'uploads/news/default-placeholder.png';
                    $featuredCat   = !empty($featuredNews['category_name']) ? $featuredNews['category_name'] : 'General';
                    $featuredColor = !empty($featuredNews['category_color']) ? $featuredNews['category_color'] : '#1565C0';
                    $featuredDate  = date("M d, Y", strtotime($featuredNews['published_at']));

                    // 2nd and 3rd Articles: Take the Side Column
                    $sideNewsList  = array_slice($newsList, 1, 2);
                ?>

                <div class="news-magazine-grid">
                    <!-- Article #1: Big Spotlight Banner -->
                    <a href="news-detail.php?slug=<?php echo htmlspecialchars($featuredNews['slug']); ?>" class="magazine-featured-card">
                        <div class="featured-image-wrapper">
                            <img src="<?php echo htmlspecialchars($featuredImage); ?>" alt="Featured Headline Image">
                            <span class="mag-badge" style="background-color: <?php echo htmlspecialchars($featuredColor); ?>;">
                                <?php echo htmlspecialchars($featuredCat); ?>
                            </span>
                        </div>
                        <div class="featured-card-body">
                            <div class="mag-date"><i class='bx bx-calendar'></i> <?php echo $featuredDate; ?></div>
                            <h2 class="mag-title"><?php echo htmlspecialchars($featuredNews['title']); ?></h2>
                            <p class="mag-excerpt">
                                <?php echo htmlspecialchars(substr($featuredNews['excerpt'], 0, 160)) . (strlen($featuredNews['excerpt']) > 160 ? '...' : ''); ?>
                            </p>
                            <span class="mag-read-btn">Read Full Story <i class='bx bx-right-arrow-alt'></i></span>
                        </div>
                    </a>

                    <!-- Articles #2 & #3: Stacked Side Cards -->
                    <div class="magazine-side-column">
                        <?php if (!empty($sideNewsList)): ?>
                            <?php foreach($sideNewsList as $row): ?>
                                <?php 
                                    $subImage = !empty($row['featured_image']) ? $row['featured_image'] : 'uploads/news/default-placeholder.png';
                                    $subCat   = !empty($row['category_name']) ? $row['category_name'] : 'General';
                                    $subColor = !empty($row['category_color']) ? $row['category_color'] : '#1565C0';
                                    $subDate  = date("M d, Y", strtotime($row['published_at']));
                                ?>
                                <a href="news-detail.php?slug=<?php echo htmlspecialchars($row['slug']); ?>" class="magazine-side-card">
                                    <div class="side-image-wrapper">
                                        <img src="<?php echo htmlspecialchars($subImage); ?>" alt="News Thumbnail">
                                    </div>
                                    <div class="side-card-body">
                                        <div class="mag-meta">
                                            <span class="mag-badge-sm" style="background-color: <?php echo htmlspecialchars($subColor); ?>;">
                                                <?php echo htmlspecialchars($subCat); ?>
                                            </span>
                                            <span class="mag-date-sm"><?php echo $subDate; ?></span>
                                        </div>
                                        <h4 class="side-title"><?php echo htmlspecialchars($row['title']); ?></h4>
                                        <p class="side-excerpt">
                                            <?php echo htmlspecialchars(substr($row['excerpt'], 0, 80)) . (strlen($row['excerpt']) > 80 ? '...' : ''); ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <div class="no-news">
                    <p>No recent news articles have been published yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Industrial Dark Split News Grid System -->
    <section class="news-section">
        <div class="news-container">
            <h2>Admissions and Inquiry</h2>
            <div style="box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.8)" class="news-card-wrapper">
                <div class="news-row-card">
                    <div class="news-img-pane" style="background-image: url('assets/images/contact.svg');"></div>
                    <div class="news-text-pane">
                        <div class="news-date-badge">contact us</div>
                        <h4>"Get in Touch with Us"</h4>
                        <p>"Have questions about admissions, academics, or school life? Reach out to our team today, and we will get back to you as soon as possible."</p> <br>
                        <a href="contact.php"><button class="btn-prime">Contact us</button></a>
                    </div>
                </div>

                <div class="news-row-card">
                    <div class="news-img-pane" style="background-image: url('assets/images/admisiion.svg');"></div>
                    <div class="news-text-pane">
                        <div class="news-date-badge">Admissions</div>
                        <h4>"Join the St. Henry’s Family"</h4>
                        <p>"We welcome bright, disciplined, and ambitious minds to grow with us. Discover our enrollment process and secure your child's future today."</p><br>
                        <a href="admissions.php"><button class="btn-prime">Apply For Admission</button></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Neexa Widget -->
    <script>
      window.neexaAsyncInit = function () {
        window.neexa.init({
          agent_id: "a2356ca1-dfa0-481a-b4cc-7fec83b2d119",
          mobile_mini_style: "greeting_only",
        });
      };
    </script>
    <script src="https://chat-widget.neexa.ai/main.js?nonce=1783503857276.8518"></script>

    <footer class="shcn-modern-footer">
        <div class="footer-top-branding">
            <div class="branding-wrapper">
                <div class="college-logo-badge"></div>
                <img style="width: 60px;height: 60px;border-radius: 500%;box-shadow: 0 4px 6px -1px rgb(0, 0, 0);" src="assets/images/logoo.svg" alt="">
                <div class="college-title-block">
                    <h2>ST. HENRY’S COLLEGE NAMUGONGO</h2>
                    <p class="motto-tagline">"FOR GREATER HORIZONS"</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert <?= htmlspecialchars($statusClass) ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="newsletter-inline-bo">
                        <input type="text" id="name" placeholder="Name" aria-label="Name subscription" name="name"><br>
                        <input type="email" id="email" placeholder="(email) Subscribe to College Newsletter" aria-label="Email subscription" name="email"><br>
                        <button type="submit" class="btn-submit">Join</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer-grid-body">
            <div class="grid-column">
                <h3>Academic Portals</h3>
                <nav class="nav-links-stack">
                    <a href="#curriculum">O & A Level Curriculum</a>
                    <a href="#admissions">Admissions & Fees Structure</a>
                    <a href="#e-learning">Digital Student Portal</a>
                    <a href="#library">E-Library Resources</a>
                </nav>
            </div>

            <div class="grid-column">
                <h3>School Life</h3>
                <nav class="nav-links-stack">
                    <a href="#houses">Residential Houses & Dorms</a>
                    <a href="#sports">Sports & Co-curricular Activities</a>
                    <a href="#clubs">Clubs, Music & Drama Societies</a>
                    <a href="#chapel">Spiritual Life & Chaplaincy</a>
                </nav>
            </div>

            <div class="grid-column">
                <h3>The Community</h3>
                <nav class="nav-links-stack">
                    <a href="gallery.php">Gallery</a>
                    <a href="#pta">Parents & Teachers Association (PTA)</a>
                    <a href="news.php">Latest College News & Announcements</a>
                    <a href="gallery.php">Campus Photo Records</a>
                </nav>
            </div>

            <div class="grid-column contact-card-column">
                <div class="contact-card-box">
                    <h3>Reach Us</h3>
                    <p><strong>Location:</strong> Namugongo, Wakiso District, Uganda</p>
                    <p><strong>Hotlines:</strong> +256 414 000 000 | +256 701 000 000</p>
                    <p><strong>Email:</strong> sthenryscollegenamugongo2@gmail.com</p>
                    <div class="social-icon-row">
                        <a href="#" aria-label="X Platform">𝕏</a>
                        <a href="#" aria-label="Facebook Platform">f</a>
                        <a href="#" aria-label="YouTube Channel">▶</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-base-legal">
            <div class="legal-container">
                <p>&copy; 2026 St. Henry’s College Namugongo. All Rights Reserved.</p>
                <div class="legal-sub-links">
                    <a href="#privacy">Privacy Policy</a>
                    <a href="#terms">Terms & services</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const menuOpenBtn = document.getElementById('menuOpenBtn');
        const menuCloseBtn = document.getElementById('menuCloseBtn');
        const bodyElement = document.body;

        menuOpenBtn.addEventListener('click', () => bodyElement.classList.add('sidebar-open'));
        menuCloseBtn.addEventListener('click', () => bodyElement.classList.remove('sidebar-open'));
        document.getElementById('sidebarOverlay').addEventListener('click', () => bodyElement.classList.remove('sidebar-open'));

        const mainHeader = document.getElementById('mainHeader');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                mainHeader.classList.add('scrolled');
            } else {
                mainHeader.classList.remove('scrolled');
            }
        });

        const heroSlides = document.querySelectorAll('.slide-layer');
        let activeSlideIndex = 0;

        setInterval(() => {
            heroSlides[activeSlideIndex].classList.remove('active');
            activeSlideIndex = (activeSlideIndex + 1) % heroSlides.length;
            heroSlides[activeSlideIndex].classList.add('active');
        }, 5000);

        const countingMetrics = document.querySelectorAll('.count-engine');
        
        const triggerCounterAnimation = (metricElement) => {
            const finalValue = parseInt(metricElement.getAttribute('data-target'));
            let currentCount = 0;
            const stepIncrement = finalValue / 40;
            
            const runCounter = () => {
                currentCount += stepIncrement;
                if(currentCount < finalValue) {
                    metricElement.innerText = Math.ceil(currentCount) + '%';
                    setTimeout(runCounter, 25);
                } else {
                    metricElement.innerText = finalValue + '%';
                }
            };
            runCounter();
        };

        const viewObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    triggerCounterAnimation(entry.target);
                    viewObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.6 });

        countingMetrics.forEach(metric => viewObserver.observe(metric));
    </script>

    <!-- MAGAZINE NEWS SECTION STYLES -->
    <style>
        .news-updates-wrapper {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto 50px auto;
        }

        .news-magazine-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
            align-items: stretch;
        }

        /* Large Featured Card (Main Spotlight) */
        .magazine-featured-card {
            background: #060926;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .magazine-featured-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(21, 101, 192, 0.25);
        }

        .featured-image-wrapper {
            position: relative;
            width: 100%;
            height: 260px;
            background-color: #0c1438;
            overflow: hidden;
        }

        .featured-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .magazine-featured-card:hover .featured-image-wrapper img {
            transform: scale(1.05);
        }

        .mag-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            color: #ffffff;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .featured-card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .mag-date {
            font-size: 13px;
            color: #f6cb0f;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .mag-title {
            font-size: 20px;
            color: #ffffff;
            margin-bottom: 10px;
            line-height: 1.35;
            font-weight: 700;
        }

        .mag-excerpt {
            font-size: 14px;
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .mag-read-btn {
            font-size: 14px;
            color: #38bdf8;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Side List Column (Sub-stories #2 & #3) */
        .magazine-side-column {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
        }

        .magazine-side-card {
            background: #060926;
            border-radius: 12px;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            gap: 14px;
            height: 100%;
            text-decoration: none;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .magazine-side-card:hover {
            transform: translateX(4px);
            background: #0d123d;
        }

        .side-image-wrapper {
            width: 110px;
            height: 100%;
            min-height: 90px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background-color: #0c1438;
        }

        .side-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .side-card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .mag-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .mag-badge-sm {
            color: #ffffff;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 12px;
            text-transform: uppercase;
        }

        .mag-date-sm {
            font-size: 11.5px;
            color: #94a3b8;
        }

        .side-title {
            font-size: 15px;
            color: #ffffff;
            margin-bottom: 6px;
            line-height: 1.3;
            font-weight: 600;
        }

        .side-excerpt {
            font-size: 12.5px;
            color: #94a3b8;
            line-height: 1.4;
        }

        .no-news {
            text-align: center;
            padding: 40px;
            color: #f87171;
            background: #2b0d0d;
            border-radius: 12px;
            border: 1px dashed #ef4444;
        }

        /* Responsive Viewport Optimizations */
        @media (max-width: 868px) {
            .news-magazine-grid {
                grid-template-columns: 1fr;
            }
            .side-image-wrapper {
                width: 90px;
                height: 90px;
            }
        }
    </style>







<style>

/* White & Blue Styled 3-Article Preview Widget */
:root {
    --primary-blue: #1d4ed8;
    --primary-hover: #1e40af;
    --light-blue: #f0f6ff;
    --border-blue: #bfdbfe;
    --text-dark: #0f172a;
    --text-muted: #64748b;
    --border-gray: #e2e8f0;
    --card-bg: #ffffff;
}

.articles-preview-container {
    background: var(--card-bg);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
}

/* Subtle top highlight bar */
.articles-preview-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1d4ed8, #60a5fa);
}

.preview-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-gray);
    margin-bottom: 14px;
}

.header-title {
    display: flex;
    align-items: center;
    gap: 8px;
}

.header-title i {
    font-size: 22px;
    color: var(--primary-blue);
}

.header-title h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

.preview-badge {
    background: var(--light-blue);
    color: var(--primary-blue);
    border: 1px solid var(--border-blue);
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Container limits output cleanly */
.preview-feed {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Individual Card Styling */
.preview-card {
    background: #ffffff;
    border: 1px solid var(--border-gray);
    border-left: 4px solid var(--primary-blue);
    padding: 14px 16px;
    border-radius: 8px;
    transition: all 0.2s ease-in-out;
}

.preview-card:hover {
    border-color: var(--border-blue);
    border-left-color: var(--primary-blue);
    transform: translateX(2px);
    box-shadow: 0 4px 12px rgba(29, 78, 216, 0.08);
}

.card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary-blue);
    margin: 0 0 6px 0;
    line-height: 1.35;
}

.card-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 11px;
    color: var(--text-muted);
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px dashed var(--border-gray);
}

.meta-author {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--light-blue);
    color: var(--primary-blue);
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.meta-date {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.card-body {
    font-size: 13px;
    color: #334155;
    line-height: 1.5;
    white-space: pre-line;
    /* Clean 3-line preview text clamp */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Empty State */
.empty-preview {
    text-align: center;
    padding: 24px 12px;
    background: var(--light-blue);
    border: 1px dashed var(--border-blue);
    border-radius: 8px;
    color: var(--text-muted);
    font-size: 12px;
}

.empty-preview i {
    font-size: 32px;
    color: var(--primary-blue);
    display: block;
    margin-bottom: 6px;
}




/* 1. Reduce overall container padding */
.articles-preview-container {
    padding: 10px 12px; /* Decreased from 18px */
    border-radius: 8px;
}

/* 2. Shrink header size and spacing */
.preview-header {
    padding-bottom: 6px;  /* Decreased from 12px */
    margin-bottom: 8px;   /* Decreased from 14px */
}

.header-title h3 {
    font-size: 13px; /* Decreased from 16px */
}

.header-title i {
    font-size: 18px; /* Decreased from 22px */
}

.preview-badge {
    padding: 2px 6px;  /* Decreased padding */
    font-size: 9px;   /* Decreased from 11px */
}

/* 3. Reduce spacing between cards */
.preview-feed {
    gap: 8px; /* Decreased from 12px */
}

/* 4. Shrink individual card padding and borders */
.preview-card {
    padding: 8px 10px;          /* Decreased from 14px 16px */
    border-left-width: 3px;    /* Thinner left accent border */
    border-radius: 6px;
}

/* 5. Reduce Title, Author, and Date sizes */
.card-title {
    font-size: 13px;  /* Decreased from 15px */
    margin: 0 0 3px 0; /* Minimal bottom space */
}

.card-meta {
    gap: 8px;
    font-size: 10px;     /* Decreased from 11px */
    margin-bottom: 6px;   /* Decreased from 10px */
    padding-bottom: 4px;  /* Decreased from 8px */
}

.meta-author {
    padding: 1px 5px; /* Compact author pill */
}

/* 6. Shorten the text body preview to 2 lines */
.card-body {
    font-size: 11px;            /* Decreased from 13px */
    line-height: 1.35;          /* Tighter line spacing */
    -webkit-line-clamp: 2;      /* Limit text snippet to 2 lines instead of 3 */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

</style>



















<script>
document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // 1. DYNAMIC ARTICLES SEARCH & FILTERING (JS UI Display)
    // -------------------------------------------------------------
    const articlesFeed = document.querySelector('.preview-feed');
    const articleCards = document.querySelectorAll('.preview-card');

    if (articlesFeed && articleCards.length > 0) {
        // Create an inline quick filter bar dynamically above the articles widget
        const filterBar = document.createElement('div');
        filterBar.className = 'js-article-filter-bar';
        filterBar.innerHTML = `
            <div style="display: flex; gap: 8px; margin-bottom: 10px; width: 100%;">
                <div style="position: relative; flex-grow: 1;">
                    <i class='bx bx-search' style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 14px;"></i>
                    <input type="text" id="jsArticleSearch" placeholder="Search recent articles..." style="width: 100%; padding: 4px 8px 4px 26px; border-radius: 6px; border: 1px solid #bfdbfe; font-size: 11px; outline: none;">
                </div>
            </div>
        `;
        articlesFeed.parentNode.insertBefore(filterBar, articlesFeed);

        // Filter Logic
        const searchInput = document.getElementById('jsArticleSearch');
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            articleCards.forEach(card => {
                const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const body = card.querySelector('.card-body')?.textContent.toLowerCase() || '';
                if (title.includes(query) || body.includes(query)) {
                    card.style.display = 'block';
                    card.style.animation = 'jsFadeIn 0.3s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // -------------------------------------------------------------
    // 2. EXPAND / COLLAPSE ARTICLE PREVIEWS (Interactive JS Toggle)
    // -------------------------------------------------------------
    articleCards.forEach(card => {
        const cardBody = card.querySelector('.card-body');
        if (cardBody && cardBody.scrollHeight > 40) {
            const expandBtn = document.createElement('button');
            expandBtn.className = 'js-expand-btn';
            expandBtn.innerText = 'Read Snippet';
            expandBtn.style.cssText = 'background:none; border:none; color:#1d4ed8; font-size:10px; font-weight:700; cursor:pointer; padding:2px 0 0 0; margin-top:2px; display:block;';
            
            expandBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const isExpanded = cardBody.style.webkitLineClamp === 'unset';
                if (isExpanded) {
                    cardBody.style.webkitLineClamp = '2';
                    expandBtn.innerText = 'Read Snippet';
                } else {
                    cardBody.style.webkitLineClamp = 'unset';
                    expandBtn.innerText = 'Show Less';
                }
            });
            card.appendChild(expandBtn);
        }
    });

    // -------------------------------------------------------------
    // 3. SCROLL REVEAL ANIMATIONS FOR HOMEPAGE CARDS & NEWS
    // -------------------------------------------------------------
    const revealElements = document.querySelectorAll('.card, .magazine-featured-card, .magazine-side-card, .news-row-card, .articles-preview-container');
    
    // Set initial hidden style
    revealElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(25px)';
        el.style.transition = 'opacity 0.6s cubic-bezier(0.165, 0.84, 0.44, 1), transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1)';
    });

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealElements.forEach(el => revealObserver.observe(el));

    // -------------------------------------------------------------
    // 4. CURSOR HOVER TILT EFFECT ON CORE VALUES CARDS
    // -------------------------------------------------------------
    const interactiveCards = document.querySelectorAll('.cardarrange .card');
    interactiveCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -6;
            const rotateY = ((x - centerX) / centerX) * 6;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
        });
    });
});
</script>

<style>
/* Keyframe animation for search filter transitions */
@keyframes jsFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.js-article-filter-bar input:focus {
    border-color: #1d4ed8 !important;
    box-shadow: 0 0 0 2px rgba(29, 78, 216, 0.15);
}
</style>
























































</body>




</html>