<?php
require_once __DIR__ . '/includes/admin_auth.php';

$page_title = "Dashboard";
$active_nav = "dashboard";

$newsRows       = ds_read('news');
$galleryRows    = ds_read('gallery');
$inquiryRows    = ds_read('admissions');
$messageRows    = ds_read('messages');
$eventRows      = ds_read('events');

$newsCount       = count($newsRows);
$galleryCount    = count($galleryRows);
$newInquiries    = count(array_filter($inquiryRows, function ($r) { return ($r['status'] ?? 'new') === 'new'; }));
$totalInquiries  = count($inquiryRows);
$unreadMessages  = count(array_filter($messageRows, function ($r) { return empty($r['is_read']); }));
$totalMessages   = count($messageRows);
$today = date('Y-m-d');
$upcomingEvents  = count(array_filter($eventRows, function ($r) use ($today) { return $r['event_date'] >= $today; }));

// Recent inquiries (last 5, newest first)
usort($inquiryRows, function ($a, $b) { return strtotime($b['submitted_at']) - strtotime($a['submitted_at']); });
$recentInquiries = array_slice($inquiryRows, 0, 5);

// Recent messages (last 5, newest first)
usort($messageRows, function ($a, $b) { return strtotime($b['submitted_at']) - strtotime($a['submitted_at']); });
$recentMessages = array_slice($messageRows, 0, 5);

require_once __DIR__ . '/includes/admin_header.php';
?>

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
      <div class="empty-state"><div class="ic">&#9993;</div>No contact messages yet.</div>
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
            <td><span class="pill pill-<?php echo !empty($row['is_read']) ? 'read' : 'unread'; ?>"><?php echo !empty($row['is_read']) ? 'Read' : 'Unread'; ?></span></td>
            <td><?php echo date('d M Y', strtotime($row['submitted_at'])); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
