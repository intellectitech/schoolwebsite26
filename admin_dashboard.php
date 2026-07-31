<?php
include 'db_connect.php';
require_once __DIR__ . '/auth.php';
require_login();

// Fallback safety for HTML escaping helper if not defined in auth.php/db_connect.php
if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }
}

/** Returns true if $table.$column exists in the connected database. */
function hasColumn(PDO $pdo, string $table, string $column): bool
{
    static $cache = [];
    $key = $table . '.' . $column;
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS c FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c'
    );
    $stmt->execute([':t' => $table, ':c' => $column]);
    $res = $stmt->fetch();
    $cache[$key] = $res ? (bool) $res['c'] : false;
    return $cache[$key];
}

$hasIsRead = hasColumn($pdo, 'contact_messages', 'is_read');

// ---------------------------------------------------------------------
// Stats
// ---------------------------------------------------------------------
$totalAdmissions = (int) ($pdo->query('SELECT COUNT(*) AS c FROM admissions')->fetch()['c'] ?? 0);
$totalAdminUsers = (int) ($pdo->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'] ?? 0);
$activeStaffCount = (int) ($pdo->query('SELECT COUNT(*) AS c FROM staff')->fetch()['c'] ?? 0);

if ($hasIsRead) {
    $unreadMessagesCount = (int) ($pdo->query('SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0')->fetch()['c'] ?? 0);
} else {
    $unreadMessagesCount = (int) ($pdo->query('SELECT COUNT(*) AS c FROM contact_messages')->fetch()['c'] ?? 0);
}

// ---------------------------------------------------------------------
// Admin Users
// ---------------------------------------------------------------------
$adminUsers = $pdo->query(
    'SELECT admin_id, username, email, created_at FROM admin_users ORDER BY created_at DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// Admissions
// ---------------------------------------------------------------------
$recentAdmissions = $pdo->query(
    'SELECT * FROM admissions ORDER BY application_date DESC LIMIT 5'
)->fetchAll();

$allAdmissions = $pdo->query(
    'SELECT * FROM admissions ORDER BY application_date DESC'
)->fetchAll();

$admissionEnquiries = $pdo->query(
    'SELECT * FROM admissions_enquiries ORDER BY created_at DESC'
)->fetchAll();

$admissionRequirements = $pdo->query(
    'SELECT * FROM admissions_requirements ORDER BY requirement_id DESC'
)->fetchAll();

$admissionDocuments = $pdo->query(
    'SELECT * FROM admissions_documents ORDER BY document_id DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// Staff & Departments
// ---------------------------------------------------------------------
$departments = $pdo->query(
    'SELECT * FROM departments ORDER BY department_name'
)->fetchAll();

$staffList = $pdo->query(
    'SELECT staff.*, departments.department_name
     FROM staff
     LEFT JOIN departments ON staff.department_id = departments.department_id
     ORDER BY staff.fullname'
)->fetchAll();

// ---------------------------------------------------------------------
// Academics (Subjects)
// ---------------------------------------------------------------------
$subjects = $pdo->query(
    'SELECT * FROM subjects ORDER BY subject_name'
)->fetchAll();

// ---------------------------------------------------------------------
// Events
// ---------------------------------------------------------------------
$events = $pdo->query(
    'SELECT * FROM events ORDER BY event_date DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// News & News Categories
// ---------------------------------------------------------------------
$newsCategories = $pdo->query(
    'SELECT * FROM news_categories ORDER BY category_name'
)->fetchAll();

$newsArticles = $pdo->query(
    'SELECT news.*, news_categories.category_name
     FROM news
     LEFT JOIN news_categories ON news.category_id = news_categories.category_id
     ORDER BY news.publish_date DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// Gallery (Albums & Photos)
// ---------------------------------------------------------------------
$galleryAlbums = $pdo->query(
    'SELECT * FROM gallery_albums ORDER BY created_at DESC'
)->fetchAll();

$galleryPhotos = $pdo->query(
    'SELECT gallery_photos.*, gallery_albums.album_title
     FROM gallery_photos
     LEFT JOIN gallery_albums ON gallery_photos.album_id = gallery_albums.album_id
     ORDER BY gallery_photos.photo_id DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// Website Content (School Info, Page Content, FAQs)
// ---------------------------------------------------------------------
$defaultSchoolInfo = [
    'school_id' => null, 'school_name' => '', 'phone' => '', 'email' => '',
    'address' => '', 'mission' => '', 'vision' => '',
];
$fetchedInfo = $pdo->query('SELECT * FROM school_info ORDER BY school_id ASC LIMIT 1')->fetch();
$schoolInfo = array_merge($defaultSchoolInfo, $fetchedInfo ?: []);

$pageContents = $pdo->query(
    'SELECT * FROM page_content ORDER BY page_id DESC'
)->fetchAll();

$faqs = $pdo->query(
    'SELECT * FROM faqs ORDER BY faq_id DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// Testimonials
// ---------------------------------------------------------------------
$testimonials = $pdo->query(
    'SELECT * FROM testimonials ORDER BY created_at DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// Newsletter Subscribers
// ---------------------------------------------------------------------
$newsletterSubscribers = $pdo->query(
    'SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC'
)->fetchAll();

// ---------------------------------------------------------------------
// Contact messages
// ---------------------------------------------------------------------
$messagesSql = $hasIsRead
    ? 'SELECT * FROM contact_messages ORDER BY sent_at DESC'
    : 'SELECT *, 0 AS is_read FROM contact_messages ORDER BY sent_at DESC';
$messages = $pdo->query($messagesSql)->fetchAll();

// ---------------------------------------------------------------------
// Audit log
// ---------------------------------------------------------------------
$auditLogs = $pdo->query(
    'SELECT audit_log.*, admin_users.username
     FROM audit_log
     LEFT JOIN admin_users ON audit_log.admin_id = admin_users.admin_id
     ORDER BY audit_log.action_time DESC
     LIMIT 200'
)->fetchAll();

$flashSuccess = $_GET['success'] ?? '';
$flashError   = $_GET['error'] ?? '';
$adminUsername = function_exists('current_admin_username') ? (current_admin_username() ?? 'Admin') : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School Management Dashboard</title>
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    /* ==========================================================================
       1. COLOR PALETTE & RESET (Green, Blue, White Theme)
       ========================================================================== */
    :root {
      --primary-blue: #1e3a8a;
      --secondary-blue: #3b82f6;
      --light-blue: #eff6ff;
      --accent-green: #10b981;
      --dark-green: #047857;
      --light-green: #ecfdf5;
      --bg-white: #ffffff;
      --bg-light: #f8fafc;
      --text-dark: #1e293b;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --sidebar-width: 260px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--bg-light);
      color: var(--text-dark);
      display: flex;
      height: 100vh;
      overflow: hidden;
    }

    /* ==========================================================================
       2. SIDEBAR STYLES
       ========================================================================== */
    .sidebar {
      width: var(--sidebar-width);
      background: linear-gradient(180deg, var(--primary-blue) 0%, #0f172a 100%);
      color: white;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
      z-index: 100;
    }

    .sidebar-header {
      padding: 24px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header i {
      font-size: 1.8rem;
      color: var(--accent-green);
    }

    .sidebar-header h2 {
      font-size: 1.2rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .sidebar-menu {
      list-style: none;
      padding: 20px 0;
      flex-grow: 1;
      overflow-y: auto;
    }

    .sidebar-menu li a {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 14px 24px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 0.95rem;
      font-weight: 500;
      transition: all 0.2s ease;
      border-left: 4px solid transparent;
      cursor: pointer;
    }

    .sidebar-menu li a:hover {
      color: white;
      background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-menu li.active a {
      color: white;
      background: rgba(16, 185, 129, 0.15);
      border-left-color: var(--accent-green);
    }

    .sidebar-menu li a i {
      width: 20px;
      font-size: 1.1rem;
    }

    .sidebar-footer {
      padding: 16px 24px 24px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-footer a {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .sidebar-footer a:hover { color: white; }

    /* ==========================================================================
       3. MAIN CONTENT AREA
       ========================================================================== */
    .main-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }

    /* Top Navigation Header */
    .top-header {
      background-color: var(--bg-white);
      height: 70px;
      padding: 0 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border-color);
    }

    .header-title h1 {
      font-size: 1.4rem;
      color: var(--primary-blue);
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .user-badge {
      background-color: var(--light-green);
      color: var(--dark-green);
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--secondary-blue);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    /* View Sections Container */
    .content-body {
      padding: 30px;
      flex-grow: 1;
    }

    .view-section {
      display: none;
      animation: fadeIn 0.3s ease-in-out;
    }

    .view-section.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ==========================================================================
       4. CARDS & STATS (Dashboard Overview)
       ========================================================================== */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: var(--bg-white);
      border-radius: 10px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 20px;
      border: 1px solid var(--border-color);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
    }

    .stat-icon.green { background-color: var(--light-green); color: var(--accent-green); }
    .stat-icon.blue { background-color: var(--light-blue); color: var(--secondary-blue); }

    .stat-info h3 {
      font-size: 1.5rem;
      color: var(--text-dark);
    }

    .stat-info p {
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    /* ==========================================================================
       5. DATA TABLES
       ========================================================================== */
    .card-table {
      background: var(--bg-white);
      border-radius: 10px;
      border: 1px solid var(--border-color);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
      overflow: hidden;
      margin-bottom: 25px;
    }

    .card-header {
      padding: 20px;
      background-color: var(--bg-white);
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-header h2 {
      font-size: 1.1rem;
      color: var(--primary-blue);
    }

    .custom-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    .custom-table th {
      background-color: #f8fafc;
      color: var(--text-muted);
      padding: 14px 20px;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      border-bottom: 1px solid var(--border-color);
    }

    .custom-table td {
      padding: 14px 20px;
      border-bottom: 1px solid var(--border-color);
      font-size: 0.9rem;
      vertical-align: top;
    }

    .custom-table tbody tr:hover {
      background-color: #f1f5f9;
    }

    .badge {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
    }

    .badge-success { background-color: var(--light-green); color: var(--dark-green); }
    .badge-info { background-color: var(--light-blue); color: var(--secondary-blue); }
    .badge-muted { background-color: #f1f5f9; color: var(--text-muted); }

    /* Button Utilities */
    .btn {
      padding: 8px 16px;
      border-radius: 6px;
      border: none;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary { background-color: var(--secondary-blue); color: white; }
    .btn-primary:hover { background-color: var(--primary-blue); }

    .btn-danger { background-color: #fef2f2; color: #b91c1c; }
    .btn-danger:hover { background-color: #fee2e2; }

    .btn-sm { padding: 5px 10px; font-size: 0.78rem; }

    .icon-btn-form { display: inline; }

    /* Flash banners */
    .flash {
      padding: 14px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .flash-success { background: var(--light-green); color: var(--dark-green); }
    .flash-error { background: #fef2f2; color: #b91c1c; }

    /* Modal */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.5);
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
      background: #fff;
      border-radius: 10px;
      width: 100%;
      max-width: 460px;
      padding: 26px;
      max-height: 90vh;
      overflow-y: auto;
    }
    .modal-box h3 { color: var(--primary-blue); margin-bottom: 18px; font-size: 1.1rem; }
    .modal-box .form-group { margin-bottom: 14px; }
    .modal-box label { display:block; font-size:0.82rem; font-weight:600; margin-bottom:5px; color: var(--text-dark); }
    .modal-box input, .modal-box select, .modal-box textarea {
      width: 100%; padding: 9px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem;
    }
    .modal-box textarea { resize: vertical; min-height: 70px; }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top: 18px; }
    .btn-cancel { background:#f1f5f9; color: var(--text-muted); }
    .btn-cancel:hover { background:#e2e8f0; }

    .mini-card {
      background: var(--bg-white);
      border-radius: 10px;
      border: 1px solid var(--border-color);
      padding: 20px;
      margin-bottom: 25px;
    }
    .mini-card h3 { font-size: 1rem; color: var(--primary-blue); margin-bottom: 14px; }
    .dept-form { display:flex; gap:10px; flex-wrap: wrap; }
    .dept-form input { flex:1; min-width: 160px; padding: 9px 12px; border: 1px solid var(--border-color); border-radius: 6px; }
    .dept-pill-list { display:flex; flex-wrap:wrap; gap:8px; margin-bottom: 14px; }
    .dept-pill { background: var(--light-blue); color: var(--secondary-blue); padding: 5px 12px; border-radius: 14px; font-size: 0.8rem; font-weight: 600; }

    /* Sub-tab navigation inside a section */
    .subtab-nav {
      display: flex;
      gap: 6px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .subtab-btn {
      padding: 9px 18px;
      border-radius: 20px;
      border: 1px solid var(--border-color);
      background: var(--bg-white);
      color: var(--text-muted);
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
    }
    .subtab-btn.active {
      background: var(--secondary-blue);
      color: white;
      border-color: var(--secondary-blue);
    }
    .subview { display: none; }
    .subview.active { display: block; }

    .inline-form-card {
      background: var(--bg-white);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 25px;
    }
    .inline-form-card h3 { font-size: 1rem; color: var(--primary-blue); margin-bottom: 14px; }
    .inline-form-card .form-row { display:flex; gap:10px; flex-wrap:wrap; }
    .inline-form-card .form-row > * { flex: 1; min-width: 160px; }
    .inline-form-card input, .inline-form-card select, .inline-form-card textarea {
      padding: 9px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; width: 100%;
    }
    .inline-form-card textarea { resize: vertical; min-height: 60px; }
    .inline-form-card .form-actions { margin-top: 12px; }
  </style>
</head>
<body>

  <!-- SIDEBAR NAVIGATION -->
  <aside class="sidebar">
    <div class="sidebar-header">
       
      <h2>Bbina islamic school</h2>
      
    </div>
    <ul class="sidebar-menu">
      <li class="active" data-view="dashboard">
        <a><i class="fa-solid fa-chart-line"></i> Dashboard</a>
      </li>
      <li data-view="admin_users">
        <a><i class="fa-solid fa-user-shield"></i> Admin Users</a>
      </li>
      <li data-view="admissions">
        <a><i class="fa-solid fa-user-plus"></i> Admissions</a>
      </li>
      <li data-view="staff">
        <a><i class="fa-solid fa-chalkboard-user"></i> Staff & Depts</a>
      </li>
      <li data-view="academics">
        <a><i class="fa-solid fa-book"></i> Academics</a>
      </li>
      <li data-view="events">
        <a><i class="fa-solid fa-calendar-days"></i> Events</a>
      </li>
      <li data-view="news">
        <a><i class="fa-solid fa-newspaper"></i> News</a>
      </li>
      <li data-view="gallery">
        <a><i class="fa-solid fa-images"></i> Gallery</a>
      </li>
      <li data-view="content">
        <a><i class="fa-solid fa-file-lines"></i> Website Content</a>
      </li>
      <li data-view="testimonials">
        <a><i class="fa-solid fa-quote-left"></i> Testimonials</a>
      </li>
      <li data-view="newsletter">
        <a><i class="fa-solid fa-paper-plane"></i> Newsletter</a>
      </li>
      <li data-view="messages">
        <a><i class="fa-solid fa-envelope"></i> Contact Messages</a>
      </li>
      <li data-view="audit">
        <a><i class="fa-solid fa-shield-halved"></i> Audit Log</a>
      </li>
    </ul>
    <div class="sidebar-footer">
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
    </div>
  </aside>

  <!-- MAIN WRAPPER -->
  <div class="main-wrapper">
    
    <!-- TOP NAVBAR -->
    <header class="top-header">
      <div class="header-title">
        <h1 id="page-title">Dashboard Overview</h1>
      </div>
      <div class="user-profile">
        <span class="user-badge"><i class="fa-solid fa-circle"></i> System Active</span>
        <div class="avatar"><?= h(strtoupper(substr((string)$adminUsername, 0, 1))) ?></div>
      </div>
    </header>

    <!-- CONTENT BODY CONTAINER -->
    <main class="content-body">

      <?php if ($flashSuccess !== ''): ?>
        <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> <?= h($flashSuccess) ?></div>
      <?php endif; ?>
      <?php if ($flashError !== ''): ?>
        <div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($flashError) ?></div>
      <?php endif; ?>

      <!-- VIEW 1: DASHBOARD OVERVIEW -->
      <section id="dashboard" class="view-section active">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-file-signature"></i></div>
            <div class="stat-info">
              <h3><?= (int) $totalAdmissions ?></h3>
              <p>Total Admissions</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
              <h3><?= (int) $totalAdminUsers ?></h3>
              <p>Admin Users</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-building-user"></i></div>
            <div class="stat-info">
              <h3><?= (int) $activeStaffCount ?></h3>
              <p>Active Staff</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-envelope-open-text"></i></div>
            <div class="stat-info">
              <h3><?= (int) $unreadMessagesCount ?></h3>
              <p>Unread Messages</p>
            </div>
          </div>
        </div>

        <div class="card-table">
          <div class="card-header">
            <h2>Recent Admission Applications</h2>
            <button class="btn btn-primary" onclick="switchTab('admissions')">View All</button>
          </div>
          <table class="custom-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Applicant Name</th>
                <th>Section Preference</th>
                <th>Parent Name</th>
                <th>Primary Phone</th>
                <th>Date Applied</th>
              </tr>
            </thead>
            <tbody id="overview-admissions-tbody">
              <?php if (empty($recentAdmissions)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--text-muted);">No admission applications yet.</td></tr>
              <?php else: foreach ($recentAdmissions as $row): ?>
                <tr>
                  <td>#<?= (int) $row['admission_id'] ?></td>
                  <td><strong><?= h($row['first_name'] . ' ' . $row['last_name']) ?></strong></td>
                  <td><span class="badge badge-info"><?= h($row['section_preference'] ?: '—') ?></span></td>
                  <td><?= h($row['parent_name']) ?></td>
                  <td><?= h($row['primary_phone']) ?></td>
                  <td><?= h($row['application_date']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW: ADMIN USERS -->
      <section id="admin_users" class="view-section">
        <div class="card-table">
          <div class="card-header">
            <h2>System Administrators</h2>
            <button class="btn btn-primary" onclick="openModal('addAdminUserModal')"><i class="fa-solid fa-user-plus"></i> Add Admin</button>
          </div>
          <table class="custom-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($adminUsers)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No administrators found.</td></tr>
              <?php else: foreach ($adminUsers as $admin): ?>
                <tr>
                  <td>#<?= (int) $admin['admin_id'] ?></td>
                  <td><strong><?= h($admin['username']) ?></strong></td>
                  <td><?= h($admin['email'] ?: '—') ?></td>
                  <td><?= h($admin['created_at']) ?></td>
                  <td>
                    <form class="icon-btn-form" action="actions/delete_admin_user.php" method="POST" onsubmit="return confirm('Remove this admin account?');">
                      <input type="hidden" name="admin_id" value="<?= (int) $admin['admin_id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW 2: ADMISSIONS (Applications | Enquiries | Requirements | Documents) -->
      <section id="admissions" class="view-section">

        <div class="subtab-nav">
          <button class="subtab-btn active" data-subtab-group="admissions" data-subtab="applications">Applications</button>
          <button class="subtab-btn" data-subtab-group="admissions" data-subtab="enquiries">Enquiries</button>
          <button class="subtab-btn" data-subtab-group="admissions" data-subtab="requirements">Requirements</button>
          <button class="subtab-btn" data-subtab-group="admissions" data-subtab="documents">Documents</button>
        </div>

        <!-- Applications (admissions table) -->
        <div class="subview active" data-subtab-group="admissions" data-subview="applications">
          <div class="card-table">
            <div class="card-header">
              <h2>Admissions Registry</h2>
            </div>
            <table class="custom-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Full Name</th>
                  <th>Gender</th>
                  <th>Section Preference</th>
                  <th>Parent Details</th>
                  <th>Address</th>
                  <th>Application Date</th>
                </tr>
              </thead>
              <tbody id="full-admissions-tbody">
                <?php if (empty($allAdmissions)): ?>
                  <tr><td colspan="7" style="text-align:center;color:var(--text-muted);">No admission applications yet.</td></tr>
                <?php else: foreach ($allAdmissions as $row): ?>
                  <tr>
                    <td>#<?= (int) $row['admission_id'] ?></td>
                    <td><strong><?= h($row['first_name'] . ' ' . $row['last_name']) ?></strong></td>
                    <td><?= h($row['gender']) ?></td>
                    <td><span class="badge badge-info"><?= h($row['section_preference'] ?: '—') ?></span></td>
                    <td><?= h($row['parent_name']) ?> (<?= h($row['primary_phone']) ?>)</td>
                    <td><?= h($row['residential_address']) ?></td>
                    <td><?= h($row['application_date']) ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Enquiries (admissions_enquiries table) -->
        <div class="subview" data-subtab-group="admissions" data-subview="enquiries">
          <div class="card-table">
            <div class="card-header"><h2>Admission Enquiries</h2></div>
            <table class="custom-table">
              <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Received</th><th></th></tr>
              </thead>
              <tbody>
                <?php if (empty($admissionEnquiries)): ?>
                  <tr><td colspan="7" style="text-align:center;color:var(--text-muted);">No enquiries received yet.</td></tr>
                <?php else: foreach ($admissionEnquiries as $en): ?>
                  <tr>
                    <td>#<?= (int) $en['enquiry_id'] ?></td>
                    <td><strong><?= h($en['fullname']) ?></strong></td>
                    <td><?= h($en['email']) ?></td>
                    <td><?= h($en['phone']) ?></td>
                    <td><?= h($en['message']) ?></td>
                    <td><?= h($en['created_at']) ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_enquiry.php" method="POST" onsubmit="return confirm('Delete this enquiry?');">
                        <input type="hidden" name="enquiry_id" value="<?= (int) $en['enquiry_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Requirements (admissions_requirements table) -->
        <div class="subview" data-subtab-group="admissions" data-subview="requirements">
          <div class="inline-form-card">
            <h3>Add Admission Requirement</h3>
            <form action="actions/add_requirement.php" method="POST" class="form-row">
              <textarea name="requirement_text" placeholder="e.g. Copy of birth certificate" required></textarea>
              <button type="submit" class="btn btn-primary" style="flex:0 0 auto;"><i class="fa-solid fa-plus"></i> Add</button>
            </form>
          </div>
          <div class="card-table">
            <div class="card-header"><h2>Admission Requirements</h2></div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Requirement</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($admissionRequirements)): ?>
                  <tr><td colspan="3" style="text-align:center;color:var(--text-muted);">No requirements listed yet.</td></tr>
                <?php else: foreach ($admissionRequirements as $req): ?>
                  <tr>
                    <td>#<?= (int) $req['requirement_id'] ?></td>
                    <td><?= h($req['requirement_text']) ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_requirement.php" method="POST" onsubmit="return confirm('Delete this requirement?');">
                        <input type="hidden" name="requirement_id" value="<?= (int) $req['requirement_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Documents (admissions_documents table) -->
        <div class="subview" data-subtab-group="admissions" data-subview="documents">
          <div class="inline-form-card">
            <h3>Add Admission Document</h3>
            <form action="actions/add_document.php" method="POST" class="form-row">
              <input type="text" name="document_name" placeholder="Document name" required>
              <input type="text" name="file_path" placeholder="File path / URL">
              <button type="submit" class="btn btn-primary" style="flex:0 0 auto;"><i class="fa-solid fa-plus"></i> Add</button>
            </form>
          </div>
          <div class="card-table">
            <div class="card-header"><h2>Admission Documents</h2></div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Document Name</th><th>File Path</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($admissionDocuments)): ?>
                  <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No documents on file.</td></tr>
                <?php else: foreach ($admissionDocuments as $doc): ?>
                  <tr>
                    <td>#<?= (int) $doc['document_id'] ?></td>
                    <td><?= h($doc['document_name']) ?></td>
                    <td><?= h($doc['file_path'] ?: '—') ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_document.php" method="POST" onsubmit="return confirm('Delete this document?');">
                        <input type="hidden" name="document_id" value="<?= (int) $doc['document_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- VIEW 3: STAFF & DEPARTMENTS -->
      <section id="staff" class="view-section">

        <div class="mini-card">
          <h3>Departments</h3>
          <div class="dept-pill-list">
            <?php if (empty($departments)): ?>
              <span style="color:var(--text-muted); font-size:0.85rem;">No departments yet — add one below.</span>
            <?php else: foreach ($departments as $dept): ?>
              <span class="dept-pill"><?= h($dept['department_name']) ?></span>
            <?php endforeach; endif; ?>
          </div>
          <form class="dept-form" action="actions/add_department.php" method="POST">
            <input type="text" name="department_name" placeholder="New department name" required>
            <input type="text" name="description" placeholder="Description (optional)">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add Department</button>
          </form>
        </div>

        <div class="card-table">
          <div class="card-header">
            <h2>Staff Directory</h2>
            <button class="btn btn-primary" onclick="openModal('addStaffModal')"><i class="fa-solid fa-plus"></i> Add Staff</button>
          </div>
          <table class="custom-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Department</th>
                <th>Email</th>
                <th>Phone</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($staffList)): ?>
                <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">No staff members added to database yet.</td></tr>
              <?php else: foreach ($staffList as $s): ?>
                <tr>
                  <td>#<?= (int) $s['staff_id'] ?></td>
                  <td><strong><?= h($s['fullname']) ?></strong></td>
                  <td><?= h($s['role'] ?: '—') ?></td>
                  <td><?= !empty($s['department_name']) ? '<span class="badge badge-info">' . h($s['department_name']) . '</span>' : '<span class="badge badge-muted">Unassigned</span>' ?></td>
                  <td><?= h($s['email'] ?: '—') ?></td>
                  <td><?= h($s['phone'] ?: '—') ?></td>
                  <td>
                    <form class="icon-btn-form" action="actions/delete_staff.php" method="POST" onsubmit="return confirm('Remove this staff member?');">
                      <input type="hidden" name="staff_id" value="<?= (int) $s['staff_id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW: ACADEMICS (subjects table) -->
      <section id="academics" class="view-section">
        <div class="inline-form-card">
          <h3>Add Subject</h3>
          <form action="actions/add_subject.php" method="POST" class="form-row">
            <input type="text" name="subject_name" placeholder="Subject name" required>
            <input type="text" name="teacher_name" placeholder="Teacher name">
            <button type="submit" class="btn btn-primary" style="flex:0 0 auto;"><i class="fa-solid fa-plus"></i> Add Subject</button>
          </form>
        </div>
        <div class="card-table">
          <div class="card-header"><h2>Subjects</h2></div>
          <table class="custom-table">
            <thead><tr><th>ID</th><th>Subject</th><th>Teacher</th><th></th></tr></thead>
            <tbody>
              <?php if (empty($subjects)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No subjects added yet.</td></tr>
              <?php else: foreach ($subjects as $subj): ?>
                <tr>
                  <td>#<?= (int) $subj['subject_id'] ?></td>
                  <td><strong><?= h($subj['subject_name']) ?></strong></td>
                  <td><?= h($subj['teacher_name'] ?: '—') ?></td>
                  <td>
                    <form class="icon-btn-form" action="actions/delete_subject.php" method="POST" onsubmit="return confirm('Delete this subject?');">
                      <input type="hidden" name="subject_id" value="<?= (int) $subj['subject_id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW 4: EVENTS -->
      <section id="events" class="view-section">
        <div class="card-table">
          <div class="card-header">
            <h2>Upcoming Events</h2>
            <button class="btn btn-primary" onclick="openModal('addEventModal')"><i class="fa-solid fa-plus"></i> Create Event</button>
          </div>
          <table class="custom-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date</th>
                <th>Location</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($events)): ?>
                <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No events found in table structure.</td></tr>
              <?php else: foreach ($events as $e): ?>
                <tr>
                  <td>#<?= (int) $e['event_id'] ?></td>
                  <td><strong><?= h($e['title']) ?></strong><?php if (!empty($e['description'])): ?><br><span style="color:var(--text-muted); font-size:0.8rem;"><?= h($e['description']) ?></span><?php endif; ?></td>
                  <td><?= h($e['event_date']) ?></td>
                  <td><?= h($e['location'] ?: '—') ?></td>
                  <td>
                    <form class="icon-btn-form" action="actions/delete_event.php" method="POST" onsubmit="return confirm('Delete this event?');">
                      <input type="hidden" name="event_id" value="<?= (int) $e['event_id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW: NEWS (news + news_categories tables) -->
      <section id="news" class="view-section">
        <div class="subtab-nav">
          <button class="subtab-btn active" data-subtab-group="news" data-subtab="articles">Articles</button>
          <button class="subtab-btn" data-subtab-group="news" data-subtab="categories">Categories</button>
        </div>

        <div class="subview active" data-subtab-group="news" data-subview="articles">
          <div class="card-table">
            <div class="card-header">
              <h2>News Articles</h2>
              <button class="btn btn-primary" onclick="openModal('addNewsModal')"><i class="fa-solid fa-plus"></i> Add Article</button>
            </div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Published</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($newsArticles)): ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No news articles yet.</td></tr>
                <?php else: foreach ($newsArticles as $n): ?>
                  <tr>
                    <td>#<?= (int) $n['news_id'] ?></td>
                    <td><strong><?= h($n['title']) ?></strong><?php if (!empty($n['content'])): ?><br><span style="color:var(--text-muted); font-size:0.8rem;"><?= h(mb_strimwidth((string)$n['content'], 0, 120, '…')) ?></span><?php endif; ?></td>
                    <td><?= !empty($n['category_name']) ? '<span class="badge badge-info">' . h($n['category_name']) . '</span>' : '<span class="badge badge-muted">Uncategorized</span>' ?></td>
                    <td><?= h($n['publish_date'] ?: '—') ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_news.php" method="POST" onsubmit="return confirm('Delete this article?');">
                        <input type="hidden" name="news_id" value="<?= (int) $n['news_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="subview" data-subtab-group="news" data-subview="categories">
          <div class="inline-form-card">
            <h3>Add News Category</h3>
            <form action="actions/add_news_category.php" method="POST" class="form-row">
              <input type="text" name="category_name" placeholder="Category name" required>
              <button type="submit" class="btn btn-primary" style="flex:0 0 auto;"><i class="fa-solid fa-plus"></i> Add</button>
            </form>
          </div>
          <div class="card-table">
            <div class="card-header"><h2>News Categories</h2></div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Category</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($newsCategories)): ?>
                  <tr><td colspan="3" style="text-align:center;color:var(--text-muted);">No categories yet.</td></tr>
                <?php else: foreach ($newsCategories as $cat): ?>
                  <tr>
                    <td>#<?= (int) $cat['category_id'] ?></td>
                    <td><?= h($cat['category_name']) ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_news_category.php" method="POST" onsubmit="return confirm('Delete this category?');">
                        <input type="hidden" name="category_id" value="<?= (int) $cat['category_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW: GALLERY (gallery_albums + gallery_photos tables) -->
      <section id="gallery" class="view-section">
        <div class="subtab-nav">
          <button class="subtab-btn active" data-subtab-group="gallery" data-subtab="albums">Albums</button>
          <button class="subtab-btn" data-subtab-group="gallery" data-subtab="photos">Photos</button>
        </div>

        <div class="subview active" data-subtab-group="gallery" data-subview="albums">
          <div class="inline-form-card">
            <h3>Create Album</h3>
            <form action="actions/add_gallery_album.php" method="POST" class="form-row">
              <input type="text" name="album_title" placeholder="Album title" required>
              <button type="submit" class="btn btn-primary" style="flex:0 0 auto;"><i class="fa-solid fa-plus"></i> Create</button>
            </form>
          </div>
          <div class="card-table">
            <div class="card-header"><h2>Gallery Albums</h2></div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Title</th><th>Created</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($galleryAlbums)): ?>
                  <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No albums yet.</td></tr>
                <?php else: foreach ($galleryAlbums as $alb): ?>
                  <tr>
                    <td>#<?= (int) $alb['album_id'] ?></td>
                    <td><strong><?= h($alb['album_title']) ?></strong></td>
                    <td><?= h($alb['created_at']) ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_gallery_album.php" method="POST" onsubmit="return confirm('Delete this album and all its photos?');">
                        <input type="hidden" name="album_id" value="<?= (int) $alb['album_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="subview" data-subtab-group="gallery" data-subview="photos">
          <div class="inline-form-card">
            <h3>Add Photo</h3>
            <form action="actions/add_gallery_photo.php" method="POST" class="form-row">
              <select name="album_id">
                <option value="">— No album —</option>
                <?php foreach ($galleryAlbums as $alb): ?>
                  <option value="<?= (int) $alb['album_id'] ?>"><?= h($alb['album_title']) ?></option>
                <?php endforeach; ?>
              </select>
              <input type="text" name="photo" placeholder="Photo filename / path" required>
              <input type="text" name="description" placeholder="Description">
              <button type="submit" class="btn btn-primary" style="flex:0 0 auto;"><i class="fa-solid fa-plus"></i> Add</button>
            </form>
          </div>
          <div class="card-table">
            <div class="card-header"><h2>Gallery Photos</h2></div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Album</th><th>Photo</th><th>Description</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($galleryPhotos)): ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No photos yet.</td></tr>
                <?php else: foreach ($galleryPhotos as $ph): ?>
                  <tr>
                    <td>#<?= (int) $ph['photo_id'] ?></td>
                    <td><?= !empty($ph['album_title']) ? h($ph['album_title']) : '<span class="badge badge-muted">None</span>' ?></td>
                    <td><?= h($ph['photo']) ?></td>
                    <td><?= h($ph['description'] ?: '—') ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_gallery_photo.php" method="POST" onsubmit="return confirm('Delete this photo?');">
                        <input type="hidden" name="photo_id" value="<?= (int) $ph['photo_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW: WEBSITE CONTENT (school_info + page_content + faqs tables) -->
      <section id="content" class="view-section">
        <div class="subtab-nav">
          <button class="subtab-btn active" data-subtab-group="content" data-subtab="schoolinfo">School Info</button>
          <button class="subtab-btn" data-subtab-group="content" data-subtab="pages">Pages</button>
          <button class="subtab-btn" data-subtab-group="content" data-subtab="faqs">FAQs</button>
        </div>

        <div class="subview active" data-subtab-group="content" data-subview="schoolinfo">
          <div class="card-table">
            <div class="card-header"><h2>School Info</h2></div>
            <div style="padding:20px;">
              <form action="actions/save_school_info.php" method="POST">
                <div class="form-group" style="margin-bottom:14px;">
                  <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:5px;">School Name</label>
                  <input type="text" name="school_name" value="<?= h($schoolInfo['school_name']) ?>" style="width:100%;padding:9px 12px;border:1px solid var(--border-color);border-radius:6px;">
                </div>
                <div class="form-row" style="display:flex;gap:10px;margin-bottom:14px;">
                  <div style="flex:1;">
                    <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:5px;">Phone</label>
                    <input type="text" name="phone" value="<?= h($schoolInfo['phone']) ?>" style="width:100%;padding:9px 12px;border:1px solid var(--border-color);border-radius:6px;">
                  </div>
                  <div style="flex:1;">
                    <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:5px;">Email</label>
                    <input type="email" name="email" value="<?= h($schoolInfo['email']) ?>" style="width:100%;padding:9px 12px;border:1px solid var(--border-color);border-radius:6px;">
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                  <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:5px;">Address</label>
                  <textarea name="address" style="width:100%;padding:9px 12px;border:1px solid var(--border-color);border-radius:6px;min-height:50px;"><?= h($schoolInfo['address']) ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                  <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:5px;">Mission</label>
                  <textarea name="mission" style="width:100%;padding:9px 12px;border:1px solid var(--border-color);border-radius:6px;min-height:60px;"><?= h($schoolInfo['mission']) ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                  <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:5px;">Vision</label>
                  <textarea name="vision" style="width:100%;padding:9px 12px;border:1px solid var(--border-color);border-radius:6px;min-height:60px;"><?= h($schoolInfo['vision']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save School Info</button>
              </form>
            </div>
          </div>
        </div>

        <div class="subview" data-subtab-group="content" data-subview="pages">
          <div class="inline-form-card">
            <h3>Add Page</h3>
            <form action="actions/add_page_content.php" method="POST">
              <div class="form-row">
                <input type="text" name="page_title" placeholder="Page title" required>
              </div>
              <div class="form-row" style="margin-top:10px;">
                <textarea name="page_body" placeholder="Page body"></textarea>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Page</button>
              </div>
            </form>
          </div>
          <div class="card-table">
            <div class="card-header"><h2>Pages</h2></div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Title</th><th>Body</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($pageContents)): ?>
                  <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No pages yet.</td></tr>
                <?php else: foreach ($pageContents as $pg): ?>
                  <tr>
                    <td>#<?= (int) $pg['page_id'] ?></td>
                    <td><strong><?= h($pg['page_title']) ?></strong></td>
                    <td><?= h(mb_strimwidth((string) $pg['page_body'], 0, 100, '…')) ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_page_content.php" method="POST" onsubmit="return confirm('Delete this page?');">
                        <input type="hidden" name="page_id" value="<?= (int) $pg['page_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="subview" data-subtab-group="content" data-subview="faqs">
          <div class="inline-form-card">
            <h3>Add FAQ</h3>
            <form action="actions/add_faq.php" method="POST">
              <div class="form-row">
                <input type="text" name="question" placeholder="Question" required>
              </div>
              <div class="form-row" style="margin-top:10px;">
                <textarea name="answer" placeholder="Answer"></textarea>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add FAQ</button>
              </div>
            </form>
          </div>
          <div class="card-table">
            <div class="card-header"><h2>FAQs</h2></div>
            <table class="custom-table">
              <thead><tr><th>ID</th><th>Question</th><th>Answer</th><th></th></tr></thead>
              <tbody>
                <?php if (empty($faqs)): ?>
                  <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No FAQs yet.</td></tr>
                <?php else: foreach ($faqs as $faq): ?>
                  <tr>
                    <td>#<?= (int) $faq['faq_id'] ?></td>
                    <td><?= h($faq['question']) ?></td>
                    <td><?= h($faq['answer'] ?: '—') ?></td>
                    <td>
                      <form class="icon-btn-form" action="actions/delete_faq.php" method="POST" onsubmit="return confirm('Delete this FAQ?');">
                        <input type="hidden" name="faq_id" value="<?= (int) $faq['faq_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VIEW: TESTIMONIALS (testimonials table) -->
      <section id="testimonials" class="view-section">
        <div class="inline-form-card">
          <h3>Add Testimonial</h3>
          <form action="actions/add_testimonial.php" method="POST">
            <div class="form-row">
              <input type="text" name="student_name" placeholder="Student name" required>
            </div>
            <div class="form-row" style="margin-top:10px;">
              <textarea name="message" placeholder="Testimonial message" required></textarea>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Testimonial</button>
            </div>
          </form>
        </div>
        <div class="card-table">
          <div class="card-header"><h2>Testimonials</h2></div>
          <table class="custom-table">
            <thead><tr><th>ID</th><th>Student</th><th>Message</th><th>Date</th><th></th></tr></thead>
            <tbody>
              <?php if (empty($testimonials)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No testimonials yet.</td></tr>
              <?php else: foreach ($testimonials as $t): ?>
                <tr>
                  <td>#<?= (int) $t['testimonial_id'] ?></td>
                  <td><strong><?= h($t['student_name']) ?></strong></td>
                  <td><?= h($t['message']) ?></td>
                  <td><?= h($t['created_at']) ?></td>
                  <td>
                    <form class="icon-btn-form" action="actions/delete_testimonial.php" method="POST" onsubmit="return confirm('Delete this testimonial?');">
                      <input type="hidden" name="testimonial_id" value="<?= (int) $t['testimonial_id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW: NEWSLETTER SUBSCRIBERS (newsletter_subscribers table) -->
      <section id="newsletter" class="view-section">
        <div class="inline-form-card">
          <h3>Add Subscriber</h3>
          <form action="actions/add_subscriber.php" method="POST" class="form-row">
            <input type="email" name="email" placeholder="email@example.com" required>
            <button type="submit" class="btn btn-primary" style="flex:0 0 auto;"><i class="fa-solid fa-plus"></i> Add</button>
          </form>
        </div>
        <div class="card-table">
          <div class="card-header"><h2>Newsletter Subscribers</h2></div>
          <table class="custom-table">
            <thead><tr><th>ID</th><th>Email</th><th>Subscribed</th><th></th></tr></thead>
            <tbody>
              <?php if (empty($newsletterSubscribers)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No subscribers yet.</td></tr>
              <?php else: foreach ($newsletterSubscribers as $sub): ?>
                <tr>
                  <td>#<?= (int) $sub['subscriber_id'] ?></td>
                  <td><?= h($sub['email']) ?></td>
                  <td><?= h($sub['subscribed_at']) ?></td>
                  <td>
                    <form class="icon-btn-form" action="actions/delete_subscriber.php" method="POST" onsubmit="return confirm('Remove this subscriber?');">
                      <input type="hidden" name="subscriber_id" value="<?= (int) $sub['subscriber_id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW 5: CONTACT MESSAGES -->
      <section id="messages" class="view-section">
        <div class="card-table">
          <div class="card-header">
            <h2>Contact Messages</h2>
          </div>
          <table class="custom-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Sender</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Sent At</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($messages)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No contact messages received yet.</td></tr>
              <?php else: foreach ($messages as $m): ?>
                <tr>
                  <td>#<?= (int) $m['message_id'] ?></td>
                  <td><strong><?= h($m['fullname']) ?></strong><br><span style="color:var(--text-muted); font-size:0.8rem;"><?= h($m['email']) ?></span></td>
                  <td><?= h($m['subject'] ?: '—') ?></td>
                  <td><?= h($m['message']) ?></td>
                  <td><?= h($m['sent_at']) ?></td>
                  <td>
                    <?php if ((int) $m['is_read'] === 1): ?>
                      <span class="badge badge-success">Read</span>
                    <?php else: ?>
                      <form class="icon-btn-form" action="actions/mark_read.php" method="POST">
                        <input type="hidden" name="message_id" value="<?= (int) $m['message_id'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Mark Read</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- VIEW 6: AUDIT LOG -->
      <section id="audit" class="view-section">
        <div class="card-table">
          <div class="card-header">
            <h2>System Audit Logs</h2>
          </div>
          <table class="custom-table">
            <thead>
              <tr>
                <th>Log ID</th>
                <th>Admin</th>
                <th>Action Description</th>
                <th>Timestamp</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($auditLogs)): ?>
                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No actions recorded in audit log.</td></tr>
              <?php else: foreach ($auditLogs as $log): ?>
                <tr>
                  <td>#<?= (int) $log['log_id'] ?></td>
                  <td><?= h(!empty($log['username']) ? $log['username'] : 'Unknown (#' . (int) $log['admin_id'] . ')') ?></td>
                  <td><?= h($log['action']) ?></td>
                  <td><?= h($log['action_time']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </section>

    </main>
  </div>

  <!-- MODAL: ADD ADMIN USER -->
  <div class="modal-overlay" id="addAdminUserModal">
    <div class="modal-box">
      <h3><i class="fa-solid fa-user-shield"></i> Add Administrator</h3>
      <form action="actions/add_admin_user.php" method="POST">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" required>
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-cancel" onclick="closeModal('addAdminUserModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Admin</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: ADD STAFF -->
  <div class="modal-overlay" id="addStaffModal">
    <div class="modal-box">
      <h3><i class="fa-solid fa-user-plus"></i> Add Staff Member</h3>
      <form action="actions/add_staff.php" method="POST">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="fullname" required>
        </div>
        <div class="form-group">
          <label>Role</label>
          <input type="text" name="role" placeholder="e.g. Teacher, Accountant">
        </div>
        <div class="form-group">
          <label>Department</label>
          <select name="department_id">
            <option value="">— Unassigned —</option>
            <?php foreach ($departments as $dept): ?>
              <option value="<?= (int) $dept['department_id'] ?>"><?= h($dept['department_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-cancel" onclick="closeModal('addStaffModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Staff Member</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: CREATE EVENT -->
  <div class="modal-overlay" id="addEventModal">
    <div class="modal-box">
      <h3><i class="fa-solid fa-calendar-plus"></i> Create Event</h3>
      <form action="actions/add_event.php" method="POST">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description"></textarea>
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="date" name="event_date" required>
        </div>
        <div class="form-group">
          <label>Location</label>
          <input type="text" name="location">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-cancel" onclick="closeModal('addEventModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Event</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: ADD NEWS ARTICLE -->
  <div class="modal-overlay" id="addNewsModal">
    <div class="modal-box">
      <h3><i class="fa-solid fa-newspaper"></i> Add News Article</h3>
      <form action="actions/add_news.php" method="POST">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" required>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category_id">
            <option value="">— Uncategorized —</option>
            <?php foreach ($newsCategories as $cat): ?>
              <option value="<?= (int) $cat['category_id'] ?>"><?= h($cat['category_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Content</label>
          <textarea name="content"></textarea>
        </div>
        <div class="form-group">
          <label>Publish Date</label>
          <input type="date" name="publish_date">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-cancel" onclick="closeModal('addNewsModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Article</button>
        </div>
      </form>
    </div>
  </div>

  <!-- INTERACTIVE JAVASCRIPT -->
  <script>
    // NAVIGATION CONTROLLER (SIDEBAR CLICK ENGINE)
    const sidebarItems = document.querySelectorAll('.sidebar-menu li');
    const viewSections = document.querySelectorAll('.view-section');
    const pageTitle = document.getElementById('page-title');

    const titles = {
      dashboard: 'Dashboard Overview',
      admin_users: 'System Administrators',
      admissions: 'Admissions',
      staff: 'Staff & Department Management',
      academics: 'Academics',
      events: 'Events',
      news: 'News & Categories',
      gallery: 'Photo Gallery',
      content: 'Website Content',
      testimonials: 'Testimonials',
      newsletter: 'Newsletter Subscribers',
      messages: 'Contact Form Inquiries',
      audit: 'System Audit Trail'
    };

    function switchSubtab(group, subviewId) {
      document.querySelectorAll('.subtab-btn[data-subtab-group="' + group + '"]').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-subtab') === subviewId);
      });
      document.querySelectorAll('.subview[data-subtab-group="' + group + '"]').forEach(panel => {
        panel.classList.toggle('active', panel.getAttribute('data-subview') === subviewId);
      });
    }

    function switchTab(viewId) {
      sidebarItems.forEach(item => {
        item.classList.toggle('active', item.getAttribute('data-view') === viewId);
      });
      viewSections.forEach(section => {
        section.classList.toggle('active', section.id === viewId);
      });
      if (titles[viewId]) {
        pageTitle.textContent = titles[viewId];
        history.replaceState(null, '', '#' + viewId);
      }
    }

    sidebarItems.forEach(item => {
      item.addEventListener('click', function () {
        switchTab(this.getAttribute('data-view'));
      });
    });

    document.querySelectorAll('.subtab-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        switchSubtab(this.getAttribute('data-subtab-group'), this.getAttribute('data-subtab'));
      });
    });

    // MODAL HELPERS
    function openModal(id) {
      document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
      document.getElementById(id).classList.remove('active');
    }
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('active');
      });
    });

    // Restore tab state from URL Hash
    document.addEventListener('DOMContentLoaded', () => {
      const hash = window.location.hash.replace('#', '');
      if (hash && titles[hash]) {
        switchTab(hash);
      }
    });
  </script>
</body>
</html>