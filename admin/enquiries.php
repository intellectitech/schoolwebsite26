<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'enquiries';
$notice = '';

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM admission_enquiries WHERE id = ?')->execute([(int) $_GET['delete']]);
    header('Location: enquiries.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'new';
    $notes  = trim($_POST['admin_notes'] ?? '');
    $validStatuses = ['new', 'contacted', 'enrolled', 'declined'];
    if (!in_array($status, $validStatuses, true)) $status = 'new';

    $pdo->prepare('UPDATE admission_enquiries SET status = ?, admin_notes = ? WHERE id = ?')
        ->execute([$status, $notes, $id]);
    auditLog($pdo, $_SESSION['admin_id'], 'UPDATE', 'admission_enquiries', $id, 'Updated enquiry status to ' . $status);
    header('Location: enquiries.php?msg=saved');
    exit;
}

$filter = $_GET['status'] ?? '';
$validStatuses = ['new', 'contacted', 'enrolled', 'declined'];
if ($filter && in_array($filter, $validStatuses, true)) {
    $stmt = $pdo->prepare('SELECT * FROM admission_enquiries WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$filter]);
    $enquiries = $stmt->fetchAll();
} else {
    $enquiries = $pdo->query('SELECT * FROM admission_enquiries ORDER BY created_at DESC')->fetchAll();
}

$counts = $pdo->query("SELECT status, COUNT(*) AS c FROM admission_enquiries GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admission Enquiries — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Admission Enquiries</h1>
    <p class="admin-sub">Enquiries submitted through the public Admissions form.</p>

    <?php if ($notice): ?><div class="admin-alert admin-alert-error"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?><div class="admin-alert admin-alert-success">Updated successfully.</div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?><div class="admin-alert admin-alert-success">Enquiry deleted.</div><?php endif; ?>

    <div style="margin-bottom:20px;display:flex;gap:8px;flex-wrap:wrap">
        <a href="enquiries.php" class="admin-btn <?= $filter === '' ? 'admin-btn-primary' : '' ?> admin-btn-sm">All (<?= array_sum($counts) ?>)</a>
        <?php foreach ($validStatuses as $s): ?>
        <a href="enquiries.php?status=<?= $s ?>" class="admin-btn <?= $filter === $s ? 'admin-btn-primary' : '' ?> admin-btn-sm">
            <?= ucfirst($s) ?> (<?= (int) ($counts[$s] ?? 0) ?>)
        </a>
        <?php endforeach; ?>
    </div>

    <div class="admin-card">
        <?php if (!$enquiries): ?>
            <p style="color:#64748b">No enquiries<?= $filter ? ' with this status' : '' ?> yet.</p>
        <?php endif; ?>
        <?php foreach ($enquiries as $e): ?>
        <div style="padding:16px 0;border-bottom:1px solid #e2e8f0;">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px">
                <strong><?= htmlspecialchars($e['parent_name']) ?> &mdash; child: <?= htmlspecialchars($e['student_name']) ?> (<?= htmlspecialchars($e['entry_level']) ?>)</strong>
                <span style="color:#64748b;font-size:13px"><?= !empty($e['created_at']) ? date('d M Y, g:i A', strtotime($e['created_at'])) : '' ?></span>
            </div>
            <p style="margin:6px 0;color:#64748b;font-size:14px">
                &#128222; <?= htmlspecialchars($e['parent_phone']) ?> &nbsp;|&nbsp; &#9993; <?= htmlspecialchars($e['parent_email']) ?>
                <?php if (!empty($e['current_school'])): ?> &nbsp;|&nbsp; Transferring from: <?= htmlspecialchars($e['current_school']) ?><?php endif; ?>
            </p>
            <?php if (!empty($e['message'])): ?>
            <p style="color:#334155;margin-bottom:10px"><?= nl2br(htmlspecialchars($e['message'])) ?></p>
            <?php endif; ?>

            <form method="POST" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-start;margin-top:8px">
                <input type="hidden" name="id" value="<?= $e['id'] ?>">
                <select name="status" style="padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1">
                    <?php foreach ($validStatuses as $s): ?>
                        <option value="<?= $s ?>" <?= $e['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="admin_notes" placeholder="Internal notes..." value="<?= htmlspecialchars($e['admin_notes'] ?? '') ?>" style="flex:1;min-width:200px;padding:8px 12px;border-radius:8px;border:1px solid #cbd5e1">
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Update</button>
                <a href="enquiries.php?delete=<?= $e['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm"
                   onclick="return confirm('Delete this enquiry?')">Delete</a>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html>
