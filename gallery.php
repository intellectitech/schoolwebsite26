<?php
// Start session to access logged-in admin details
session_start();

// Database Connection Settings
$host = 'localhost';
$db   = 'school_website_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// Fallback user ID to Super Admin 'macarthy junior' if no session exists[cite: 1]
$current_admin_id = $_SESSION['admin_id'] ?? 18;

$message = "";
$message_type = "";

// Handle Form Submission safely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely check toggle value to avoid undefined key warnings
    $album_selection_type = $_POST['album_selection_type'] ?? 'existing';
    $album_id = null;

    try {
        // Start a transaction to keep tables synchronized
        $pdo->beginTransaction();

        // 1. Handle NEW Album Creation
        if ($album_selection_type === 'new') {
            // Safely fetch inputs using null coalescing fallback strings to prevent PHP 8.2 trim() deprecation notices
            $raw_name = $_POST['new_album_name'] ?? '';
            $raw_desc = $_POST['new_album_desc'] ?? '';

            $new_album_name = trim(htmlspecialchars($raw_name, ENT_QUOTES, 'UTF-8'));
            $new_album_desc = trim(htmlspecialchars($raw_desc, ENT_QUOTES, 'UTF-8'));
            
            if (empty($new_album_name)) {
                throw new Exception("Please provide a name for the new album.");
            }

            // Insert into gallery_albums[cite: 1]
            $album_sql = "INSERT INTO gallery_albums (name, description, sort_order, is_published) 
                          VALUES (:name, :description, 0, 1)";
            $album_stmt = $pdo->prepare($album_sql);
            $album_stmt->execute([
                ':name'        => $new_album_name,
                ':description' => !empty($new_album_desc) ? $new_album_desc : null
            ]);
            
            // Get the newly generated ID[cite: 1]
            $album_id = $pdo->lastInsertId();
        } else {
            // Use existing album
            $album_id = filter_input(INPUT_POST, 'album_id', FILTER_VALIDATE_INT);
            if (!$album_id) {
                throw new Exception("Please select an existing album.");
            }
        }

        // 2. Handle Photo Upload Processing
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Please select a valid image file to upload.");
        }

        // Safely fetch caption and sort order to prevent null errors
        $raw_caption = $_POST['caption'] ?? '';
        $caption = trim(htmlspecialchars($raw_caption, ENT_QUOTES, 'UTF-8'));
        
        $sort_order_raw = $_POST['sort_order'] ?? '0';
        $sort_order = is_numeric($sort_order_raw) ? (int)$sort_order_raw : 0;
        
        $file_tmp_path = $_FILES['photo']['tmp_name'];
        $file_name = $_FILES['photo']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_ext, $allowed_extensions)) {
            throw new Exception("Invalid file type. Allowed formats: JPG, JPEG, PNG, GIF, WEBP.");
        }

        // Generate a unique file name
        $new_filename = 'gallery_' . uniqid('', true) . '.' . $file_ext;

        // FIXED PATHS:
        // We write the file physically inside the website's root relative to dashboard.php
        $upload_dir = __DIR__ . '/uploads/gallery/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $dest_path = $upload_dir . $new_filename; // Absolute path for the server to save the file

        // Move physical file from tmp directory to system directory
        if (!move_uploaded_file($file_tmp_path, $dest_path)) {
            throw new Exception("There was an error saving the uploaded image file.");
        }

        // Web Path: This is what the browser needs to fetch the file in gallery.php[cite: 1]
        $web_save_path = 'uploads/gallery/' . $new_filename;

        // Insert metadata into gallery_photos using the clean web path[cite: 1]
        $photo_sql = "INSERT INTO gallery_photos (album_id, filename, caption, sort_order, uploaded_by) 
                      VALUES (:album_id, :filename, :caption, :sort_order, :uploaded_by)";
        
        $photo_stmt = $pdo->prepare($photo_sql);
        $photo_stmt->execute([
            ':album_id'    => $album_id,
            ':filename'    => $web_save_path, // Saves 'uploads/gallery/gallery_xxx.jpg' to DB[cite: 1]
            ':caption'     => !empty($caption) ? $caption : null,
            ':sort_order'  => $sort_order,
            ':uploaded_by' => $current_admin_id
        ]);

        // If we reach here, apply changes permanently
        $pdo->commit();
        $message = "Success! Album created and photo published successfully!";
        $message_type = "success";

    } catch (Exception $e) {
        // If anything breaks, roll back DB queries to keep data clean
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $message = $e->getMessage();
        $message_type = "error";
    }
}

// (Make sure your database connection $pdo is already established above this)

// 1. Fetch all albums (to load your dropdown or filter menus)
try {
    $album_stmt = $pdo->query("SELECT id, name FROM gallery_albums ORDER BY name ASC");
    $albums = $album_stmt->fetchAll();
} catch (Exception $e) {
    $albums = [];
}

// 2. NEW QUERY: Fetch all the actual photos to display in your gallery cards!
try {
    // This SQL query gets the photo path, caption, and the corresponding album name
    $photo_query = "SELECT gp.id, gp.filename, gp.caption, gp.sort_order, ga.name AS album_name 
                    FROM gallery_photos gp
                    LEFT JOIN gallery_albums ga ON gp.album_id = ga.id 
                    ORDER BY gp.sort_order ASC, gp.id DESC";
                    
    $photo_stmt = $pdo->query($photo_query);
    $photos = $photo_stmt->fetchAll();
} catch (Exception $e) {
    $photos = [];
    $error_msg = "Could not load gallery images: " . $e->getMessage();
}








?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>

    <link rel="stylesheet" href="assets/css/gallery.css">

    
</head>
<body>
<div class="gallery-section">
    <h2>School Photo Gallery Albums</h2>
    <p class="section-subtitle">Browse through our latest events and school memories</p>

    <?php if (!empty($error_msg)): ?>
        <div class="alert-error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <?php if (empty($photos)): ?>
        <p class="no-photos">No photos have been uploaded to the gallery yet.</p>
    <?php else: ?>
        <!-- The Responsive Grid Container -->
        <div class="gallery-grid">
            <?php foreach ($photos as $photo): 
                // --- BULLETPROOF PATH CLEANER ---
                // Convert Windows backslashes to forward slashes
                $clean_path = str_replace('\\', '/', $photo['filename']);
                
                // If it's an old absolute path, strip out everything before "uploads/"
                if (strpos($clean_path, 'uploads/gallery/') !== false) {
                    $parts = explode('uploads/gallery/', $clean_path);
                    $clean_path = 'uploads/gallery/' . end($parts);
                }
                // --------------------------------
            ?>
                <!-- Individual Photo Card -->
                <div class="gallery-card">
                    <div class="card-image-wrapper">
                        <!-- Displays the cleaned-up path relative to your root website folder -->
                        <img src="./<?php echo htmlspecialchars($clean_path); ?>" alt="Gallery Image" loading="lazy">
                        
                        <!-- Album Tag -->
                        <?php if (!empty($photo['album_name'])): ?>
                            <span class="album-tag"><?php echo htmlspecialchars($photo['album_name']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-content">
                        <!-- Display caption or default text if empty -->
                        <p class="photo-caption">
                            <?php echo !empty($photo['caption']) ? htmlspecialchars($photo['caption']) : "<em>No caption provided</em>"; ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>










    
</body>
</html>