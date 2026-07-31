<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_enquiries.php

// Handle Status Change Action
if (isset($_GET['status_change']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $newStatus = $_GET['status_change'];
    $validStatuses = ['new', 'contacted', 'enrolled', 'declined'];
    
    if (in_array($newStatus, $validStatuses)) {
        try {
            // Get original info for audit log
            $infoStmt = $pdo->prepare("SELECT student_name, status FROM admissions_enquiries WHERE id = ?");
            $infoStmt->execute([$id]);
            $original = $infoStmt->fetch();
            
            if ($original) {
                $stmt = $pdo->prepare("UPDATE admissions_enquiries SET status = ? WHERE id = ?");
                $stmt->execute([$newStatus, $id]);
                
                log_audit_action('update_status', 'admissions_enquiries', $id, "Changed status of student '{$original['student_name']}' from '{$original['status']}' to '$newStatus'");
                
                header("Location: admin.php?page=enquiries&msg=status_updated");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = "Error updating status: " . $e->getMessage();
        }
    }
}

// Handle Delete Action
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    try {
        $infoStmt = $pdo->prepare("SELECT student_name FROM admissions_enquiries WHERE id = ?");
        $infoStmt->execute([$deleteId]);
        $name = $infoStmt->fetchColumn();
        
        if ($name) {
            $stmt = $pdo->prepare("DELETE FROM admissions_enquiries WHERE id = ?");
            $stmt->execute([$deleteId]);
            
            log_audit_action('delete', 'admissions_enquiries', $deleteId, "Deleted enquiry of student '$name'");
            
            header("Location: admin.php?page=enquiries&msg=deleted");
            exit;
        }
    } catch (PDOException $e) {
        $errorMsg = "Error deleting enquiry: " . $e->getMessage();
    }
}

// Fetch all enquiries
try {
    $enqStmt = $pdo->query("SELECT * FROM admissions_enquiries ORDER BY created_at DESC");
    $enquiries = $enqStmt->fetchAll();
} catch (PDOException $e) {
    $enquiries = [];
}

// View details logic
$detailItem = null;
if (isset($_GET['detail_id'])) {
    $detailId = (int)$_GET['detail_id'];
    foreach ($enquiries as $enq) {
        if ((int)$enq['id'] === $detailId) {
            $detailItem = $enq;
            break;
        }
    }
}
?>

<div class="dashboard-card">
    <div class="card-header-flex">
        <h3>Admissions Enquiries Management</h3>
        <div>
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'status_updated'): ?>
                <span style="color: var(--accent-green); font-size: 0.8rem; font-weight: 600; margin-right: 1rem;"><i class="fas fa-check-circle"></i> Status updated successfully!</span>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                <span style="color: var(--accent-red); font-size: 0.8rem; font-weight: 600; margin-right: 1rem;"><i class="fas fa-trash-alt"></i> Enquiry deleted!</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details View Panel if clicked -->
    <?php if ($detailItem): ?>
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; position: relative;">
            <a href="admin.php?page=enquiries" style="position: absolute; right: 1.5rem; top: 1.5rem; color: var(--text-muted); text-decoration: none; font-size: 1.2rem;"><i class="fas fa-times"></i></a>
            
            <h4 style="color: var(--accent-yellow); margin-bottom: 1.5rem;"><i class="fas fa-info-circle"></i> Enquiry Details for <?= e($detailItem['student_name']) ?></h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; text-align: left;">
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Student Full Name</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= e($detailItem['student_name']) ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Parent/Guardian Name</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= e($detailItem['parent_name']) ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Parent Phone Number</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem; color: #aae0fa;"><?= e($detailItem['parent_phone']) ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Parent Email Address</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= e($detailItem['parent_email'] ?: 'N/A') ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Target Entry Level</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><span style="background: rgba(170,224,250,0.15); color: #aae0fa; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem;"><?= e($detailItem['entry_level']) ?></span></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Current/Previous School</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= e($detailItem['current_school'] ?: 'N/A') ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">PLE Aggregate</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= e($detailItem['ple_aggregate'] ?: 'N/A') ?></p>
                </div>
                <div>
                    <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Submission Date</p>
                    <p style="font-weight: 600; font-size: 1rem; margin-top: 0.2rem;"><?= date('F d, Y &bull; g:i a', strtotime($detailItem['created_at'])) ?></p>
                </div>
            </div>
            
            <div style="text-align: left; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <p style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Message & Application Notes</p>
                <div style="background: var(--bg-base); padding: 1.2rem; border-radius: 8px; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap; border: 1px solid var(--border-color);"><?= e($detailItem['message']) ?></div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="admin.php?page=enquiries" class="btn" style="background: rgba(255,255,255,0.08); color: #fff; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; border: 1px solid var(--border-color);">Close Details</a>
                <a href="admin.php?page=enquiries&delete_id=<?= $detailItem['id'] ?>" onclick="return confirm('Are you sure you want to delete this enquiry?')" class="btn" style="background: var(--accent-red); color: #fff; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">Delete Enquiry</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Enquiries Table -->
    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                    <th style="padding: 1rem;">Student Name</th>
                    <th style="padding: 1rem;">Parent Name</th>
                    <th style="padding: 1rem;">Phone</th>
                    <th style="padding: 1rem;">Level</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem;">Date</th>
                    <th style="padding: 1rem; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($enquiries)): ?>
                    <?php foreach ($enquiries as $enq): ?>
                        <?php 
                        // Set status classes and names
                        $statusClass = '';
                        switch ($enq['status']) {
                            case 'new':
                                $statusClass = 'background: rgba(254,209,0,0.15); color: var(--accent-yellow);';
                                break;
                            case 'contacted':
                                $statusClass = 'background: rgba(0,132,255,0.15); color: var(--accent-blue);';
                                break;
                            case 'enrolled':
                                $statusClass = 'background: rgba(0,184,148,0.15); color: var(--accent-green);';
                                break;
                            case 'declined':
                                $statusClass = 'background: rgba(208,17,22,0.15); color: var(--accent-red);';
                                break;
                        }
                        ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='none'">
                            <td style="padding: 1rem; font-weight: 600;"><?= e($enq['student_name']) ?></td>
                            <td style="padding: 1rem;"><?= e($enq['parent_name']) ?></td>
                            <td style="padding: 1rem;"><?= e($enq['parent_phone']) ?></td>
                            <td style="padding: 1rem;"><span style="background: rgba(170,224,250,0.1); color: #aae0fa; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600; font-size: 0.78rem;"><?= e($enq['entry_level']) ?></span></td>
                            <td style="padding: 1rem;">
                                <span style="display: inline-block; padding: 0.25rem 0.6rem; border-radius: 30px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; <?= $statusClass ?>">
                                    <?= e($enq['status']) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($enq['created_at'])) ?></td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    <a href="admin.php?page=enquiries&detail_id=<?= $enq['id'] ?>" class="btn" style="background: rgba(0,132,255,0.15); color: var(--accent-blue); padding: 0.35rem 0.7rem; border-radius: 6px; font-size: 0.75rem; text-decoration: none; font-weight: 600;"><i class="far fa-eye"></i> View</a>
                                    
                                    <!-- Change Status Dropdown quick action -->
                                    <select onchange="location.href='admin.php?page=enquiries&id=<?= $enq['id'] ?>&status_change=' + this.value" style="background: var(--bg-base); border: 1px solid var(--border-color); color: #fff; font-size: 0.75rem; padding: 0.3rem 0.5rem; border-radius: 6px; cursor: pointer;">
                                        <option value="">Update Status...</option>
                                        <option value="new">New</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="enrolled">Enrolled</option>
                                        <option value="declined">Declined</option>
                                    </select>

                                    <a href="admin.php?page=enquiries&delete_id=<?= $enq['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?')" class="btn" style="color: var(--accent-red); padding: 0.35rem; font-size: 0.85rem;" title="Delete"><i class="far fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">No admissions enquiries found in the database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
