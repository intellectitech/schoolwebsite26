<?php
// ============================================================
//  process_contact.php — Contact Form Handler
//  Receives POST data from contact.php and stores it in
//  the contact_messages table.
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Only accept POST requests — anything else just bounces back
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('contact.php');
}

// ── STEP 1: CSRF CHECK ───────────────────────────────────────
if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo('contact.php');
}

// ── STEP 2: SANITIZE ─────────────────────────────────────────
$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = clean($_POST['message'] ?? '');

// Honeypot field — real visitors never fill this in, only bots do.
// See the hidden "website" field added to the form in contact.php.
if (!empty($_POST['website'] ?? '')) {
    // Silently pretend it worked so the bot moves on.
    redirectTo('contact.php');
}

// ── STEP 3: VALIDATE ─────────────────────────────────────────
$errors = [];

if ($name === '')
    $errors[] = 'Your name is required.';
if (mb_strlen($name) > 200)
    $errors[] = 'Your name is too long.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Please enter a valid email address.';
if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{7,30}$/', $phone))
    $errors[] = 'Please enter a valid phone number.';
if ($subject === '')
    $errors[] = 'Please choose a subject.';
if ($message === '')
    $errors[] = 'Please write a message.';
if (mb_strlen($message) < 10)
    $errors[] = 'Your message must be at least 10 characters.';
if (mb_strlen($message) > 2000)
    $errors[] = 'Your message is too long (2000 characters max).';

if (!empty($errors)) {
    setFlashErrors($errors);
    setOldInput(['name' => $name, 'email' => $email, 'phone' => $phone, 'subject' => $subject, 'message' => $message]);
    redirectTo('contact.php');
}

// ── STEP 4: SAVE TO DATABASE ─────────────────────────────────
// Column names below match database_patch.sql (ip_address, not
// the misspelled ip_addess from the raw dump; is_read explicit
// 0 = unread; replied_at explicit NULL = not replied yet).
$stmt = $pdo->prepare(
    'INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, is_read, replied_at)
     VALUES (?, ?, ?, ?, ?, ?, 0, NULL)'
);
$stmt->execute([
    $name,
    $email,
    $phone,
    $subject,
    $message,
    $_SERVER['REMOTE_ADDR']
]);

// ── STEP 5: REDIRECT WITH SUCCESS ────────────────────────────
setFlashSuccess('Thank you, ' . $name . '! Your message has been received. We will reply within two working days.');
redirectTo('contact.php');
