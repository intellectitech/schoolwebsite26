<?php
require_once __DIR__ . '/includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();
    $id = $_POST['id'] ?? null;
    if ($id) {
        $staff = ds_read('staff');
        $staff = ds_remove($staff, $id);
        if (ds_write('staff', $staff)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Staff member deleted.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Could not delete staff member.'];
        }
    }
}
header('Location: staff.php');
exit;
