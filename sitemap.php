<?php
// 1. Prevent output buffering issues so XML headers render properly
ob_start();

// 2. Load database configuration
require_once 'config/database.php';

// 3. Fallback check for common database connection variable names
if (!isset($pdo)) {
    if (isset($conn)) {
        $pdo = $conn;
    } elseif (isset($db)) {
        $pdo = $db;
    } else {
        $pdo = null;
    }
}

// 4. Set Base URL dynamically
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $scheme . "://" . $host;

// If hosted in a subdirectory (e.g., /schoolwebsite26-main), append directory path
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($scriptDir !== '') {
    $baseUrl .= $scriptDir;
}

// Clear any accidental output prior to sending headers
ob_end_clean();

// 5. Send proper XML Header
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <!-- Static Pages -->
    <url>
        <loc><?= $baseUrl ?>/index.php</loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <url>
        <loc><?= $baseUrl ?>/about.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc><?= $baseUrl ?>/admissions.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc><?= $baseUrl ?>/gallery.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <url>
        <loc><?= $baseUrl ?>/news.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc><?= $baseUrl ?>/contact.php</loc>
        <changefreq>yearly</changefreq>
        <priority>0.6</priority>
    </url>

    <!-- Dynamic News Articles -->
    <?php
    if ($pdo instanceof PDO) {
        try {
            $stmt = $pdo->query("SELECT id, created_at FROM news ORDER BY created_at DESC LIMIT 100");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $newsDate = date('Y-m-d', strtotime($row['created_at']));
                ?>
    <url>
        <loc><?= $baseUrl ?>/news-detail.php?id=<?= $row['id'] ?></loc>
        <lastmod><?= $newsDate ?></lastmod>
        <changefreq>never</changefreq>
        <priority>0.6</priority>
    </url>
                <?php
            }
        } catch (Exception $e) {
            // Silently ignore DB errors so XML sitemap still renders static pages
        }
    }
    ?>

</urlset>