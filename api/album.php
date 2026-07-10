<?php
// api/album.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../includes/functions.php';

$albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$albumId) {
    echo json_encode(['error' => 'Album ID required']);
    exit;
}

// Get album info
$albumStmt = $pdo->prepare("SELECT * FROM gallery_albums WHERE id = ? AND is_published = 1");
$albumStmt->execute([$albumId]);
$album = $albumStmt->fetch();

if (!$album) {
    echo json_encode(['error' => 'Album not found']);
    exit;
}

// Get photos
$photoStmt = $pdo->prepare("
    SELECT * FROM gallery_photos 
    WHERE album_id = ? 
    ORDER BY sort_order
");
$photoStmt->execute([$albumId]);
$photos = $photoStmt->fetchAll();

echo json_encode([
    'album' => $album,
    'photos' => $photos
]);
?>