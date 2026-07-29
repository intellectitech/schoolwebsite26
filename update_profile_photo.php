<?php
// update_profile_photo.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized session access.']);
    exit;
}

$host = '127.0.0.1'; $db = 'school_website_db'; $user = 'root'; $pass = ''; $charset = 'utf8mb4';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database failure.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $file = $_FILES['profile_photo'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload system error encountered.']);
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image format type. Allowed: JPG, PNG, WEBP.']);
        exit;
    }

    // Generate secure clean unique identifier name
    $new_filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target_dir = './uploads/profiles/';
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target_dir . $new_filename)) {
        // Fetch old image file name to delete it and clear system disk space storage
        $oldStmt = $pdo->prepare("SELECT profile_photo FROM admin_users WHERE id = ?");
        $oldStmt->execute([$_SESSION['admin_id']]);
        $old_photo = $oldStmt->fetchColumn();

        if (!empty($old_photo) && file_exists($target_dir . $old_photo)) {
            unlink($target_dir . $old_photo); // Deletes old file safely
        }

        // Commit new image to database map array
        $update = $pdo->prepare("UPDATE admin_users SET profile_photo = ?, updated_at = NOW() WHERE id = ?");
        $update->execute([$new_filename, $_SESSION['admin_id']]);

        // Synchronize active current user session matrix
        $_SESSION['admin_photo'] = $new_filename;

        echo json_encode(['success' => true, 'filename' => $new_filename]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request operations execution.']);