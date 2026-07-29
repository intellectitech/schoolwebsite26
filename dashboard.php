<?php
session_start();

// Security Guard: Prevent guest access to administrative view
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// 1. Unified Database Configurations (MySQLi & PDO)
include 'database.php'; // Provides $conn (mysqli)

$host     = '127.0.0.1';
$db       = 'school_website_db'; 
$user     = 'root';              
$password = '';                  
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. Handle Contact Messages Actions (Mark Read / Delete)
$actionMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['msg_id'])) {
        $msgId = (int)$_POST['msg_id'];

        if ($_POST['action'] === 'toggle_read') {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = NOT is_read WHERE id = :id");
            $stmt->execute([':id' => $msgId]);
            $actionMessage = "Message status updated.";
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
            $stmt->execute([':id' => $msgId]);
            $actionMessage = "Message deleted successfully.";
        }
    }
}

// 3. Handle Audit Log Deletions (Single & Delete All)
$auditMessage = "";
$auditMessageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['audit_action'])) {
    if ($_POST['audit_action'] === 'delete_single' && isset($_POST['log_id'])) {
        $logId = (int)$_POST['log_id'];
        try {
            $delStmt = $pdo->prepare("DELETE FROM admin_activity_log WHERE id = :id");
            $delStmt->execute([':id' => $logId]);
            $auditMessage = "Audit log entry #{$logId} deleted successfully.";
            $auditMessageType = "success";
        } catch (\PDOException $e) {
            $auditMessage = "Failed to delete log entry.";
            $auditMessageType = "error";
        }
    } elseif ($_POST['audit_action'] === 'delete_all') {
        try {
            $pdo->exec("TRUNCATE TABLE admin_activity_log");
            $auditMessage = "All audit log entries have been cleared.";
            $auditMessageType = "success";
        } catch (\PDOException $e) {
            $auditMessage = "Failed to clear audit logs.";
            $auditMessageType = "error";
        }
    }
}

// Fetch Messages (Most recent first)
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

// Count unread messages
$unreadStmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$unreadCount = $unreadStmt->fetchColumn();

// 4. Handle Articles Submission
$article_message = "";
$article_message_type = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_article'])) {
    $art_title   = trim($_POST['title']);
    $art_author  = trim($_POST['author']);
    $art_content = trim($_POST['content']);

    if (!empty($art_title) && !empty($art_author) && !empty($art_content)) {
        $art_stmt = $conn->prepare("INSERT INTO articles (title, author, content) VALUES (?, ?, ?)");
        $art_stmt->bind_param("sss", $art_title, $art_author, $art_content);

        if ($art_stmt->execute()) {
            $article_message = "Published!";
            $article_message_type = "success";
        } else {
            $article_message = "Failed to save.";
            $article_message_type = "error";
        }
        $art_stmt->close();
    } else {
        $article_message = "Fill all fields.";
        $article_message_type = "error";
    }
}

$articles_result = $conn->query("SELECT * FROM articles ORDER BY created_at DESC");

// 5. Fetch News Categories & Handle News Form Submission
$categories = [];
$cat_query = "SELECT id, name FROM news_categories ORDER BY name ASC";
$cat_result = $conn->query($cat_query);
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$news_message = "";
$news_message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && !isset($_POST['submit_article'])) {
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $title = trim($_POST['title']);
    $excerpt = trim($_POST['excerpt']);
    $body = trim($_POST['body']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    $author_id = $_SESSION['admin_id'] ?? null;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    $views = 0;

    if (!empty($title) && !empty($body)) {
        $featured_image = "";
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['featured_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid('news_', true) . '.' . $ext;
                $upload_dir = 'uploads/news/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_dir . $new_filename)) {
                    $featured_image = $upload_dir . $new_filename;
                }
            }
        }

        $current_timestamp = date("Y-m-d H:i:s");
        $published_at = $is_published ? $current_timestamp : null;

        $news_stmt = $conn->prepare("INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, author_id, views, is_published, is_featured, published_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $news_stmt->bind_param("isssssiiiisss", $category_id, $title, $slug, $excerpt, $body, $featured_image, $author_id, $views, $is_published, $is_featured, $published_at, $current_timestamp, $current_timestamp);

        if ($news_stmt->execute()) {
            $news_message = "News article published successfully!";
            $news_message_type = "success";
        } else {
            if ($conn->errno == 1062) {
                $news_message = "An article with this title or slug already exists.";
            } else {
                $news_message = "Error saving article: " . $conn->error;
            }
            $news_message_type = "error";
        }
        $news_stmt->close();
    } else {
        $news_message = "Please fill in all mandatory fields (Title and Body).";
        $news_message_type = "error";
    }
}

// 6. Fetch Admin Profile Details & Admissions Summary
try {
    $profile_stmt = $pdo->prepare("SELECT email, created_at, profile_photo FROM admin_users WHERE id = ?");
    $profile_stmt->execute([$_SESSION['admin_id']]);
    $db_user = $profile_stmt->fetch() ?: [];
} catch (\PDOException $e) {
    $db_user = [];
}

$c_name   = $_SESSION['admin_name'] ?? 'User Account';
$c_role   = $_SESSION['admin_role'] ?? 'Staff Member';
$c_email  = $db_user['email'] ?? 'Not Available';
$c_photo  = $db_user['profile_photo'] ?? '';
$c_join_date = !empty($db_user['created_at']) ? date("F j, Y", strtotime($db_user['created_at'])) : 'Not Available';

try {
    $statusStmt = $pdo->query("
        SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS new_count,
            SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) AS contacted_count,
            SUM(CASE WHEN status = 'enrolled' THEN 1 ELSE 0 END) AS enrolled_count,
            SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) AS declined_count
        FROM admissions_enquiries
    ");
    $statusSummary = $statusStmt->fetch();

    $levelStmt = $pdo->query("
        SELECT entry_level, COUNT(*) as count 
        FROM admissions_enquiries 
        GROUP BY entry_level
    ");
    $levelSummary = $levelStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $recentStmt = $pdo->query("
        SELECT id, parent_name, parent_phone, student_name, entry_level, ple_aggregate, status, created_at 
        FROM admissions_enquiries 
        ORDER BY created_at DESC 
        LIMIT 4
    ");
    $recentEnquiries = $recentStmt->fetchAll();
} catch (\PDOException $e) {
    $statusSummary = ['total' => 0, 'new_count' => 0, 'contacted_count' => 0, 'enrolled_count' => 0, 'declined_count' => 0];
    $levelSummary = [];
    $recentEnquiries = [];
}

// 7. Fetch Audit Activity Logs from admin_activity_log
try {
    $logStmt = $pdo->query("
        SELECT 
            l.id, 
            l.admin_id, 
            l.action, 
            l.target_type, 
            l.target_id, 
            l.details, 
            l.created_at,
            u.name AS admin_name,
            u.role AS admin_role
        FROM admin_activity_log l
        LEFT JOIN admin_users u ON l.admin_id = u.id
        ORDER BY l.created_at DESC
        LIMIT 50
    ");
    $auditLogs = $logStmt->fetchAll();
} catch (\PDOException $e) {
    $auditLogs = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        :root {
            --primary-blue: #1d4ed8;
            --primary-hover: #1e40af;
            --light-blue: #eff6ff;
            --border-blue: #bfdbfe;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
        }

        /* Compact Dashboard Card Container */
        .widget-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 8px;
            max-width: 700px;
            margin: 0 auto;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .widget-header {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
        }

        .widget-header svg {
            width: 16px;
            height: 16px;
            fill: var(--primary-blue);
        }

        .widget-header h3 {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .widget-body {
            padding: 12px;
        }

        /* Small Alerts */
        .alert {
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .alert.success { background: #dcfce7; color: #166534; }
        .alert.error { background: #fee2e2; color: #991b1b; }

        /* Compact Forms */
        .form-group { margin-bottom: 8px; }
        .form-row { display: flex; gap: 8px; }
        .form-row .form-group { flex: 1; }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 3px;
        }

        input[type="text"], textarea {
            width: 100%;
            background: var(--bg-body);
            border: 1px solid var(--border);
            color: var(--text-dark);
            padding: 5px 8px;
            border-radius: 4px;
            outline: none;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: var(--primary-blue);
            background: #ffffff;
        }

        .btn-widget {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            background: var(--primary-blue);
            color: #ffffff;
            border: none;
            padding: 6px;
            font-weight: 600;
            font-size: 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 12px;
        }

        .btn-widget:hover { background: var(--primary-hover); }
        .btn-widget svg { width: 13px; height: 13px; fill: #ffffff; }

        .articles-feed {
            max-height: 220px;
            overflow-y: auto;
            border-top: 1px solid var(--border);
            padding-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .article-item {
            background: var(--bg-body);
            border: 1px solid var(--border);
            padding: 8px 10px;
            border-radius: 6px;
        }

        .article-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 2px;
        }

        .article-meta {
            font-size: 10px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .article-content {
            font-size: 11px;
            color: var(--text-dark);
            line-height: 1.3;
            max-height: 36px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* Inbox Styling */
        .inbox-card {
            background: var(--card-bg);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .inbox-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: var(--light-blue);
            border-bottom: 1px solid var(--border-blue);
        }

        .header-title { display: flex; align-items: center; gap: 8px; }
        .header-title i { font-size: 20px; color: var(--primary-blue); }
        .header-title h3 { font-size: 15px; font-weight: 700; color: var(--primary-blue); }

        .unread-badge {
            background: var(--primary-blue);
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .inbox-body { padding: 16px; }
        .alert-notice {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .messages-feed {
            max-height: 520px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-right: 4px;
        }

        .msg-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid var(--border-blue);
            border-radius: 6px;
            padding: 12px 14px;
        }

        .msg-card.unread {
            border-left-color: var(--primary-blue);
            background: #fafcfe;
        }

        .msg-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
        .sender-info { display: flex; align-items: center; gap: 6px; font-weight: 700; color: var(--text-dark); font-size: 13px; }
        .sender-info i { color: var(--primary-blue); }
        .msg-date { font-size: 11px; color: var(--text-muted); }
        .msg-contact-details { display: flex; gap: 12px; font-size: 11px; color: var(--text-muted); margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #e2e8f0; }
        .msg-subject { font-size: 13px; font-weight: 700; color: var(--primary-blue); margin-bottom: 4px; }
        .msg-content { font-size: 12px; color: #334155; line-height: 1.4; white-space: pre-line; background: var(--bg-body); padding: 8px; border-radius: 4px; margin-bottom: 8px; }
        .msg-actions { display: flex; justify-content: flex-end; gap: 6px; }
        .btn-msg-action { display: inline-flex; align-items: center; gap: 4px; border: none; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; }
        .btn-read { background: var(--light-blue); color: var(--primary-blue); }
        .btn-delete { background: #fee2e2; color: #b91c1c; }

        /* Audit Logs Specific Styles */
        .audit-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        }
        .audit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .audit-header h2 {
            font-size: 1.4rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .audit-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-delete-all {
            background-color: #ef4444;
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-delete-all:hover {
            background-color: #dc2626;
        }
        .btn-log-delete {
            background-color: #fee2e2;
            color: #991b1b;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: background 0.2s;
        }
        .btn-log-delete:hover {
            background-color: #fca5a5;
        }
        .audit-table-responsive { overflow-x: auto; }
        .audit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            text-align: left;
        }
        .audit-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            padding: 12px 16px;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .audit-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .badge-action {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-create { background-color: #dcfce7; color: #166534; }
        .badge-update { background-color: #e0f2fe; color: #0369a1; }
        .badge-delete { background-color: #fee2e2; color: #991b1b; }
        .badge-other { background-color: #f1f5f9; color: #475569; }
    </style>
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">
    <div class="logo">
        <img class="logo-img" src="assets/images/logoo.svg" alt="Logo">
        <span>ADMIN PANEL</span>
    </div>
    <ul class="menu">
        <li class="active" data-page="dashboard"><i class='bx bxs-dashboard'></i><span>Dashboard</span></li>
        <li data-page="classes"><i class='bx bxs-news'></i><span>Articles</span></li>
        <li data-page="Admissions"><i class='bx bxs-file-doc'></i><span>Admissions</span></li>
        <li data-page="NEWS & EVENTS"><i class='bx bx-news'></i><span>News & Events</span></li>
        <li data-page="gallery"><i class='bx bx-image'></i><span>Gallery</span></li>
        <li data-page="messages"><i class='bx bx-envelope'></i><span>Messages</span></li>
        <li data-page="settings"><i class='bx bx-cog'></i><span>Settings</span></li>
        <li data-page="audit-logs"><i class='bx bx-history'></i><span>Audit Logs</span></li>
    </ul>
</aside>

<!-- ================= MAIN ================= -->
<main class="main">

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="left">
            <button id="menuBtn"><i class='bx bx-menu'></i></button>
            <h2 id="pageTitle">Dashboard</h2>
        </div>
        <div class="search">
            <i class='bx bx-search'></i>
            <input type="text" placeholder="Search anything...">
        </div>
        <div class="right">
            <button>
                <i class='bx bx-bell'></i>
                <span class="count"><?= htmlspecialchars($unreadCount) ?></span>
            </button>
            <button class="nav-msg-btn">
                <i class='bx bx-envelope'></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="count"><?= $unreadCount ?></span>
                <?php endif; ?>
            </button>
            <button id="themeToggle"><i class='bx bx-moon'></i></button>
            
            <div class="profile">
                <div id="topbarAvatarDisplay" class="topbar-avatar-wrapper">
                    <?php if (!empty($c_photo) && file_exists('./uploads/profiles/' . $c_photo)): ?>
                        <img src="./uploads/profiles/<?= htmlspecialchars($c_photo) ?>" alt="Profile" class="avatar-img-sm">
                    <?php else: ?>
                        <i class='bx bxs-user-circle avatar-placeholder-sm'></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h4><?= htmlspecialchars($c_name) ?></h4>
                    <p><?= htmlspecialchars($c_role) ?></p>
                </div>
            </div>
        </div>
    </header>

    <!-- ================= CONTENT ================= -->
    <div class="content">

        <!-- DASHBOARD PAGE -->
        <section class="page active" id="dashboard">
            <h1>Dashboard Overview</h1>

            <div class="cards">
                <div class="card">
                    <i class='bx bx-user'></i>
                    <h2 class="counter">1250</h2>
                    <p>Total Students</p>
                </div>
                <div class="card">
                    <i class='bx bx-book'></i>
                    <h2 class="counter">145</h2>
                    <p>Enquiry Messages</p>
                </div>
                <div class="card">
                    <i class='bx bx-user-plus'></i>
                    <h2 class="counter">42</h2>
                    <p>Admissions</p>
                </div>
            </div>

            <div class="grid">
                <div class="panel">
                    <h3>Recent Admissions Enquiries</h3>
                    <div class="dashboard-enquiries-wrapper">
                        <div class="cards-container-mini">
                            <div class="card-mini">
                                <h3>Total</h3>
                                <div class="number"><?= htmlspecialchars($statusSummary['total'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>New</h3>
                                <div class="number text-warning"><?= htmlspecialchars($statusSummary['new_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>Contacted</h3>
                                <div class="number text-navy"><?= htmlspecialchars($statusSummary['contacted_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>Enrolled</h3>
                                <div class="number text-success"><?= htmlspecialchars($statusSummary['enrolled_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>Declined</h3>
                                <div class="number text-danger"><?= htmlspecialchars($statusSummary['declined_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>S1/S5</h3>
                                <div class="number text-sm">
                                    <?= htmlspecialchars($levelSummary['S1'] ?? 0) ?> / <?= htmlspecialchars($levelSummary['S5'] ?? 0) ?>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive-mini">
                            <table class="table-mini">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Parent Phone</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentEnquiries)): ?>
                                        <?php foreach ($recentEnquiries as $row): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                                                <td><?= htmlspecialchars($row['parent_phone']) ?></td>
                                                <td><?= htmlspecialchars($row['entry_level'] ?: 'N/A') ?></td>
                                                <td>
                                                    <span class="badge-mini badge-<?= htmlspecialchars($row['status']) ?>">
                                                        <?= htmlspecialchars($row['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, H:i', strtotime($row['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No recent enquiries found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <h3>Quick Actions</h3>
                    <div class="actions">
                        <a href="newsletters.php"><button class="btn-action">Newsletters subscribers</button></a>
                        <a href="add_user.php"><button class="btn-action">Add User</button></a>
                        <button class="btn-action">Send Notice</button>
                        <button class="btn-action">Upload Results</button>
                        <button class="btn-action">Create Event</button>
                        <button class="btn-action">Generate Report</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ARTICLES -->
        <section class="page" id="classes">
            <div class="widget-card">
                <div class="widget-header">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    <h3>Articles Manager</h3>
                </div>

                <div class="widget-body">
                    <?php if (!empty($article_message)): ?>
                        <div class="alert <?= $article_message_type ?>">
                            <?= htmlspecialchars($article_message) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form action="" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>Author</label>
                                <input type="text" name="author" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="content" rows="2" required></textarea>
                        </div>

                        <button type="submit" name="submit_article" class="btn-widget">
                            <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                            Post
                        </button>
                    </form>

                    <!-- Feed -->
                    <div class="articles-feed">
                        <?php if ($articles_result && $articles_result->num_rows > 0): ?>
                            <?php while ($row = $articles_result->fetch_assoc()): ?>
                                <div class="article-item">
                                    <div class="article-title"><?= htmlspecialchars($row['title']) ?></div>
                                    <div class="article-meta">
                                        By <strong><?= htmlspecialchars($row['author']) ?></strong> &bull; <?= date("M j", strtotime($row['created_at'])) ?>
                                    </div>
                                    <div class="article-content">
                                        <?= htmlspecialchars($row['content']) ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="empty-state">No articles yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- ADMISSIONS -->
        <section class="page" id="Admissions">
            <h1>Admissions Management</h1>
            <br>
            <div class="grid">
                <div class="panel">
                    <h3>Recent Admissions Enquiries</h3>
                    <div class="dashboard-enquiries-wrapper">
                        <div class="cards-container-mini">
                            <div class="card-mini">
                                <h3>Total</h3>
                                <div class="number"><?= htmlspecialchars($statusSummary['total'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>New</h3>
                                <div class="number text-warning"><?= htmlspecialchars($statusSummary['new_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>Contacted</h3>
                                <div class="number text-navy"><?= htmlspecialchars($statusSummary['contacted_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>Enrolled</h3>
                                <div class="number text-success"><?= htmlspecialchars($statusSummary['enrolled_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>Declined</h3>
                                <div class="number text-danger"><?= htmlspecialchars($statusSummary['declined_count'] ?? 0) ?></div>
                            </div>
                            <div class="card-mini">
                                <h3>S1/S5</h3>
                                <div class="number text-sm">
                                    <?= htmlspecialchars($levelSummary['S1'] ?? 0) ?> / <?= htmlspecialchars($levelSummary['S5'] ?? 0) ?>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive-mini">
                            <table class="table-mini">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Parent Phone</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentEnquiries)): ?>
                                        <?php foreach ($recentEnquiries as $row): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                                                <td><?= htmlspecialchars($row['parent_phone']) ?></td>
                                                <td><?= htmlspecialchars($row['entry_level'] ?: 'N/A') ?></td>
                                                <td>
                                                    <span class="badge-mini badge-<?= htmlspecialchars($row['status']) ?>">
                                                        <?= htmlspecialchars($row['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, H:i', strtotime($row['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No recent enquiries found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div style="display:flex; gap:40px; margin-top:15px;">  
                                <a href="read.php"><button class="btn-submit btn-w-200">READ ADMISSIONS</button></a>
                                <a href="check.php"><button class="btn-submit btn-w-220">APPROVE DOCUMENTS</button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- NEWS AND EVENTS -->
        <section class="page" id="NEWS & EVENTS">
            <?php if (!empty($news_message)): ?>
                <div class="alert-popup alert-<?= $news_message_type ?>">
                    <span><?= $news_message ?></span>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <h2>Publish School News</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Article Title *</label>
                        <input type="text" id="title" name="title" required placeholder="e.g., Annual Sports Gala">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">News Category</label>
                            <select id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="featured_image">Featured Banner Image</label>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*" class="file-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="excerpt">Brief Summary (Excerpt)</label>
                        <textarea id="excerpt" name="excerpt" rows="2" placeholder="A short catch-phrase summary..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="body">Main Content Body *</label>
                        <textarea id="body" name="body" rows="8" required placeholder="Write the full news details here..."></textarea>
                    </div>
                    <div class="checkbox-group">
                        <label class="checkbox-label"><input type="checkbox" name="is_featured" value="1"> Set as Featured Post</label>
                        <label class="checkbox-label"><input type="checkbox" name="is_published" value="1" checked> Publish Immediately</label>
                    </div>
                    <button type="submit" class="btn-submit">Save & Publish Post</button> <br><br>
                </form>

                <a href="manage_news.php">
                    <button type="button" class="btn-submit">Delete Old news</button>
                </a>
            </div>
        </section>

        <!-- GALLERY -->
        <section class="page" id="gallery">
            <div class="form-container">
                <form action="gallery.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="photo">Choose Photo *</label>
                        <input type="file" name="photo" id="photo" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="caption">Caption (Optional)</label>
                        <input type="text" name="caption" id="caption" placeholder="Describe this photo..." maxlength="300">
                    </div>
                    <div class="form-group">
                        <label for="sort_order">Sort Order Position</label>
                        <input type="number" name="sort_order" id="sort_order" value="0" min="0">
                    </div>
                    <button type="submit" class="btn-submit">Publish Gallery Entry</button> <br><br>
                </form>
            </div>

            <a href="manageposts.php">
                <button type="button" class="btn-submit">Delete Gallery</button> 
            </a>
        </section>

        <!-- MESSAGES -->
        <section class="page" id="messages">
            <h1>Messages</h1>
            <div class="inbox-card">
                <div class="inbox-header">
                    <div class="header-title">
                        <i class='bx bx-envelope'></i>
                        <h3>Contact Messages Inbox</h3>
                    </div>
                    <span class="unread-badge"><?= $unreadCount ?> Unread</span>
                </div>

                <div class="inbox-body">
                    <?php if (!empty($actionMessage)): ?>
                        <div class="alert-notice"><?= htmlspecialchars($actionMessage) ?></div>
                    <?php endif; ?>

                    <div class="messages-feed">
                        <?php if (!empty($messages)): ?>
                            <?php foreach ($messages as $msg): ?>
                                <div class="msg-card <?= $msg['is_read'] == 0 ? 'unread' : '' ?>">
                                    <div class="msg-header">
                                        <div class="sender-info">
                                            <i class='bx bx-user-circle'></i>
                                            <?= htmlspecialchars($msg['name']) ?>
                                        </div>
                                        <span class="msg-date">
                                            <i class='bx bx-time-five'></i>
                                            <?= date("M j, Y • g:i a", strtotime($msg['created_at'])) ?>
                                        </span>
                                    </div>

                                    <div class="msg-contact-details">
                                        <span><i class='bx bx-mail-send'></i> <?= htmlspecialchars($msg['email']) ?></span>
                                        <?php if (!empty($msg['phone'])): ?>
                                            <span><i class='bx bx-phone'></i> <?= htmlspecialchars($msg['phone']) ?></span>
                                        <?php endif; ?>
                                        <span><i class='bx bx-desktop'></i> IP: <?= htmlspecialchars($msg['ip_address']) ?></span>
                                    </div>

                                    <div class="msg-subject">Subject: <?= htmlspecialchars($msg['subject']) ?></div>
                                    <div class="msg-content"><?= htmlspecialchars($msg['message']) ?></div>

                                    <div class="msg-actions">
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="msg_id" value="<?= $msg['id'] ?>">
                                            <input type="hidden" name="action" value="toggle_read">
                                            <button type="submit" class="btn-msg-action btn-read">
                                                <i class='bx <?= $msg['is_read'] ? 'bx-envelope' : 'bx-envelope-open' ?>'></i>
                                                <?= $msg['is_read'] ? 'Mark Unread' : 'Mark Read' ?>
                                            </button>
                                        </form>

                                        <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?');">
                                            <input type="hidden" name="msg_id" value="<?= $msg['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-msg-action btn-delete">
                                                <i class='bx bx-trash'></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-inbox">
                                <i class='bx bx-folder-open'></i>
                                <p>No messages received yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- SETTINGS -->
        <section class="page" id="settings">
            <h1>Settings</h1>
            <div class="settings-card">
                <div class="profile-setup-section">
                    <div class="settings-avatar-wrapper">
                        <div id="avatarDisplay">
                            <?php if (!empty($c_photo) && file_exists('./uploads/profiles/' . $c_photo)): ?>
                                <img src="./uploads/profiles/<?= htmlspecialchars($c_photo) ?>" alt="Avatar" class="settings-avatar">
                            <?php else: ?>
                                <i class='bx bxs-user-circle settings-avatar-icon'></i>
                            <?php endif; ?>
                        </div>
                        <label class="photo-upload-label" for="asyncPhotoInput" title="Change profile picture">
                            <i class='bx bxs-camera'></i>
                            <input type="file" id="asyncPhotoInput" accept="image/*">
                        </label>
                    </div>
                    <div class="meta-details">
                        <h3><?= htmlspecialchars($c_name) ?></h3>
                        <span class="badge-role"><?= htmlspecialchars($c_role) ?></span>
                        <div id="uploadStatus" class="status-toast"></div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-row">
                        <label>Registered Email Address</label>
                        <span><?= htmlspecialchars($c_email) ?></span>
                    </div>
                    <div class="info-row">
                        <label>Account Created On</label>
                        <span><i class='bx bx-calendar'></i> <?= htmlspecialchars($c_join_date) ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- AUDIT LOGS -->
        <section class="page" id="audit-logs">
            <div class="audit-card">
                <div class="audit-header">
                    <h2><i class='bx bx-history'></i> System Audit Logs</h2>
                    <div class="audit-header-actions">
                        <span class="unread-badge"><?= count($auditLogs) ?> Entries</span>
                        <?php if (!empty($auditLogs)): ?>
                            <form action="" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete ALL audit logs? This cannot be undone.');">
                                <input type="hidden" name="audit_action" value="delete_all">
                                <button type="submit" class="btn-delete-all">
                                    <i class='bx bx-trash'></i> Delete All Logs
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($auditMessage)): ?>
                    <div class="alert <?= $auditMessageType ?>" style="margin-bottom: 15px;">
                        <?= htmlspecialchars($auditMessage) ?>
                    </div>
                <?php endif; ?>

                <div class="audit-table-responsive">
                    <table class="audit-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Admin User</th>
                                <th>Action</th>
                                <th>Target Type</th>
                                <th>Target ID</th>
                                <th>Details</th>
                                <th>Timestamp</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($auditLogs)): ?>
                                <?php foreach ($auditLogs as $log): ?>
                                    <?php 
                                        $actionClass = 'badge-other';
                                        if (strpos(strtoupper($log['action']), 'CREATE') !== false) {
                                            $actionClass = 'badge-create';
                                        } elseif (strpos(strtoupper($log['action']), 'SUBSCRIBE') !== false || strpos(strtoupper($log['action']), 'UPDATE') !== false) {
                                            $actionClass = 'badge-update';
                                        } elseif (strpos(strtoupper($log['action']), 'DELETE') !== false) {
                                            $actionClass = 'badge-delete';
                                        }
                                    ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars($log['id']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($log['admin_name'] ?: 'System / Guest') ?></strong>
                                            <?php if (!empty($log['admin_role'])): ?>
                                                <br><small style="color: #64748b;"><?= htmlspecialchars($log['admin_role']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge-action <?= $actionClass ?>">
                                                <?= htmlspecialchars($log['action']) ?>
                                            </span>
                                        </td>
                                        <td><code><?= htmlspecialchars($log['target_type']) ?></code></td>
                                        <td><?= htmlspecialchars($log['target_id'] ?: '-') ?></td>
                                        <td><?= htmlspecialchars($log['details'] ?: 'No details recorded') ?></td>
                                        <td><?= date('M d, Y • g:i A', strtotime($log['created_at'])) ?></td>
                                        <td>
                                            <form action="" method="POST" style="margin: 0;" onsubmit="return confirm('Delete log entry #<?= $log['id'] ?>?');">
                                                <input type="hidden" name="audit_action" value="delete_single">
                                                <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                                                <button type="submit" class="btn-log-delete" title="Delete entry">
                                                    <i class='bx bx-trash'></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #64748b; padding: 20px;">
                                        No activity logs found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>
</main>

<script>
    // Theme Toggle
    const themeToggleBtn = document.getElementById('themeToggle');
    const themeIcon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        if (themeIcon) themeIcon.classList.replace('bx-moon', 'bx-sun');
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (themeIcon) {
                if (isDark) {
                    themeIcon.classList.replace('bx-moon', 'bx-sun');
                } else {
                    themeIcon.classList.replace('bx-sun', 'bx-moon');
                }
            }
        });
    }

    // Sidebar Navigation & Page Switcher
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main');

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
        });
    }

    const menuItems = document.querySelectorAll('.menu li[data-page]');
    const pages = document.querySelectorAll('.page');
    const pageTitle = document.getElementById('pageTitle');

    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            menuItems.forEach(i => i.classList.remove('active'));
            pages.forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            const targetPageId = this.getAttribute('data-page');
            const targetPage = document.getElementById(targetPageId);
            
            if (targetPage) {
                targetPage.classList.add('active');
                if (pageTitle) pageTitle.textContent = this.querySelector('span').textContent;
            }

            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
            }
        });
    });

    // Profile Photo Upload AJAX
    const photoInput = document.getElementById('asyncPhotoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const fileField = this.files[0];
            if (!fileField) return;

            const statusDiv = document.getElementById('uploadStatus');
            const avatarContainer = document.getElementById('avatarDisplay');
            const topbarContainer = document.getElementById('topbarAvatarDisplay');
            
            const payload = new FormData();
            payload.append('profile_photo', fileField);

            statusDiv.style.display = 'block';
            statusDiv.style.color = '#1565C0';
            statusDiv.textContent = "Uploading image...";

            fetch('update_profile_photo.php', {
                method: 'POST',
                body: payload
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    statusDiv.style.color = '#2e7d32';
                    statusDiv.textContent = "Updated successfully!";
                    
                    const timestamp = new Date().getTime();
                    const imgHtml = `<img src="./uploads/profiles/${data.filename}?t=${timestamp}" alt="Avatar" class="settings-avatar">`;
                    const topbarHtml = `<img src="./uploads/profiles/${data.filename}?t=${timestamp}" alt="Profile" class="avatar-img-sm">`;
                    
                    avatarContainer.innerHTML = imgHtml;
                    if (topbarContainer) topbarContainer.innerHTML = topbarHtml;
                } else {
                    statusDiv.style.color = '#d32f2f';
                    statusDiv.textContent = data.message || "Upload failed.";
                }
            })
            .catch(() => {
                statusDiv.style.color = '#d32f2f';
                statusDiv.textContent = "Network linkage failure.";
            });
        });
    }
</script>

</body>
</html>