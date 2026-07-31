<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_dashboard.php

// Count queries
try {
    $totalEnquiries = $pdo->query("SELECT COUNT(*) FROM admissions_enquiries")->fetchColumn();
    $pendingEnquiries = $pdo->query("SELECT COUNT(*) FROM admissions_enquiries WHERE status = 'new'")->fetchColumn();
    $unreadMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
    $totalStaff = $pdo->query("SELECT COUNT(*) FROM staff WHERE is_active = 1")->fetchColumn();
} catch (PDOException $e) {
    $totalEnquiries = $pendingEnquiries = $unreadMessages = $totalStaff = 0;
}

// Fetch latest pending enquiries
try {
    $latestEnquiries = $pdo->query("SELECT * FROM admissions_enquiries WHERE status = 'new' ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $latestEnquiries = [];
}

// Fetch latest unread messages
try {
    $latestMessages = $pdo->query("SELECT * FROM contact_messages WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $latestMessages = [];
}

// Fetch latest audit logs
try {
    $latestLogs = $pdo->query("SELECT a.*, u.name AS admin_name FROM audit_log a LEFT JOIN admin_users u ON a.admin_id = u.id ORDER BY a.created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $latestLogs = [];
}
?>

<!-- Metrics Grid -->
<div class="metrics-grid">
    <div class="metric-card card-bg-purple">
        <div class="metric-top">
            <span class="metric-badge"><i class="fas fa-user-clock"></i> Pending</span>
            <i class="fas fa-ellipsis-h" style="opacity: 0.4;"></i>
        </div>
        <div class="metric-bottom">
            <h3><?= (int)$pendingEnquiries ?></h3>
            <p>New Enquiries</p>
        </div>
    </div>

    <div class="metric-card card-bg-yellow">
        <div class="metric-top">
            <span class="metric-badge"><i class="far fa-envelope-open"></i> Unread</span>
            <i class="fas fa-ellipsis-h" style="opacity: 0.4;"></i>
        </div>
        <div class="metric-bottom">
            <h3><?= (int)$unreadMessages ?></h3>
            <p>New Messages</p>
        </div>
    </div>

    <div class="metric-card card-bg-blue">
        <div class="metric-top">
            <span class="metric-badge"><i class="fas fa-users"></i> Staff</span>
            <i class="fas fa-ellipsis-h" style="opacity: 0.4;"></i>
        </div>
        <div class="metric-bottom">
            <h3><?= (int)$totalStaff ?></h3>
            <p>Active Instructors</p>
        </div>
    </div>

    <div class="metric-card card-bg-gold">
        <div class="metric-top">
            <span class="metric-badge"><i class="fas fa-file-invoice"></i> Total</span>
            <i class="fas fa-ellipsis-h" style="opacity: 0.4;"></i>
        </div>
        <div class="metric-bottom">
            <h3><?= (int)$totalEnquiries ?></h3>
            <p>Applications Received</p>
        </div>
    </div>
</div>

<!-- Main Split Layout -->
<div class="main-workspace-grid" style="margin-top: 1.5rem;">
    
    <!-- Left Column: Pending Enquiries & Messages -->
    <div class="left-column">
        
        <!-- Enquiries Card -->
        <div class="dashboard-card" style="margin-bottom: 1.5rem;">
            <div class="card-header-flex">
                <h3>Latest Admissions Enquiries</h3>
                <a href="admin.php?page=enquiries" style="color: var(--accent-yellow); font-size: 0.8rem; text-decoration: none; font-weight: 600;">View All</a>
            </div>
            
            <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            <th style="padding: 0.8rem;">Student</th>
                            <th style="padding: 0.8rem;">Parent</th>
                            <th style="padding: 0.8rem;">Phone</th>
                            <th style="padding: 0.8rem;">Level</th>
                            <th style="padding: 0.8rem;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($latestEnquiries)): ?>
                            <?php foreach ($latestEnquiries as $enq): ?>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                    <td style="padding: 0.8rem; font-weight: 600;"><?= e($enq['student_name']) ?></td>
                                    <td style="padding: 0.8rem;"><?= e($enq['parent_name']) ?></td>
                                    <td style="padding: 0.8rem;"><?= e($enq['parent_phone']) ?></td>
                                    <td style="padding: 0.8rem;"><span style="background: rgba(170,224,250,0.1); color: #aae0fa; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;"><?= e($enq['entry_level']) ?></span></td>
                                    <td style="padding: 0.8rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($enq['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">No pending enquiries.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Messages Card -->
        <div class="dashboard-card">
            <div class="card-header-flex">
                <h3>Latest Contact Messages</h3>
                <a href="admin.php?page=messages" style="color: var(--accent-yellow); font-size: 0.8rem; text-decoration: none; font-weight: 600;">View All</a>
            </div>
            
            <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            <th style="padding: 0.8rem;">Sender</th>
                            <th style="padding: 0.8rem;">Subject</th>
                            <th style="padding: 0.8rem;">Email</th>
                            <th style="padding: 0.8rem;">Received At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($latestMessages)): ?>
                            <?php foreach ($latestMessages as $msg): ?>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                    <td style="padding: 0.8rem; font-weight: 600;"><?= e($msg['name']) ?></td>
                                    <td style="padding: 0.8rem; color: #aae0fa;"><?= e($msg['subject']) ?></td>
                                    <td style="padding: 0.8rem;"><?= e($msg['email']) ?></td>
                                    <td style="padding: 0.8rem; color: var(--text-muted);"><?= date('M d, g:i a', strtotime($msg['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="padding: 1.5rem; text-align: center; color: var(--text-muted);">No unread messages.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Right Column: Audit Logs & Quick Stats -->
    <div class="right-column">
        
        <div class="dashboard-card">
            <div class="card-header-flex">
                <h3>Recent System Activity</h3>
                <i class="fas fa-history" style="color: var(--text-muted);"></i>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (!empty($latestLogs)): ?>
                    <?php foreach ($latestLogs as $log): ?>
                        <div style="border-left: 3px solid var(--accent-yellow); padding-left: 0.8rem; padding-top: 0.2rem; padding-bottom: 0.2rem;">
                            <h4 style="font-size: 0.85rem; font-weight: 600; color: #fff;"><?= e($log['action']) ?> on <?= e($log['table_name']) ?></h4>
                            <p style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem;"><?= e($log['description']) ?></p>
                            <span style="font-size: 0.65rem; color: var(--accent-yellow); display: block; margin-top: 0.25rem;">
                                By <?= e($log['admin_name'] ?: 'System') ?> &bull; <?= date('M d, g:i a', strtotime($log['created_at'])) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 1rem 0;">No activities logged yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-card" style="text-align: center; padding: 2rem;">
            <i class="fas fa-university" style="font-size: 2.8rem; color: var(--accent-yellow); margin-bottom: 1rem;"></i>
            <h3>BorgiaHub Portal</h3>
            <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; margin-top: 0.5rem; margin-bottom: 1.2rem;">
                This workspace connects the St. Francis Borgia school website directly to the database. Use the menu panel to update announcements, staff records, subjects, enquiries, settings, and other core features.
            </p>
            <a href="index.php" target="_blank" class="btn" style="background: var(--accent-red); color: #fff; border: none; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fas fa-globe"></i> View Website
            </a>
        </div>

    </div>

</div>
