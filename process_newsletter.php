<?php
// ============================================================
//  process_newsletter.php — Newsletter Sign-up Handler
//  Receives POST data from the newsletter form on news.php and
//  stores it in the newsletters_subscribers table.
//
//  NOTE: the supplied school_website_db.sql already has a UNIQUE key on
//  newsletters_subscribers.email, so the same address can't be stored
//  twice (an unpatched raw dump had no such constraint — see README.md
//  §5).
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('news.php');
}

if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo('news.php');
}

$email = clean($_POST['newsletter_email'] ?? '');

if (!empty($_POST['website'] ?? '')) {
    redirectTo('news.php'); // honeypot
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashErrors(['Please enter a valid email address to subscribe.']);
    redirectTo('news.php#newsletter');
}

// Already subscribed? Treat it as a friendly success, not an error.
$exists = $pdo->prepare('SELECT id FROM newsletters_subscribers WHERE email = ? LIMIT 1');
$exists->execute([$email]);

if (!$exists->fetch()) {
    $token = bin2hex(random_bytes(16));
    $pdo->prepare(
        'INSERT INTO newsletters_subscribers (email, name, is_confirmed, confirm_token, unsubscribed_at)
         VALUES (?, \'\', 0, ?, NULL)'
    )->execute([$email, $token]);
}

setFlashSuccess('Thank you — you have been subscribed to our termly newsletter.');
redirectTo('news.php#newsletter');
