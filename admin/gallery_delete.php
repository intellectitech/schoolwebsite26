<?php
require_once __DIR__ . '/includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $id = $_POST['id'] ?? null;
    if ($id && $pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Photo deleted.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not delete the photo.'];
        }
    }
}
header('Location: gallery.php');
exit;
