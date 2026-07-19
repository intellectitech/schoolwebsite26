<?php
require_once __DIR__ . '/includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $id = $_POST['id'] ?? null;
    if ($id) {
        $posts = ds_read('news');
        $posts = ds_remove($posts, $id);
        if (ds_write('news', $posts)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post deleted.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not delete the post.'];
        }
    }
}
header('Location: news.php');
exit;
