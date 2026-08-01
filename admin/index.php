<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Dashboard";
$active_nav = "dashboard";

function safe_count($pdo, $sql) {
    if (!$pdo) return 0;
    try { return (int) $pdo->query($sql)->fetchColumn(); } catch (Exception $e) { return 0; }
}
function safe_rows($pdo, $sql) {
    if (!$pdo) return [];
    try { return $pdo->query($sql)->fetchAll(); } catch (Exception $e) { return []; }
}

$newsCount       = safe_count($pdo, "SELECT COUNT(*) FROM news_posts");
$galleryCount    = safe_count($pdo, "SELECT COUNT(*) FROM gallery_images");
$newInquiries    = safe_count($pdo, "SELECT COUNT(*) FROM admissions_inquiries WHERE status='new'");
$totalInquiries  = safe_count($pdo, "SELECT COUNT(*) FROM admissions_inquiries");
$unreadMessages  = safe_count($pdo, "SELECT COUNT(*) FROM contact_messages WHERE is_read=0");
$totalMessages   = safe_count($pdo, "SELECT COUNT(*) FROM contact_messages");
$upcomingEvents  = safe_count($pdo, "SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()");

$recentInquiries = safe_rows($pdo, "SELECT * FROM admissions_inquiries ORDER BY submitted_at DESC LIMIT 5");
$recentMessages  = safe_rows($pdo, "SELECT * FROM contact_messages ORDER BY submitted_at DESC LIMIT 5");

require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if (!$pdo): ?>
  <div class="alert alert-error">
    Could not connect to the database. The dashboard below is showing zeros until <code>includes/db.php</code> is configured with working credentials and the database has been imported.
    <?php if (!empty($db_connection_error)): ?>
      <br><br><strong>Technical detail:</strong><br><?php echo htmlspecialchars($db_connection_error); ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="stat-cards">
  <div class="stat-card">
    <div class="stat-num"><?php echo $newsCount; ?></div>
    <div class="stat-label">News &amp; Blog Posts</div>
    <a href="news.php" class="stat-link">Manage news &rarr;</a>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?php echo $galleryCount; ?></div>
    <div class="stat-label">Gallery Photos</div>
    <a href="gallery.php" class="stat-link">Manage gallery &rarr;</a>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?php echo $newInquiries; ?> <span style="font-size:1rem; color:var(--text-light); font-weight:600;">/ <?php echo $totalInquiries; ?></span></div>
    <div class="stat-label">New Admission Inquiries</div>
    <a href="admissions.php" class="stat-link">View inquiries &rarr;</a>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?php echo $unreadMessages; ?> <span style="font-size:1rem; color:var(--text-light); font-weight:600;">/ <?php echo $totalMessages; ?></span></div>
    <div class="stat-label">Unread Contact Messages</div>
    <a href="messages.php" class="stat-link">View messages &rarr;</a>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?php echo $upcomingEvents; ?></div>
    <div class="stat-label">Upcoming Events</div>
    <a href="events.php" class="stat-link">Manage events &rarr;</a>
  </div>
</div>

<div class="quick-actions">
  <a href="news_edit.php" class="btn btn-gold">+ New Post</a>
  <a href="gallery_edit.php" class="btn btn-primary">+ Add Photo</a>
  <a href="events_edit.php" class="btn btn-outline">+ Add Event</a>
  <a href="staff_edit.php" class="btn btn-outline">+ Add Staff Member</a>
</div>

<div class="panel">
  <div class="panel-header">
    <h2>Recent Admission Inquiries</h2>
    <a href="admissions.php" class="btn btn-outline btn-sm">View All</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($recentInquiries)): ?>
      <div class="empty-state">No admission inquiries yet.</div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Parent</th><th>Child</th><th>Desired Class</th><th>Status</th><th>Submitted</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recentInquiries as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['parent_name']); ?><br><span style="color:var(--text-light); font-size:0.8rem;"><?php echo htmlspecialchars($row['parent_email']); ?></span></td>
            <td><?php echo htmlspecialchars($row['child_name']); ?></td>
            <td><?php echo htmlspecialchars($row['desired_class']); ?></td>
            <td><span class="pill pill-<?php echo htmlspecialchars($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
            <td><?php echo date('d M Y', strtotime($row['submitted_at'])); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<div class="panel">
  <div class="panel-header">
    <h2>Recent Contact Messages</h2>
    <a href="messages.php" class="btn btn-outline btn-sm">View All</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($recentMessages)): ?>
      <div class="empty-state">No contact messages yet.</div>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>From</th><th>Subject</th><th>Status</th><th>Received</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recentMessages as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['full_name']); ?><br><span style="color:var(--text-light); font-size:0.8rem;"><?php echo htmlspecialchars($row['email']); ?></span></td>
            <td><?php echo htmlspecialchars($row['subject'] ?: '(No subject)'); ?></td>
            <td><span class="pill pill-<?php echo $row['is_read'] ? 'read' : 'unread'; ?>"><?php echo $row['is_read'] ? 'Read' : 'Unread'; ?></span></td>
            <td><?php echo date('d M Y', strtotime($row['submitted_at'])); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
