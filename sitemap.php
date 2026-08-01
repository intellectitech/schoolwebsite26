<?php
header("Content-Type: application/xml; charset=utf-8");

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=utf-8');


$staticPages = [
    ['path' => '/',            'priority' => '1.0'],
    ['path' => '/about',       'priority' => '0.8'],
    ['path' => '/academics',   'priority' => '0.8'],
    ['path' => '/events',      'priority' => '0.7'],
    ['path' => '/admissions',  'priority' => '0.9'],
    ['path' => '/gallery',     'priority' => '0.6'],
    ['path' => '/news',        'priority' => '0.7'],
    ['path' => '/contact',     'priority' => '0.6'],
];


$newsUrls = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, published_at FROM news_posts WHERE is_published = 1 ORDER BY published_at DESC");
        $newsUrls = $stmt->fetchAll();
    } catch (Exception $e) {
        $newsUrls = [];
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $p): ?>
  <url>
    <loc><?php echo htmlspecialchars(SITE_URL . $p['path']); ?></loc>
    <priority><?php echo $p['priority']; ?></priority>
  </url>
<?php endforeach; ?>
<?php foreach ($newsUrls as $post): ?>
  <url>
    <loc><?php echo htmlspecialchars(SITE_URL . '/news/' . rawurlencode($post['slug'])); ?></loc>
    <lastmod><?php echo date('Y-m-d', strtotime($post['published_at'])); ?></lastmod>
    <priority>0.6</priority>
  </url>
  <url>
    <loc>https://www.mbuyaparentsschool.com/</loc>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
</url>
<?php endforeach; ?>
</urlset>
