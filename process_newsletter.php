<?php
// ============================================================
//  process_newsletter.php — Newsletter Sign-up Handler
//  Receives POST data from the newsletter box on index.php and
//  news.php, and stores it in newsletters_subscribers.
// ============================================================
require_once 'includes/functions.php';

// Where to bounce back to — whichever page the form was on.
$backTo = $_SERVER['HTTP_REFERER'] ?? 'index.php';
// Only allow a same-site relative redirect (avoid open-redirect via a forged Referer).
if (!preg_match('#^[a-zA-Z0-9_\-./]+\.php$#', parse_url($backTo, PHP_URL_PATH) ?? '')) {
    $backTo = 'index.php';
} else {
    $backTo = ltrim(parse_url($backTo, PHP_URL_PATH), '/');
    $backTo = basename($backTo) ?: 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo($backTo);
}

$email = clean($_POST['email'] ?? '');

// Honeypot — bots fill every field, real visitors never see this one.
if (!empty($_POST['website'] ?? '')) {
    redirectTo($backTo);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashErrors(['Please enter a valid email address.']);
    setOldInput(['email' => $email]);
    redirectTo($backTo);
}

// Already subscribed?
$check = $pdo->prepare('SELECT id, unsubscribed_at FROM newsletters_subscribers WHERE email = ?');
$check->execute([$email]);
$existing = $check->fetch();

if ($existing && $existing['unsubscribed_at'] === null) {
    setFlashSuccess("You're already on our newsletter list — thank you!");
    redirectTo($backTo);
}

if ($existing) {
    // Re-subscribing after a previous unsubscribe.
    $pdo->prepare('UPDATE newsletters_subscribers SET unsubscribed_at = NULL WHERE id = ?')
        ->execute([$existing['id']]);
} else {
    $token = bin2hex(random_bytes(16));
    $pdo->prepare(
        'INSERT INTO newsletters_subscribers (email, name, is_confirmed, confirm_token) VALUES (?, ?, 1, ?)'
    )->execute([$email, '', $token]);
}

setFlashSuccess('Thank you for subscribing! You will hear from us with school updates.');
redirectTo($backTo);
