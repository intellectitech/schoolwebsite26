<?php
require_once __DIR__ . '/includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $id = $_POST['id'] ?? null;
    $statusFilter = $_POST['status_filter'] ?? null;
    if ($id && $pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM admissions_inquiries WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Admission inquiry deleted.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not delete the inquiry.'];
        }
    }
}
$redirect = 'admissions.php';
if (!empty($statusFilter)) { $redirect .= '?status=' . urlencode($statusFilter); }
header('Location: ' . $redirect);
exit;
