<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_messages.php

// Handle Delete Action
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    try {
        $infoStmt = $pdo->prepare("SELECT name, subject FROM contact_messages WHERE id = ?");
        $infoStmt->execute([$deleteId]);
        $msgInfo = $infoStmt->fetch();
        
        if ($msgInfo) {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$deleteId]);
            
            log_audit_action('delete', 'contact_messages', $deleteId, "Deleted message from '{$msgInfo['name']}' with subject '{$msgInfo['subject']}'");
            
            header("Location: admin.php?page=messages&msg=deleted");
            exit;
        }
    } catch (PDOException $e) {
        $errorMsg = "Error deleting message: " . $e->getMessage();
    }
}

// Handle Mark Read/Unread Toggle Action
if (isset($_GET['toggle_read']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $newVal = (int)$_GET['toggle_read'];
    
    try {
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
        $stmt->execute([$newVal, $id]);
        
        $actionName = $newVal ? 'mark_read' : 'mark_unread';
        log_audit_action($actionName, 'contact_messages', $id, "Marked message ID $id as " . ($newVal ? "read" : "unread"));
        
        header("Location: admin.php?page=messages");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error toggling status: " . $e->getMessage();
    }
}

// Fetch all contact messages
try {
    $msgStmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $msgStmt->fetchAll();
} catch (PDOException $e) {
    $messages = [];
}

// View details logic
$detailItem = null;
if (isset($_GET['detail_id'])) {
    $detailId = (int)$_GET['detail_id'];
    foreach ($messages as $msg) {
        if ((int)$msg['id'] === $detailId) {
            $detailItem = $msg;
            break;
        }
    }
    
    if ($detailItem && !$detailItem['is_read']) {
        // Automatically mark as read when detail panel is opened
        try {
            $updateStmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            $updateStmt->execute([$detailId]);
            
            log_audit_action('read_message', 'contact_messages', $detailId, "Opened and read message from '{$detailItem['name']}'");
            
            // Update local array state so it renders correctly on this load
            $detailItem['is_read'] = 1;
            
            // Reload page in background to clear URL parameter if desired, or let user stay.
            // (We just refresh the database list state)
            $msgStmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
            $messages = $msgStmt->fetchAll();
        } catch (PDOException $e) {
            // Fail silently
        }
    }
}
?>

<div class="dashboard-card">
    <div class="card-header-flex">
        <h3>Contact Messages Inbox</h3>
        <div>
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                <span style="color: var(--accent-red); font-size: 0.8rem; font-weight: 600; margin-right: 1rem;"><i class="fas fa-trash-alt"></i> Message deleted!</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Message Details Panel -->
    <?php if ($detailItem): ?>
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; position: relative;">
            <a href="admin.php?page=messages" style="position: absolute; right: 1.5rem; top: 1.5rem; color: var(--text-muted); text-decoration: none; font-size: 1.2rem;"><i class="fas fa-times"></i></a>
            
            <h4 style="color: var(--accent-yellow); margin-bottom: 1.5rem;"><i class="far fa-envelope-open"></i> Reading Message from <?= e($detailItem['name']) ?></h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; text-align: left; margin-bottom: 1.5rem;">
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Sender Name</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= e($detailItem['name']) ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Email Address</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem; color: #aae0fa;"><?= e($detailItem['email']) ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Phone Number</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= e($detailItem['phone'] ?: 'N/A') ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Received At</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= date('F d, Y &bull; g:i a', strtotime($detailItem['created_at'])) ?></p>
                </div>
            </div>
            
            <div style="text-align: left; border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-bottom: 1.5rem;">
                <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Subject</p>
                <p style="font-weight: 700; font-size: 1.1rem; color: #fff;"><?= e($detailItem['subject']) ?></p>
            </div>
            
            <div style="text-align: left;">
                <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Message Content</p>
                <div style="background: var(--bg-base); padding: 1.5rem; border-radius: 8px; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap; border: 1px solid var(--border-color);"><?= e($detailItem['message']) ?></div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="admin.php?page=messages&toggle_read=0&id=<?= $detailItem['id'] ?>" class="btn" style="background: rgba(255,255,255,0.05); color: var(--accent-yellow); font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; border: 1px solid var(--border-color); font-weight: 600;">Mark as Unread</a>
                <a href="mailto:<?= e($detailItem['email']) ?>?subject=Re: <?= urlencode($detailItem['subject']) ?>" class="btn" style="background: var(--accent-blue); color: #fff; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem;"><i class="fas fa-reply"></i> Reply via Email</a>
                <a href="admin.php?page=messages&delete_id=<?= $detailItem['id'] ?>" onclick="return confirm('Are you sure you want to delete this message?')" class="btn" style="background: var(--accent-red); color: #fff; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">Delete Message</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Inbox Messages Table -->
    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                    <th style="padding: 1rem; width: 40px; text-align: center;">Status</th>
                    <th style="padding: 1rem;">Sender</th>
                    <th style="padding: 1rem;">Subject</th>
                    <th style="padding: 1rem;">Email Address</th>
                    <th style="padding: 1rem;">Received At</th>
                    <th style="padding: 1rem; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: 0.2s; <?= !$msg['is_read'] ? 'font-weight: 600; background: rgba(255,255,255,0.015);' : 'color: var(--text-muted);' ?>" onmouseover="this.style.background='rgba(255,255,255,0.025)'" onmouseout="this.style.background='<?= !$msg['is_read'] ? 'rgba(255,255,255,0.015)' : 'none' ?>'">
                            <td style="padding: 1rem; text-align: center;">
                                <?php if (!$msg['is_read']): ?>
                                    <i class="fas fa-circle" style="color: var(--accent-yellow); font-size: 0.65rem;" title="Unread"></i>
                                <?php else: ?>
                                    <i class="far fa-envelope-open" style="color: var(--text-muted); font-size: 0.8rem;" title="Read"></i>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; color: <?= !$msg['is_read'] ? '#fff' : 'inherit' ?>;"><?= e($msg['name']) ?></td>
                            <td style="padding: 1rem; color: <?= !$msg['is_read'] ? '#aae0fa' : 'inherit' ?>;"><?= e($msg['subject']) ?></td>
                            <td style="padding: 1rem;"><?= e($msg['email']) ?></td>
                            <td style="padding: 1rem; font-size: 0.8rem;"><?= date('M d, Y &bull; g:i a', strtotime($msg['created_at'])) ?></td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    <a href="admin.php?page=messages&detail_id=<?= $msg['id'] ?>" class="btn" style="background: rgba(0,184,148,0.15); color: var(--accent-green); padding: 0.35rem 0.7rem; border-radius: 6px; font-size: 0.75rem; text-decoration: none; font-weight: 600;"><i class="far fa-envelope-open"></i> Read</a>
                                    
                                    <?php if ($msg['is_read']): ?>
                                        <a href="admin.php?page=messages&toggle_read=0&id=<?= $msg['id'] ?>" class="btn" style="color: var(--accent-yellow); font-size: 0.75rem;" title="Mark Unread"><i class="fas fa-envelope"></i></a>
                                    <?php else: ?>
                                        <a href="admin.php?page=messages&toggle_read=1&id=<?= $msg['id'] ?>" class="btn" style="color: var(--accent-blue); font-size: 0.75rem;" title="Mark Read"><i class="fas fa-envelope-open"></i></a>
                                    <?php endif; ?>

                                    <a href="admin.php?page=messages&delete_id=<?= $msg['id'] ?>" onclick="return confirm('Are you sure you want to delete this message?')" class="btn" style="color: var(--accent-red); padding: 0.35rem; font-size: 0.85rem;" title="Delete"><i class="far fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">Inbox is empty. No messages received.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
