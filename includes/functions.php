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
// NEWS IMAGE UPLOAD — validates and stores an uploaded photo
// for a news article under images/news/. Returns
//   ['path' => 'images/news/xyz.jpg', 'error' => null]   on success
//   ['path' => null, 'error' => null]                    if no file was chosen (not an error)
//   ['path' => null, 'error' => 'message']                on a real problem
// Only JPG / PNG / WEBP are accepted, verified by actually
// reading the image data (getimagesize), not just the filename.
// ----------------------------------------------------------
function uploadNewsImage($fileKey)
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

    $destDir = __DIR__ . '/images/news';
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $filename = bin2hex(random_bytes(8)) . '.' . $allowed[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) {
        return ['path' => null, 'error' => 'The server could not save that image.'];
    }
    return ['path' => 'images/news/' . $filename, 'error' => null];
}

// ----------------------------------------------------------
// DELETE a news image file when an article's photo is replaced
// or removed. Restricted to images/news/ so this can never be
// used to delete an arbitrary file elsewhere on the server.
// ----------------------------------------------------------
function deleteNewsImageFile($relativePath)
{
    if (!$relativePath || strpos($relativePath, 'images/news/') !== 0) {
        return;
    }
    $full = __DIR__ . '/' . $relativePath;
    if (is_file($full)) {
        @unlink($full);
    }
}

// ----------------------------------------------------------
// EVENT IMAGE UPLOAD / DELETE — same rules as uploadNewsImage(),
// stored under images/events/ instead of images/news/.
// ----------------------------------------------------------
function uploadEventImage($fileKey)
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

    $destDir = __DIR__ . '/images/events';
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $filename = bin2hex(random_bytes(8)) . '.' . $allowed[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) {
        return ['path' => null, 'error' => 'The server could not save that image.'];
    }
    return ['path' => 'images/events/' . $filename, 'error' => null];
}

function deleteEventImageFile($relativePath)
{
    if (!$relativePath || strpos($relativePath, 'images/events/') !== 0) {
        return;
    }
    $full = __DIR__ . '/' . $relativePath;
    if (is_file($full)) {
        @unlink($full);
    }
}
