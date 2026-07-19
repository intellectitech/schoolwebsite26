<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Contact Messages";
$active_nav = "messages";

// Mark as read/unread toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_read'])) {
    admin_verify_csrf();
    $id = $_POST['message_id'] ?? null;
    if ($id) {
        $messages = ds_read('messages');
        $current = ds_find($messages, $id);
        if ($current) {
            $messages = ds_update($messages, $id, ['is_read' => empty($current['is_read'])]);
            ds_write('messages', $messages);
        }
    }
    header('Location: messages.php');
    exit;
}

$messages = ds_read('messages');
usort($messages, function ($a, $b) { return strtotime($b['submitted_at']) - strtotime($a['submitted_at']); });

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['message']); ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h2>Contact Messages (<?php echo count($messages); ?>)</h2>
  </div>
  <div class="table-wrap">
    <?php if (empty($messages)): ?>
      <div class="empty-state"><div class="ic">&#9993;</div>No contact messages yet.</div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $row): ?>
          <tr style="<?php echo !empty($row['is_read']) ? '' : 'background:#fffaf0;'; ?>">
            <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong><br><span style="color:var(--text-light); font-size:0.8rem;"><?php echo htmlspecialchars($row['email']); ?></span></td>
            <td><?php echo htmlspecialchars($row['subject'] ?: '(No subject)'); ?></td>
            <td style="max-width:280px; white-space:normal;"><?php echo htmlspecialchars(truncate_text($row['message'], 140)); ?></td>
            <td><?php echo date('d M Y', strtotime($row['submitted_at'])); ?></td>
            <td><span class="pill pill-<?php echo !empty($row['is_read']) ? 'read' : 'unread'; ?>"><?php echo !empty($row['is_read']) ? 'Read' : 'Unread'; ?></span></td>
            <td class="row-actions">
              <a href="message_view.php?id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm">View</a>
              <form action="messages.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="toggle_read" value="1">
                <button type="submit" class="btn btn-outline btn-sm"><?php echo !empty($row['is_read']) ? 'Mark Unread' : 'Mark Read'; ?></button>
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
