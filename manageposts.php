<?php
// 1. Database Connection Configuration
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
    die("Database connection failed: " . $e->getMessage());
}

$message = '';
$error   = '';

// 2. Handle Individual & Bulk Delete Actions Safely
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    
    // Check if bulk deletion or single item request
    $photo_ids = [];
    if (isset($_POST['photo_ids']) && is_array($_POST['photo_ids'])) {
        $photo_ids = array_filter(array_map('intval', $_POST['photo_ids']));
    } elseif (isset($_POST['photo_id'])) {
        $single_id = filter_input(INPUT_POST, 'photo_id', FILTER_VALIDATE_INT);
        if ($single_id) {
            $photo_ids[] = $single_id;
        }
    }

    if (!empty($photo_ids)) {
        try {
            // Prepare placeholders for IN clause
            $placeholders = implode(',', array_fill(0, count($photo_ids), '?'));

            // Fetch filenames to remove physical files from server disk
            $stmt = $pdo->prepare("SELECT filename FROM gallery_photos WHERE id IN ($placeholders)");
            $stmt->execute($photo_ids);
            $photos = $stmt->fetchAll();

            foreach ($photos as $photo) {
                if (!empty($photo['filename']) && file_exists($photo['filename'])) {
                    unlink($photo['filename']);
                }
            }

            // Remove records from database
            $deleteStmt = $pdo->prepare("DELETE FROM gallery_photos WHERE id IN ($placeholders)");
            $deleteStmt->execute($photo_ids);

            $count = count($photo_ids);
            $message = $count === 1 ? "Photo permanently removed." : "{$count} photos permanently removed from gallery.";
        } catch (\PDOException $e) {
            $error = "Error executing deletion: " . $e->getMessage();
        }
    } else {
        $error = "No valid photos were selected for deletion.";
    }
}

// 3. Fetch Photos from Published Albums
try {
    $query = "SELECT p.id, p.filename, p.caption, a.name AS album_name 
              FROM gallery_photos p
              JOIN gallery_albums a ON p.album_id = a.id
              WHERE a.is_published = 1
              ORDER BY p.id DESC";
              
    $photos = $pdo->query($query)->fetchAll();
} catch (\PDOException $e) {
    die("Error loading photos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Manager</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

<div class="admin-container">
    <form id="gallery-form" action="" method="POST">
        <input type="hidden" name="action" value="delete">

        <!-- Top Header & Action Controls -->
        <header class="page-header">
            <div>
                <h1><i class='bx bx-images'></i> Gallery Asset Manager</h1>
                <p>Manage, inspect, and remove studio assets from public albums.</p>
            </div>

            <div class="header-actions">
                <span class="counter-badge">
                    <i class='bx bx-photo-album'></i> <?= count($photos) ?> Active Items
                </span>
                <?php if (!empty($photos)): ?>
                    <button type="submit" id="btn-bulk-delete" class="btn-bulk-delete" onclick="return confirm('Are you sure you want to delete all selected items?');" disabled>
                        <i class='bx bx-trash'></i> Delete Selected (<span id="select-count">0</span>)
                    </button>
                <?php endif; ?>
            </div>
        </header>

        <!-- Dynamic Notification Alerts -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class='bx bx-check-circle'></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class='bx bx-error-circle'></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Selection Controls Bar -->
        <?php if (!empty($photos)): ?>
            <div class="selection-bar">
                <label class="checkbox-label">
                    <input type="checkbox" id="select-all-toggle">
                    <span>Select All Items</span>
                </label>
            </div>
        <?php endif; ?>

        <!-- Photos Grid View -->
        <?php if (empty($photos)): ?>
            <div class="empty-state">
                <i class='bx bx-landscape'></i>
                <h3>No gallery assets found</h3>
                <p>Published albums currently have no photos to display.</p>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($photos as $photo): ?>
                    <?php 
                        $imgSource = !empty($photo['filename']) ? $photo['filename'] : 'uploads/default-placeholder.png'; 
                    ?>
                    <div class="photo-card" id="card-<?= $photo['id'] ?>">
                        <div class="img-container">
                            <label class="card-checkbox">
                                <input type="checkbox" name="photo_ids[]" value="<?= $photo['id'] ?>" class="item-checkbox" onchange="updateSelectionState()">
                                <span class="custom-check"></span>
                            </label>
                            
                            <img src="<?= htmlspecialchars($imgSource) ?>" alt="<?= htmlspecialchars($photo['caption'] ?? 'Asset') ?>">
                            
                            <span class="album-tag">
                                <?= htmlspecialchars($photo['album_name']) ?>
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <p class="caption">
                                <?= htmlspecialchars($photo['caption'] ?: 'Untitled Gallery Asset') ?>
                            </p>
                            
                            <div class="card-footer">
                                <span class="asset-id">ID: #<?= $photo['id'] ?></span>

                                <!-- Direct POST Button Action -->
                                <button type="submit" 
                                        formaction="" 
                                        name="photo_id" 
                                        value="<?= $photo['id'] ?>" 
                                        class="btn-delete-single"
                                        onclick="return confirm('Are you sure you want to permanently delete asset #<?= $photo['id'] ?>?');">
                                    <i class='bx bx-trash'></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
    const selectAllToggle = document.getElementById('select-all-toggle');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkDeleteBtn = document.getElementById('btn-bulk-delete');
    const selectCountSpan = document.getElementById('select-count');

    if (selectAllToggle) {
        selectAllToggle.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = this.checked;
                toggleCardHighlight(cb);
            });
            updateSelectionState();
        });
    }

    function updateSelectionState() {
        let checkedCount = 0;
        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                checkedCount++;
            }
            toggleCardHighlight(cb);
        });

        if (selectCountSpan) selectCountSpan.textContent = checkedCount;

        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = checkedCount === 0;
        }

        if (selectAllToggle) {
            selectAllToggle.checked = checkedCount > 0 && checkedCount === itemCheckboxes.length;
        }
    }

    function toggleCardHighlight(cb) {
        const card = document.getElementById('card-' + cb.value);
        if (card) {
            if (cb.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        }
    }
</script>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
    body { background-color: #030617; color: #f1f5f9; padding: 40px 20px; }
    
    .admin-container { max-width: 1200px; margin: 0 auto; }
    
    .page-header { 
        margin-bottom: 25px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        border-bottom: 1px solid rgba(255,255,255,0.08); 
        padding-bottom: 20px; 
        flex-wrap: wrap;
        gap: 15px;
    }
    .page-header h1 { font-size: 26px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
    .page-header p { color: #94a3b8; font-size: 14px; margin-top: 4px; }

    .header-actions { display: flex; align-items: center; gap: 12px; }

    .counter-badge {
        background: rgba(56, 189, 248, 0.12);
        color: #38bdf8;
        border: 1px solid rgba(56, 189, 248, 0.3);
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-bulk-delete {
        background: #dc2626;
        color: #fff;
        border: none;
        padding: 9px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-bulk-delete:disabled {
        background: rgba(255, 255, 255, 0.05);
        color: #64748b;
        cursor: not-allowed;
    }

    /* Status Alerts */
    .alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #86efac; }
    .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; }

    /* Controls Bar */
    .selection-bar {
        background: #080c2b;
        border: 1px solid rgba(255,255,255,0.08);
        padding: 12px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
    }
    .checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #cbd5e1;
        cursor: pointer;
    }

    /* Grid Layout */
    .gallery-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); 
        gap: 20px; 
    }

    /* Photo Card Styling */
    .photo-card {
        background: #080c2b;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.2s ease;
    }
    .photo-card:hover {
        transform: translateY(-4px);
        border-color: rgba(56, 189, 248, 0.4);
    }
    .photo-card.selected {
        border-color: #ef4444;
        box-shadow: 0 0 0 1px #ef4444;
    }

    .img-container {
        position: relative;
        width: 100%;
        height: 170px;
        background: #04071d;
    }
    .img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .card-checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
        cursor: pointer;
    }
    .item-checkbox { display: none; }
    .custom-check {
        width: 20px;
        height: 20px;
        background: rgba(3, 6, 23, 0.8);
        border: 1.5px solid rgba(255,255,255,0.4);
        border-radius: 6px;
        display: block;
        transition: all 0.2s;
    }
    .item-checkbox:checked + .custom-check {
        background: #ef4444;
        border-color: #ef4444;
    }
    .item-checkbox:checked + .custom-check::after {
        content: '\2713';
        color: #fff;
        font-size: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .album-tag {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(3, 6, 23, 0.85);
        color: #38bdf8;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .caption {
        font-size: 13px;
        color: #e2e8f0;
        line-height: 1.4;
        margin-bottom: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 36px;
    }

    .card-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .asset-id { font-size: 11px; color: #64748b; font-weight: 700; }

    .btn-delete-single {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
    }
    .btn-delete-single:hover {
        background: #dc2626;
        color: #ffffff;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: #080c2b;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        color: #64748b;
    }
    .empty-state i { font-size: 48px; margin-bottom: 10px; color: #38bdf8; }
    .empty-state h3 { font-size: 18px; color: #fff; margin-bottom: 4px; }
    .empty-state p { font-size: 13px; }
</style>

</body>
</html>