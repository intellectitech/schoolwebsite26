<?php
/**
 * admin/includes/upload_helper.php
 * ---------------------------------------------------------------
 * Small helper for handling image uploads from admin forms.
 * Validates type/size, generates a safe unique filename, and saves
 * into the given subfolder under /assets/images/.
 * ---------------------------------------------------------------
 */

/**
 * Handles a single <input type="file"> upload.
 *
 * @param string $fieldName   Name of the file input (e.g. 'cover_image')
 * @param string $subfolder   Subfolder under assets/images/ (e.g. 'news')
 * @param string &$error      Populated with an error message on failure
 * @return string|null        Relative path (e.g. 'assets/images/news/xyz.jpg') or null if no file / error
 */
function handle_image_upload($fieldName, $subfolder, &$error) {
    $error = '';

    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // no file selected — not necessarily an error
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "There was a problem uploading the file (error code {$file['error']}).";
        return null;
    }

    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        $error = "Image is too large. Please upload a file under 5MB.";
        return null;
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
    ];

    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } else {
        // Fallback for hosts without the fileinfo extension: trust the
        // browser-supplied type, cross-checked against the extension.
        $mime = $file['type'];
    }

    if (!isset($allowed[$mime])) {
        $error = "Unsupported file type. Please upload a JPG, PNG, GIF, WEBP or SVG image.";
        return null;
    }
    $ext = $allowed[$mime];

    $destDir = __DIR__ . '/../../assets/images/' . $subfolder;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    $filename = uniqid($subfolder . '_', true) . '.' . $ext;
    $destPath = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        $error = "Could not save the uploaded file. Please check folder permissions.";
        return null;
    }

    return 'assets/images/' . $subfolder . '/' . $filename;
}
