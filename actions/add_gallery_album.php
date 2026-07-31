<?php
// actions/add_gallery_album.php

// 1. Fixed relative paths (stepping out of actions/ folder with ../)
include __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth.php';

// 2. Ensure user is authenticated
require_login();

// Safe HTML helper function
if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_title = trim($_POST['album_title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($album_title)) {
        header('Location: ../gallery.php?error=' . urlencode('Album title is required.'));
        exit();
    }

    try {
        // Inspect table columns dynamically to prevent SQL missing column errors
        $stmtCol = $pdo->prepare("
            SELECT COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gallery_albums'
        ");
        $stmtCol->execute();
        $columns = $stmtCol->fetchAll(PDO::FETCH_COLUMN);

        // Detect column name for title (title, album_title, album_name, name)
        $titleCol = 'title';
        foreach (['title', 'album_title', 'album_name', 'name'] as $col) {
            if (in_array($col, $columns)) {
                $titleCol = $col;
                break;
            }
        }

        $hasDesc = in_array('description', $columns);

        // Insert based on available columns
        if ($hasDesc) {
            $stmt = $pdo->prepare("INSERT INTO gallery_albums ({$titleCol}, description) VALUES (:title, :description)");
            $stmt->execute([
                ':title'       => $album_title,
                ':description' => $description
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO gallery_albums ({$titleCol}) VALUES (:title)");
            $stmt->execute([
                ':title' => $album_title
            ]);
        }

        header('Location: ../gallery.php?success=' . urlencode('Album created successfully!'));
        exit();

    } catch (PDOException $e) {
        header('Location: ../gallery.php?error=' . urlencode('Database error: ' . $e->getMessage()));
        exit();
    }
} else {
    header('Location: ../gallery.php');
    exit();
}