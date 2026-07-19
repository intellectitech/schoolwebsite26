<?php
require_once __DIR__ . '/includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $id = $_POST['id'] ?? null;
    if ($id) {
        $images = ds_read('gallery');
        $images = ds_remove($images, $id);
        if (ds_write('gallery', $images)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Photo deleted.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not delete the photo.'];
        }
    }
}
header('Location: gallery.php');
exit;
