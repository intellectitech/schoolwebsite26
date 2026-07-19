<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Message Detail";
$active_nav = "messages";

$message = null;
if (!empty($_GET['id'])) {
    $messages = ds_read('messages');
    $message = ds_find($messages, $_GET['id']);

    // auto mark as read when opened
    if ($message && empty($message['is_read'])) {
        $messages = ds_update($messages, $_GET['id'], ['is_read' => true]);
        ds_write('messages', $messages);
        $message['is_read'] = true;
    }
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="panel">
  <div class="panel-header">
    <h2>Message Detail</h2>
    <a href="messages.php" class="btn btn-outline btn-sm">&larr; Back to Messages</a>
  </div>
  <div class="panel-body">
    <?php if (!$message): ?>
      <div class="empty-state"><div class="ic">&#10060;</div>Message not found.</div>
    <?php else: ?>
      <div class="detail-meta">
        <div><strong>From</strong><?php echo htmlspecialchars($message['full_name']); ?></div>
        <div><strong>Email</strong><a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" style="color:var(--blue); font-weight:700;"><?php echo htmlspecialchars($message['email']); ?></a></div>
        <div><strong>Subject</strong><?php echo htmlspecialchars($message['subject'] ?: '(No subject)'); ?></div>
        <div><strong>Received</strong><?php echo date('d F Y, g:i a', strtotime($message['submitted_at'])); ?></div>
      </div>
      <div class="detail-message"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>

      <div class="form-actions">
        <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo htmlspecialchars($message['subject'] ?: "Your message to Mbuya Parents' School"); ?>" class="btn btn-gold">Reply by Email</a>
        <a href="messages.php" class="btn btn-outline">Back to Messages</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
