<?php
// 1. Database Connection Configuration
$host     = '127.0.0.1';
$db       = 'school_website_db'; 
$user     = 'root';              
$password = '';                  
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$feedback = '';

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect Parent & Student details
    $parent_name    = trim($_POST['parent_name'] ?? '');
    $parent_phone   = trim($_POST['parent_phone'] ?? '');
    $parent_email   = trim($_POST['parent_email'] ?? '');
    $student_name   = trim($_POST['student_name'] ?? '');
    $entry_level    = $_POST['entry_level'] ?? 'S1'; 
    $current_school = trim($_POST['current_school'] ?? '');
    $ple_aggregate  = trim($_POST['ple_aggregate'] ?? '');
    $message        = trim($_POST['message'] ?? '');
    $status         = 'pending'; // Default status[cite: 1]

    // File Details
    $doc_title      = trim($_POST['doc_title'] ?? 'Academic Document');
    $doc_description= trim($_POST['doc_description'] ?? 'Submitted during online application.');

    // Upload configuration
    $uploadDir   = 'uploads/documents/';
    $allowedMime = [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png'
    ];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    // Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Validate inputs
    if (empty($parent_name) || empty($student_name) || empty($parent_phone)) {
        $feedback = "<div class='alert alert-danger'>Please fill in all required fields (Parent Name, Student Name, and Phone Number).</div>";
    } elseif (!isset($_FILES['academic_file']) || $_FILES['academic_file']['error'] !== UPLOAD_ERR_OK) {
        $feedback = "<div class='alert alert-danger'>Please select a valid academic file to upload (e.g. PLE Slip).</div>";
    } else {
        $file = $_FILES['academic_file'];
        $bytes = $file['size'];

        // Size format calculation
        if ($bytes >= 1048576) {
            $fileSizeString = number_format($bytes / 1048576, 2) . ' MB';
        } else {
            $fileSizeString = number_format($bytes / 1024, 0) . ' KB';
        }

        // Validations
        if ($bytes > $maxFileSize) {
            $feedback = "<div class='alert alert-danger'>The file is too large. Maximum size allowed is 5MB.</div>";
        } elseif (!in_array($file['type'], $allowedMime)) {
            $feedback = "<div class='alert alert-danger'>Invalid file format! Only PDF, Word Documents, and Images are allowed.</div>";
        } else {
            // Setup secure file name
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $secureFilename = 'academic_doc_' . uniqid() . '.' . $fileExt;
            $destination = $uploadDir . $secureFilename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                try {
                    // START DATABASE TRANSACTION
                    $pdo->beginTransaction();

                    // Step A: Insert into admissions_enquiries[cite: 1]
                    $enquirySql = "INSERT INTO admissions_enquiries (parent_name, parent_phone, parent_email, student_name, entry_level, current_school, ple_aggregate, message, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt1 = $pdo->prepare($enquirySql);
                    $stmt1->execute([$parent_name, $parent_phone, $parent_email, $student_name, $entry_level, $current_school, $ple_aggregate, $message, $status]);

                    // Get the ID of the enquiry we just created
                    $newEnquiryId = $pdo->lastInsertId();

                    // Step B: Insert into admissions_documents (Linked via enquiry_id)[cite: 1]
                    $docSql = "INSERT INTO admissions_documents (title, description, filename, file_size, level, enquiry_id) 
                               VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt2 = $pdo->prepare($docSql);
                    $stmt2->execute([$doc_title, $doc_description, $destination, $fileSizeString, $entry_level, $newEnquiryId]);

                    // Commit changes to database
                    $pdo->commit();

                    $feedback = "<div class='alert alert-success'>Application submitted successfully! Your details and files have been saved.</div>";
                } catch (\PDOException $e) {
                    // If anything goes wrong, rollback transactions
                    $pdo->rollBack();
                    $feedback = "<div class='alert alert-danger'>Application Failed: Could not process database records. " . htmlspecialchars($e->getMessage()) . "</div>";
                }
            } else {
                $feedback = "<div class='alert alert-danger'>Failed to move the uploaded file to server storage.</div>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form</title>
 <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="assets/css/admissions.css">


<link rel="stylesheet" href="assets/css/index.css">

</head>
<body>  

    <!-- Unified Header Engine Container -->
    <div class="header-container" id="mainHeader">
        <div class="utility-bar">
            <div>
                <span>Call: <b>+256 700 380950</b></span>
                <span>Mail: <b>sthenryscollegenamugongo2@gmail.com</b></span>
            </div>
        </div>
        <header style="height: 95px;" class="main-navbar">
            <div class="navbar-brand-pane">
          
                  <img style="width: 80px;border-radius: 500%;background-color: white;height: 80px;margin-left: 30px; box-shadow: 0 10px 30px rgba(0, 2, 10, 0.754)    ;" src="assets/images/logoo.svg" alt="">
                <h2 >St. Henry’s College Namugongo</h2>  
                    
            
          
            </div>
        </header>
    </div>

    <!-- Minimal Overlay Panel Drawer Nav -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="app-sidebar" id="sidebarPanel">
        <button class="close-sidebar-btn" id="menuCloseBtn">&times;</button>
        <nav class="navigation-menu">
                 <img style="width: 100px;border-radius: 500%;background-color: white;height: 100px;margin-left: 30px;" src="assets/images/logoo.svg" alt="">
            <a href="index.html" class="active">Overview Home</a>
            <a href="about.html">About us</a>
            <a href="admissions.html">Admissions Panel</a>
            <a href="contact.html">Contact us</a>
        </nav>



    </aside> 
    <br>  <br>

    















  









  
<section  class="contact">

<div class="heading">







    <div  class="banner-container">
        <div class="banner-track">
            <!-- Repeating the string across the track creates the seamless loop loop -->
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
            <span class="banner-text">ADMISSIONS OPEN <span class="highlight">APPLY NOW</span> &nbsp;&bull;&nbsp; </span>
        </div>
    </div>




<h1>Apply Now</h1>  



























</div>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Application Form</title>
  
</head>
<body>

<div class="container">


    <!-- Feedback Message -->
    <?= $feedback ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            
            <!-- SECTION 1: Parent Information -->
            <div class="span-2 form-section-title">Parent / Guardian Details</div>

            <div class="form-group">
                <label for="parent_name">Parent Name *</label>
                <input type="text" id="parent_name" name="parent_name" class="form-control" placeholder="e.g. Mukasa John" required>
            </div>

            <div class="form-group">
                <label for="parent_phone">Phone Number *</label>
                <input type="tel" id="parent_phone" name="parent_phone" class="form-control" placeholder="e.g. +256700000000" required>
            </div>

            <div class="form-group span-2">
                <label for="parent_email">Email Address</label>
                <input type="email" id="parent_email" name="parent_email" class="form-control" placeholder="e.g. john.mukasa@gmail.com">
            </div>


            <!-- SECTION 2: Student Information -->
            <div class="span-2 form-section-title">Student Academic Profile</div>

            <div class="form-group">
                <label for="student_name">Student Full Name *</label>
                <input type="text" id="student_name" name="student_name" class="form-control" placeholder="e.g. Gloria Mukasa" required>
            </div>

            <div class="form-group">
                <label for="entry_level">Entry Class *</label>
                <select id="entry_level" name="entry_level" class="form-control">
                    <option value="S1">Senior One (S1)</option>
                    <option value="S5">Senior Five (S5)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="current_school">Previous School</label>
                <input type="text" id="current_school" name="current_school" class="form-control" placeholder="e.g. Kampala Parents School">
            </div>

            <div class="form-group">
                <label for="ple_aggregate">PLE / UCE Aggregate</label>
                <input type="number" id="ple_aggregate" name="ple_aggregate" class="form-control" placeholder="e.g. 8" min="4" max="36">
            </div>

            <div class="form-group span-2">
                <label for="message">Extra Notes / Message</label>
                <textarea id="message" name="message" class="form-control" rows="3" placeholder="Any special remarks..."></textarea>
            </div>


            <!-- SECTION 3: Academic File Upload -->
            <div class="span-2 form-section-title">Submit Academic Document</div>

            <div class="form-group">
                <label for="doc_title">Document Title</label>
                <input type="text" id="doc_title" name="doc_title" class="form-control" placeholder="e.g. PLE Results Slip, Report Card" required>
            </div>

            <div class="form-group">
                <label for="doc_description">Document Description (Optional)</label>
                <input type="text" id="doc_description" name="doc_description" class="form-control" placeholder="e.g. Scan of PLE slips from UNEB">
            </div>

            <div class="form-group span-2">
                <label>Upload Document File *</label>
                <div class="file-input-wrapper">
                    <div class="file-upload-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    </div>
                    <p id="file-label-text">Click or drag your document here</p>
                    <span>Allowed formats: PDF, Word Doc, Images (Max 5MB)</span>
                    <input type="file" id="academic_file" name="academic_file" required onchange="updateFileName(this)">
                </div>
            </div>

        </div>

        <button type="submit" class="btn-submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            Submit Application
        </button>
    </form>
</div>

<script>
    function updateFileName(input) {
        const textElement = document.getElementById('file-label-text');
        if (input.files && input.files[0]) {
            textElement.innerHTML = `Selected: <strong>${input.files[0].name}</strong>`;
            textElement.style.color = "var(--brand-color)";
        } else {
            textElement.innerHTML = "Click or drag your document here";
            textElement.style.color = "var(--text-muted)";
        }
    }
</script>











</body>
</html>











<style>

  <style>
        :root {
            --bg-color: #f1f5f9;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --text-dark: #0f172a;
            --border-color: #e2e8f0;
            --brand-color: #2563eb;
            --brand-hover: #1d4ed8;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 750px;
            width: 100%;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            border-top: 6px solid var(--brand-color);
            box-shadow: var(--shadow);
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .header p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
            .span-2 {
                grid-column: span 2;
            }
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--brand-color);
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 8px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--text-dark);
            background-color: #fafbfc;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .file-input-wrapper {
            position: relative;
            border: 2px dashed var(--border-color);
            border-radius: 10px;
            padding: 30px 20px;
            text-align: center;
            background-color: #fafbfc;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-input-wrapper:hover {
            background-color: #eff6ff;
            border-color: var(--brand-color);
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-icon {
            color: var(--brand-color);
            margin-bottom: 12px;
        }

        .file-input-wrapper p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .file-input-wrapper span {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .btn-submit {
            width: 100%;
            background-color:rgb(4, 4, 48);
            color: #ffffff;
            border: none;
            padding: 15px;
            font-size: 1.05rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
            margin-top: 25px;
        }

        .btn-submit:hover {
            background-color: var(--brand-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-weight: 500;
            font-size: 0.95rem;
            text-align: center;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
    </style>







</style>


































</form>

</div>

</div>

</section>

</body>
</html>
