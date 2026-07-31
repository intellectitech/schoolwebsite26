<?php
// Handles inline image uploads triggered from the article body editor.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Must be logged in to upload
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No image received']);
    exit;
}

$fileTmpPath = $_FILES['image']['tmp_name'];
$fileName = $_FILES['image']['name'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($fileExtension, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp']);
    exit;
}

// 5MB max
if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'Image is too large (max 5MB)']);
    exit;
}

$uploadDir = 'uploads/news/inline/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
$destPath = $uploadDir . $newFileName;

if (move_uploaded_file($fileTmpPath, $destPath)) {
    echo json_encode(['url' => $destPath]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save the uploaded image']);
}
