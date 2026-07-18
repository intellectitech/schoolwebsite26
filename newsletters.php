<?php
// 1. Database Connection
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
    die(json_encode(['success' => false, 'error' => "Connection failed: " . $e->getMessage()]));
}

// 2. Handle Subscription Status Toggle (AJAX Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    header('Content-Type: application/json');
    $subscriberId = intval($_POST['id']);
    $action       = $_POST['action']; // 'subscribe' or 'unsubscribe'
    
    // Mapping action to your is_confirmed schema (1 for active, 0 for unsubscribed)
    $isConfirmed = ($action === 'subscribe') ? 1 : 0;
    $currentTimestamp = date('Y-m-d H:i:s');

    try {
        if ($isConfirmed === 1) {
            // Re-subscribing: Set is_confirmed to 1 and clear unsubscribed_at timestamp
            $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET is_confirmed = ?, unsubscribed_at = NULL WHERE id = ?");
            $stmt->execute([$isConfirmed, $subscriberId]);
        } else {
            // Unsubscribing: Set is_confirmed to 0 and log the exact unsubscribed_at timestamp
            $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET is_confirmed = ?, unsubscribed_at = ? WHERE id = ?");
            $stmt->execute([$isConfirmed, $currentTimestamp, $subscriberId]);
        }
        
        echo json_encode(['success' => true, 'is_confirmed' => $isConfirmed]);
        exit;
    } catch (\PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// 3. Fetch All Subscribers matching your exact column structure
try {
    $stmt = $pdo->query("SELECT id, email, name, is_confirmed, subscribed_at, unsubscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    $subscribers = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error fetching from newsletter_subscribers: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Management</title>
    <style>
        :root {
            --bg-color: #f8fafc;
            --text-dark: #0f172a;
            --text-primary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            
            --primary-blue: #2563eb;
            --status-confirmed: #10b981;
            --status-inactive: #ef4444;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            padding: 40px 20px;
        }

        .dashboard-wrapper {
            max-width: 1100px;
            margin: 0 auto;
        }

        .dashboard-header {
            margin-bottom: 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.025em;
        }

        .nav-shortcuts {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-nav {
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: white;
            color: var(--text-primary);
            transition: all 0.2s;
        }

        .btn-nav:hover {
            background: #f1f5f9;
            color: var(--text-dark);
        }

        .counter-badge {
            background-color: var(--primary-blue);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
            color: var(--text-primary);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .subscriber-name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 12px;
            letter-spacing: 0.05em;
            display: inline-block;
        }

        .status-active { background-color: #d1fae5; color: #065f46; }
        .status-inactive { background-color: #fee2e2; color: #991b1b; }

        .btn-table-action {
            background: none;
            border: 1px solid var(--border-color);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-unsubscribe { color: #ef4444; border-color: #fca5a5; }
        .btn-unsubscribe:hover { background-color: #fee2e2; }

        .btn-subscribe { color: #10b981; border-color: #6ee7b7; }
        .btn-subscribe:hover { background-color: #d1fae5; }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <div>
            <h1>Newsletter Mailing List</h1>
        </div>
        <div class="nav-shortcuts">
            <a href="dashboard.php" class="btn-nav">Main Dashboard</a>
            <span class="counter-badge"><?= count($subscribers) ?> Profiles</span>
        </div>
    </div>

    <?php if (empty($subscribers)): ?>
        <div class="empty-state" style="text-align: center; padding: 60px; background: white; border-radius: 12px; border: 2px dashed var(--border-color);">
            <h3>No Subscribers Present</h3>
            <p>Your database table newsletter_subscribers holds no records right now.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th>Subscribed At</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $sub): ?>
                        <tr id="subscriber-row-<?= $sub['id'] ?>">
                            <td class="subscriber-name"><?= htmlspecialchars($sub['name'] ?: 'Anonymous') ?></td>
                            <td><?= htmlspecialchars($sub['email']) ?></td>
                            <td>
                                <span id="status-tag-<?= $sub['id'] ?>" class="status-badge <?= $sub['is_confirmed'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $sub['is_confirmed'] == 1 ? 'Confirmed' : 'Unsubscribed' ?>
                                </span>
                            </td>
                            <td><?= $sub['subscribed_at'] ? date('M d, Y', strtotime($sub['subscribed_at'])) : 'N/A' ?></td>
                            <td style="text-align: right;" id="action-cell-<?= $sub['id'] ?>">
                                <?php if ($sub['is_confirmed'] == 1): ?>
                                    <button class="btn-table-action btn-unsubscribe" onclick="toggleSubscription(<?= $sub['id'] ?>, 'unsubscribe')">
                                        Unsubscribe
                                    </button>
                                <?php else: ?>
                                    <button class="btn-table-action btn-subscribe" onclick="toggleSubscription(<?= $sub['id'] ?>, 'subscribe')">
                                        Re-Subscribe
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleSubscription(subscriberId, action) {
    const formData = new FormData();
    formData.append('id', subscriberId);
    formData.append('action', action);

    fetch(window.location.pathname, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const statusTag = document.getElementById(`status-tag-${subscriberId}`);
            const actionCell = document.getElementById(`action-cell-${subscriberId}`);
            
            statusTag.className = 'status-badge';
            
            if (data.is_confirmed === 1) {
                statusTag.classList.add('status-active');
                statusTag.innerText = 'Confirmed';
                actionCell.innerHTML = `<button class="btn-table-action btn-unsubscribe" onclick="toggleSubscription(${subscriberId}, 'unsubscribe')">Unsubscribe</button>`;
            } else {
                statusTag.classList.add('status-inactive');
                statusTag.innerText = 'Unsubscribed';
                actionCell.innerHTML = `<button class="btn-table-action btn-subscribe" onclick="toggleSubscription(${subscriberId}, 'subscribe')">Re-Subscribe</button>`;
            }
        } else {
            alert('Operation failed: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An issue occurred while modifying database settings.');
    });
}
</script>

</body>
</html>