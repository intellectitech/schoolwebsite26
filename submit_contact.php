<?php
require_once __DIR__ . '/includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid email address.']);
    exit;
}

if (!$pdo instanceof PDO) {
    echo json_encode(['status' => 'error', 'message' => 'We could not reach the database right now. Please try again shortly.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)');
    $stmt->execute([':name' => $name, ':email' => $email, ':message' => $message]);

    echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'We could not send your message right now. Please try again shortly.']);
}
