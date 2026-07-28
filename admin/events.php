<?php
// ============================================================
//  admin/events.php — Manage School Events
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// ── HANDLE ACTIONS (publish/unpublish, delete) ──────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
  $id = (int) ($_POST['id'] ?? 0);
  $action = $_POST['action'] ?? '';

  if ($id && $action === 'publish') {
    $pdo->prepare('UPDATE events SET is_published = 1 WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'events', $id, 'Published event');
  } elseif ($id && $action === 'unpublish') {
    $pdo->prepare('UPDATE events SET is_published = 0 WHERE id = ?')->execute([$id]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'events', $id, 'Unpublished event');
  } elseif ($id && $action === 'delete') {
    $imgStmt = $pdo->prepare('SELECT featured_img FROM events WHERE id = ?');
    $imgStmt->execute([$id]);
    $img = $imgStmt->fetchColumn();
    $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([$id]);
    deleteEventImageFile($img);
    auditLog($pdo, $_SESSION['admin_id'], 'delete', 'events', $id, 'Deleted event');
  }
  redirectTo('events.php' . (isset($_GET['when']) ? '?when=' . urlencode($_GET['when']) : ''));
}

$flash = getFlash();

// ── FILTER TABS ────────────────────────────────────────────
$when = $_GET['when'] ?? 'upcoming';
$where = 'WHERE event_date >= CURDATE()';
if ($when === 'past')
  $where = 'WHERE event_date < CURDATE()';
if ($when === 'all')
  $where = '';

$counts = [
  'upcoming' => (int) $pdo->query('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()')->fetchColumn(),
  'past' => (int) $pdo->query('SELECT COUNT(*) FROM events WHERE event_date < CURDATE()')->fetchColumn(),
  'all' => (int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn(),
];

$order = $when === 'past' ? 'event_date DESC' : 'event_date ASC';
$events = $pdo->query("SELECT * FROM events {$where} ORDER BY {$order}")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events · Admin · Uganda Martyrs Primary School</title>
  <meta name="robots" content="noindex, nofollow">
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="admin-body">
  <div class="admin-shell">
    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
      <header class="admin-topbar admin-topbar-with-action">
        <div>
          <h1>Events</h1>
          <p><?= $counts['all'] ?> event<?= $counts['all'] === 1 ? '' : 's' ?> in total</p>
        </div>
        <a href="event-form.php" class="btn btn-primary">+ New Event</a>
      </header>

      <?php if ($flash['success']): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash['success']) ?></div><?php endif; ?>
      <?php if ($flash['errors']): ?>
        <div class="alert alert-error">
          <ul><?php foreach ($flash['errors'] as $err): ?>
              <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="news-cat-tabs" role="group" aria-label="Filter events by date">
        <a href="events.php?when=upcoming" class="cat-tab <?= $when === 'upcoming' ? 'active' : '' ?>">Upcoming
          (<?= $counts['upcoming'] ?>)</a>
        <a href="events.php?when=past" class="cat-tab <?= $when === 'past' ? 'active' : '' ?>">Past
          (<?= $counts['past'] ?>)</a>
        <a href="events.php?when=all" class="cat-tab <?= $when === 'all' ? 'active' : '' ?>">All
          (<?= $counts['all'] ?>)</a>
      </div>

      <section class="admin-panel">
        <?php if ($events): ?>
          <table class="admin-table admin-table-news">
            <thead>
              <tr>
                <th></th>
                <th>Event</th>
                <th>Location</th>
                <th>Date &amp; Time</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($events as $ev): ?>
                <?php $hasRealImage = $ev['featured_img'] && file_exists(__DIR__ . '/../' . $ev['featured_img']); ?>
                <tr>
                  <td>
                    <?php if ($hasRealImage): ?>
                      <img class="admin-thumb" src="../<?= htmlspecialchars($ev['featured_img']) ?>" alt="">
                    <?php else: ?>
                      <span class="admin-thumb admin-thumb-color" style="background:#2F6B4F"></span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($ev['title']) ?></td>
                  <td><?= htmlspecialchars($ev['location']) ?></td>
                  <td>
                    <?= date('d M Y', strtotime($ev['event_date'])) ?><br>
                    <span style="color:var(--ink-soft);font-size:0.85em"><?= date('g:ia', strtotime($ev['start_time'])) ?> –
                      <?= date('g:ia', strtotime($ev['end_time'])) ?></span>
                  </td>
                  <td><span
                      class="admin-badge admin-badge-<?= $ev['is_published'] ? 'published' : 'draft' ?>"><?= $ev['is_published'] ? 'Published' : 'Draft' ?></span>
                  </td>
                  <td class="admin-table-actions">
                    <a href="event-form.php?edit=<?= (int) $ev['id'] ?>">Edit</a>
                    <form method="POST" action="events.php<?= $when !== 'upcoming' ? '?when=' . urlencode($when) : '' ?>">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $ev['id'] ?>">
                      <input type="hidden" name="action" value="<?= $ev['is_published'] ? 'unpublish' : 'publish' ?>">
                      <button type="submit" class="link-btn"><?= $ev['is_published'] ? 'Unpublish' : 'Publish' ?></button>
                    </form>
                    <form method="POST" action="events.php<?= $when !== 'upcoming' ? '?when=' . urlencode($when) : '' ?>"
                      onsubmit="return confirm('Delete this event permanently? This cannot be undone.');">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= (int) $ev['id'] ?>">
                      <input type="hidden" name="action" value="delete">
                      <button type="submit" class="link-btn link-btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="admin-empty">No <?= $when === 'past' ? 'past' : ($when === 'all' ? '' : 'upcoming') ?> events yet. <a
              href="event-form.php">Add one →</a></p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>

</html>