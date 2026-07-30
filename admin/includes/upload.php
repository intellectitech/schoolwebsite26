<?php
// admin/includes/upload.php - COMPLETE UPLOAD HELPER

/**
 * Upload an image file
 * 
 * @param array $file The $_FILES array element
 * @param string $folder The subfolder to upload to (news, staff, gallery, events)
 * @param int $maxSize Maximum file size in bytes (default: 5MB)
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function uploadImage($file, $folder = 'uploads', $maxSize = 5242880) {
    // Check if file exists
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error occurred.'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $maxSizeMB = $maxSize / 1048576;
        return ['success' => false, 'error' => "File is too large. Maximum size is {$maxSizeMB}MB."];
    }
    
    // Check file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowedExts)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: JPG, PNG, WEBP, GIF.'];
    }
    
    // Create upload directory
    $root = dirname(__DIR__);
    $uploadPath = $root . '/assets/images/' . $folder . '/';
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }
    
    // Generate unique filename
    $filename = $folder . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $fullPath = $uploadPath . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        chmod($fullPath, 0644);
        return [
            'success' => true,
            'path' => 'assets/images/' . $folder . '/' . $filename,
            'filename' => $filename,
            'full_path' => $fullPath
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to upload file. Check folder permissions.'];
}

/**
 * Delete an image file
 * 
 * @param string $path The relative path to the image
 * @return bool True on success
 */
function deleteImage($path) {
    if (empty($path)) {
        return true;
    }
    
    $root = dirname(__DIR__);
    $fullPath = $root . '/' . $path;
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    
    return true;
}

/**
 * Validate an image file before upload
 * 
 * @param array $file The $_FILES array element
 * @param int $maxSize Maximum file size in bytes
 * @return array ['valid' => bool, 'error' => string]
 */
function validateImage($file, $maxSize = 5242880) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'No file uploaded.'];
    }
    
    if ($file['size'] > $maxSize) {
        $maxSizeMB = $maxSize / 1048576;
        return ['valid' => false, 'error' => "File too large. Max {$maxSizeMB}MB."];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowedExts)) {
        return ['valid' => false, 'error' => 'Invalid file type. Allowed: JPG, PNG, WEBP, GIF.'];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Get all images from a folder
 * 
 * @param string $folder The folder name
 * @return array Array of image paths
 */
function getImagesFromFolder($folder) {
    $root = dirname(__DIR__);
    $path = $root . '/assets/images/' . $folder . '/';
    if (!is_dir($path)) {
        return [];
    }
    
    $images = [];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    
    if ($handle = opendir($path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExts)) {
                    $images[] = 'assets/images/' . $folder . '/' . $entry;
                }
            }
        }
        closedir($handle);
    }
    
    sort($images);
    return $images;
}

/**
 * Get image file size in readable format
 * 
 * @param string $path The relative path to the image
 * @return string Formatted file size
 */
function getImageFileSize($path) {
    $root = dirname(__DIR__);
    $fullPath = $root . '/' . $path;
    if (!file_exists($fullPath)) {
        return '0 B';
    }
    
    $size = filesize($fullPath);
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    
    return round($size, 1) . ' ' . $units[$i];
}

/**
 * Check if an image exists
 * 
 * @param string $path The relative path to the image
 * @return bool True if image exists
 */
function imageExists($path) {
    if (empty($path)) {
        return false;
    }
    $root = dirname(__DIR__);
    $fullPath = $root . '/' . $path;
    return file_exists($fullPath) && is_file($fullPath);
}
?>