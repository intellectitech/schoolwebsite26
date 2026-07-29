<?php
// 1. Database Connection Configuration
$host     = '127.0.0.1';
$db       = 'school_website_db'; // From your schema
$user     = 'root';              // Replace with your database username
$password = '';                  // Replace with your database password
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

// 2. Cookie-Based "Last Opened" Tracker
// We check if the cookie exists. If not, we assume they haven't opened it yet (default to 24 hours ago).
$last_viewed_time = isset($_COOKIE['last_viewed_enquiries']) ? $_COOKIE['last_viewed_enquiries'] : date('Y-m-d H:i:s', strtotime('-1 day'));

// NOW update the cookie to "current time" because the user is opening/viewing the tab right now.
// The cookie will expire in 30 days.
setcookie('last_viewed_enquiries', date('Y-m-d H:i:s'), time() + (86400 * 30), "/");

// 3. Handle Delete Action
$messageFeedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $deleteId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if ($deleteId) {
        try {
            $stmt = $pdo->prepare("DELETE FROM admissions_enquiries WHERE id = ?"); // From your schema
            $stmt->execute([$deleteId]);
            $messageFeedback = "<div class='alert alert-success'>Enquiry #$deleteId has been deleted successfully.</div>";
        } catch (\PDOException $e) {
            $messageFeedback = "<div class='alert alert-danger'>Error: Could not delete record. " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// 4. Count Unread Enquiries (sent AFTER the last time this page/tab was loaded)
try {
    $countQuery = $pdo->prepare("SELECT COUNT(*) FROM admissions_enquiries WHERE created_at > ?"); // From your schema
    $countQuery->execute([$last_viewed_time]);
    $unreadCount = $countQuery->fetchColumn();
} catch (\PDOException $e) {
    $unreadCount = 0; 
}

// 5. Fetch all admissions enquiries ordered by latest first
try {
    $stmt = $pdo->query("SELECT id, parent_name, parent_phone, parent_email, student_name, entry_level, current_school, ple_aggregate, message, status, created_at FROM admissions_enquiries ORDER BY created_at DESC"); // From your schema[cite: 1]
    $enquiries = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions Enquiries</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <header class="page-header">
        <h1   style="text-align: center; " >Admissions Enquiries</h1>
        <p   style="text-align: center;" >Manage and view incoming student applications and messages.</p>
    </header>

    <!-- Feedback alert container -->
    <?= $messageFeedback ?>

    <div class="card-grid">
        <?php if (empty($enquiries)): ?>
            <div class="no-data">No admissions enquiries found.</div>
        <?php else: ?>
            <?php foreach ($enquiries as $row): ?>
                <!-- Individual Enquiry Card -->
                <div class="enquiry-card status-<?= htmlspecialchars(strtolower($row['status'] ?? 'new')) ?>">
                    <div class="card-header">
                        <span class="enquiry-id">#<?= htmlspecialchars($row['id']) ?></span>
                        <span class="status-badge"><?= htmlspecialchars(ucfirst($row['status'] ?? 'new')) ?></span>
                    </div>

                    <div class="card-body">
                        <!-- Parent Info -->
                        <div class="section-title">Parent / Guardian Details</div>
                        <p><strong>Name:</strong> <?= htmlspecialchars($row['parent_name']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($row['parent_phone']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['parent_email'] ?: 'N/A') ?></p>

                        <hr class="divider">

                        <!-- Student Info -->
                        <div class="section-title">Student Details</div>
                        <p><strong>Name:</strong> <?= htmlspecialchars($row['student_name']) ?></p>
                        <p><strong>Target Class:</strong> <?= htmlspecialchars($row['entry_level'] ?: 'N/A') ?></p>
                        <p><strong>Former School:</strong> <?= htmlspecialchars($row['current_school'] ?: 'N/A') ?></p>
                        <p><strong>PLE Aggregate:</strong> <span class="aggregate-badge"><?= htmlspecialchars($row['ple_aggregate'] !== null ? $row['ple_aggregate'] : 'N/A') ?></span></p>

                        <hr class="divider">

                        <!-- Message -->
                        <div class="section-title">Message / Notes</div>
                        <p class="message-text">
                            <?= nl2br(htmlspecialchars($row['message'] ?: 'No message left.')) ?>
                        </p>
                    </div>

                    <div class="card-footer">
                        <span class="timestamp">Submitted: <?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></span>
                        
                        <!-- Delete Form Trigger with JS Confirmation Dialog -->
                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this admission?');" style="display: inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn-delete" title="Delete Admission">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>

.card-footer {
    padding: 12px 20px;
    background-color: #f7fafc;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Delete Button Styling */
.btn-delete {
    background-color: #fff5f5;
    color: var(--status-declined);
    border: 1px solid #fed7d7;
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease-in-out;
}

.btn-delete:hover {
    background-color: var(--status-declined);
    color: #ffffff;
    border-color: var(--status-declined);
}

.icon-trash {
    flex-shrink: 0;
}

/* Notification Alerts styling */
.alert {
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-weight: 500;
    font-size: 0.95rem;
}

.alert-success {
    background-color: #f0fff4;
    color: var(--status-enrolled);
    border: 1px solid #c6f6d5;
}

.alert-danger {
    background-color: #fff5f5;
    color: var(--status-declined);
    border: 1px solid #fed7d7;
}

/* Base Variables & Reset */
:root {
    --bg-color: #f4f6f9;
    --card-bg: #ffffff;
    --text-primary: #2d3748;
    --text-muted: #718096;
    --border-color: #e2e8f0;
    
    /* Status Colors */
    --status-new: #3182ce;
    --status-contacted: #dd6b20;
    --status-enrolled: #38a169;
    --status-declined: #e53e3e;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--bg-color);
    color: var(--text-primary);
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    margin: 0 0 8px 0;
    font-size: 2rem;
    color: #1a202c;
}

.page-header p {
    margin: 0;
    color: var(--text-muted);
}

/* Card Grid Layout */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}

/* Card Styling */
.enquiry-card {
    background: var(--card-bg);
    border-radius: 12px;
    border-top: 5px solid var(--border-color);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.enquiry-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Dynamic Top Border matching the Enquiry Status */
.enquiry-card.status-new { border-top-color: var(--status-new); }
.enquiry-card.status-contacted { border-top-color: var(--status-contacted); }
.enquiry-card.status-enrolled { border-top-color: var(--status-enrolled); }
.enquiry-card.status-declined { border-top-color: var(--status-declined); }

/* Header Elements */
.card-header {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
}

.enquiry-id {
    font-weight: bold;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.status-badge {
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
}

/* Dynamic badge background depending on parent card's state */
.status-new .status-badge { background-color: #ebf8ff; color: var(--status-new); }
.status-contacted .status-badge { background-color: #fffaf0; color: var(--status-contacted); }
.status-enrolled .status-badge { background-color: #f0fff4; color: var(--status-enrolled); }
.status-declined .status-badge { background-color: #fff5f5; color: var(--status-declined); }

/* Card Content Body */
.card-body {
    padding: 20px;
    flex-grow: 1;
}

.section-title {
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.card-body p {
    margin: 6px 0;
    font-size: 0.95rem;
    line-height: 1.4;
}

.card-body p strong {
    color: #4a5568;
    display: inline-block;
    width: 110px;
}

.aggregate-badge {
    background: #edf2f7;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: bold;
    color: #4a5568;
}

.divider {
    border: 0;
    border-top: 1px dashed var(--border-color);
    margin: 16px 0;
}

.message-text {
    font-style: italic;
    color: #4a5568;
    background-color: #f7fafc;
    padding: 10px;
    border-radius: 6px;
    border-left: 3px solid #cbd5e0;
}

/* Footer Section */
.card-footer {
    padding: 12px 20px;
    background-color: #f7fafc;
    border-top: 1px solid var(--border-color);
    text-align: right;
}

.timestamp {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.no-data {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    background: white;
    border-radius: 8px;
    color: var(--text-muted);
    font-weight: 500;
}



















  </style>


</body>
</html>




