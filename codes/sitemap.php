<?php
// sitemap.php
// Generates sitemap.xml dynamically. Point Google Search Console at:
//   https://yourdomain.com/sitemap.php
// (Most hosts also let you rename this to sitemap.xml via .htaccess if preferred.)

require_once 'database.php';
header('Content-Type: application/xml; charset=utf-8');

$siteUrl = 'https://stfrancisborgia.ac.ug'; // update to the real live domain

// Static pages that always exist
$staticPages = [
    ['loc' => 'index.php', 'priority' => '1.0'],
    ['loc' => 'about.php', 'priority' => '0.8'],
    ['loc' => 'academics.php', 'priority' => '0.8'],
    ['loc' => 'admissions.php', 'priority' => '0.9'],
    ['loc' => 'facilities.php', 'priority' => '0.7'],
    ['loc' => 'news.php', 'priority' => '0.8'],
    ['loc' => 'contact.php', 'priority' => '0.6'],
];

// Every published news article gets its own entry
try {
    $articles = $pdo->query("SELECT id, created_at, published_at FROM news WHERE is_published = 1")->fetchAll();
} catch (PDOException $e) {
    $articles = [];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($staticPages as $p): ?>
    <url>
        <loc><?= htmlspecialchars($siteUrl . '/' . $p['loc']) ?></loc>
        <priority><?= $p['priority'] ?></priority>
    </url>
    <?php endforeach; ?>
    <?php foreach ($articles as $a): ?>
    <url>
        <loc><?= htmlspecialchars($siteUrl . '/news_detail.php?id=' . $a['id']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($a['published_at'] ?: $a['created_at'])) ?></lastmod>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
</urlset>
