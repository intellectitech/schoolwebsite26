<?php
// ============================================================
//  process_admissions.php — Admissions Enquiry Form Handler
//  Receives POST data from admissions.php and stores it in
//  the admission_enquiries table.
//
//  NOTE: admission_enquiries.entry_level ships in the raw dump
//  as a secondary-school enum ('S1','S2'). Run database_patch.sql
//  first — it widens the enum to Primary One - Seven ('P1'..'P7')
//  and makes ple_aggregate optional, both of which this form needs.
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('admissions.php');
}

// ── STEP 1: CSRF CHECK ───────────────────────────────────────
if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo('admissions.php#enquiry');
}

// ── STEP 2: SANITIZE ─────────────────────────────────────────
$parentName    = clean($_POST['parent_name']    ?? '');
$childName     = clean($_POST['child_name']     ?? '');
$phone         = clean($_POST['phone']          ?? '');
$email         = clean($_POST['email']          ?? '');
$grade         = clean($_POST['grade']          ?? '');
$term          = clean($_POST['term']           ?? '');
$currentSchool = clean($_POST['current_school'] ?? '');
$message       = clean($_POST['message']        ?? '');

// Honeypot — bots fill every field, real visitors never see this one.
if (!empty($_POST['website'] ?? '')) {
    redirectTo('admissions.php');
}

// ── STEP 3: VALIDATE ─────────────────────────────────────────
$validGrades = ['P1', 'P2', 'P3', 'P4', 'P5', 'P6', 'P7'];
$errors = [];

if ($parentName === '')                                $errors[] = 'Parent / guardian name is required.';
if ($childName === '')                                  $errors[] = "Child's name is required.";
if ($phone === '' || !preg_match('/^[0-9+\-\s()]{7,30}$/', $phone))
                                                         $errors[] = 'Please enter a valid phone number.';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
                                                         $errors[] = 'Please enter a valid email address, or leave it blank.';
if (!in_array($grade, $validGrades, true))              $errors[] = 'Please select the grade you are applying for.';

if (!empty($errors)) {
    setFlashErrors($errors);
    setOldInput([
        'parent_name' => $parentName, 'child_name' => $childName, 'phone' => $phone,
        'email' => $email, 'grade' => $grade, 'term' => $term,
        'current_school' => $currentSchool, 'message' => $message,
    ]);
    redirectTo('admissions.php#enquiry');
}

// The intake term isn't its own column in admission_enquiries,
// so fold it into the free-text message the admissions office reads.
$termLabels = ['term1' => 'Term One (February)', 'term2' => 'Term Two (June)', 'term3' => 'Term Three (September)'];
$fullMessage = $message;
if (isset($termLabels[$term])) {
    $fullMessage = 'Preferred intake: ' . $termLabels[$term] . ($fullMessage !== '' ? "\n\n" . $fullMessage : '');
}

// ── STEP 4: SAVE TO DATABASE ─────────────────────────────────
$stmt = $pdo->prepare(
    'INSERT INTO admission_enquiries
        (parent_name, parent_phone, parent_email, student_name, entry_level,
         current_school, ple_aggregate, message, status, admin_notes, created_at)
     VALUES (?, ?, ?, ?, ?, ?, NULL, ?, \'new\', \'\', CURDATE())'
);
$stmt->execute([
    $parentName, $phone, $email, $childName, $grade, $currentSchool, $fullMessage,
]);

// ── STEP 5: REDIRECT WITH SUCCESS ────────────────────────────
setFlashSuccess('Thank you, ' . $parentName . '! Your admissions enquiry for ' . $childName . ' has been received. Our admissions team will be in touch within two working days.');
redirectTo('admissions.php#enquiry');
