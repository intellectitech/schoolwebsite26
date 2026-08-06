<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$currentAdminPage = 'events';
$editRow = null;
$notice = '';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('SELECT featured_img FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        deleteLocalImage($row['featured_img']);
        $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([$id]);
        auditLog($pdo, $_SESSION['admin_id'], 'DELETE', 'events', $id, 'Deleted event');
    }
    header('Location: events.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int) ($_POST['id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $eventDate   = $_POST['event_date'] ?? '';
    $startTime   = $_POST['start_time'] ?? '';
    $endTime     = $_POST['end_time'] ?? '';
    $image       = trim($_POST['featured_img'] ?? '');
    $isPublished = isset($_POST['is_published']) ? 1 : 0;

    // Schema stores start_time/end_time as DATETIME
    $startDt = ($eventDate && $startTime) ? ($eventDate . ' ' . $startTime . ':00') : ($eventDate . ' 00:00:00');
    $endDt   = ($eventDate && $endTime)   ? ($eventDate . ' ' . $endTime . ':00')   : ($eventDate . ' 23:59:59');

    if ($title === '' || $eventDate === '') {
        $notice = 'Title and event date are required.';
    } else {
        $upload = saveUploadedImage($_FILES['photo'] ?? [], 'events');
        if (!empty($upload['skipped'])) {
            // keep existing path from hidden field
        } elseif (!$upload['ok']) {
            $notice = $upload['error'];
        } else {
            deleteLocalImage($image);
            $image = $upload['path'];
        }

        if ($notice === '') {
        if ($id) {
            $pdo->prepare(
                'UPDATE events SET title=?, description=?, location=?, event_date=?, start_time=?, end_time=?, featured_img=?, is_published=? WHERE id=?'
            )->execute([$title, $description, $location, $eventDate, $startDt, $endDt, $image, $isPublished, $id]);
            auditLog($pdo, $_SESSION['admin_id'], 'UPDATE', 'events', $id, 'Updated: ' . $title);
        } else {
            $pdo->prepare(
                'INSERT INTO events (title, description, location, event_date, start_time, end_time, featured_img, is_published, created_by)
                 VALUES (?,?,?,?,?,?,?,?,?)'
            )->execute([$title, $description, $location, $eventDate, $startDt, $endDt, $image, $isPublished, $_SESSION['admin_id']]);
            auditLog($pdo, $_SESSION['admin_id'], 'INSERT', 'events', $pdo->lastInsertId(), 'Created: ' . $title);
        }
        header('Location: events.php?msg=saved');
        exit;
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editRow = $stmt->fetch();
}

$eventsList = $pdo->query('SELECT * FROM events ORDER BY event_date DESC')->fetchAll();

function timeOnly($dt) {
    if (!$dt) return '';
    return date('H:i', strtotime($dt));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Events — Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <h1>Events</h1>
    <p class="admin-sub">Manage upcoming and past school events.</p>

    <?php if ($notice): ?><div class="admin-alert admin-alert-error"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?><div class="admin-alert admin-alert-success">Saved successfully.</div><?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?><div class="admin-alert admin-alert-success">Event deleted.</div><?php endif; ?>

    <div class="admin-card">
        <h2 style="margin-bottom:10px"><?= $editRow ? 'Edit Event' : 'Add New Event' ?></h2>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="id" value="<?= $editRow['id'] ?? '' ?>">
            <input type="hidden" name="featured_img" value="<?= htmlspecialchars($editRow['featured_img'] ?? '') ?>">
            <label>Title</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>">

            <label>Description</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($editRow['description'] ?? '') ?></textarea>

            <label>Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($editRow['location'] ?? '') ?>">

            <label>Event Date</label>
            <input type="date" name="event_date" required value="<?= htmlspecialchars($editRow['event_date'] ?? '') ?>">

            <label>Start Time</label>
            <input type="time" name="start_time" value="<?= htmlspecialchars(isset($editRow['start_time']) ? timeOnly($editRow['start_time']) : '') ?>">

            <label>End Time</label>
            <input type="time" name="end_time" value="<?= htmlspecialchars(isset($editRow['end_time']) ? timeOnly($editRow['end_time']) : '') ?>">

            <label>Featured Image (JPG, PNG, GIF, or WebP, max 5 MB)</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (!empty($editRow['featured_img'])): ?>
            <p style="margin-top:8px;color:#6b7280;font-size:14px">Current: <?= htmlspecialchars($editRow['featured_img']) ?></p>
            <img class="thumb" src="../<?= htmlspecialchars($editRow['featured_img']) ?>" style="margin-top:8px" alt="">
            <?php endif; ?>

            <label style="display:flex;align-items:center;gap:8px;margin-top:16px">
                <input type="checkbox" name="is_published" style="width:auto"
                       <?= (!isset($editRow) || $editRow['is_published']) ? 'checked' : '' ?>>
                Published (visible on the site)
            </label>

            <div style="margin-top:18px">
                <button type="submit" class="admin-btn admin-btn-primary"><?= $editRow ? 'Update Event' : 'Add Event' ?></button>
                <?php if ($editRow): ?><a href="events.php" class="admin-btn">Cancel</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom:14px">All Events</h2>
        <table class="admin-table">
            <thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($eventsList as $ev): ?>
                <tr>
                    <td><?= htmlspecialchars($ev['title']) ?></td>
                    <td><?= date('d M Y', strtotime($ev['event_date'])) ?></td>
                    <td><?= htmlspecialchars($ev['location']) ?></td>
                    <td><?= $ev['is_published'] ? 'Published' : 'Draft' ?></td>
                    <td>
                        <a href="events.php?edit=<?= $ev['id'] ?>" class="admin-btn admin-btn-edit admin-btn-sm">Edit</a>
                        <a href="events.php?delete=<?= $ev['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm"
                           onclick="return confirm('Delete this event?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
