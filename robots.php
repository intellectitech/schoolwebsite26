<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=utf-8');
?>
User-agent: *
Disallow: /admin/
Disallow: /includes/
Disallow: /database/
Disallow: /docs/
Allow: /

Sitemap: <?php echo SITE_URL; ?>/sitemap.xml
