<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_testimonials.php

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle save (create/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_testimonial'])) {
    $tId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $authorName = trim($_POST['author_name'] ?? '');
    $authorRole = trim($_POST['author_role'] ?? '');
    $photo = trim($_POST['photo'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $rating = (int)$_POST['rating'];
    $sortOrder = (int)$_POST['sort_order'];
    $isPublished = isset($_POST['is_published']) ? 1 : 0;

    if ($authorName && $content) {
        try {
            if ($tId > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE testimonials SET author_name = ?, author_role = ?, photo = ?, content = ?, rating = ?, sort_order = ?, is_published = ? WHERE id = ?");
                $stmt->execute([$authorName, $authorRole, $photo, $content, $rating, $sortOrder, $isPublished, $tId]);
                log_audit_action('update', 'testimonials', $tId, "Updated testimonial from '$authorName'");
                header("Location: admin.php?page=testimonials&msg=updated");
                exit;
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO testimonials (author_name, author_role, photo, content, rating, sort_order, is_published) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$authorName, $authorRole, $photo, $content, $rating, $sortOrder, $isPublished]);
                log_audit_action('create', 'testimonials', $pdo->lastInsertId(), "Created testimonial from '$authorName'");
                header("Location: admin.php?page=testimonials&msg=created");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = "Database error: " . $e->getMessage();
        }
    } else {
        $errorMsg = "Author name and testimonial content are required.";
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    $delId = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$delId]);
        log_audit_action('delete', 'testimonials', $delId, "Deleted testimonial ID $delId");
        header("Location: admin.php?page=testimonials&msg=deleted");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error deleting testimonial: " . $e->getMessage();
    }
}

// Fetch all testimonials
try {
    $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC")->fetchAll();
} catch (PDOException $e) {
    $testimonials = [];
}

// Fetch edit item
$editTestimonial = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($testimonials as $t) {
        if ((int)$t['id'] === $editId) { $editTestimonial = $t; break; }
    }
}
?>

<div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-start; text-align: left;">

    <!-- Left Panel: List -->
    <div style="flex: 2; min-width: 320px;" class="dashboard-card">
        <div class="card-header-flex">
            <h3>Student & Parent Testimonials</h3>
            <?php if ($msg === 'created'): ?>
                <span style="color: var(--accent-green); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Testimonial created!</span>
            <?php elseif ($msg === 'updated'): ?>
                <span style="color: var(--accent-green); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Testimonial updated!</span>
            <?php elseif ($msg === 'deleted'): ?>
                <span style="color: var(--accent-red); font-size: 0.8rem; font-weight: 600;"><i class="fas fa-trash-alt"></i> Testimonial deleted!</span>
            <?php endif; ?>
        </div>

        <div class="table-responsive" style="overflow-x: auto; width: 100%; margin-top: 1rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                        <th style="padding: 0.8rem; width: 45px;">Photo</th>
                        <th style="padding: 0.8rem;">Author</th>
                        <th style="padding: 0.8rem;">Role</th>
                        <th style="padding: 0.8rem;">Testimony</th>
                        <th style="padding: 0.8rem;">Rating</th>
                        <th style="padding: 0.8rem; text-align: center;">Order</th>
                        <th style="padding: 0.8rem;">Status</th>
                        <th style="padding: 0.8rem; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($testimonials)): ?>
                        <?php foreach ($testimonials as $t): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td style="padding: 0.6rem;">
                                    <img src="<?= e($t['photo'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80') ?>" style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%; border: 1px solid var(--border-color);">
                                </td>
                                <td style="padding: 0.8rem; font-weight: 600;"><?= e($t['author_name']) ?></td>
                                <td style="padding: 0.8rem; color: #aae0fa;"><?= e($t['author_role'] ?: 'Student') ?></td>
                                <td style="padding: 0.8rem; color: var(--text-muted); font-size: 0.8rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= e($t['content']) ?></td>
                                <td style="padding: 0.8rem; color: var(--accent-yellow); font-size: 0.75rem;">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="<?= $i < $t['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </td>
                                <td style="padding: 0.8rem; text-align: center; font-weight: 600;"><?= (int)$t['sort_order'] ?></td>
                                <td style="padding: 0.8rem;">
                                    <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 700; <?= $t['is_published'] ? 'background: rgba(0,184,148,0.15); color: var(--accent-green);' : 'background: rgba(255,255,255,0.1); color: var(--text-muted);' ?>">
                                        <?= $t['is_published'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td style="padding: 0.8rem; text-align: center;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="admin.php?page=testimonials&action=edit&id=<?= $t['id'] ?>" class="btn" style="background: rgba(0,132,255,0.1); color: var(--accent-blue); padding: 0.25rem 0.5rem; border-radius: 4px; text-decoration: none; font-size: 0.72rem;">Edit</a>
                                        <a href="admin.php?page=testimonials&action=delete&id=<?= $t['id'] ?>" onclick="return confirm('Are you sure you want to delete this testimonial?')" class="btn" style="color: var(--accent-red); font-size: 0.8rem;"><i class="far fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">No testimonials registered.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div style="flex: 1; min-width: 280px; max-width: 400px;" class="dashboard-card">
        <h3><?= $editTestimonial ? 'Edit Testimonial' : 'Add Testimonial' ?></h3>

        <?php if (isset($errorMsg)): ?>
            <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 0.8rem; border-radius: 6px; margin: 1rem 0; font-size: 0.8rem;">
                <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php?page=testimonials" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
            <?php if ($editTestimonial): ?>
                <input type="hidden" name="id" value="<?= $editTestimonial['id'] ?>">
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Author Name *</label>
                <input type="text" name="author_name" class="form-control" required placeholder="e.g. Mugisha Ronald" style="padding-left: 0.8rem; height: 38px;" value="<?= $editTestimonial ? e($editTestimonial['author_name']) : '' ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Role / Designation</label>
                <input type="text" name="author_role" class="form-control" placeholder="e.g. Alumnus 2024, Parent S3" style="padding-left: 0.8rem; height: 38px;" value="<?= $editTestimonial ? e($editTestimonial['author_role']) : '' ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Photo URL (optional)</label>
                <input type="text" name="photo" class="form-control" placeholder="e.g. naps/parent1.jpg or a full https:// link" style="padding-left: 0.8rem; height: 38px;" value="<?= $editTestimonial ? e($editTestimonial['photo']) : '' ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Rating (1 to 5 Stars)</label>
                    <select name="rating" class="form-control" style="padding-left: 0.8rem; height: 38px; background-color: var(--bg-base); border: 1px solid var(--border-color); color: #fff; border-radius: 8px;">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= ($editTestimonial && (int)$editTestimonial['rating'] === $i) ? 'selected' : '' ?>><?= $i ?> Stars</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" style="padding-left: 0.8rem; height: 38px;" value="<?= $editTestimonial ? (int)$editTestimonial['sort_order'] : '0' ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.3rem; display: block;">Testimony Content *</label>
                <textarea name="content" rows="5" class="form-control" required placeholder="Type testimonial text here..." style="padding-left: 0.8rem; font-size: 0.85rem; line-height: 1.5;"><?= $editTestimonial ? e($editTestimonial['content']) : '' ?></textarea>
            </div>

            <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 0.3rem; cursor: pointer; margin: 0.3rem 0;">
                <input type="checkbox" name="is_published" value="1" <?= (!$editTestimonial || $editTestimonial['is_published']) ? 'checked' : '' ?> style="width: 15px; height: 15px;"> Active (Visible on Home)
            </label>

            <button type="submit" name="save_testimonial" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.6rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; text-align: center;"><i class="fas fa-save"></i> Save Testimonial</button>
            <?php if ($editTestimonial): ?>
                <a href="admin.php?page=testimonials" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-color); text-align: center; text-decoration: none; padding: 0.6rem; border-radius: 8px; font-size: 0.85rem;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

</div>
