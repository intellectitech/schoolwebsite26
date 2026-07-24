<?php
// admin/remove-hero.php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$index = isset($_GET['index']) ? (int)$_GET['index'] : -1;

if ($index >= 0) {
    $currentImages = getSetting($pdo, 'hero_images', '');
    $images = $currentImages ? array_map('trim', explode(',', $currentImages)) : [];
    $images = array_filter($images);
    
    // Re-index the array
    $images = array_values($images);
    
    if (isset($images[$index])) {
        // Remove image from list
        array_splice($images, $index, 1);
        
        // Update settings
        $newImages = implode(', ', $images);
        $stmt = $pdo->prepare("
            INSERT INTO school_info (setting_key, setting_value, updated_by) 
            VALUES ('hero_images', ?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?
        ");
        $stmt->execute([$newImages, $_SESSION['admin_id'], $newImages, $_SESSION['admin_id']]);
        
        // Log activity
        $logStmt = $pdo->prepare("
            INSERT INTO audit_log (admin_id, action, table_name, description, ip_address) 
            VALUES (?, 'removed_hero_image', 'school_info', 'Removed hero image from slider', ?)
        ");
        $logStmt->execute([$_SESSION['admin_id'], $_SERVER['REMOTE_ADDR']]);
        
        $_SESSION['flash_message'] = 'Image removed successfully!';
    }
}

header('Location: upload-images.php');
exit;
?>