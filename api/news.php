<?php
// api/news.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../includes/functions.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;
$limit = min($limit, 10); // Max 10

$stmt = $pdo->prepare("
    SELECT n.id, n.title, n.slug, n.excerpt, n.body, n.featured_image, 
           n.published_at, n.created_at,
           nc.name as cat_name, nc.color_code as cat_color
    FROM news n
    LEFT JOIN news_categories nc ON nc.id = n.category_id
    WHERE n.is_published = 1
    ORDER BY n.published_at DESC, n.created_at DESC
    LIMIT ?
");
$stmt->execute([$limit]);
$news = $stmt->fetchAll();

echo json_encode($news);
?>