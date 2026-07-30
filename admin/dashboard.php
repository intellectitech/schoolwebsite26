<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'dashboard';

$newsCount    = (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
$eventsCount  = (int) $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
$msgCount     = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
$galleryCount = (int) $pdo->query('SELECT COUNT(*) FROM gallery_photos')->fetchColumn();
$enquiryCount = (int) $pdo->query("SELECT COUNT(*) FROM admission_enquiries WHERE status = 'new'")->fetchColumn();
$subCount     = (int) $pdo->query('SELECT COUNT(*) FROM newsletters_subscribers WHERE unsubscribed_at IS NULL')->fetchColumn();

$recentMessages = $pdo->query(
    'SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$recentEnquiries = $pdo->query(
    'SELECT * FROM admission_enquiries ORDER BY created_at DESC LIMIT 5'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Welcome back, <?= htmlspecialchars($_SESSION['admin_name']) ?></h1>
    <p class="admin-sub">Here's what's happening on your site.</p>

    <div class="admin-stats-grid">
        <div class="admin-stat-card"><h3><?= $newsCount ?></h3><p>News Articles</p></div>
        <div class="admin-stat-card"><h3><?= $eventsCount ?></h3><p>Upcoming Events</p></div>
        <div class="admin-stat-card"><h3><?= $enquiryCount ?></h3><p>New Admission Enquiries</p></div>
        <div class="admin-stat-card"><h3><?= $msgCount ?></h3><p>Unread Messages</p></div>
        <div class="admin-stat-card"><h3><?= $galleryCount ?></h3><p>Gallery Photos</p></div>
        <div class="admin-stat-card"><h3><?= $subCount ?></h3><p>Newsletter Subscribers</p></div>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom:14px">Recent Admission Enquiries</h2>
        <?php if (!$recentEnquiries): ?>
            <p style="color:#64748b">No enquiries yet.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead><tr><th>Parent</th><th>Child</th><th>Class</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($recentEnquiries as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['parent_name']) ?></td>
                    <td><?= htmlspecialchars($e['student_name']) ?></td>
                    <td><?= htmlspecialchars($e['entry_level']) ?></td>
                    <td><?= ucfirst($e['status']) ?></td>
                    <td><a href="enquiries.php" class="admin-btn admin-btn-primary admin-btn-sm">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom:14px">Recent Contact Messages</h2>
        <?php if (!$recentMessages): ?>
            <p style="color:#64748b">No messages yet.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Subject</th><th>Date</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($recentMessages as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['subject']) ?></td>
                    <td><?= !empty($m['created_at']) ? date('d M Y', strtotime($m['created_at'])) : '—' ?></td>
                    <td><a href="messages.php" class="admin-btn admin-btn-primary admin-btn-sm">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
