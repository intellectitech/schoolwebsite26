<?php
// ============================================================
//  process_admissions.php — Admissions Enquiry Form Handler
//  Receives POST data from admissions.php and stores it in the
//  admission_enquiries table.
// ============================================================
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('admissions.php');
}

$parentName   = clean($_POST['parent_name']   ?? '');
$parentPhone  = clean($_POST['parent_phone']  ?? '');
$parentEmail  = clean($_POST['parent_email']  ?? '');
$studentName  = clean($_POST['student_name']  ?? '');
$entryLevel   = clean($_POST['entry_level']   ?? '');
$currentSchool= clean($_POST['current_school']?? '');
$message      = clean($_POST['message']       ?? '');

// Honeypot — real visitors never see or fill this field in.
if (!empty($_POST['website'] ?? '')) {
    redirectTo('admissions.php');
}

// Must match one of the classes offered on the form.
$validLevels = ['Baby Class','Middle Class','Top Class','P1','P2','P3','P4','P5','P6','P7'];

$errors = [];
if ($parentName === '') $errors[] = 'Parent/guardian name is required.';
if ($parentPhone === '' || !preg_match('/^[0-9+\-\s()]{7,30}$/', $parentPhone)) $errors[] = 'Please enter a valid phone number.';
if (!filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
if ($studentName === '') $errors[] = "Child's name is required.";
if (!in_array($entryLevel, $validLevels, true)) $errors[] = 'Please choose the class you are applying for.';

if ($errors) {
    setFlashErrors($errors);
    setOldInput([
        'parent_name' => $parentName, 'parent_phone' => $parentPhone, 'parent_email' => $parentEmail,
        'student_name' => $studentName, 'entry_level' => $entryLevel, 'current_school' => $currentSchool,
        'message' => $message,
    ]);
    redirectTo('admissions.php');
}

$stmt = $pdo->prepare(
    'INSERT INTO admission_enquiries
        (parent_name, parent_phone, parent_email, student_name, entry_level, current_school, ple_aggregate, message, status, admin_notes)
     VALUES (?, ?, ?, ?, ?, ?, NULL, ?, ?, ?)'
);
$stmt->execute([$parentName, $parentPhone, $parentEmail, $studentName, $entryLevel, $currentSchool, $message, 'new', '']);

setFlashSuccess('Thank you, ' . $parentName . '! Your enquiry has been received. Our admissions office will contact you shortly.');
redirectTo('admissions.php');
