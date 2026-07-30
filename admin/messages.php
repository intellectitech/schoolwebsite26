<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'messages';

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([(int) $_GET['delete']]);
    header('Location: messages.php');
    exit;
}

if (isset($_GET['read'])) {
    $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([(int) $_GET['read']]);
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messages — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Contact Messages</h1>
    <p class="admin-sub">Messages submitted through the public Contact form.</p>

    <div class="admin-card">
        <?php if (!$messages): ?>
            <p style="color:#64748b">No messages yet.</p>
        <?php endif; ?>
        <?php foreach ($messages as $m): ?>
        <div style="padding:14px 0;border-bottom:1px solid #e2e8f0;<?= empty($m['is_read']) ? 'background:#eff6ff' : '' ?>">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px">
                <strong><?= htmlspecialchars($m['name']) ?> &lt;<?= htmlspecialchars($m['email']) ?>&gt;</strong>
                <span style="color:#64748b;font-size:13px"><?= !empty($m['created_at']) ? date('d M Y, g:i A', strtotime($m['created_at'])) : '' ?></span>
            </div>
            <?php if (!empty($m['phone'])): ?>
            <p style="margin:4px 0;color:#64748b;font-size:13px">Phone: <?= htmlspecialchars($m['phone']) ?></p>
            <?php endif; ?>
            <p style="margin:6px 0;font-weight:600"><?= htmlspecialchars($m['subject']) ?></p>
            <p style="color:#334155"><?= nl2br(htmlspecialchars($m['message'])) ?></p>
            <div style="margin-top:8px">
                <?php if (empty($m['is_read'])): ?>
                <a href="messages.php?read=<?= $m['id'] ?>" class="admin-btn admin-btn-primary admin-btn-sm">Mark Read</a>
                <?php endif; ?>
                <a href="messages.php?delete=<?= $m['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm"
                   onclick="return confirm('Delete this message?')">Delete</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html>
