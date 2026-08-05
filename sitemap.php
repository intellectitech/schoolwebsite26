<?php
// ============================================================
//  sitemap.php — dynamically generated sitemap.xml
//  Served at the conventional /sitemap.xml path via the
//  rewrite rule in .htaccess. Lists every static page plus
//  every published news article, so it never goes stale as
//  articles are added, edited, or unpublished from the admin
//  panel — nothing here needs manual upkeep.
// ============================================================

require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$base = siteBaseUrl();

// Static routes, matching the clean paths defined in .htaccess.
// changefreq/priority are hints only — Google treats them as
// optional and largely ignores them, but they're valid and cheap
// to include for other crawlers that do read them.
$staticPages = [
    ['loc' => '/', 'changefreq' => 'weekly', 'priority' => '1.0'],
    ['loc' => '/about', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['loc' => '/admissions', 'changefreq' => 'monthly', 'priority' => '0.9'],
    ['loc' => '/contact', 'changefreq' => 'yearly', 'priority' => '0.6'],
    ['loc' => '/gallery', 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => '/news', 'changefreq' => 'weekly', 'priority' => '0.7'],
    ['loc' => '/staff', 'changefreq' => 'monthly', 'priority' => '0.5'],
];

// Every published news article, most recently updated first.
$articles = $pdo->query(
    "SELECT slug, updated_at FROM news WHERE is_published = 1 ORDER BY updated_at DESC"
)->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page): ?>
  <url>
    <loc><?= htmlspecialchars($base . $page['loc']) ?></loc>
    <changefreq><?= $page['changefreq'] ?></changefreq>
    <priority><?= $page['priority'] ?></priority>
  </url>
<?php endforeach; ?>
<?php foreach ($articles as $article): ?>
  <url>
    <loc><?= htmlspecialchars($base . '/' . $article['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($article['updated_at'])) ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
</urlset>
