<?php
// ============================================================
//  admin/dashboard.php — Admin Dashboard
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// ── STATS ────────────────────────────────────────────────────
$stats = [
  'unread_messages' => (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn(),
  'total_messages' => (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn(),
  'new_enquiries' => (int) $pdo->query("SELECT COUNT(*) FROM admission_enquiries WHERE status = 'new'")->fetchColumn(),
  'total_enquiries' => (int) $pdo->query('SELECT COUNT(*) FROM admission_enquiries')->fetchColumn(),
  'newsletter_subs' => (int) $pdo->query('SELECT COUNT(*) FROM newsletters_subscribers WHERE unsubscribed_at IS NULL')->fetchColumn(),
  'published_news' => (int) $pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 1')->fetchColumn(),
  'draft_news' => (int) $pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 0')->fetchColumn(),
  'upcoming_events' => (int) $pdo->query('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()')->fetchColumn(),
  'gallery_photos' => (int) $pdo->query('SELECT COUNT(*) FROM gallery_photos')->fetchColumn(),
  'active_staff' => (int) $pdo->query('SELECT COUNT(*) FROM staff WHERE is_active = 1')->fetchColumn(),
];

// ── RECENT ACTIVITY ─────────────────────────────────────────
$recentMessages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
$recentEnquiries = $pdo->query('SELECT * FROM admission_enquiries ORDER BY created_at DESC, id DESC LIMIT 5')->fetchAll();
$recentArticles = $pdo->query('SELECT * FROM news ORDER BY updated_at DESC LIMIT 5')->fetchAll();
$upcomingEvents = $pdo->query('SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard · Admin · Uganda Martyrs Primary School</title>
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
      <header class="admin-topbar">
        <h1>Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($_SESSION['admin_name']) ?>.</p>
      </header>

      <div class="admin-stats-grid">
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['unread_messages'] ?></span>
          <span class="admin-stat-label">Unread Messages</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['total_messages'] ?></span>
          <span class="admin-stat-label">Total Messages</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['new_enquiries'] ?></span>
          <span class="admin-stat-label">New Enquiries</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['total_enquiries'] ?></span>
          <span class="admin-stat-label">Total Enquiries</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['newsletter_subs'] ?></span>
          <span class="admin-stat-label">Newsletter Subscribers</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['published_news'] ?></span>
          <span class="admin-stat-label">Published News Articles</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['draft_news'] ?></span>
          <span class="admin-stat-label">Draft Articles</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['upcoming_events'] ?></span>
          <span class="admin-stat-label">Upcoming Events</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['gallery_photos'] ?></span>
          <span class="admin-stat-label">Gallery Photos</span>
        </div>
        <div class="admin-stat-card">
          <span class="admin-stat-num"><?= $stats['active_staff'] ?></span>
          <span class="admin-stat-label">Active Staff</span>
        </div>
      </div>

      <div class="admin-panels-grid">
        <section class="admin-panel">
          <div class="admin-panel-head">
            <h2>Recent Contact Messages</h2>
            <a href="messages.php">View all →</a>
          </div>
          <?php if ($recentMessages): ?>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Subject</th>
                  <th>Received</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentMessages as $m): ?>
                  <tr class="<?= $m['is_read'] ? '' : 'admin-row-unread' ?>">
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['subject']) ?></td>
                    <td><?= date('d M, H:i', strtotime($m['created_at'])) ?></td>
                    <td><a href="messages.php?view=<?= (int) $m['id'] ?>">View</a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="admin-empty">No messages yet.</p>
          <?php endif; ?>
        </section>

        <section class="admin-panel">
          <div class="admin-panel-head">
            <h2>Recent Admission Enquiries</h2>
            <a href="enquiries.php">View all →</a>
          </div>
          <?php if ($recentEnquiries): ?>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Parent</th>
                  <th>Child</th>
                  <th>Grade</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentEnquiries as $e): ?>
                  <tr>
                    <td><?= htmlspecialchars($e['parent_name']) ?></td>
                    <td><?= htmlspecialchars($e['student_name']) ?></td>
                    <td><?= htmlspecialchars($e['entry_level']) ?></td>
                    <td><span
                        class="admin-badge admin-badge-<?= htmlspecialchars($e['status']) ?>"><?= htmlspecialchars(ucfirst($e['status'])) ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="admin-empty">No enquiries yet.</p>
          <?php endif; ?>
        </section>

        <section class="admin-panel">
          <div class="admin-panel-head">
            <h2>Recent Articles</h2>
            <a href="news.php">Manage all →</a>
          </div>
          <?php if ($recentArticles): ?>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Status</th>
                  <th>Updated</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentArticles as $a): ?>
                  <tr>
                    <td><?= htmlspecialchars($a['title']) ?></td>
                    <td><span
                        class="admin-badge admin-badge-<?= $a['is_published'] ? 'published' : 'draft' ?>"><?= $a['is_published'] ? 'Published' : 'Draft' ?></span>
                    </td>
                    <td><?= date('d M, H:i', strtotime($a['updated_at'])) ?></td>
                    <td><a href="news-form.php?edit=<?= (int) $a['id'] ?>">Edit</a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="admin-empty">No articles yet. <a href="news-form.php">Write the first one →</a></p>
          <?php endif; ?>
        </section>

        <section class="admin-panel">
          <div class="admin-panel-head">
            <h2>Upcoming Events</h2>
            <a href="events.php">Manage all →</a>
          </div>
          <?php if ($upcomingEvents): ?>
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Event</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($upcomingEvents as $ev): ?>
                  <tr>
                    <td><?= htmlspecialchars($ev['title']) ?></td>
                    <td><?= date('d M Y', strtotime($ev['event_date'])) ?></td>
                    <td><span
                        class="admin-badge admin-badge-<?= $ev['is_published'] ? 'published' : 'draft' ?>"><?= $ev['is_published'] ? 'Published' : 'Draft' ?></span>
                    </td>
                    <td><a href="event-form.php?edit=<?= (int) $ev['id'] ?>">Edit</a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="admin-empty">No upcoming events. <a href="event-form.php">Add one →</a></p>
          <?php endif; ?>
        </section>
      </div>
    </main>
  </div>
</body>

</html>