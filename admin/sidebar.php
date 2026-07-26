<?php
// ============================================================
//  admin/sidebar.php — Shared admin navigation
//  Include after requireAdmin() on every admin page.
// ============================================================
$adminCurrentPage = basename($_SERVER['PHP_SELF'], '.php');

$unreadCount = $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
$newEnquiryCount = $pdo->query("SELECT COUNT(*) FROM admission_enquiries WHERE status = 'new'")->fetchColumn();
$draftNewsCount = $pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 0')->fetchColumn();
$upcomingEventsCount = $pdo->query('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()')->fetchColumn();
?>
<aside class="admin-sidebar">
  <div class="admin-sidebar-brand">
    <img src="../assets/images/ESD_69e8c39b15887.webp" alt="School logo" class="admin-sidebar-logo">
    <span>Admin Panel</span>
  </div>
  <nav class="admin-nav">
    <a href="dashboard.php" class="<?= $adminCurrentPage === 'dashboard' ? 'current' : '' ?>">Dashboard</a>
    <a href="news.php" class="<?= in_array($adminCurrentPage, ['news', 'news-form'], true) ? 'current' : '' ?>">
      News &amp; Stories
      <?php if ($draftNewsCount > 0): ?><span class="admin-nav-badge"><?= (int) $draftNewsCount ?></span><?php endif; ?>
    </a>
    <a href="events.php" class="<?= in_array($adminCurrentPage, ['events', 'event-form'], true) ? 'current' : '' ?>">
      Events
      <?php if ($upcomingEventsCount > 0): ?><span class="admin-nav-badge"><?= (int) $upcomingEventsCount ?></span><?php endif; ?>
    </a>
    <a href="messages.php" class="<?= $adminCurrentPage === 'messages' ? 'current' : '' ?>">
      Contact Messages
      <?php if ($unreadCount > 0): ?><span class="admin-nav-badge"><?= (int) $unreadCount ?></span><?php endif; ?>
    </a>
    <a href="enquiries.php" class="<?= $adminCurrentPage === 'enquiries' ? 'current' : '' ?>">
      Admission Enquiries
      <?php if ($newEnquiryCount > 0): ?><span class="admin-nav-badge"><?= (int) $newEnquiryCount ?></span><?php endif; ?>
    </a>
    <a href="../index.php" target="_blank">View Site ↗</a>
  </nav>
  <div class="admin-sidebar-footer">
    <p>Signed in as<br><strong><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></strong></p>
    <a href="logout.php" class="btn btn-ghost admin-logout-btn">Log Out</a>
  </div>
</aside>
