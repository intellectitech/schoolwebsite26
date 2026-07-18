<?php
// 1. Database Connection Configuration
$host     = '127.0.0.1';
$db       = 'school_website_db'; // From your schema[cite: 1]
$user     = 'root';              // Replace with your database username
$password = '';                  // Replace with your database password
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

// 2. Form & File Upload Handling
$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $level       = $_POST['level'] ?? 'ALL'; // S1, S5, or ALL[cite: 1]
    
    // File validation configurations
    $uploadDir   = 'uploads/documents/';
    $allowedMime = [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png'
    ];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB limit

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (empty($title)) {
        $feedback = "<div class='alert alert-danger'>Please provide a document title.</div>";
    } elseif (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
        $feedback = "<div class='alert alert-danger'>Please select a valid file to upload.</div>";
    } else {
        $file = $_FILES['document_file'];
        
        // Dynamic file size conversion (e.g., "1.2 MB" or "450 KB")
        $bytes = $file['size'];
        if ($bytes >= 1048576) {
            $fileSizeString = number_format($bytes / 1048576, 2) . ' MB';
        } else {
            $fileSizeString = number_format($bytes / 1024, 0) . ' KB';
        }

        // Validate File Size
        if ($bytes > $maxFileSize) {
            $feedback = "<div class='alert alert-danger'>File size exceeds the 5MB limit.</div>";
        }
        // Validate MIME type
        elseif (!in_array($file['type'], $allowedMime)) {
            $feedback = "<div class='alert alert-danger'>Invalid file type! Only PDF, Word Documents, and Images are allowed.</div>";
        } else {
            // Generate a secure, unique file name to avoid duplicates
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $secureFilename = 'doc_' . uniqid() . '.' . $fileExt;
            $destination = $uploadDir . $secureFilename;

            // Move the file to the destination directory
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                try {
                    // Save file reference into your table `admissions_documents`[cite: 1]
                    $stmt = $pdo->prepare("
                        INSERT INTO admissions_documents (title, description, filename, file_size, level) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([$title, $description, $destination, $fileSizeString, $level]);
                    $feedback = "<div class='alert alert-success'>Academic file uploaded and recorded successfully!</div>";
                } catch (\PDOException $e) {
                    $feedback = "<div class='alert alert-danger'>Database error: Could not save document record.</div>";
                }
            } else {
                $feedback = "<div class='alert alert-danger'>Error: Failed to save the file onto the server.</div>";
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
    <title>Submit Academic Document</title>
    <style>
        /* Modern Theme variables */
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --text-dark: #0f172a;
            --border-color: #e2e8f0;
            --brand-blue: #2563eb;
            --brand-hover: #1d4ed8;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
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
            padding: 20px;
        }

        .form-container {
            max-width: 550px;
            width: 100%;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            border-top: 6px solid var(--brand-blue);
            box-shadow: var(--shadow);
            padding: 40px 30px;
        }

        .form-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .form-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Form Controls styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 8px;
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
            border-color: var(--brand-blue);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* Dynamic level dropdown select custom styling */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 40px;
        }

        /* Styled drag and drop or classic file upload box */
        .file-input-wrapper {
            position: relative;
            border: 2px dashed var(--border-color);
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            background-color: #fafbfc;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-input-wrapper:hover {
            background-color: #eff6ff;
            border-color: var(--brand-blue);
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
            color: var(--brand-blue);
            margin-bottom: 10px;
        }

        .file-input-wrapper p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .file-input-wrapper span {
            font-size: 0.75rem;
            color: #94a3b8;
            display: block;
            margin-top: 4px;
        }

        /* Interactive submit button styling */
        .btn-submit {
            width: 100%;
            background-color: var(--brand-blue);
            color: #ffffff;
            border: none;
            padding: 14px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: var(--brand-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alert styling */
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
            font-size: 0.9rem;
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
</head>
<body>

<div class="form-container">
    <div class="form-header">
        <h1>Submit Academic File</h1>
        <p>Upload files or circulars to attach to school admission records.</p>
    </div>

    <!-- Feedback Banner -->
    <?= $feedback ?>

    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <!-- 1. Document Title -->
        <div class="form-group">
            <label for="title">Document Title</label>
            <input type="text" id="title" name="title" class="form-control" placeholder="e.g. S1 Circular Letter, Medical Form" required>
        </div>

        <!-- 2. Target Academic Level -->
        <div class="form-group">
            <label for="level">Applicable Student Level</label>
            <select id="level" name="level" class="form-control">
                <option value="ALL">All Applicants (S1 & S5)</option>
                <option value="S1">Senior One (S1) Only</option>
                <option value="S5">Senior Five (S5) Only</option>
            </select>
        </div>

        <!-- 3. Brief Description -->
        <div class="form-group">
            <label for="description">Short Description</label>
            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief explanation about this file..."></textarea>
        </div>

        <!-- 4. Modern File Upload Input -->
        <div class="form-group">
            <label>Select Document File</label>
            <div class="file-input-wrapper">
                <div class="file-upload-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                </div>
                <p id="file-label-text">Drag & drop your file here or click to browse</p>
                <span>Allowed file types: PDF, DOC, DOCX, JPEG, PNG (Max 5MB)</span>
                <input type="file" id="document_file" name="document_file" required onchange="updateFileName(this)">
            </div>
        </div>

        <!-- 5. Submit Button -->
        <button type="submit" class="btn-submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Upload Document
        </button>
    </form>
</div>

<script>
    // Simple utility function to display the selected file's name inside the box
    function updateFileName(input) {
        const textElement = document.getElementById('file-label-text');
        if (input.files && input.files[0]) {
            textElement.innerHTML = `Selected File: <strong>${input.files[0].name}</strong>`;
            textElement.style.color = "var(--brand-blue)";
        } else {
            textElement.innerHTML = "Drag & drop your file here or click to browse";
            textElement.style.color = "var(--text-muted)";
        }
    }
</script>

</body>
</html>