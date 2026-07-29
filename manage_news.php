<?php
// manage_news.php
require_once 'database.php';

// Helper function to create URL slug from title
function createSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

$error = '';
$msg   = $_GET['msg'] ?? '';

// --- 1. DIRECT POST DELETION (NO LINKS / NO MODALS) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_news_id'])) {
    $delete_id = (int)$_POST['delete_news_id'];

    if ($delete_id > 0) {
        try {
            // Fetch image path first
            $stmt = $pdo->prepare("SELECT featured_image FROM news WHERE id = ?");
            $stmt->execute([$delete_id]);
            $news_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($news_item) {
                // Delete physical image file if present
                if (!empty($news_item['featured_image']) && file_exists($news_item['featured_image'])) {
                    unlink($news_item['featured_image']);
                }

                // Delete database row
                $deleteStmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
                $deleteStmt->execute([$delete_id]);
            }
            header("Location: manage_news.php?msg=deleted");
            exit();
        } catch (\PDOException $e) {
            $error = "Error deleting article: " . $e->getMessage();
        }
    }
}

// --- 2. HANDLE SAVE / EDIT NEWS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_news'])) {
    $id           = (int)($_POST['id'] ?? 0);
    $title        = trim($_POST['title']);
    $category_id  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $excerpt      = trim($_POST['excerpt']);
    $body         = trim($_POST['body']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $is_featured  = isset($_POST['is_featured']) ? 1 : 0;
    $author_id    = !empty($_POST['author_id']) ? (int)$_POST['author_id'] : null;
    $slug         = createSlug($title);
    
    $existing_image = $_POST['existing_image'] ?? '';
    $featured_image = $existing_image;

    // Handle File Upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['featured_image']['tmp_name'];
        $fileName    = $_FILES['featured_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = 'uploads/news/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'news_' . uniqid() . '.' . $fileExtension;
            $destPath    = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                if (!empty($existing_image) && file_exists($existing_image)) {
                    unlink($existing_image);
                }
                $featured_image = $destPath;
            }
        }
    }

    try {
        if ($id > 0) {
            // UPDATE
            $sql = "UPDATE news 
                    SET category_id = ?, title = ?, slug = ?, excerpt = ?, body = ?, 
                        featured_image = ?, author_id = ?, is_published = ?, is_featured = ? 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id, $title, $slug, $excerpt, $body, $featured_image, $author_id, $is_published, $is_featured, $id]);
            $res_msg = "updated";
        } else {
            // INSERT
            $published_at = $is_published ? date('Y-m-d H:i:s') : null;
            $sql = "INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, author_id, is_published, is_featured, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id, $title, $slug, $excerpt, $body, $featured_image, $author_id, $is_published, $is_featured, $published_at]);
            $res_msg = "created";
        }

        header("Location: manage_news.php?msg=" . $res_msg);
        exit();
    } catch (\PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// --- 3. FETCH NEWS LIST ---
$news_list = [];
try {
    $list_stmt = $pdo->query("SELECT n.*, c.name AS category_name, c.color AS category_color 
                              FROM news n 
                              LEFT JOIN news_categories c ON n.category_id = c.id 
                              ORDER BY n.id DESC");
    $news_list = $list_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $error = "Failed to load news list: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News Articles</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

    <div class="admin-container">
        <header class="page-header">
            <div>
                <h1><i class='bx bx-news'></i> News Management</h1>
                <p>View or delete news records directly from your database.</p>
            </div>
        </header>

        <!-- Status Alerts -->
        <?php if ($msg === 'deleted'): ?>
            <div class="alert alert-success"><i class='bx bx-check-circle'></i> Article was successfully deleted.</div>
        <?php elseif ($msg === 'created'): ?>
            <div class="alert alert-success"><i class='bx bx-check-circle'></i> Article created.</div>
        <?php elseif ($msg === 'updated'): ?>
            <div class="alert alert-success"><i class='bx bx-check-circle'></i> Article details updated.</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class='bx bx-error-circle'></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Table View -->
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Article Details</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($news_list)): ?>
                        <?php foreach ($news_list as $item): ?>
                            <?php 
                                $rowImg = !empty($item['featured_image']) ? $item['featured_image'] : 'uploads/news/default-placeholder.png';
                            ?>
                            <tr>
                                <td><span class="id-tag">#<?php echo $item['id']; ?></span></td>
                                
                                <!-- 📸 Thumbnail Picture View -->
                                <td>
                                    <div class="table-thumb-wrapper">
                                        <img src="<?php echo htmlspecialchars($rowImg); ?>" alt="Thumbnail">
                                    </div>
                                </td>

                                <td>
                                    <div class="table-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <div class="table-excerpt"><?php echo htmlspecialchars(substr($item['excerpt'], 0, 60)) . '...'; ?></div>
                                </td>
                                <td>
                                    <span class="table-cat" style="background-color: <?php echo htmlspecialchars($item['category_color'] ?? '#1565C0'); ?>;">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'General'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['is_published']): ?>
                                        <span class="status-badge published"><i class='bx bx-check'></i> Published</span>
                                    <?php else: ?>
                                        <span class="status-badge draft"><i class='bx bx-time'></i> Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <!-- Pure Form Submit (No links attached) -->
                                        <form action="manage_news.php" method="POST" onsubmit="return confirm('Are you sure you want to delete article #<?php echo $item['id']; ?>?');">
                                            <input type="hidden" name="delete_news_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn-direct-delete">
                                                <i class='bx bx-trash'></i> Delete Record
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                No news articles found in database.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #030617; color: #f1f5f9; padding: 40px 20px; }
        .admin-container { max-width: 1100px; margin: 0 auto; }
        
        .page-header { margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 20px; }
        .page-header h1 { font-size: 28px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
        .page-header p { color: #94a3b8; font-size: 14px; margin-top: 4px; }

        .alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; margin-bottom: 24px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #86efac; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; }

        .table-card { background: #080c2b; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .data-table { width: 100%; border-collapse: collapse; text-align: left; }
        .data-table th { background: #04071d; padding: 16px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .data-table td { padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }

        .table-thumb-wrapper {
            width: 60px;
            height: 45px;
            border-radius: 8px;
            overflow: hidden;
            background: #04071d;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .table-thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .id-tag { color: #38bdf8; font-weight: 700; font-size: 13px; }
        .table-title { color: #fff; font-weight: 700; font-size: 14.5px; margin-bottom: 2px; }
        .table-excerpt { color: #94a3b8; font-size: 12.5px; }
        .table-cat { color: #fff; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 12px; text-transform: uppercase; }

        .status-badge { font-size: 11.5px; font-weight: 600; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px; }
        .status-badge.published { background: rgba(34, 197, 94, 0.15); color: #4ade80; }
        .status-badge.draft { background: rgba(234, 179, 8, 0.15); color: #facc15; }

        .table-actions { display: flex; justify-content: flex-end; }
        
        /* Direct Form Delete Button (No href/links) */
        .btn-direct-delete {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-direct-delete:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }
    </style>

</body>
</html>