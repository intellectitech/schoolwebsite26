<?php
include 'database.php'; 

$query = "SELECT n.title, n.slug, n.excerpt, n.featured_image, n.published_at, c.name AS category_name, c.color AS category_color 
          FROM news n 
          LEFT JOIN news_categories c ON n.category_id = c.id 
          WHERE n.is_published = 1 
          ORDER BY n.published_at DESC, n.id DESC 
          LIMIT 3";

$result = $conn->query($query);
?>


<?php
// 1. Database Connection
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

// 2. Process Form Submission
$message = '';
$statusClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Basic Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $statusClass = "alert-error";
    } else {
        try {
            // Check if the email already exists in your table
            $stmt = $pdo->prepare("SELECT id, is_confirmed FROM newsletter_subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                if ($existing['is_confirmed'] == 1) {
                    $message = "This email is already subscribed to our newsletter!";
                    $statusClass = "alert-info";
                } else {
                    // If they unsubscribed or haven't confirmed previously, re-subscribe them
                    $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET is_confirmed = 1, name = ?, unsubscribed_at = NULL WHERE id = ?");
                    $stmt->execute([$name ?: 'Anonymous', $existing['id']]);
                    $message = "Welcome back! Your subscription has been reactivated.";
                    $statusClass = "alert-success";
                }
            } else {
                // Generate a random confirmation token for your confirm_token column
                $confirmToken = bin2hex(random_bytes(16));
                $subscribedAt = date('Y-m-d H:i:s');
                
                // Insert new record matching your exact schema layout
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
<body >
  

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
                <button class="menu-toggle-btn" id="menuOpenBtn"> <img width="40px"   class="menu-toggle-btn" class="menu-toggle-btn" src="assets/images/menubutton.svg" alt=""></button>
                  <img style="width: 80px;border-radius: 500%;background-color: white;height: 80px;margin-left: 30px; box-shadow: 0 10px 30px rgba(0, 2, 10, 0.754)    ;" src="assets/images/logoo.svg" alt="">
                <h2 >St. Henry’s College Namugongo</h2>  
                <a href="gallery.php">Gallery</a>
                    
            <a href="contact.php">Contact us</a>
            <a href="about.html">About us</a>
             <a href="admissions.php">Admissions</a>
                <a href="news.php">News</a>
          
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

  <div style="display: flex;" >

               <a  href="admissions.php">
                 <button class="btn-prime">Apply For Admission  
                <i class="bx bx-file"></i>
                </button>
               </a>

                 
            </div> 

            <a href="gallery.php">  <button class="btn-secondary">Virtual Campus Tour</button>  </a>
        
 
        </div>
        </div>
    </section>





    <div class="banner-container">
        <div class="banner-track">
            <!-- Repeating the string across the track creates the seamless loop loop -->
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


<br> <br> <br>





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

            <h3 class="count-engine"

            data-target="35">0</h3>
        
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
        <div  class="card">
            <img src="assets/images/WhatsApp Image 2026-07-07 at 7.17.28 PM.svg">
            <div class="card-content">
                   
                <h1>Sports    <i  class="bx bx-football"></i>   </h1>
                <p>Advanced curriculum architecture emphasizing data literacy, strategic thinking, and computational foundational mechanics.</p>
            </div>
        </div>

        <div class="card">
            <img src="assets/images/WhatsApp Image 2026-07-07 at 7.17.26 PM.svg" alt="Faculty Profile">
            <div class="card-content">
                <h1>Expert Educators <i class="bx bx-group"></i>      </h1>
                <p>Guided mentorship by leading technical practitioners dedicated to real-world capability development.</p>
            </div>
        </div>

        <div class="card">
            <img src="assets/images/1783360660660.svg" alt="Infrastructures">
            <div class="card-content">
                <h1>Elite Infrastructures <i class="bx bx-buildings"></i>          </h1>
                <p>High-end digital research facilities, robust computing centers, and premium environments for personal development.</p>
            </div>
        </div>


<div> 
  

    </main>

    <h1 style="text-align: center;">SCHOOL UPDATES</h1> <br>

    
          
            
      
          
         
      



<div class="container">
    
    
    <div class="news-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php 
                    $image = !empty($row['featured_image']) ? $row['featured_image'] : 'uploads/news/default-placeholder.png';
                    $cat_name = !empty($row['category_name']) ? $row['category_name'] : 'General';
                    $cat_color = !empty($row['category_color']) ? $row['category_color'] : '#1565C0';
                    $formatted_date = date("M d, Y", strtotime($row['published_at']));
                ?>
                <a href="news-detail.php?slug=<?php echo $row['slug']; ?>" class="news-card">
                    <div class="card-image-wrapper">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="News Banner" class="card-image">
                        <span class="category-badge" style="background-color: <?php echo htmlspecialchars($cat_color); ?>;">
                            <?php echo htmlspecialchars($cat_name); ?>
                        </span>
                    </div>
                    <div class="card-content">
                        <div class="card-date"><?php echo $formatted_date; ?></div>
                        <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="card-excerpt">
                            <?php echo htmlspecialchars(substr($row['excerpt'], 0, 120)) . (strlen($row['excerpt']) > 120 ? '...' : ''); ?>
                        </p>
                        <span class="read-more">Read Full Article &rarr;</span>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-news">
                <p>No recent news articles have been published yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>










































         
    




    <!-- Industrial Dark Split News Grid System -->
    <section  class="news-section">
        <div  class="news-container">
            <h2>Admissions and Quiry</h2>
            <div style="box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.8)" class="news-card-wrapper">
                <div class="news-row-card">
                    <div class="news-img-pane" style="background-image: url('assets/images/contact.svg');"></div>
                    <div class="news-text-pane">
                        <div class="news-date-badge">contact us</div>
                        <h4>"Get in Touch with Us"</h4>
                        <p>"Have questions about admissions, academics, or school life? Reach out to our team today, and we will get back to you as soon as possible."</p> <br>
  <a href="contact.html">   <button class="btn-prime">Contact us</button>   </a>
   

                    </div>
                </div>


                <div class="news-row-card">
                    <div class="news-img-pane" style="background-image: url('assets/images/admisiion.svg');"></div>
                    <div class="news-text-pane">
                        <div class="news-date-badge">Admissions</div>

                        <h4>"Join the St. Henry’s Family"</h4>
                        <p> "We welcome bright, disciplined, and ambitious minds to grow with us. Discover our enrollment process and secure your child's future today."</p><br>
  
   <a href="admissions.php">  <button class="btn-prime">Apply For Admission</button>          </a>

                    </div>
                </div>
            </div>
        </div>
    </section>
  

    <!-- Your website content -->

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
  <!-- Top Brand Panel -->
  <div class="footer-top-branding">
    <div class="branding-wrapper">

      <div class="college-logo-badge">
        <!-- SVG Icon representing a traditional university crest -->
    
      </div>
      <img style="width: 60px;height: 60px;border-radius: 500%;box-shadow:     0 4px 6px -1px rgb(0, 0, 0);" src="assets/images/logoo.svg" alt="">
      <div class="college-title-block">
        <h2>ST. HENRY’S COLLEGE NAMUGONGO</h2>
        <p class="motto-tagline">"FOR GREATER HORIZONS"</p>
      </div>

<?php if (!empty($message)): ?>
        <div class="alert <?= $statusClass ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

  
  <form action="" method="POST">
    <div class="newsletter-inline-bo">
  <input type="text"  id="name"  placeholder="Name" aria-label="Email subscription" name="name"   > <br>

 
        <input type="email" id="email"    placeholder=" (email)Subscribe to College Newsletter" aria-label="Email subscription" name="email"  > <br>

        <button type="submit"    class="btn-submit" type="button">Join</button>
      
      </div>
    </div>
  </div>


  </form>


  <!-- Main Links Navigation Grid -->
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
        <p><strong>Email:</strong>sthenryscollegenamugongo2@gmail.com</p>
        <div class="social-icon-row">
          <a href="#" aria-label="X Platform">𝕏</a>
          <a href="#" aria-label="Facebook Platform">f</a>
          <a href="#" aria-label="YouTube Channel">▶</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bottom Legal Bar -->
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




































    <!-- Interactive JavaScript Controls -->
    <script>
        // Drawer Navigation Logic Toggle Elements
        const menuOpenBtn = document.getElementById('menuOpenBtn');
        const menuCloseBtn = document.getElementById('menuCloseBtn');
        const bodyElement = document.body;

        menuOpenBtn.addEventListener('click', () => bodyElement.classList.add('sidebar-open'));
        menuCloseBtn.addEventListener('click', () => bodyElement.classList.remove('sidebar-open'));
        document.getElementById('sidebarOverlay').addEventListener('click', () => bodyElement.classList.remove('sidebar-open'));

        // Dynamic Sticky Header Styling Switch Engine
        const mainHeader = document.getElementById('mainHeader');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                mainHeader.classList.add('scrolled');
            } else {
                mainHeader.classList.remove('scrolled');
            }
        });

        // Loop Controls for Hero Cinematic Banner
        const heroSlides = document.querySelectorAll('.slide-layer');
        let activeSlideIndex = 0;

        setInterval(() => {
            heroSlides[activeSlideIndex].classList.remove('active');
            activeSlideIndex = (activeSlideIndex + 1) % heroSlides.length;
            heroSlides[activeSlideIndex].classList.add('active');
        }, 5000);

        // Responsive Number Count Animation Logic Setup
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




<style>
   
        
        .container {
          
            margin: 0 auto;
          
        }
        .section-title {
            font-size: 32px;
            color: #1565C0;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
        }
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }
        .news-card {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px #081b548c;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
        
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .card-image-wrapper {
            position: relative;
            width: 100%;
            height: 200px;
            background-color: #e9ecef;
        }
        .card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .category-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            color: #ffffff;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 20px;
            text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-date {
            font-size: 13px;
            color: #888888;
            margin-bottom: 10px;
        }
        .card-title {
            font-size: 18px;
            color: #222222;
            margin-bottom: 12px;
            line-height: 1.4;
            font-weight: 600;
        }
        .card-excerpt {
            font-size: 14px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .read-more {
            font-size: 14px;
            color: #1565C0;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .no-news {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #777777;
            background: #ffffff;
            border-radius: 8px;
            border: 1px dashed #cccccc;
        }
    </style>













</body>
</html>
<?php $conn->close(); ?>