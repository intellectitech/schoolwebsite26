<?php
require_once __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admission.php');
    exit;
}

$parentName = trim($_POST['parent_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$childName = trim($_POST['child_name'] ?? '');
$childAge = trim($_POST['child_age'] ?? '');
$grade = trim($_POST['grade'] ?? '');
$message = trim($_POST['message'] ?? '');

$status = 'success';
$notice = 'Your admission request has been received successfully. We will contact you soon.';

if ($parentName === '' || $email === '' || $phone === '' || $childName === '' || $childAge === '' || $grade === '' || $message === '') {
    $status = 'error';
    $notice = 'Please fill in all fields before submitting your admission request.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $status = 'error';
    $notice = 'Please provide a valid email address.';
} elseif (!$pdo instanceof PDO) {
    $status = 'error';
    $notice = 'We could not reach the database right now. Please call the school office instead.';
} else {
    try {
        $stmt = $pdo->prepare('INSERT INTO admission_applications (parent_name, email, phone, child_name, child_age, preferred_grade, message) VALUES (:parent_name, :email, :phone, :child_name, :child_age, :preferred_grade, :message)');
        $stmt->execute([
            ':parent_name' => $parentName,
            ':email' => $email,
            ':phone' => $phone,
            ':child_name' => $childName,
            ':child_age' => (int) $childAge,
            ':preferred_grade' => $grade,
            ':message' => $message,
        ]);
    } catch (PDOException $e) {
        $status = 'error';
        $notice = 'We could not save your application right now. Please try again shortly.';
    }
}

$pageTitle = 'Admission Submitted';
$pageDescription = 'Admission request status for Namugongo Model Primary School.';
require_once __DIR__ . '/includes/header.php';
?>
        <section class="page-hero">
            <div class="container">
                <h1>Admission Request</h1>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="form-card reveal">
                    <h3>Application Status</h3>
                    <p class="notice notice-<?php echo $status === 'success' ? 'success' : 'error'; ?>"><?php echo h($notice); ?></p>
                    <p><a class="btn" href="admission.php">Submit Another Application</a></p>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
