<?php
// 1. DATABASE CONNECTION (PDO)
$host     = '127.0.0.1';
$db       = 'school_website_db';
$user     = 'root';
$pass     = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

// 2. HANDLE ACTIONS (Mark Read / Delete)
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

// 3. FETCH MESSAGES (Most recent first)
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

// Count unread messages
$unreadStmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$unreadCount = $unreadStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages Inbox</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-dark);
            padding: 1rem;
        }

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

        .header-title {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-title i {
            font-size: 20px;
            color: var(--primary-blue);
        }

        .header-title h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .unread-badge {
            background: var(--primary-blue);
            color: #ffffff;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .inbox-body {
            padding: 16px;
        }

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

        /* Scrollable Feed */
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
            transition: all 0.15s ease;
        }

        .msg-card.unread {
            border-left-color: var(--primary-blue);
            background: #fafcfe;
        }

        .msg-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .sender-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 13px;
        }

        .sender-info i {
            color: var(--primary-blue);
        }

        .msg-date {
            font-size: 11px;
            color: var(--text-muted);
        }

        .msg-contact-details {
            display: flex;
            gap: 12px;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .msg-subject {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 4px;
        }

        .msg-content {
            font-size: 12px;
            color: #334155;
            line-height: 1.4;
            white-space: pre-line;
            background: var(--bg-body);
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        /* Action Buttons */
        .msg-actions {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-read {
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        .btn-delete {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-action:hover {
            opacity: 0.85;
        }

        .empty-inbox {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
        }

        .empty-inbox i {
            font-size: 32px;
            color: var(--border-blue);
            display: block;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

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
                            <!-- Toggle Read Form -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="msg_id" value="<?= $msg['id'] ?>">
                                <input type="hidden" name="action" value="toggle_read">
                                <button type="submit" class="btn-action btn-read">
                                    <i class='bx <?= $msg['is_read'] ? 'bx-envelope' : 'bx-envelope-open' ?>'></i>
                                    <?= $msg['is_read'] ? 'Mark Unread' : 'Mark Read' ?>
                                </button>
                            </form>

                            <!-- Delete Form -->
                            <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?');">
                                <input type="hidden" name="msg_id" value="<?= $msg['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn-action btn-delete">
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

</body>
</html>