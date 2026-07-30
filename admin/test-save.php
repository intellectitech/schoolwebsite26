<?php
// admin/settings.php - FIXED VERSION (based on working test-save.php)
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Get current settings
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM school_info");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Handle form submission - SIMPLE LIKE test-save.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Update school name
    if (isset($_POST['school_name'])) {
        $value = clean($_POST['school_name']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('school_name', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['school_name'] = $value;
        $message = 'School name updated!';
    }
    
    // Update school phone
    if (isset($_POST['school_phone'])) {
        $value = clean($_POST['school_phone']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('school_phone', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['school_phone'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // Update school email
    if (isset($_POST['school_email'])) {
        $value = clean($_POST['school_email']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('school_email', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['school_email'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // Update school address
    if (isset($_POST['school_address'])) {
        $value = clean($_POST['school_address']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('school_address', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['school_address'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // Update founded year
    if (isset($_POST['founded_year'])) {
        $value = clean($_POST['founded_year']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('founded_year', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['founded_year'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // Update total students
    if (isset($_POST['total_students'])) {
        $value = clean($_POST['total_students']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('total_students', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['total_students'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // Update total teachers
    if (isset($_POST['total_teachers'])) {
        $value = clean($_POST['total_teachers']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('total_teachers', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['total_teachers'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // Update pass rate
    if (isset($_POST['pass_rate'])) {
        $value = clean($_POST['pass_rate']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('pass_rate', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['pass_rate'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // Update meta description
    if (isset($_POST['meta_description'])) {
        $value = clean($_POST['meta_description']);
        $stmt = $pdo->prepare("INSERT INTO school_info (setting_key, setting_value) VALUES ('meta_description', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$value, $value]);
        $settings['meta_description'] = $value;
        $message = 'Settings updated successfully!';
    }
    
    // If no specific field was updated but form was submitted
    if (empty($message) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = 'Settings updated successfully!';
    }
}

// Get values for display
$schoolName = $settings['school_name'] ?? '';
$schoolPhone = $settings['school_phone'] ?? '';
$schoolEmail = $settings['school_email'] ?? '';
$schoolAddress = $settings['school_address'] ?? '';
$foundedYear = $settings['founded_year'] ?? '1985';
$totalStudents = $settings['total_students'] ?? '1,200';
$totalTeachers = $settings['total_teachers'] ?? '60';
$passRate = $settings['pass_rate'] ?? '86';
$metaDescription = $settings['meta_description'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5e6d3; color: #1a1a1a; min-height: 100vh; }
        .admin-wrapper { display: flex; min-height: 100vh; }

        .admin-sidebar {
            width: 260px;
            background: #0a0a0a;
            color: #fff;
            padding: 30px 20px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            border-right: 2px solid #00C853;
        }
        .admin-sidebar .logo { text-align: center; padding-bottom: 30px; border-bottom: 2px solid rgba(0,200,83,0.2); margin-bottom: 30px; }
        .admin-sidebar .logo .icon-wrapper { display: inline-block; width: 55px; height: 55px; background: linear-gradient(135deg, #009624, #00C853); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; box-shadow: 0 10px 30px rgba(0,200,83,0.25); }
        .admin-sidebar .logo i { font-size: 2rem; color: #fff; }
        .admin-sidebar .logo h2 { color: #fff; font-size: 1.1rem; font-weight: 700; }
        .admin-sidebar .user { padding: 15px; background: rgba(255,255,255,0.05); border-radius: 16px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
        .admin-sidebar .user .name { font-weight: 600; color: #00C853; }
        .admin-sidebar .user .role { font-size: 0.8rem; opacity: 0.5; color: rgba(255,255,255,0.6); }
        .admin-sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.5); border-radius: 14px; transition: all 0.3s ease; margin-bottom: 4px; text-decoration: none; }
        .admin-sidebar nav a:hover, .admin-sidebar nav a.active { background: rgba(0,200,83,0.12); color: #00C853; border: 1px solid rgba(0,200,83,0.1); transform: translateX(4px); }
        .admin-sidebar nav a i { width: 20px; color: rgba(255,255,255,0.3); transition: all 0.3s ease; }
        .admin-sidebar nav a:hover i, .admin-sidebar nav a.active i { color: #00C853; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,0.4); cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; width: 100%; font-size: 1rem; font-family: inherit; border-radius: 14px; transition: all 0.3s ease; margin-top: 10px; }
        .logout-btn:hover { background: rgba(255,0,0,0.08); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.1); }

        .admin-content { flex: 1; padding: 30px; background: #f5e6d3; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; padding: 20px 30px; background: #fff; border-radius: 20px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); border-left: 4px solid #00C853; }
        .admin-header h1 { color: #0a0a0a; font-size: 1.6rem; font-weight: 700; }
        .admin-header h1 i { color: #00C853; margin-right: 10px; }

        .settings-form { background: #fff; border-radius: 20px; padding: 40px; max-width: 900px; box-shadow: 0 5px 30px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #0a0a0a; font-size: 0.9rem; }
        .form-group label .required { color: #FF6B6B; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 1rem; transition: border-color 0.3s; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #00C853; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        .btn-save { 
            padding: 16px 40px; 
            background: linear-gradient(135deg, #009624, #00C853); 
            color: #fff; 
            border: none; 
            border-radius: 14px; 
            font-size: 1.1rem; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            box-shadow: 0 10px 30px rgba(0,200,83,0.3);
            width: 100%;
        }
        .btn-save:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 15px 40px rgba(0,200,83,0.4); 
        }

        .alert-success { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 14px; margin-bottom: 20px; border: 1px solid #f5c6cb; }

        .section-title { color: #0a0a0a; font-size: 1.2rem; font-weight: 700; margin: 30px 0 20px; padding-bottom: 10px; border-bottom: 2px solid #00C853; }
        .section-title i { color: #00C853; margin-right: 10px; }
        .helper-text { font-size: 0.85rem; color: #999; margin-top: 5px; }

        @media (max-width: 768px) { .admin-sidebar { width: 200px; padding: 20px 15px; } .form-row { grid-template-columns: 1fr; } }
        @media (max-width: 480px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; } .settings-form { padding: 20px; } .admin-header { flex-direction: column; align-items: stretch; } }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="logo">
            <div class="icon-wrapper"><i class="fas fa-graduation-cap"></i></div>
            <h2>School Admin</h2>
        </div>
        <div class="user">
            <div class="name"><?= clean($_SESSION['admin_name']) ?></div>
            <div class="role"><?= clean($_SESSION['admin_role'] ?? 'Admin') ?></div>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage-news.php"><i class="fas fa-newspaper"></i> Manage News</a>
            <a href="manage-events.php"><i class="fas fa-calendar"></i> Manage Events</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
            <a href="manage-staff.php"><i class="fas fa-users"></i> Staff</a>
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-cog"></i> Site Settings</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= clean($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="settings-form">
            <!-- ======================== -->
            <!-- SCHOOL INFORMATION -->
            <!-- ======================== -->
            <h3 class="section-title"><i class="fas fa-school"></i> School Information</h3>

            <div class="form-group">
                <label for="school_name">School Name <span class="required">*</span></label>
                <input type="text" id="school_name" name="school_name" value="<?= clean($schoolName) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="school_phone">Phone Number</label>
                    <input type="text" id="school_phone" name="school_phone" value="<?= clean($schoolPhone) ?>">
                </div>
                <div class="form-group">
                    <label for="school_email">Email Address</label>
                    <input type="email" id="school_email" name="school_email" value="<?= clean($schoolEmail) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="school_address">Address</label>
                <input type="text" id="school_address" name="school_address" value="<?= clean($schoolAddress) ?>">
            </div>

            <!-- ======================== -->
            <!-- STATISTICS -->
            <!-- ======================== -->
            <h3 class="section-title"><i class="fas fa-chart-bar"></i> Statistics</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="founded_year">Founded Year</label>
                    <input type="text" id="founded_year" name="founded_year" value="<?= clean($foundedYear) ?>">
                </div>
                <div class="form-group">
                    <label for="total_students">Total Students</label>
                    <input type="text" id="total_students" name="total_students" value="<?= clean($totalStudents) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_teachers">Total Teachers</label>
                    <input type="text" id="total_teachers" name="total_teachers" value="<?= clean($totalTeachers) ?>">
                </div>
                <div class="form-group">
                    <label for="pass_rate">Pass Rate (%)</label>
                    <input type="text" id="pass_rate" name="pass_rate" value="<?= clean($passRate) ?>">
                </div>
            </div>

            <!-- ======================== -->
            <!-- SEO -->
            <!-- ======================== -->
            <h3 class="section-title"><i class="fas fa-search"></i> SEO</h3>

            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="3"><?= clean($metaDescription) ?></textarea>
                <div class="helper-text"><i class="fas fa-info-circle"></i> Appears in Google search results. Keep under 160 characters.</div>
            </div>

            <!-- ======================== -->
            <!-- SAVE BUTTON -->
            <!-- ======================== -->
            <div style="margin-top:30px;padding-top:20px;border-top:2px solid #e0e0e0;">
                <button type="submit" name="save" class="btn-save"><i class="fas fa-save"></i> Save All Settings</button>
            </div>
        </form>
    </main>
</div>

<script>
// Simple debug
document.querySelector('form').addEventListener('submit', function(e) {
    console.log('Form submitted!');
});
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>