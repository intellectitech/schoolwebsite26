<?php
require_once __DIR__ . '/includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $id = $_POST['id'] ?? null;
    if ($id) {
        $events = ds_read('events');
        $events = ds_remove($events, $id);
        if (ds_write('events', $events)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Event deleted.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not delete the event.'];
        }
    }
}
header('Location: events.php');
exit;
