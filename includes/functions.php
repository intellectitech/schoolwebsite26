<?php
// ============================================================
//  includes/functions.php — Helper Functions
//  Namugongo Parents' Primary School (NPPS) · school_website_db
// ============================================================

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

// One session for the whole site (public forms use it for
// flash messages, admin pages use it for login). Safe to call this
// from any entry point — public page, admin page, or processor —
// without worrying about "session already started" warnings.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------------------------------------
// CLEAN user input — always call this before using $_POST /
// $_GET values.
//
// NOTE: this deliberately does NOT htmlspecialchars() the value.
// SQL safety comes from prepared statements (used everywhere data
// is stored); HTML safety comes from calling htmlspecialchars()
// at the point of OUTPUT (every template here does that). Escaping
// on the way in as well would double-encode: an apostrophe in
// "O'Brien" would be stored as "O&#039;Brien" and then re-escaped
// on display into the literal text "O&amp;#039;Brien".
// ----------------------------------------------------------
function clean($data) {
    $data = trim($data ?? '');
    $data = stripslashes($data);
    return $data;
}

/**
 * GET a setting from school_info by setting_key.
 */
function getSetting($pdo, $key, $default = '') {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    try {
        $stmt = $pdo->prepare('SELECT setting_value FROM school_info WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $cache[$key] = ($val !== false && $val !== '' ? $val : $default);
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * UPDATE or INSERT a setting (school_info has unique setting_key).
 */
function setSetting($pdo, $key, $value, $adminId = 0) {
    $stmt = $pdo->prepare('SELECT id FROM school_info WHERE setting_key = ?');
    $stmt->execute([$key]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        $upd = $pdo->prepare(
            'UPDATE school_info SET setting_value = ?, updated_by = ?, updated_at = NOW() WHERE setting_key = ?'
        );
        return $upd->execute([$value, $adminId ?: 0, $key]);
    }
    $ins = $pdo->prepare(
        'INSERT INTO school_info (setting_key, setting_value, description, updated_by) VALUES (?, ?, ?, ?)'
    );
    return $ins->execute([$key, $value, '', $adminId ?: 0]);
}

function excerpt($text, $len = 150) {
    $text = strip_tags($text ?? '');
    return strlen($text) > $len ? substr($text, 0, $len) . '...' : $text;
}

function formatDate($date) {
    if (!$date) return '';
    return date('l d F Y', strtotime($date));
}

function makeSlug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $slug = trim(preg_replace('/[\s-]+/', '-', $text), '-');
    return $slug !== '' ? $slug : 'item';
}

/**
 * UNIQUE SLUG — appends -2, -3, ... until the slug is free in $table.
 * $excludeId lets a record keep its own slug while editing.
 */
function uniqueSlug($pdo, $table, $column, $baseSlug, $excludeId = null) {
    $slug = $baseSlug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE {$column} = ?" . ($excludeId ? " AND id != ?" : "");
        $params = [$slug];
        if ($excludeId) $params[] = $excludeId;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) return $slug;
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

function requireAdmin($loginPath = 'login.php') {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ' . $loginPath);
        exit;
    }
}

function auditLog($pdo, $adminId, $action, $table, $recordId, $desc) {
    try {
        $pdo->prepare(
            'INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address)
             VALUES (?, ?, ?, ?, ?, ?)'
        )->execute([
            $adminId, $action, $table, $recordId, $desc, $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
    } catch (PDOException $e) {
        error_log('auditLog failed: ' . $e->getMessage());
    }
}

function getAdminName($pdo, $adminId) {
    if (!$adminId) return 'Admin';
    static $names = [];
    if (isset($names[$adminId])) return $names[$adminId];
    $stmt = $pdo->prepare('SELECT name FROM admin_users WHERE id = ?');
    $stmt->execute([$adminId]);
    $n = $stmt->fetchColumn();
    return $names[$adminId] = ($n ?: 'Admin');
}

// ----------------------------------------------------------
// REDIRECT — small wrapper so every processor redirects the
// same way and always stops execution afterwards.
// ----------------------------------------------------------
function redirectTo($url) {
    header('Location: ' . $url);
    exit;
}

// ----------------------------------------------------------
// FLASH MESSAGES — one-time success / error notices carried
// across a redirect via the session, then cleared on display.
//   setFlashSuccess('Saved!');
//   setFlashErrors(['Name is required.']);
//   ... after redirect, on the page that shows the form ...
//   $flash = getFlash();   // ['success' => '', 'errors' => [], 'old' => []]
// ----------------------------------------------------------
function setFlashSuccess($message) {
    $_SESSION['success'] = $message;
}

function setFlashErrors(array $errors) {
    $_SESSION['errors'] = $errors;
}

function setOldInput(array $data) {
    $_SESSION['old'] = $data;
}

function getFlash() {
    $flash = [
        'success' => $_SESSION['success'] ?? '',
        'errors'  => $_SESSION['errors']  ?? [],
        'old'     => $_SESSION['old']     ?? [],
    ];
    unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old']);
    return $flash;
}

/**
 * OLD — re-populate a form field with the previously submitted value
 * after a validation error. Pass $flash['old'] from getFlash().
 */
function old($oldData, $key, $default = '') {
    return htmlspecialchars($oldData[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}
