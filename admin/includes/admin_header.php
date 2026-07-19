<?php
/**
 * admin/includes/admin_header.php
 * Shared sidebar + topbar for all protected admin pages.
 * Expects: $page_title, $active_nav (one of: dashboard, news, gallery,
 *          admissions, messages, events, staff, settings)
 */
if (!isset($page_title)) { $page_title = 'Admin Dashboard'; }
if (!isset($active_nav)) { $active_nav = ''; }

// Small helpers for sidebar "unread" counters
$newInquiriesCount = count(array_filter(ds_read('admissions'), function ($r) { return ($r['status'] ?? 'new') === 'new'; }));
$unreadMessagesCount = count(array_filter(ds_read('messages'), function ($r) { return empty($r['is_read']); }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?> | Mbuya Parents' School Admin</title>
<link rel="icon" type="image/svg+xml" href="../assets/images/badge.jpg">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-shell">

  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <img src="../assets/images/badge.jpg" alt="Badge">
      <div class="sidebar-brand-text">
        Mbuya Parents' School
        <span>ADMIN DASHBOARD</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-label">Overview</div>
      <a href="index.php" class="<?php echo $active_nav === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>

      <div class="nav-label">Content</div>
      <a href="news.php" class="<?php echo $active_nav === 'news' ? 'active' : ''; ?>">News &amp; Blog</a>
      <a href="gallery.php" class="<?php echo $active_nav === 'gallery' ? 'active' : ''; ?>">Gallery</a>
      <a href="events.php" class="<?php echo $active_nav === 'events' ? 'active' : ''; ?>">Events</a>
      <a href="staff.php" class="<?php echo $active_nav === 'staff' ? 'active' : ''; ?>">Staff</a>

      <div class="nav-label">Inbox</div>
      <a href="admissions.php" class="<?php echo $active_nav === 'admissions' ? 'active' : ''; ?>">
        Admissions
        <?php if ($newInquiriesCount > 0): ?><span class="badge-count"><?php echo $newInquiriesCount; ?></span><?php endif; ?>
      </a>
      <a href="messages.php" class="<?php echo $active_nav === 'messages' ? 'active' : ''; ?>">
        Messages
        <?php if ($unreadMessagesCount > 0): ?><span class="badge-count"><?php echo $unreadMessagesCount; ?></span><?php endif; ?>
      </a>

      <div class="nav-label">Account</div>
      <a href="change_password.php" class="<?php echo $active_nav === 'settings' ? 'active' : ''; ?>">Change Password</a>
    </nav>

    <div class="sidebar-footer">
      <a href="../index.php" target="_blank" class="view-site">View Live Site</a>
      <a href="logout.php">&#10162; Log Out</a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div style="display:flex; align-items:center; gap:14px;">
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">&#9776;</button>
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
      </div>
      <div class="topbar-meta">Signed in as <strong><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?></strong></div>
    </div>
    <div class="content">
