<?php
include 'database.php'; 

$query = "SELECT n.title, n.slug, n.excerpt, n.featured_image, n.published_at, c.name AS category_name, c.color AS category_color 
          FROM news n 
          LEFT JOIN news_categories c ON n.category_id = c.id 
          WHERE n.is_published = 1 
          ORDER BY n.published_at DESC, n.id DESC 
          LIMIT 40";

$result = $conn->query($query);

// Fetch all items into an array to split the 1st (Featured) from the rest
$articles = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
}
$featured = !empty($articles) ? array_shift($articles) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus News & Updates</title>
    
    <!-- Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

    <!-- Header / Title Section -->
    <header class="news-header-hero">
        <div class="header-overlay"></div>
        <div class="header-content">
            <span class="subhead-tag"><i class='bx bx-news'></i> Stay Updated</span>
            <h1>Latest News & Announcements</h1>
            <p>Discover the latest stories, achievements, and events happening around our campus community.</p>
        </div>
    </header>

    <div class="main-container">
        
        <?php if ($featured): ?>
            <!-- 🌟 Spotlight Featured Article (First Item) -->
            <?php 
                $feat_image = !empty($featured['featured_image']) ? $featured['featured_image'] : 'uploads/news/default-placeholder.png';
                $feat_cat = !empty($featured['category_name']) ? $featured['category_name'] : 'General';
                $feat_color = !empty($featured['category_color']) ? $featured['category_color'] : '#1565C0';
                $feat_date = date("F d, Y", strtotime($featured['published_at']));
            ?>
            <section class="featured-wrapper">
                <a href="news-detail.php?slug=<?php echo htmlspecialchars($featured['slug']); ?>" class="featured-hero-card">
                    <div class="featured-image-pane">
                        <img src="<?php echo htmlspecialchars($feat_image); ?>" alt="Featured News Image">
                        <span class="category-badge" style="background-color: <?php echo htmlspecialchars($feat_color); ?>;">
                            ★ Featured • <?php echo htmlspecialchars($feat_cat); ?>
                        </span>
                    </div>
                    <div class="featured-text-pane">
                        <div class="card-meta">
                            <span class="card-date"><i class='bx bx-calendar'></i> <?php echo $feat_date; ?></span>
                        </div>
                        <h2 class="featured-title"><?php echo htmlspecialchars($featured['title']); ?></h2>
                        <p class="featured-excerpt">
                            <?php echo htmlspecialchars(substr($featured['excerpt'], 0, 220)) . (strlen($featured['excerpt']) > 220 ? '...' : ''); ?>
                        </p>
                    </div>
                </a>
            </section>
        <?php endif; ?>

        <!-- 📰 Remaining Articles Grid -->
        <section class="news-grid-section">
            <h3 class="section-divider-title"><i class='bx bx-grid-alt'></i> Recent Updates</h3>
            
            <div class="news-grid">
                <?php if (!empty($articles)): ?>
                    <?php foreach($articles as $row): ?>
                        <?php 
                            $image = !empty($row['featured_image']) ? $row['featured_image'] : 'uploads/news/default-placeholder.png';
                            $cat_name = !empty($row['category_name']) ? $row['category_name'] : 'General';
                            $cat_color = !empty($row['category_color']) ? $row['category_color'] : '#1565C0';
                            $formatted_date = date("M d, Y", strtotime($row['published_at']));
                        ?>
                        <a href="news-detail.php?slug=<?php echo htmlspecialchars($row['slug']); ?>" class="news-card">
                            <div class="card-image-wrapper">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="News Banner" class="card-image">
                                <span class="category-badge" style="background-color: <?php echo htmlspecialchars($cat_color); ?>;">
                                    <?php echo htmlspecialchars($cat_name); ?>
                                </span>
                            </div>
                            <div class="card-content">
                                <div class="card-date"><i class='bx bx-calendar-alt'></i> <?php echo $formatted_date; ?></div>
                                <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="card-excerpt">
                                    <?php echo htmlspecialchars(substr($row['excerpt'], 0, 120)) . (strlen($row['excerpt']) > 120 ? '...' : ''); ?>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php elseif (!$featured): ?>
                    <div class="no-news">
                        <i class='bx bx-news' style="font-size: 48px; margin-bottom: 10px;"></i>
                        <p>No recent news articles have been published yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Modern Dark Premium Styling -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #030617;
            color: #f1f5f9;
            padding-bottom: 60px;
        }

        /* Hero Header */
        .news-header-hero {
            position: relative;
            background: linear-gradient(135deg, #0b132b 0%, #030617 100%);
            padding: 60px 20px 50px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 40px;
            overflow: hidden;
        }

        .header-content {
            position: relative;
            max-width: 700px;
            margin: 0 auto;
            z-index: 2;
        }

        .subhead-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 15px;
            border: 1px solid rgba(56, 189, 248, 0.2);
        }

        .news-header-hero h1 {
            font-size: 38px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .news-header-hero p {
            color: #94a3b8;
            font-size: 16px;
            line-height: 1.6;
        }

        /* Layout Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Category Badges */
        .category-badge {
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
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            z-index: 2;
        }

        /* Featured Big Banner (First Article) */
        .featured-wrapper {
            margin-bottom: 50px;
        }

        .featured-hero-card {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            background: #080d28;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            overflow: hidden;
            text-decoration: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .featured-hero-card:hover {
            transform: translateY(-4px);
            border-color: rgba(56, 189, 248, 0.4);
            box-shadow: 0 20px 40px rgba(21, 101, 192, 0.25);
        }

        .featured-image-pane {
            position: relative;
            min-height: 320px;
            overflow: hidden;
        }

        .featured-image-pane img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .featured-hero-card:hover .featured-image-pane img {
            transform: scale(1.05);
        }

        .featured-text-pane {
            padding: 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card-meta {
            margin-bottom: 12px;
        }

        .card-date {
            font-size: 13px;
            color: #f6cb0f;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .featured-title {
            font-size: 26px;
            color: #ffffff;
            line-height: 1.3;
            margin-bottom: 14px;
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .featured-hero-card:hover .featured-title {
            color: #38bdf8;
        }

        .featured-excerpt {
            color: #cbd5e1;
            font-size: 15px;
            line-height: 1.6;
        }

        /* Section Title Divider */
        .section-divider-title {
            font-size: 20px;
            color: #ffffff;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 12px;
        }

        /* News Cards Grid */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 28px;
        }

        .news-card {
            background: #060920;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            text-decoration: none;
        }

        .news-card:hover {
            transform: translateY(-6px);
            border-color: rgba(56, 189, 248, 0.3);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
        }

        .card-image-wrapper {
            position: relative;
            width: 100%;
            height: 200px;
            background-color: #0d1538;
            overflow: hidden;
        }

        .card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .news-card:hover .card-image {
            transform: scale(1.06);
        }

        .card-content {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-title {
            font-size: 18px;
            color: #ffffff;
            margin: 10px 0 12px 0;
            line-height: 1.4;
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .news-card:hover .card-title {
            color: #38bdf8;
        }

        .card-excerpt {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.6;
            flex-grow: 1;
        }

        .no-news {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #f87171;
            background: rgba(239, 68, 68, 0.05);
            border-radius: 12px;
            border: 1px dashed rgba(239, 68, 68, 0.3);
        }

        /* Mobile Optimization */
        @media (max-width: 850px) {
            .featured-hero-card {
                grid-template-columns: 1fr;
            }
            .featured-image-pane {
                min-height: 220px;
            }
            .featured-text-pane {
                padding: 24px;
            }
            .featured-title {
                font-size: 20px;
            }
            .news-header-hero h1 {
                font-size: 28px;
            }
        }
    </style>

</body>
</html>