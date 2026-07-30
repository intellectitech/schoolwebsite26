<?php
// ============================================================
//  process_contact.php — Contact Form Handler
//  Receives POST data from contact.php and stores it in the
//  contact_messages table.
// ============================================================
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('contact.php');
}

$name    = clean($_POST['name']    ?? '');
$email   = clean($_POST['email']   ?? '');
$phone   = clean($_POST['phone']   ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = clean($_POST['message'] ?? '');

// Honeypot — real visitors never see or fill this field in.
if (!empty($_POST['website'] ?? '')) {
    redirectTo('contact.php');
}

$errors = [];
if ($name === '') $errors[] = 'Your name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{7,30}$/', $phone)) $errors[] = 'Please enter a valid phone number.';
if ($subject === '') $errors[] = 'Please enter a subject.';
if (mb_strlen($message) < 10) $errors[] = 'Your message must be at least 10 characters.';
if (mb_strlen($message) > 2000) $errors[] = 'Your message is too long (2000 characters max).';

if ($errors) {
    setFlashErrors($errors);
    setOldInput(['name' => $name, 'email' => $email, 'phone' => $phone, 'subject' => $subject, 'message' => $message]);
    redirectTo('contact.php');
}

$stmt = $pdo->prepare(
    'INSERT INTO contact_messages (name, email, phone, subject, message, ip_addess, is_read, replied_at)
     VALUES (?, ?, ?, ?, ?, ?, 0, NULL)'
);
$stmt->execute([$name, $email, $phone, $subject, $message, $_SERVER['REMOTE_ADDR'] ?? '']);

setFlashSuccess('Thank you, ' . $name . '! Your message has been received. We will reply within two working days.');
redirectTo('contact.php');
