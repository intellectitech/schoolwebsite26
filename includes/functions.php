<?php
// ============================================================
//  includes/functions.php — Helper Functions
//  Include once at the top of every PHP page.
// ============================================================

// ----------------------------------------------------------
// SANITIZE user input — always call this before using
// $_POST or $_GET values in your code or database.
//
// NOTE: this deliberately does NOT htmlspecialchars() the value.
// SQL safety comes from prepared statements (used everywhere data
// is stored), and HTML safety comes from calling htmlspecialchars()
// at the point of OUTPUT (every template in this project already
// does that). Escaping here too would double-encode: an apostrophe
// in "O'Brien" would be stored as "O&#039;Brien" and then re-escaped
// on display into the literal text "O&amp;#039;Brien".
// ----------------------------------------------------------
function clean($data)
{
    $data = trim($data);           // remove leading/trailing spaces
    $data = stripslashes($data);   // remove backslashes
    return $data;
}

// ----------------------------------------------------------
// GET a setting from the school_info table.
// Uses static cache so we only query the DB once per key.
// Usage: $name = getSetting($pdo, 'school_name');
// ----------------------------------------------------------
function getSetting($pdo, $key)
{
    static $cache = [];
    if (isset($cache[$key]))
        return $cache[$key];
    $stmt = $pdo->prepare('SELECT setting_value FROM school_info WHERE setting_key = ?');
    $stmt->execute([$key]);
    return $cache[$key] = ($stmt->fetchColumn() ?: '');
}

// ----------------------------------------------------------
// EXCERPT — truncate text to N characters, strip HTML tags.
// Usage: echo excerpt($article['body'], 150);
// ----------------------------------------------------------
function excerpt($text, $len = 150)
{
    $text = strip_tags($text);
    return strlen($text) > $len ? substr($text, 0, $len) . '...' : $text;
}

// ----------------------------------------------------------
// FORMAT DATE — convert DB date to readable format.
// Usage: echo formatDate('2026-06-25');  → Wednesday 25 June 2026
// ----------------------------------------------------------
function formatDate($date)
{
    return date('l d F Y', strtotime($date));
}

// ----------------------------------------------------------
// MAKE SLUG — convert text to URL-friendly slug.
// Usage: $slug = makeSlug('S4 Wins Football!');  → s4-wins-football
// ----------------------------------------------------------
function makeSlug($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    return preg_replace('/[\s-]+/', '-', $text);
}

// ----------------------------------------------------------
// REQUIRE ADMIN — redirect to login if not logged in.
// Call this at the top of every admin page.
// Relative redirect so it works regardless of the folder the
// site is deployed under (no hard-coded "/school-website/").
// ----------------------------------------------------------
function requireAdmin()
{
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

// ----------------------------------------------------------
// AUDIT LOG — record an admin action.
// Call after every INSERT / UPDATE / DELETE in admin pages.
// ----------------------------------------------------------
function auditLog($pdo, $adminId, $action, $table, $recordId, $desc)
{
    $pdo->prepare(
        'INSERT INTO audit_log
            (admin_id, action, table_name, record_id, description, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)'
    )->execute([
                $adminId,
                $action,
                $table,
                $recordId,
                $desc,
                $_SERVER['REMOTE_ADDR']
            ]);
}

// ----------------------------------------------------------
// REDIRECT — small wrapper so every processor redirects the
// same way and always stops execution afterwards.
// ----------------------------------------------------------
function redirectTo($url)
{
    header('Location: ' . $url);
    exit;
}

// ----------------------------------------------------------
// FLASH MESSAGES — one-time success / error notices carried
// across a redirect via the session, then cleared on display.
// Usage:
//   setFlashSuccess('Saved!');
//   setFlashErrors(['Name is required.']);
//   ... after redirect, in the page that shows the form ...
//   $flash = getFlash();   // ['success' => '', 'errors' => []]
// ----------------------------------------------------------
function setFlashSuccess($message)
{
    $_SESSION['success'] = $message;
}

function setFlashErrors(array $errors)
{
    $_SESSION['errors'] = $errors;
}

function setOldInput(array $data)
{
    $_SESSION['old'] = $data;
}

function getFlash()
{
    $flash = [
        'success' => $_SESSION['success'] ?? '',
        'errors' => $_SESSION['errors'] ?? [],
        'old' => $_SESSION['old'] ?? [],
    ];
    unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old']);
    return $flash;
}

// ----------------------------------------------------------
// OLD — re-populate a form field with the previously submitted
// value after a validation error, so the visitor does not have
// to retype the whole form. Call AFTER getFlash() has been read
// into $flash and pass $flash['old'].
// Usage: value attribute set to old($flash['old'], 'email') inside a PHP block.
// ----------------------------------------------------------
function old($oldData, $key, $default = '')
{
    return htmlspecialchars($oldData[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

// ----------------------------------------------------------
// CSRF PROTECTION — a random token is stamped into every form
// and checked when the form is submitted, so another website
// cannot silently submit our forms on a visitor's behalf.
// Usage in a form: echo csrfField() inside a PHP block.
// Usage in a processor:
//   if (!verifyCsrf($_POST['csrf_token'] ?? '')) { ...reject... }
// ----------------------------------------------------------
function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

// ----------------------------------------------------------
// SLUGIFY — turn "Best O-Level Results!" into "best-o-level-results"
// ----------------------------------------------------------
function slugify($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? $text : 'article';
}

// ----------------------------------------------------------
// UNIQUE SLUG — appends -2, -3, ... until the slug is free.
// $excludeId lets an article keep its own slug while editing.
// $table/$column are always hard-coded call sites in this
// project, never user input, so simple interpolation is safe.
// ----------------------------------------------------------
function uniqueSlug($pdo, $table, $column, $baseSlug, $excludeId = null)
{
    $slug = $baseSlug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE {$column} = ?" . ($excludeId ? " AND id != ?" : "");
        $params = [$slug];
        if ($excludeId) {
            $params[] = $excludeId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

// ----------------------------------------------------------
// IMAGE UPLOAD (shared implementation) — validates and stores an
// uploaded photo under images/{$subdir}/. Returns
//   ['path' => 'images/{$subdir}/xyz.jpg', 'error' => null]  on success
//   ['path' => null, 'error' => null]                        if no file was chosen (not an error)
//   ['path' => null, 'error' => 'message']                    on a real problem
// Only JPG / PNG / WEBP are accepted, verified by actually
// reading the image data (getimagesize), not just the filename.
// This backs uploadNewsImage() / uploadEventImage() / uploadGalleryImage()
// / uploadStaffImage() below — each just fixes the subdirectory.
// ----------------------------------------------------------
function uploadImageTo($fileKey, $subdir)
{
    if (empty($_FILES[$fileKey]['name'])) {
        return ['path' => null, 'error' => null];
    }
    $file = $_FILES[$fileKey];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => 'The image failed to upload. Please try again.'];
    }
    if ($file['size'] > 3 * 1024 * 1024) {
        return ['path' => null, 'error' => 'That image is too large (3MB maximum).'];
    }
    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        return ['path' => null, 'error' => 'That file is not a valid image.'];
    }
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!isset($allowed[$info[2]])) {
        return ['path' => null, 'error' => 'Please upload a JPG, PNG, or WEBP image.'];
    }

    $destDir = __DIR__ . '/../images/' . $subdir;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $filename = bin2hex(random_bytes(8)) . '.' . $allowed[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) {
        return ['path' => null, 'error' => 'The server could not save that image.'];
    }
    return ['path' => 'images/' . $subdir . '/' . $filename, 'error' => null];
}

// ----------------------------------------------------------
// DELETE an uploaded image file, restricted to images/{$subdir}/
// so this can never be used to delete an arbitrary file elsewhere
// on the server. Backs the deleteXImageFile() helpers below.
// ----------------------------------------------------------
function deleteImageFrom($relativePath, $subdir)
{
    if (!$relativePath || strpos($relativePath, 'images/' . $subdir . '/') !== 0) {
        return;
    }
    $full = __DIR__ . '/../' . $relativePath;
    if (is_file($full)) {
        @unlink($full);
    }
}

// ----------------------------------------------------------
// NEWS IMAGE UPLOAD / DELETE — stored under images/news/.
// ----------------------------------------------------------
function uploadNewsImage($fileKey)
{
    return uploadImageTo($fileKey, 'news');
}

function deleteNewsImageFile($relativePath)
{
    deleteImageFrom($relativePath, 'news');
}

// ----------------------------------------------------------
// EVENT IMAGE UPLOAD / DELETE — stored under images/events/.
// ----------------------------------------------------------
function uploadEventImage($fileKey)
{
    return uploadImageTo($fileKey, 'events');
}

function deleteEventImageFile($relativePath)
{
    deleteImageFrom($relativePath, 'events');
}

// ----------------------------------------------------------
// GALLERY IMAGE UPLOAD / DELETE — stored under images/gallery/.
// Used for both album cover photos and individual gallery photos.
// ----------------------------------------------------------
function uploadGalleryImage($fileKey)
{
    return uploadImageTo($fileKey, 'gallery');
}

function deleteGalleryImageFile($relativePath)
{
    deleteImageFrom($relativePath, 'gallery');
}

// ----------------------------------------------------------
// STAFF PHOTO UPLOAD / DELETE — stored under images/staff/.
// ----------------------------------------------------------
function uploadStaffImage($fileKey)
{
    return uploadImageTo($fileKey, 'staff');
}

function deleteStaffImageFile($relativePath)
{
    deleteImageFrom($relativePath, 'staff');
}

// ----------------------------------------------------------
// TESTIMONIAL PHOTO UPLOAD / DELETE — stored under images/testimonials/.
// ----------------------------------------------------------
function uploadTestimonialImage($fileKey)
{
    return uploadImageTo($fileKey, 'testimonials');
}

function deleteTestimonialImageFile($relativePath)
{
    deleteImageFrom($relativePath, 'testimonials');
}

// ----------------------------------------------------------
// UPDATE A SETTING — writes a new value to an existing
// school_info row and stamps who changed it. Returns true if
// the value actually changed (so callers can skip audit-logging
// for fields nobody touched).
// Usage: updateSetting($pdo, $id, $newValue, $adminId);
// ----------------------------------------------------------
function updateSetting($pdo, $id, $newValue, $adminId)
{
    $stmt = $pdo->prepare('SELECT setting_value FROM school_info WHERE id = ?');
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    if ($current === false || $current === $newValue) {
        return false;
    }
    $pdo->prepare('UPDATE school_info SET setting_value = ?, updated_by = ? WHERE id = ?')
        ->execute([$newValue, $adminId, $id]);
    return true;
}

// ----------------------------------------------------------
// SITE BASE URL — scheme + host + whatever folder the site is
// deployed under, detected from the current request. Same
// deployment-folder-agnostic approach requireAdmin() already
// uses for its redirect, so canonical/OG tags and the sitemap
// work whether the site lives at the domain root or a subfolder
// like /schoolwebsite26 on a local XAMPP install.
// Usage: siteBaseUrl() . '/about'
// ----------------------------------------------------------
function siteBaseUrl()
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    return $scheme . '://' . $host . $dir;
}

// ----------------------------------------------------------
// RENDER SEO TAGS — outputs <title>, meta description, the
// canonical link, and Open Graph tags for one page, straight
// into its <head>. Pulls copy from includes/seo-config.php by
// route key; pass $titleOverride/$descriptionOverride for
// pages (like article.php) whose SEO text comes from the
// database instead of the static config.
// Usage:
//   renderSeoTags('about');                       // static page
//   renderSeoTags(null, $title, $desc, $slug);     // dynamic page
// ----------------------------------------------------------
function renderSeoTags($routeKey, $titleOverride = null, $descriptionOverride = null, $canonicalPath = '', $ogImage = null)
{
    static $seoConfig = null;
    if ($seoConfig === null) {
        $seoConfig = require __DIR__ . '/seo-config.php';
    }

    $fallback = ['title' => 'Uganda Martyrs Primary School, Namugongo', 'description' => ''];
    $entry = $seoConfig[$routeKey] ?? $fallback;
    $title = $titleOverride ?: $entry['title'];
    $description = $descriptionOverride ?: $entry['description'];
    $canonical = siteBaseUrl() . '/' . ltrim($canonicalPath, '/');
    $image = $ogImage ?: (siteBaseUrl() . '/assets/images/ESD_69e8c39b15887.webp');

    echo '  <title>' . htmlspecialchars($title) . "</title>\n";
    echo '  <meta name="description" content="' . htmlspecialchars($description) . "\">\n";
    echo '  <link rel="canonical" href="' . htmlspecialchars($canonical) . "\">\n";
    echo "  <meta property=\"og:type\" content=\"website\">\n";
    echo '  <meta property="og:title" content="' . htmlspecialchars($title) . "\">\n";
    echo '  <meta property="og:description" content="' . htmlspecialchars($description) . "\">\n";
    echo '  <meta property="og:url" content="' . htmlspecialchars($canonical) . "\">\n";
    echo '  <meta property="og:image" content="' . htmlspecialchars($image) . "\">\n";
}
