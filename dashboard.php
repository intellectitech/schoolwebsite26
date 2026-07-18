<?php
session_start();

include'database.php';

$message = "";
$message_type = "";

$categories = [];
$cat_query = "SELECT id, name FROM news_categories ORDER BY name ASC";
$cat_result = $conn->query($cat_query);
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $title = trim($_POST['title']);
    $excerpt = trim($_POST['excerpt']);
    $body = trim($_POST['body']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    $author_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    $views = 0;

    if (!empty($title) && !empty($body)) {
        
        $featured_image = "";
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['featured_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid('news_', true) . '.' . $ext;
                $upload_dir = 'uploads/news/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_dir . $new_filename)) {
                    $featured_image = $upload_dir . $new_filename;
                }
            }
        }

        $current_timestamp = date("Y-m-d H:i:s");
        $published_at = $is_published ? $current_timestamp : null;

        $stmt = $conn->prepare("INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, author_id, views, is_published, is_featured, published_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssiiiisss", $category_id, $title, $slug, $excerpt, $body, $featured_image, $author_id, $views, $is_published, $is_featured, $published_at, $current_timestamp, $current_timestamp);

        if ($stmt->execute()) {
            $message = "News article published successfully!";
            $message_type = "success";
        } else {
            if ($conn->errno == 1062) {
                $message = "An article with this title or slug already exists.";
            } else {
                $message = "Error saving article: " . $conn->error;
            }
            $message_type = "error";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all mandatory fields (Title and Body).";
        $message_type = "error";
    }
}
$conn->close();

?>



<?php
// 1. Establish database connection
$host     = '127.0.0.1';
$db       = 'school_website_db'; // From your schema[cite: 1]
$user     = 'root';              // Replace with your username
$password = '';                  // Replace with your password
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

// 2. Determine when you last loaded/opened the tab
// If no cookie exists, fallback to counting messages from the last 24 hours[cite: 1]
$last_viewed_time = isset($_COOKIE['last_viewed_enquiries']) ? $_COOKIE['last_viewed_enquiries'] : date('Y-m-d H:i:s', strtotime('-1 day'));

// Update the cookie timestamp to right now (expires in 30 days)
setcookie('last_viewed_enquiries', date('Y-m-d H:i:s'), time() + (86400 * 30), "/");

// 3. Count only the new messages sent AFTER your last tab visit
try {
    $countQuery = $pdo->prepare("SELECT COUNT(*) FROM admissions_enquiries WHERE created_at > ?"); // From your schema[cite: 1]
    $countQuery->execute([$last_viewed_time]);
    $unreadCount = $countQuery->fetchColumn();
} catch (\PDOException $e) {
    $unreadCount = 0; // Fallback
}
?>



















<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>

<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/dashboard.css">

</head>

<body>

<!-- ================= SIDEBAR ================= -->

<aside style="background:#030637;   " class="sidebar" id="sidebar">

<div class="logo">

        <img style="width: 70px;border-radius: 500%;background-color: white;height: 70px;margin-left: 30px; box-shadow: 0 10px 30px rgba(0, 2, 10, 0.754)    ;" src="assets/images/logoo.svg" alt="">

<span>ADMIN PANEL</span>

</div>

<ul class="menu">

<li class="active" data-page="dashboard">

<i class='bx bxs-dashboard'></i>

<span>Dashboard</span>

</li>

<li data-page="students">

<i class='bx bx-user'></i>

<span>Students</span>

</li>

<li data-page="teachers">

<i class='bx bx-book-reader'></i>

<span>Teachers</span>

</li>

<li data-page="classes">

<i class='bx bx-buildings'></i>

<span>Classes</span>

</li>

<li data-page="Admissions">

<i class='bx bxs-file-doc'></i>



<span>Admissions</span>

</li>



<li data-page="NEWS & EVENTS">

<i class='bx bx-news'></i>

<span>News & Events</span>

</li>

<li data-page="gallery">

<i class='bx bx-image'></i>

<span>Gallery</span>

</li>

<li data-page="messages">

<i class='bx bx-envelope'></i>

<span>Messages</span>

</li>

<li data-page="reports">

<i class='bx bx-bar-chart'></i>

<span>Reports</span>

</li>

<li data-page="settings">

<i class='bx bx-cog'></i>

<span>Settings</span>

</li>

<li>

<i class='bx bx-log-out'></i>

<span>Logout</span>

</li>

</ul>

</aside>

<!-- ================= MAIN ================= -->

<main class="main">

<!-- NAVBAR -->

<header class="navbar">

<div class="left">

<button id="menuBtn">

<i class='bx bx-menu'></i>

</button>

<h2 id="pageTitle">

Dashboard

</h2>

</div>

<div class="search">

<i class='bx bx-search'></i>

<input type="text" placeholder="Search anything...">

</div>

<div class="right">

<button>

<i class='bx bx-bell'></i>



<!-- Dynamically prints the unread count in your span element -->
<span class="count"><?= htmlspecialchars($unreadCount) ?></span>

</button>

<button>

<i class='bx bx-envelope'></i>

<span class="count">3</span>

</button>

<button id="themeToggle">

<i class='bx bx-moon'></i>

</button>

<div class="profile">

<img src="https://i.pravatar.cc/45">

<div>

<h4>Admin</h4>

<p>Super Admin</p>

</div>

</div>

</div>

</header>

<!-- ================= CONTENT ================= -->

<div class="content">

<!-- DASHBOARD -->

<section class="page active" id="dashboard">

<h1>Dashboard Overview</h1>

<div class="cards">

<div class="card">

<i class='bx bx-user'></i>

<h2 style=" font-size: 20px;" class="counter">1250</h2>

<p>Total Students</p>

</div>

<div class="card">

<i class='bx bx-book'></i>

<h2  style=" font-size: 20px;"  class="counter">145</h2>

<p>equiry messages</p>

</div>

<div class="card">

<i class='bx bx-user-plus'></i>

<h2 style=" font-size: 20px;"  class="counter">42</h2>

<p>Admissions</p>

</div>



</div>

<div class="grid">

<div class="panel">

<h3>Recent Admissions</h3>

<table>

<thead>

<tr>

<th>Name</th>

<th>Class</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<tr>

<td>John Doe</td>

<td>S4</td>

<td><span class="success">Approved</span></td>

</tr>

<tr>

<td>Mary Jane</td>

<td>S2</td>

<td><span class="pending">Pending</span></td>

</tr>

<tr>

<td>Peter Smith</td>

<td>P7</td>

<td><span class="success">Approved</span></td>

</tr>

<tr>

<td>David Brown</td>

<td>S1</td>

<td><span class="danger">Rejected</span></td>

</tr>

</tbody>

</table>

</div>

<div class="panel">

<h3>Quick Actions</h3>

<div  class="actions">


<a href="newsletters.php">
<button>Newsletters   subscribers</button>
</a>

<button>Add Teacher</button>

<button>Send Notice</button>

<button>Upload Results</button>

<button>Create Event</button>

<button>Generate Report</button>

</div>

</div>

</div>

</section>

<!-- STUDENTS -->

<section class="page" id="students">

<h1>Students</h1>

<p>Manage students here.</p>



    

























































</section>

</div>



<!-- TEACHERS -->

<section class="page" id="teachers">

<h1>Teachers</h1>

<p>Manage teachers here.</p>

</section>

<!-- CLASSES -->

<section class="page" id="classes">
<h1>classes</h1>











</section>



<!-- Admissions -->

<section class="page" id="Admissions">

<h1>Admissions</h1>
<div class="right">

<button>

<i class='bx bx-bell'></i>



<!-- Dynamically prints the unread count in your span element -->
<span class="count"><?= htmlspecialchars($unreadCount) ?></span>

</button>
</div>
 



  <a href="read.php">


  <button  style="width: 200px;"   type="submit" class="btn-submit">READ ADMISSIONS
  </button>

  </a>

<br> <br> <br> <br>





  <a href="check.php">

  
  <button style="width: 200px;"  type="submit" class="btn-submit">APPROVE ADMISSION DOCUMENTS</button>

  </a>










</section>










<!-- nesws and events -->

<section class="page"  id="NEWS & EVENTS">


<?php if (!empty($message)): ?>
        <div class="alert-popup alert-<?php echo $message_type; ?>">
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <h2>Publish school News</h2>

        <form action="dashboard.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="title">Article Title *</label>
                <input type="text" id="title" name="title" required placeholder="e.g., title">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">News Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">events</option>
                         <option value="">sports</option>
                          <option value="">other</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="featured_image">Featured Banner Image</label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*" style="padding-top: 8px;">
                </div>
            </div>

            <div class="form-group">
                <label for="excerpt">Brief Summary (Excerpt)</label>
                <textarea id="excerpt" name="excerpt" rows="2" placeholder="A short catch-phrase or introduction summary..."></textarea>
            </div>

            <div class="form-group">
                <label for="body">Main Content Body *</label>
                <textarea id="body" name="body" rows="8" required placeholder="Write the full news details here..."></textarea>
            </div>

            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_featured" value="1"> 
                    Set as Featured Post (Top Post)
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="is_published" value="1" checked> 
                    Publish Immediately
                </label>
            </div>

            <button type="submit" class="btn-submit">Save & Publish Post</button>
        </form>
    </div>































































</section>

<!-- GALLERY -->

<section class="page" id="gallery">



<div class="form-container">
    <h2>Add Photo to Gallery</h2>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="gallery.php" method="POST" enctype="multipart/form-data">
        
        <!-- Choose between Existing Album or New Album -->
        <label>Where should this photo go?</label>
        <div class="toggle-container">
            <div class="toggle-option">
                <input type="radio" id="choose_existing" name="album_selection_type" value="existing" checked>
                <label for="choose_existing">Existing Album</label>
            </div>
            <div class="toggle-option">
                <input type="radio" id="choose_new" name="album_selection_type" value="new">
                <label for="choose_new">+ Create New Album</label>
            </div>
        </div>

        <!-- Section A: Existing Album Dropdown[cite: 1] -->
        <div id="existing_album_group" class="form-group">
            <label for="album_id">Select Album *</label>
            <select name="album_id" id="album_id">
                <option value="">-- Choose Album --</option>
                <?php foreach ($albums as $album): ?>
                    <option value="<?php echo $album['id']; ?>"><?php echo htmlspecialchars($album['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Section B: Dynamic Create New Album Form (Hidden by default)[cite: 1] -->
        <div id="new_album_group" class="new-album-section hidden">
            <h3>New Album Details</h3>
            <div class="form-group">
                <label for="new_album_name">Album Name *</label>
                <input type="text" name="new_album_name" id="new_album_name" placeholder="e.g. Sports Day 2026">
            </div>
            <div class="form-group">
                <label for="new_album_desc">Description / Theme (Optional)</label>
                <textarea name="new_album_desc" id="new_album_desc" placeholder="Brief description of the album's contents..."></textarea>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 25px 0;">

        <!-- Photo Section -->
        <div class="form-group">
            <label for="photo">Choose Photo *</label>
            <input type="file" name="photo" id="photo" accept="image/*" required>
            <p class="info-text">Supports JPG, PNG, GIF, or WEBP formats.</p>
        </div>

        <div class="form-group">
            <label for="caption">Caption (Optional)</label>
            <input type="text" name="caption" id="caption" placeholder="Describe this photo..." maxlength="300">
        </div>

        <div class="form-group">
            <label for="sort_order">Sort Order Position</label>
            <input type="number" name="sort_order" id="sort_order" value="0" min="0">
        </div>

        <button type="submit" class="btn-submit">Publish Gallery Entry</button>
    </form>
</div>

<!-- JavaScript to handle toggling sections dynamically -->
<script>
    const radioExisting = document.getElementById('choose_existing');
    const radioNew = document.getElementById('choose_new');
    const existingGroup = document.getElementById('existing_album_group');
    const newGroup = document.getElementById('new_album_group');
    const albumSelect = document.getElementById('album_id');
    const newAlbumName = document.getElementById('new_album_name');

    function toggleFormSections() {
        if (radioExisting.checked) {
            existingGroup.classList.remove('hidden');
            newGroup.classList.add('hidden');
            // Set fields as required accordingly
            albumSelect.required = true;
            newAlbumName.required = false;
        } else {
            existingGroup.classList.add('hidden');
            newGroup.classList.remove('hidden');
            // Set fields as required accordingly
            albumSelect.required = false;
            newAlbumName.required = true;
        }
    }

    // Bind event listeners
    radioExisting.addEventListener('change', toggleFormSections);
    radioNew.addEventListener('change', toggleFormSections);

    // Run on initial load to match starting state
    toggleFormSections();
</script>























































</section>

<!-- MESSAGES -->

<section class="page" id="messages">

<h1>Messages</h1>

<p>Messaging center.</p>

</section>

<!-- REPORTS -->

<section class="page" id="reports">

<h1>Reports</h1>

<p>Generate reports.</p>

</section>

<!-- SETTINGS -->

<section class="page" id="settings">

<h1>Settings</h1>

<p>System settings.</p>

</section>

</div>

</main>








<script src="assets/css/script.js"></script>


<style>


 * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f0f2f5;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .form-container {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 700px;
        }
        .form-container h2 {
            margin-bottom: 25px;
            color: #1565C0;
            font-size: 26px;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #444444;
            font-size: 14px;
            font-weight: 600;
        }
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cccccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #1565C0;
            outline: none;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .checkbox-group {
            display: flex;
            gap: 25px;
            margin: 20px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            color: #333;
            cursor: pointer;
            font-weight: 500;
        }
        .checkbox-label input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #1565C0;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #0d47a1;
        }
        .alert-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: #ffffff;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.5s ease forwards, fadeOut 0.5s ease 3.5s forwards;
            z-index: 9999;
        }
        .alert-success { background-color: #2e7d32; border-left: 6px solid #1b5e20; }
        .alert-error { background-color: #d32f2f; border-left: 6px solid #b71c1c; }
        
        @keyframes slideIn {
            from { transform: translateX(120%); }
            to { transform: translateX(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(120%); visibility: hidden; }
        }
    




</style>
















</body>
</html>