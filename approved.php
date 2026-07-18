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
    die("Connection failed: " . $e->getMessage());
}

// 2. Fetch ONLY APPROVED (enrolled) applications along with their documents
try {
    $sql = "SELECT 
                e.id AS enquiry_id,
                e.parent_name,
                e.parent_phone,
                e.parent_email,
                e.student_name,
                e.entry_level,
                e.current_school,
                e.ple_aggregate,
                e.message,
                e.status,
                e.created_at,
                d.title AS doc_title,
                d.filename AS doc_path,
                d.file_size AS doc_size
            FROM admissions_enquiries e
            LEFT JOIN admissions_documents d ON e.id = d.enquiry_id
            WHERE e.status = 'enrolled'
            ORDER BY e.created_at DESC";
            
    $stmt = $pdo->query($sql);
    $approvedApplications = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error fetching approved applications: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Applications</title>
    <style>
        :root {
            --bg-color: #f8fafc;
            --text-dark: #0f172a;
            --text-primary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            
            --primary-blue: #2563eb;
            --status-enrolled: #10b981;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
            color: #065f46; /* Deep green for approved page theme */
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

        .btn-active-nav {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .counter-badge {
            background-color: var(--status-enrolled);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .applications-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .applications-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .app-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .app-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .app-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .student-title h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .student-title span {
            font-size: 0.8rem;
            font-weight: 700;
            background-color: #f1f5f9;
            color: var(--text-primary);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 12px;
            letter-spacing: 0.05em;
            background-color: #d1fae5; 
            color: #065f46;
        }

        .app-card-body {
            padding: 24px;
            flex-grow: 1;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.95rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .notes-section {
            border-top: 1px dashed var(--border-color);
            padding-top: 16px;
            margin-bottom: 16px;
        }

        .notes-text {
            font-size: 0.9rem;
            color: var(--text-primary);
            font-style: italic;
        }

        .document-box {
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .doc-details {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .doc-icon {
            color: var(--primary-blue);
        }

        .doc-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .doc-size {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .btn-view-doc {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary-blue);
            text-decoration: none;
            border: 1px solid var(--primary-blue);
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-view-doc:hover {
            background-color: var(--primary-blue);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 12px;
            border: 2px dashed var(--border-color);
        }
        .empty-state h3 { margin-bottom: 8px; color: var(--text-dark); }
        .empty-state p { color: var(--text-muted); }
        .btn-back {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <div>
            <h1>Approved Roster</h1>
        </div>
        <div class="nav-shortcuts">
            <a href="check.php" class="btn-nav">Main Dashboard</a>
            <a href="approve.php" class="btn-nav btn-active-nav">Approved List</a>
            <a href="declined.php" class="btn-nav">Declined List</a>
            <span class="counter-badge"><?= count($approvedApplications) ?> Enrolled</span>
        </div>
    </div>

    <?php if (empty($approvedApplications)): ?>
        <div class="empty-state">
            <h3>No Approved Applications Yet</h3>
            <p>Go to the Main Dashboard to review pending submissions and enroll students.</p>
            <a href="test.php" class="btn-back">Go to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="applications-grid">
            <?php foreach ($approvedApplications as $app): ?>
                <div class="app-card" id="app-card-<?= $app['enquiry_id'] ?>">
                    
                    <div class="app-card-header">
                        <div class="student-title">
                            <h2><?= htmlspecialchars($app['student_name']) ?></h2>
                            <span>Class: <?= htmlspecialchars($app['entry_level']) ?></span>
                        </div>
                        <span class="status-badge">
                            <?= htmlspecialchars($app['status']) ?>
                        </span>
                    </div>

                    <div class="app-card-body">
                        <div class="info-grid">
                            <div>
                                <div class="info-label">Parent / Guardian</div>
                                <div class="info-value"><?= htmlspecialchars($app['parent_name']) ?></div>
                            </div>
                            <div>
                                <div class="info-label">Phone Contact</div>
                                <div class="info-value"><?= htmlspecialchars($app['parent_phone']) ?></div>
                            </div>
                            <div>
                                <div class="info-label">Previous School</div>
                                <div class="info-value"><?= htmlspecialchars($app['current_school'] ?: 'Not Stated') ?></div>
                            </div>
                            <div>
                                <div class="info-label">PLE Aggregates</div>
                                <div class="info-value"><?= htmlspecialchars($app['ple_aggregate'] ?: 'N/A') ?></div>
                            </div>
                        </div>

                        <?php if(!empty($app['message'])): ?>
                            <div class="notes-section">
                                <div class="info-label">Parent Comments</div>
                                <div class="notes-text">"<?= htmlspecialchars($app['message']) ?>"</div>
                            </div>
                        <?php endif; ?>

                        <div class="info-label">Uploaded Document</div>
                        <?php if(!empty($app['doc_path'])): ?>
                            <div class="document-box">
                                <div class="doc-details">
                                    <div class="doc-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    </div>
                                    <div>
                                        <div class="doc-title" title="<?= htmlspecialchars($app['doc_title']) ?>"><?= htmlspecialchars($app['doc_title']) ?></div>
                                        <span class="doc-size"><?= htmlspecialchars($app['doc_size']) ?></span>
                                    </div>
                                </div>
                                <a href="<?= htmlspecialchars($app['doc_path']) ?>" target="_blank" class="btn-view-doc">View File</a>
                            </div>
                        <?php else: ?>
                            <div style="font-size:0.9rem; color:var(--text-muted); font-style:italic;">No file associated.</div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>