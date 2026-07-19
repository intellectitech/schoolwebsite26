<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Events";
$active_nav = "events";

$events = ds_read('events');
usort($events, function ($a, $b) { return strtotime($a['event_date']) - strtotime($b['event_date']); });

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['message']); ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h2>All Events (<?php echo count($events); ?>)</h2>
    <a href="events_edit.php" class="btn btn-gold btn-sm">+ Add Event</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($events)): ?>
      <div class="empty-state"><div class="ic">&#128197;</div>No events yet. <br><a href="events_edit.php" class="btn btn-primary btn-sm" style="margin-top:14px;">Add your first event</a></div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Event</th><th>Date</th><th>Location</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($events as $ev):
              $isPast = strtotime($ev['event_date']) < strtotime(date('Y-m-d'));
          ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($ev['title']); ?></strong><br><span style="color:var(--text-light); font-size:0.8rem;"><?php echo htmlspecialchars(truncate_text($ev['description'] ?? '', 80)); ?></span></td>
            <td><?php echo date('d M Y', strtotime($ev['event_date'])); ?> <?php echo $isPast ? '<span class="pill pill-closed">Past</span>' : '<span class="pill pill-published">Upcoming</span>'; ?></td>
            <td><?php echo htmlspecialchars($ev['location']); ?></td>
            <td class="row-actions">
              <a href="events_edit.php?id=<?php echo $ev['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
              <form action="events_delete.php" method="POST" class="js-confirm-delete" data-confirm="Delete this event?">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="id" value="<?php echo $ev['id']; ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
