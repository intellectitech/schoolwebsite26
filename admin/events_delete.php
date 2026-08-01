<?php
require_once __DIR__ . '/includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $id = $_POST['id'] ?? null;
    if ($id && $pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Event deleted.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not delete the event.'];
        }
    }
}
header('Location: events.php');
exit;
