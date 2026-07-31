<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_faqs.php

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle save (create/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_faq'])) {
    $fId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $sortOrder = (int)$_POST['sort_order'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    if ($question && $answer) {
        try {
            if ($fId > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE faqs SET question = ?, answer = ?, category = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$question, $answer, $category, $sortOrder, $isActive, $fId]);
                log_audit_action('update', 'faqs', $fId, "Updated FAQ: '$question'");
                header("Location: admin.php?page=faqs&msg=updated");
                exit;
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, category, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$question, $answer, $category, $sortOrder, $isActive]);
                log_audit_action('create', 'faqs', $pdo->lastInsertId(), "Created FAQ: '$question'");
                header("Location: admin.php?page=faqs&msg=created");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = "Database error: " . $e->getMessage();
        }
    } else {
        $errorMsg = "Question and answer fields are required.";
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    $delId = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
        $stmt->execute([$delId]);
        log_audit_action('delete', 'faqs', $delId, "Deleted FAQ ID $delId");
        header("Location: admin.php?page=faqs&msg=deleted");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error deleting FAQ: " . $e->getMessage();
    }
}

// Fetch all FAQs
try {
    $faqs = $pdo->query("SELECT * FROM faqs ORDER BY category ASC, sort_order ASC")->fetchAll();
} catch (PDOException $e) {
    $faqs = [];
}

// Fetch edit item
$editFaq = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($faqs as $f) {
        if ((int)$f['id'] === $editId) { $editFaq = $f; break; }
    }
}
?>

<div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start; text-align: left;">
    
    <!-- Left Panel: List -->
    <div style="flex: 2; min-width: 320px;" class="dashboard-card">
        <div class="card-header-flex">
            <h3>Frequently Asked Questions</h3>
            <?php if ($msg === 'created'): ?>
                <span style="color: var(--accent-green); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-check-circle"></i> FAQ created!</span>
            <?php elseif ($msg === 'updated'): ?>
                <span style="color: var(--accent-green); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-check-circle"></i> FAQ updated!</span>
            <?php elseif ($msg === 'deleted'): ?>
                <span style="color: var(--accent-red); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-trash-alt"></i> FAQ deleted!</span>
            <?php endif; ?>
        </div>

        <div class="table-responsive" style="overflow-x: auto; width: 100%; margin-top: 1rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                        <th style="padding: 0.8rem;">Category</th>
                        <th style="padding: 0.8rem;">Question</th>
                        <th style="padding: 0.8rem;">Answer Snippet</th>
                        <th style="padding: 0.8rem; text-align: center;">Order</th>
                        <th style="padding: 0.8rem;">Status</th>
                        <th style="padding: 0.8rem; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($faqs)): ?>
                        <?php foreach ($faqs as $f): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td style="padding: 0.8rem;"><span style="background: rgba(170,224,250,0.1); color: #aae0fa; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;"><?= e($f['category']) ?></span></td>
                                <td style="padding: 0.8rem; font-weight: 600;"><?= e($f['question']) ?></td>
                                <td style="padding: 0.8rem; color: var(--text-muted); font-size: 0.8rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= e($f['answer']) ?></td>
                                <td style="padding: 0.8rem; text-align: center; font-weight: 600;"><?= (int)$f['sort_order'] ?></td>
                                <td style="padding: 0.8rem;">
                                    <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 700; <?= $f['is_active'] ? 'background: rgba(0,184,148,0.15); color: var(--accent-green);' : 'background: rgba(255,255,255,0.1); color: var(--text-muted);' ?>">
                                        <?= $f['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td style="padding: 0.8rem; text-align: center;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="admin.php?page=faqs&action=edit&id=<?= $f['id'] ?>" class="btn" style="background: rgba(0,132,255,0.1); color: var(--accent-blue); padding: 0.25rem 0.5rem; border-radius: 4px; text-decoration: none; font-size: 0.72rem;">Edit</a>
                                        <a href="admin.php?page=faqs&action=delete&id=<?= $f['id'] ?>" onclick="return confirm('Are you sure you want to delete this FAQ?')" class="btn" style="color: var(--accent-red); font-size: 0.8rem;"><i class="far fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">No FAQs registered in the system.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div style="flex: 1; min-width: 280px; max-width: 400px;" class="dashboard-card">
        <h3><?= $editFaq ? 'Edit FAQ' : 'Add FAQ' ?></h3>
        
        <?php if (isset($errorMsg)): ?>
            <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 0.8rem; border-radius: 6px; margin: 1rem 0; font-size: 0.8rem;">
                <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php?page=faqs" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
            <?php if ($editFaq): ?>
                <input type="hidden" name="id" value="<?= $editFaq['id'] ?>">
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">FAQ Question *</label>
                <input type="text" name="question" class="form-control" required placeholder="e.g. What is the school uniform fee?" style="padding-left: 0.8rem; height: 38px;" value="<?= $editFaq ? e($editFaq['question']) : '' ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="e.g. Admissions, Academics" style="padding-left: 0.8rem; height: 38px;" value="<?= $editFaq ? e($editFaq['category']) : 'General' ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" style="padding-left: 0.8rem; height: 38px;" value="<?= $editFaq ? (int)$editFaq['sort_order'] : '0' ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">FAQ Answer *</label>
                <textarea name="answer" rows="6" class="form-control" required placeholder="Type the detailed FAQ answer here..." style="padding-left: 0.8rem; font-size: 0.85rem; line-height: 1.5;"><?= $editFaq ? e($editFaq['answer']) : '' ?></textarea>
            </div>

            <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem; cursor: pointer; margin: 0.3rem 0;">
                <input type="checkbox" name="is_active" value="1" <?= (!$editFaq || $editFaq['is_active']) ? 'checked' : '' ?> style="width: 15px; height: 15px;"> Active (Visible on Admissions/Support)
            </label>

            <button type="submit" name="save_faq" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.6rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; text-align: center;"><i class="fas fa-save"></i> Save FAQ</button>
            <?php if ($editFaq): ?>
                <a href="admin.php?page=faqs" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-color); text-align: center; text-decoration: none; padding: 0.6rem; border-radius: 8px; font-size: 0.85rem;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

</div>
