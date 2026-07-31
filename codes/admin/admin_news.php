<?php
// C:\Users\Juliet\.gemini\antigravity\scratch\st_francis_borgia_mukono\admin_news.php

// Helper to generate slug
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Remove accents
    $text = preg_replace('~[^\x20-\x7E]~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

$action = $_GET['sub'] ?? 'list';
$msg = $_GET['msg'] ?? '';

// Handle Category Actions (Add / Delete)
if (isset($_POST['add_category'])) {
    $catName = trim($_POST['cat_name'] ?? '');
    $catColor = trim($_POST['cat_color'] ?? '#1565C0');
    
    if ($catName) {
        $catSlug = slugify($catName);
        try {
            $stmt = $pdo->prepare("INSERT INTO news_categories (name, slug, color) VALUES (?, ?, ?)");
            $stmt->execute([$catName, $catSlug, $catColor]);
            log_audit_action('create', 'news_categories', $pdo->lastInsertId(), "Created news category '$catName'");
            header("Location: admin.php?page=news&msg=category_added");
            exit;
        } catch (PDOException $e) {
            $errorMsg = "Category already exists or database error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['delete_cat_id'])) {
    $delCatId = (int)$_GET['delete_cat_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM news_categories WHERE id = ?");
        $stmt->execute([$delCatId]);
        log_audit_action('delete', 'news_categories', $delCatId, "Deleted news category ID $delCatId");
        header("Location: admin.php?page=news&msg=category_deleted");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error deleting category (make sure it has no associated news): " . $e->getMessage();
    }
}

// Handle News Article Actions (Insert / Update / Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_article'])) {
    $articleId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $categoryId = (int)$_POST['category_id'] ?: null;
    $excerpt = trim($_POST['excerpt'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $authorId = $_SESSION['admin_id'] ?? null;
    
    if ($title && $body) {
        $slug = slugify($title);
        $featuredImage = trim($_POST['existing_image'] ?? '');
        $imageUrlInput = trim($_POST['image_url'] ?? '');
        if ($imageUrlInput) {
            $featuredImage = $imageUrlInput;
        }
        
        // Handle image upload (a real uploaded file always wins over a typed URL/path)
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['featured_image']['tmp_name'];
            $fileName = $_FILES['featured_image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = 'uploads/news/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $featuredImage = $destPath;
                }
            }
        }
        
        try {
            if ($articleId > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE news SET title = ?, slug = ?, category_id = ?, excerpt = ?, body = ?, featured_image = ?, is_published = ?, is_featured = ?, published_at = CASE WHEN is_published = 0 AND ? = 1 THEN NOW() ELSE published_at END WHERE id = ?");
                $stmt->execute([$title, $slug, $categoryId, $excerpt, $body, $featuredImage, $isPublished, $isFeatured, $isPublished, $articleId]);
                
                log_audit_action('update', 'news', $articleId, "Updated news article '$title'");
                header("Location: admin.php?page=news&msg=updated");
                exit;
            } else {
                // Insert
                $publishedAt = $isPublished ? date('Y-m-d H:i:s') : null;
                $stmt = $pdo->prepare("INSERT INTO news (title, slug, category_id, excerpt, body, featured_image, is_published, is_featured, author_id, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $categoryId, $excerpt, $body, $featuredImage, $isPublished, $isFeatured, $authorId, $publishedAt]);
                
                log_audit_action('create', 'news', $pdo->lastInsertId(), "Created news article '$title'");
                header("Location: admin.php?page=news&msg=created");
                exit;
            }
        } catch (PDOException $e) {
            $errorMsg = "Error saving article (title may already exist as a slug): " . $e->getMessage();
        }
    } else {
        $errorMsg = "Title and body content are required.";
    }
}

if (isset($_GET['delete_article_id'])) {
    $delArtId = (int)$_GET['delete_article_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$delArtId]);
        log_audit_action('delete', 'news', $delArtId, "Deleted news article ID $delArtId");
        header("Location: admin.php?page=news&msg=deleted");
        exit;
    } catch (PDOException $e) {
        $errorMsg = "Error deleting article: " . $e->getMessage();
    }
}

// Fetch all news articles
try {
    $newsStmt = $pdo->query("SELECT n.*, c.name AS category_name, u.name AS author_name FROM news n LEFT JOIN news_categories c ON n.category_id = c.id LEFT JOIN admin_users u ON n.author_id = u.id ORDER BY n.created_at DESC");
    $articles = $newsStmt->fetchAll();
} catch (PDOException $e) {
    $articles = [];
}

// Fetch categories list
try {
    $catStmt = $pdo->query("SELECT * FROM news_categories ORDER BY name ASC");
    $categories = $catStmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Edit article fetch
$editItem = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    foreach ($articles as $art) {
        if ((int)$art['id'] === $editId) {
            $editItem = $art;
            break;
        }
    }
}
?>

<div style="display: flex; gap: 1.5rem; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; text-align: left;">
    
    <!-- Left Column: Articles Management / Forms -->
    <div style="flex: 2; min-width: 320px;">
        
        <?php if ($msg === 'created'): ?>
            <div style="background: rgba(0,184,148,0.15); color: var(--accent-green); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
                <i class="fas fa-check-circle"></i> News article created successfully!
            </div>
        <?php elseif ($msg === 'updated'): ?>
            <div style="background: rgba(0,184,148,0.15); color: var(--accent-green); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
                <i class="fas fa-check-circle"></i> News article updated!
            </div>
        <?php elseif ($msg === 'deleted'): ?>
            <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
                <i class="fas fa-trash-alt"></i> News article deleted!
            </div>
        <?php endif; ?>

        <?php if (isset($errorMsg)): ?>
            <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
                <i class="fas fa-exclamation-triangle"></i> <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'add' || ($action === 'edit' && $editItem)): ?>
            <!-- Add/Edit Article Form -->
            <div class="dashboard-card">
                <div class="card-header-flex" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3><?= $action === 'edit' ? 'Edit News Article' : 'Create News Article' ?></h3>
                    <a href="admin.php?page=news" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; border: 1px solid var(--border-color);"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>

                <form method="POST" action="admin.php?page=news&sub=list" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php if ($editItem): ?>
                        <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
                        <input type="hidden" name="existing_image" value="<?= e($editItem['featured_image']) ?>">
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">Article Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter article title" required style="padding-left: 1rem;" value="<?= $editItem ? e($editItem['title']) : '' ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">Category</label>
                            <select name="category_id" class="form-control" style="padding-left: 1rem; height: 46px; background-color: var(--bg-base); border: 1px solid var(--border-color); color: #fff; border-radius: 10px;">
                                <option value="">-- Choose Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($editItem && (int)$editItem['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">Brief Excerpt *</label>
                        <input type="text" name="excerpt" class="form-control" placeholder="Summarize the article in one sentence" required style="padding-left: 1rem;" value="<?= $editItem ? e($editItem['excerpt']) : '' ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">Featured Image</label>
                        <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 0.8rem;">
                            <?php if ($editItem && $editItem['featured_image']): ?>
                                <img src="<?= e($editItem['featured_image']) ?>" style="height: 60px; width: 80px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                            <?php endif; ?>
                            <input type="file" name="featured_image" accept="image/*" style="font-size: 0.8rem; color: var(--text-muted);">
                        </div>
                        <input type="text" name="image_url" class="form-control" placeholder="...or type an image path/URL instead, e.g. naps/news-sports-gala.svg" style="padding-left: 1rem;" value="<?= ($editItem && !empty($editItem['featured_image']) && strpos($editItem['featured_image'], 'uploads/') === false) ? e($editItem['featured_image']) : '' ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">Article Body Content *</label>
                        <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
                        <div id="bodyEditor" style="background: #fff; color: #111; border-radius: 0 0 10px 10px; min-height: 260px;"></div>
                        <textarea name="body" id="bodyHidden" style="display:none;"><?= $editItem ? e($editItem['body']) : '' ?></textarea>
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.4rem;">Use the image icon in the toolbar to insert pictures directly into the article text.</p>
                    </div>

                    <div style="display: flex; gap: 2rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="is_published" value="1" <?= (!$editItem || $editItem['is_published']) ? 'checked' : '' ?> style="width: 16px; height: 16px;"> Publish immediately
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="is_featured" value="1" <?= ($editItem && $editItem['is_featured']) ? 'checked' : '' ?> style="width: 16px; height: 16px;"> Highlight on Homepage
                        </label>
                    </div>

                    <button type="submit" name="save_article" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.8rem; border-radius: 10px; cursor: pointer; font-size: 0.95rem; text-align: center;"><i class="fas fa-save"></i> Save Article</button>
                </form>

                <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.js"></script>
                <script>
                (function() {
                    var quill = new Quill('#bodyEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: {
                                container: [
                                    [{ header: [2, 3, false] }],
                                    ['bold', 'italic', 'underline'],
                                    [{ list: 'ordered' }, { list: 'bullet' }],
                                    ['link', 'image'],
                                    ['clean']
                                ],
                                handlers: { image: imageHandler }
                            }
                        }
                    });

                    var hidden = document.getElementById('bodyHidden');
                    if (hidden.value.trim()) {
                        quill.root.innerHTML = hidden.value;
                    }

                    function imageHandler() {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.click();

                        input.onchange = function() {
                            var file = input.files[0];
                            if (!file) return;

                            var formData = new FormData();
                            formData.append('image', file);

                            fetch('admin_ajax_upload.php', { method: 'POST', body: formData })
                                .then(function(res) { return res.json(); })
                                .then(function(data) {
                                    if (data.url) {
                                        var range = quill.getSelection(true);
                                        quill.insertEmbed(range.index, 'image', data.url);
                                    } else {
                                        alert('Image upload failed: ' + (data.error || 'Unknown error'));
                                    }
                                })
                                .catch(function() {
                                    alert('Image upload failed. Please check your connection and try again.');
                                });
                        };
                    }

                    var form = hidden.closest('form');
                    form.addEventListener('submit', function(e) {
                        hidden.value = quill.root.innerHTML;
                        if (quill.getText().trim().length === 0) {
                            e.preventDefault();
                            alert('Please write some article content before saving.');
                        }
                    });
                })();
                </script>
            </div>
        <?php else: ?>
            <!-- Articles List -->
            <div class="dashboard-card">
                <div class="card-header-flex">
                    <h3>Published Highlights & Announcements</h3>
                    <a href="admin.php?page=news&sub=add" class="btn" style="background: var(--accent-yellow); color: #111; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 700;"><i class="fas fa-plus"></i> Add Article</a>
                </div>

                <div class="table-responsive" style="overflow-x: auto; width: 100%;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                                <th style="padding: 1rem; width: 80px;">Image</th>
                                <th style="padding: 1rem;">Article Title</th>
                                <th style="padding: 1rem;">Category</th>
                                <th style="padding: 1rem;">Views</th>
                                <th style="padding: 1rem;">Status</th>
                                <th style="padding: 1rem;">Date</th>
                                <th style="padding: 1rem; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($articles)): ?>
                                <?php foreach ($articles as $art): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='none'">
                                        <td style="padding: 0.8rem;">
                                            <img src="<?= e($art['featured_image'] ?: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=150&q=80') ?>" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-color);">
                                        </td>
                                        <td style="padding: 0.8rem; font-weight: 600;">
                                            <?= e($art['title']) ?>
                                            <?php if ($art['is_featured']): ?>
                                                <span style="font-size: 0.65rem; background: var(--accent-yellow); color: #111; padding: 0.15rem 0.3rem; border-radius: 3px; margin-left: 0.4rem; font-weight: 800;">FEATURED</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 0.8rem;"><span style="color: #aae0fa;"><?= e($art['category_name'] ?: 'General') ?></span></td>
                                        <td style="padding: 0.8rem; font-weight: 600;"><?= (int)$art['views'] ?></td>
                                        <td style="padding: 0.8rem;">
                                            <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; <?= $art['is_published'] ? 'background: rgba(0,184,148,0.15); color: var(--accent-green);' : 'background: rgba(255,255,255,0.1); color: var(--text-muted);' ?>">
                                                <?= $art['is_published'] ? 'Published' : 'Draft' ?>
                                            </span>
                                        </td>
                                        <td style="padding: 0.8rem; color: var(--text-muted); font-size: 0.8rem;">
                                            <?= $art['published_at'] ? date('M d, Y', strtotime($art['published_at'])) : 'Draft' ?>
                                        </td>
                                        <td style="padding: 0.8rem; text-align: center;">
                                            <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                                <a href="admin.php?page=news&sub=edit&id=<?= $art['id'] ?>" class="btn" style="background: rgba(0,132,255,0.15); color: var(--accent-blue); padding: 0.35rem 0.7rem; border-radius: 6px; font-size: 0.75rem; text-decoration: none; font-weight: 600;" title="Edit"><i class="far fa-edit"></i> Edit</a>
                                                <a href="admin.php?page=news&delete_article_id=<?= $art['id'] ?>" onclick="return confirm('Are you sure you want to delete this article?')" class="btn" style="color: var(--accent-red); padding: 0.35rem; font-size: 0.85rem;" title="Delete"><i class="far fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="padding: 2.5rem; text-align: center; color: var(--text-muted);">No news articles found. Click "Add Article" to write one.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Right Column: Categories Management -->
    <div style="flex: 1; min-width: 280px; max-width: 380px;">
        
        <?php if ($msg === 'category_added'): ?>
            <div style="background: rgba(0,184,148,0.15); color: var(--accent-green); padding: 0.8rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.82rem; font-weight: 500;">
                <i class="fas fa-check-circle"></i> Category added!
            </div>
        <?php elseif ($msg === 'category_deleted'): ?>
            <div style="background: rgba(208,17,22,0.15); color: var(--accent-red); padding: 0.8rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.82rem; font-weight: 500;">
                <i class="fas fa-trash-alt"></i> Category deleted!
            </div>
        <?php endif; ?>

        <!-- Add Category Card -->
        <div class="dashboard-card" style="margin-bottom: 1.5rem;">
            <h3 style="margin-bottom: 1.2rem; font-size: 1rem;"><i class="fas fa-folder-plus"></i> Add News Category</h3>
            
            <form method="POST" action="admin.php?page=news" style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.4rem; display: block;">Category Name *</label>
                    <input type="text" name="cat_name" class="form-control" placeholder="e.g. Sports, Academics" required style="padding-left: 0.8rem; height: 38px;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.4rem; display: block;">Color Accent Tag</label>
                    <div style="display: flex; gap: 0.8rem; align-items: center;">
                        <input type="color" name="cat_color" value="#1565C0" style="border: none; background: none; width: 40px; height: 38px; cursor: pointer; padding: 0;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Pick a color for tags</span>
                    </div>
                </div>
                <button type="submit" name="add_category" class="btn" style="background: var(--accent-yellow); color: #111; font-weight: 700; border: none; padding: 0.6rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; text-align: center;"><i class="fas fa-plus"></i> Create Category</button>
            </form>
        </div>

        <!-- Categories List Card -->
        <div class="dashboard-card">
            <h3 style="margin-bottom: 1.2rem; font-size: 1rem;"><i class="fas fa-folder"></i> News Categories</h3>
            
            <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); padding: 0.6rem 0.8rem; border-radius: 8px; border: 1px solid var(--border-color); border-left: 4px solid <?= e($cat['color']) ?>;">
                            <span style="font-weight: 600; font-size: 0.85rem;"><?= e($cat['name']) ?></span>
                            <a href="admin.php?page=news&delete_cat_id=<?= $cat['id'] ?>" onclick="return confirm('Are you sure you want to delete this category? Make sure no articles are using it!')" style="color: var(--accent-red); font-size: 0.8rem;"><i class="far fa-trash-alt"></i></a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 1rem 0;">No categories defined.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
