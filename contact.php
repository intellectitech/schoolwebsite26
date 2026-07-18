<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>


<link rel="stylesheet" href="assets/css/index.css">

<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    


    <link rel="stylesheet" href="assets/css/contact.css">


<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">


</head>
<body>
    

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

    



    <div style="display: flex;">

    </div>


<div class="info-card">
    <div class="info-header">
        <h2>Contact Information</h2>
        <p>Find us on campus or reach out through our official digital channels.</p>
    </div>

    <div class="info-body">
        <!-- Contact Item 1 -->
        <div class="info-item">
            <div class="icon-wrapper">
                <i class='bx bx-map-pin'></i>
            </div>
            <div>
                <h4>Our College</h4>
                <p>St. Henry’s College Namugongo</p>
            </div>
        </div>

        <!-- Contact Item 2 -->
        <div class="info-item">
            <div class="icon-wrapper">
                <i class='bx bx-phone'></i>
            </div>
            <div>
                <h4>Call Center</h4>
                <p> +256 700 380950 </p>
            </div>
        </div>

        <!-- Contact Item 3 -->
        <div class="info-item">
            <div class="icon-wrapper">
                <i class='bx bx-envelope'></i>
            </div>
            <div>
                <h4>Email Desk</h4>
                <p> sthenryscollegenamugongo2@gmail.com</p>
            </div>
        </div>
    </div>


       <img style="width: 100px;border-radius: 500%;background-color: white;height: 100px;margin-left: 30px;" src="assets/images/logoo.svg" alt="">



     


       <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.6587635292333!2d32.65171717424364!3d0.3837130639908483!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177db9907f1396a5%3A0x6280436d4bc3f139!2sSt.%20Henry&#39;s%20College%20Namugongo!5e0!3m2!1sen!2sug!4v1710000000000!5m2!1sen!2sug" 
                width="100%" 
                height="220" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>



















    <!-- Social Handles Grid -->
    <div class="social-section">
        <h4>Connect With Us</h4>
        <div class="social-grid">
            <!-- X / Twitter -->
            <a href="https://x.com" class="social-link" target="_blank" aria-label="X">
                <i class='bx bxl-twitter'></i>
            </a>

            <!-- Facebook -->
            <a href="https://facebook.com" class="social-link" target="_blank" aria-label="Facebook">
                <i class='bx bxl-facebook'></i>
            </a>

            <!-- Instagram -->
            <a href="https://instagram.com" class="social-link" target="_blank" aria-label="Instagram">
                <i class='bx bxl-instagram'></i>
            </a>

            <!-- LinkedIn -->
            <a href="https://linkedin.com" class="social-link" target="_blank" aria-label="LinkedIn">
                <i class='bx bxl-linkedin'></i>
            </a>
        </div>
    </div>
</div>























<div class="contact-card">
    <div class="contact-header">
        <h2>Get in Touch</h2>
        <p>Have questions? Drop us a message and we'll reply as soon as possible.</p>
    </div>

    <?php if (!empty($messageStatus)): ?>
        <div class="alert <?= $statusClass ?>">
            <?= htmlspecialchars($messageStatus) ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Your Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" placeholder="john@example.com" required>
            </div>

            <div class="form-group full-width">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>" placeholder="+256...">
            </div>

            <div class="form-group full-width">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($subject ?? '') ?>" placeholder="What is this regarding?" required>
            </div>

            <div class="form-group full-width">
                <label for="message">Message *</label>
                <textarea id="message" name="message" class="form-control" placeholder="Write your details here..." required><?= htmlspecialchars($msgContent ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit">Send Message</button>
    </form>
</div>

































    <style>
        :root {
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #475569;
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --border-color: #cbd5e1;
            
            --success-bg: #d1fae5;
            --success-text: #065f46;
            --error-bg: #fee2e2;
            --error-text: #991b1b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .contact-card {
            background: var(--card-bg);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            margin-top: 100px;
            margin-bottom:50px;
            height:50%;

            
        }

        .contact-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .contact-header h2 {
            font-size: 1.75rem;
            color: var(--text-main);
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .contact-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.95rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: #f8fafc;
            color: var(--text-main);
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 25px;
            line-height: 1.4;
        }
        .alert-success { background-color: var(--success-bg); color: var(--success-text); }
        .alert-error { background-color: var(--error-bg); color: var(--error-text); }

        @media (max-width: 500px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .full-width {
                grid-column: span 1;
            }
        }



.info-card {
    background: var(--card-bg);
    max-width: 600px;
    width: 100%;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
       margin-bottom: 180px;
       margin-top:250px
          

       
    
}

.info-header {
    margin-bottom: 30px;
}

.info-header h2 {
    font-size: 1.75rem;
    color: var(--text-dark);
    font-weight: 800;
    margin-bottom: 8px;
    letter-spacing: -0.025em;
}

.info-header p {
    color: var(--text-muted);
    font-size: 0.95rem;
    line-height: 1.5;
}

.info-body {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-bottom: 35px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.icon-wrapper {
    background-color: #eff6ff;
    color: var(--primary-color);
    padding: 10px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Give Boxicons custom sizing inside standard details wrapper */
.icon-wrapper i {
    font-size: 1.35rem;
}

.info-item h4 {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 4px;
}

.info-item p {
    font-size: 0.9rem;
    color: var(--text-muted);
    line-height: 1.4;
}

.social-section {
    border-top: 1px solid var(--border-color);
    padding-top: 25px;
}

.social-section h4 {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
    margin-bottom: 15px;
}

.social-grid {
    display: flex;
    gap: 12px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background-color: #f8fafc;
    color: var(--text-muted);
    transition: all 0.2s ease-in-out;
    text-decoration: none;
}

/* Adjust font size for boxicon branding links */
.social-link i {
    font-size: 1.25rem;
}

.social-link:hover {
    color: var(--primary-color);
    border-color: var(--primary-color);
    background-color: #eff6ff;
    transform: translateY(-2px);
}



























    </style>












  

</body>
</html>