<?php
// admin/settings-debug.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get current school name
$stmt = $pdo->query("SELECT setting_value FROM school_info WHERE setting_key = 'school_name'");
$schoolName = $stmt->fetchColumn();

// Handle form submission - EXACT COPY OF test-save.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update school name - same as test-save.php
    if (isset($_POST['school_name'])) {
        $value = $_POST['school_name'];
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('school_name', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $schoolName = $value;
        $message = 'School name updated!';
    }
    
    // Also update other fields
    $fields = ['school_phone', 'school_email', 'school_address', 'founded_year', 'total_students', 'total_teachers', 'pass_rate', 'meta_description'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$field, $value, $value]);
        }
    }
    
    // Redirect to clear POST
    header('Location: settings-debug.php?success=1');
    exit;
}

$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5e6d3; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 30px rgba(0,0,0,0.08); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        input, textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; margin-bottom: 15px; font-family: inherit; }
        button { padding: 12px 30px; background: #00C853; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
        button:hover { background: #009624; }
        label { font-weight: 600; display: block; margin-bottom: 5px; }
        .field-group { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Settings Debug</h1>
        
        <?php if ($success): ?>
            <div class="success">✅ Settings updated successfully! (redirect)</div>
        <?php endif; ?>
        
        <p><strong>Current School Name:</strong> <?= clean($schoolName ?? 'Not set') ?></p>
        
        <form method="POST" action="">
            <div class="field-group">
                <label>School Name</label>
                <input type="text" name="school_name" value="<?= clean($schoolName) ?>">
            </div>
            
            <div class="field-group">
                <label>Phone</label>
                <input type="text" name="school_phone" value="<?= clean($stmt = $pdo->query("SELECT setting_value FROM school_info WHERE setting_key = 'school_phone'")->fetchColumn()) ?>">
            </div>
            
            <div class="field-group">
                <label>Email</label>
                <input type="email" name="school_email" value="<?= clean($stmt = $pdo->query("SELECT setting_value FROM school_info WHERE setting_key = 'school_email'")->fetchColumn()) ?>">
            </div>
            
            <button type="submit">Save All</button>
        </form>
        
        <p style="margin-top:20px;font-size:14px;color:#999;">
            <a href="settings.php">Back to Settings</a>
        </p>
    </div>
</body>
</html>