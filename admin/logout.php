<?php
// ============================================================
//  admin/logout.php — Admin Logout
// ============================================================
session_start();
if (isset($_SESSION['admin_id'])) {
    require_once '../config/database.php';
    require_once '../includes/functions.php';
    auditLog($pdo, $_SESSION['admin_id'], 'logout', 'admin_users', $_SESSION['admin_id'], 'Logged out');
}
session_unset();
session_destroy();
header('Location: login.php');
exit;
