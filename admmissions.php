<?php
// 1. Database Configuration
$host     = '127.0.0.1';
$db       = 'school_website_db';
$user     = 'root';            // Default phpMyAdmin user
$password = '';                // Default phpMyAdmin password
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_admmission'])) {
    
    try {
        // 2. Establish Connection
        $pdo = new PDO($dsn, $user, $password, $options);

        // 3. Bind Live Form Data using ternary fallbacks for empty inputs
        $data = [
            'first_name'             => $_POST['student_first_name'] ?? '',
            'last_name'              => $_POST['student_last_name'] ?? '',
            'gender'                 => $_POST['student_gender'] ?? '',
            'date_of_birth'          => $_POST['student_dob'] ?? null, 
            'section_preference'     => ($_POST['applying_class'] ?? '') . " - " . ($_POST['boarding_or_day'] ?? ''),
            'previous_school'        => $_POST['previous_school'] ?? '',
            'parent_name'            => $_POST['parent_name'] ?? '',
            'primary_phone'          => $_POST['parent_phone_primary'] ?? '',
            'alternative_phone'      => $_POST['parent_phone_secondary'] ?? '',
            'residential_address'    => $_POST['parent_residence'] ?? '',
            'medical_conditions'     => $_POST['medical_history'] ?? 'None',
            'special_considerations' => $_POST['parent_comments'] ?? 'None',
            'application_date'       => date('Y-m-d H:i:s')
        ];

        // 4. Prepared SQL Statement
        $sql = "INSERT INTO admissions (
                    first_name, 
                    last_name, 
                    gender, 
                    date_of_birth, 
                    section_preference, 
                    previous_school, 
                    parent_name, 
                    primary_phone, 
                    alternative_phone, 
                    residential_address, 
                    medical_conditions, 
                    special_considerations, 
                    application_date
                ) VALUES (
                    :first_name, 
                    :last_name, 
                    :gender, 
                    :date_of_birth, 
                    :section_preference, 
                    :previous_school, 
                    :parent_name, 
                    :primary_phone, 
                    :alternative_phone, 
                    :residential_address, 
                    :medical_conditions, 
                    :special_considerations, 
                    :application_date
                )";

        // 5. Execute the query safely
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        // Success Alert
        echo "<script>alert('Thank you! The admission application has been safely submitted.');</script>";

    } catch (\PDOException $e) {
        // Handle database errors securely
        echo "<div style='color:red; padding:15px; background:#fff1f1;'>Submission Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>









































<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Admissions | Bbina Islamic Primary School</title>
    <!-- Font Awesome for Icons (Working CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Link to external CSS stylesheet -->
    <link rel="stylesheet" href="style.css">
    
    <!-- WhatsApp Floating Widget Custom Styles -->
    <style>
        .whatsapp-floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #25D366;
            color: #fff;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .whatsapp-floating-btn:hover {
            transform: scale(1.1);
            background-color: #128C7E;
        }
        .whatsapp-floating-btn i {
            font-size: 32px;
        }
        .whatsapp-tooltip {
            position: absolute;
            right: 75px;
            background-color: #111;
            color: #fff;
            padding: 8px 14px;
            font-size: 0.85rem;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            font-family: sans-serif;
        }
        .whatsapp-floating-btn:hover .whatsapp-tooltip {
            opacity: 1;
            visibility: visible;
        }
        
        /* Inline styling helper for structural layout of form columns */
        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-section-divider {
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin: 30px 0 20px 0;
            color: var(--primary-color, #104e3b);
            font-size: 1.2rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- Top Utility Contacts Bar -->
    <div class="top-bar">
        <div class="top-bar-contact">
            <span><i class="fa-solid fa-location-dot"></i> Kampala, Uganda</span>
            <span><i class="fa-solid fa-envelope"></i> infob@bbina.ac.ug</span>
            <span><i class="fa-solid fa-phone"></i> +256 772605229</span>
        </div>
        <div class="top-bar-social">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-youtube"></i></a>
        </div>
    </div>

    <!-- Main Header & Navigation -->
    <header>
        <a href="index.html" class="logo-container">
            <div class="logo-placeholder-container">
                <img class="logo-placeholder" src="images/bina.png" alt="Bbina Islamic School Logo">
            </div>
            <div class="logo-text">
                <h1>Bbina Islamic Primary School</h1>
                <span>Primary</span>
            </div>
        </a>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="admmissions.php" class="active">Admmissions</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="news.php">News & Gallery</a></li>
                <li><a href="admmissions.php" class="btn-apply">Apply Now</a></li>
            </ul>
        </nav>
    </header>

    <!-- Inner Page Banner -->
     <section style="background-image: url('images/hero5.jpg');" class="hero-banner">
        <div class="hero-content">
            <h2>ONLINE ADMISSIONS (2026)</h2>
            <p>Begin your child's journey toward academic excellence and strong moral values today.</p>
        </div>
    </section>

    <!-- Main Container Content Layout Split -->
    <div class="main-layout">
        
        <!-- Left Column: Primary Admissions Application Form -->
        <main>
            <section class="welcome-box">
                <h3 class="section-title">Student Registration Form</h3>
                <p>Please complete this form carefully. Once submitted, our admissions desk will evaluate your request and contact you via phone or email to schedule an interview or physical assessment.</p>
                




                <!-- Email-Integrated Admissions Form -->
                <form action="admmissions.php" method="post" style="margin-top: 30px;">
                    
                    <!-- Section 1: Child details -->
                    <div class="form-section-divider"><i class="fa-solid fa-child"></i> 1. Student Personal Details</div>
                    
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">First Name <span style="color: red;">*</span></label>
                            <input type="text" name="student_first_name" required placeholder="e.g. Aisha" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Last Name (Surname) <span style="color: red;">*</span></label>
                            <input type="text" name="student_last_name" required placeholder="e.g. Nakato" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Gender <span style="color: red;">*</span></label>
                            <select name="student_gender" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Select --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Date of Birth <span style="color: red;">*</span></label>
                            <input type="date" name="student_dob" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Applying for Class <span style="color: red;">*</span></label>
                            <select name="applying_class" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Choose Class --</option>
                                <option value="Nursery">Nursery / Kindergarten</option>
                                <option value="Primary 1">Primary One (P.1)</option>
                                <option value="Primary 2">Primary Two (P.2)</option>
                                <option value="Primary 3">Primary Three (P.3)</option>
                                <option value="Primary 4">Primary Four (P.4)</option>
                                <option value="Primary 5">Primary Five (P.5)</option>
                                <option value="Primary 6">Primary Six (P.6)</option>
                                <option value="Primary 7">Primary Seven (P.7)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Section Preference <span style="color: red;">*</span></label>
                            <select name="boarding_or_day" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Select --</option>
                                <option value="Day Scholar">Day Scholar</option>
                                <option value="Boarding Section">Boarding Section</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 5px;">Previous School Attended (If applicable)</label>
                        <input type="text" name="previous_school" placeholder="e.g. Hillside Nursery School" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <!-- Section 2: Parent/Guardian details -->
                    <div class="form-section-divider"><i class="fa-solid fa-users"></i> 2. Parent / Guardian Information</div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Parent/Guardian Name <span style="color: red;">*</span></label>
                            <input type="text" name="parent_name" required placeholder="e.g. Musa Lwanga" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Primary Phone Number <span style="color: red;">*</span></label>
                            <input type="tel" name="parent_phone_primary" required placeholder="e.g. +256 772 000000" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Alternative Phone Number</label>
                            <input type="tel" name="parent_phone_secondary" placeholder="e.g. +256 701 000000" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group" style="grid-column: span 2;">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Email Address <span style="color: red;">*</span></label>
                            <input type="email" name="parent_email" required placeholder="parent@gmail.com" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Residential Address <span style="color: red;">*</span></label>
                            <input type="text" name="parent_residence" required placeholder="e.g. Bbina, Nakawa" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <!-- Section 3: Health and Special Requirements -->
                    <div class="form-section-divider"><i class="fa-solid fa-briefcase-medical"></i> 3. Medical & Additional Information</div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 5px;">Are there any medical conditions or allergies we should be aware of?</label>
                        <textarea name="medical_history" rows="3" placeholder="Specify if none..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 5px;">Special considerations or comments</label>
                        <textarea name="parent_comments" rows="3" placeholder="Any specific learning needs, physical requirements, or guidance notes..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                    </div>

                    <button type="submit" class="btn-apply" name="submit_admmission" style="border: none; padding: 14px 40px; font-size: 1.1rem; cursor: pointer; display: inline-block;">
                        Submit Application Form <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                    </button>
                </form>
            </section>
        </main>

        <!-- Right Column: Sidebar Office Coordinates & Guides -->
        <aside>
            
            <!-- Admission Help Desk Info -->
            <div class="sidebar-widget" style="background-color: #fafafa; border-top: 4px solid var(--primary-color, #104e3b);">
                <h4 class="sidebar-title">Admissions Support</h4>
                <p style="font-size: 0.95rem; line-height: 1.6; margin-bottom: 15px;">Need help with filling this application? Connect with our registration counselor directly.</p>
                <ul class="footer-contact-coordinates" style="padding-left: 0; list-style: none; font-size: 0.9rem;">
                    <li style="margin-bottom: 15px; display: flex; align-items: flex-start; gap: 10px;">
                        <i class="fa-solid fa-phone" style="color: var(--primary-color, #104e3b); margin-top: 3px;"></i>
                        <span><strong>Phone Support:</strong><br>+256 772605229</span>
                    </li>
                    <li style="margin-bottom: 15px; display: flex; align-items: flex-start; gap: 10px;">
                        <i class="fa-solid fa-envelope" style="color: var(--primary-color, #104e3b); margin-top: 3px;"></i>
                        <span><strong>Admissions Mail:</strong><br>admissions@bbina.ac.ug</span>
                    </li>
                </ul>
            </div>

            <!-- Documents Checklist -->
            <div class="sidebar-widget">
                <h4 class="sidebar-title">Interview Requirements</h4>
                <p style="font-size: 0.9rem; margin-bottom: 12px;">When coming for assessments/interviews, parents should bring the following physical documents:</p>
                <ul class="links-list" style="padding-left: 10px; font-size: 0.85rem;">
                    <li><i class="fa-solid fa-circle-check" style="color: var(--primary-color, #104e3b);"></i> Photocopy of Child's Birth Certificate</li>
                    <li><i class="fa-solid fa-circle-check" style="color: var(--primary-color, #104e3b);"></i> Child's Immunization Card</li>
                    <li><i class="fa-solid fa-circle-check" style="color: var(--primary-color, #104e3b);"></i> Report Card from previous school</li>
                    <li><i class="fa-solid fa-circle-check" style="color: var(--primary-color, #104e3b);"></i> 3 Passport photos of the child</li>
                    <li><i class="fa-solid fa-circle-check" style="color: var(--primary-color, #104e3b);"></i> Photocopy of parent's National ID / Passport</li>
                </ul>
            </div>

            <!-- Fees and Payments -->
            <div class="sidebar-widget" style="background-color: var(--primary-color, #104e3b); color: #fff; border-radius: 8px; padding: 20px;">
                <h4 class="sidebar-title" style="color: #fff; border-bottom: 1px solid rgba(255,255,255,0.2); margin-bottom: 15px;">Fees Payment Info</h4>
                <p style="font-size: 0.9rem; line-height: 1.6; margin-bottom: 12px;">All school fees installments must be channelled securely using standard platforms. We do not accept cash transactions inside the school offices.</p>
                <div style="background-color: rgba(255,255,255,0.1); padding: 10px; border-radius: 4px; margin-bottom: 10px; font-size: 0.85rem;">
                    <i class="fa-solid fa-wallet"></i> <strong>SchoolPay Portal Approved</strong>
                </div>
                <div style="background-color: rgba(255,255,255,0.1); padding: 10px; border-radius: 4px; font-size: 0.85rem;">
                    <i class="fa-solid fa-mobile-screen-button"></i> <strong>Mobile Money Pay Supported</strong>
                </div>
            </div>

        </aside>

    </div>

    <!-- PREMIUM ARCHITECTURE FOOTER COMPONENT -->
    <footer class="premium-footer">
        <div class="footer-matrix-grid">
            
            <!-- Column One: School Profile & Social Media -->
            <div class="footer-matrix-col school-profile-summary">
                <div class="footer-brand-logo">
                    <div class="brand-crest-mini">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3>Bbina Islamic Primary School</h3>
                </div>
                <p class="school-motto-statement">"Strive For Excellence"</p>
                <p class="school-description">A premier standard-setting educational hub in Uganda dedicated to fostering technical competency networks, analytical thinking skillsets, and empathetic leadership values.</p>
                <div class="social-icon-networks">
                    <a href="#" class="network-badge facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="network-badge x-twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="network-badge youtube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="network-badge linkedin"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Column Two: Quick Portals Links -->
            <div class="footer-matrix-col">
                <h4 class="footer-col-title">Quick Portals</h4>
                <ul class="footer-links-list">
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Digital Student Portal</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> E-Learning Framework</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> UNEB Center Details</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Alumni Resource Circle</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Vacancies & Staff Hiring</a></li>
                </ul>
            </div>

            <!-- Column Three: Ministry Registration & Fees Channels -->
            <div class="footer-matrix-col">
                <h4 class="footer-col-title">Verification & Fees</h4>
                <p class="verification-meta">Fully registered by the Ministry of Education and Sports (MoES).</p>
                <p class="verification-meta"><strong>EMIS Number:</strong> 5558/130119</p>
                
                <h5 class="payment-title-heading">Supported Payment Channels</h5>
                <div class="payment-platforms-wrapper">
                    <div class="payment-badge-node" title="SchoolPay Framework Enabled">
                        <i class="fa-solid fa-wallet"></i> <span>SchoolPay Channel</span>
                    </div>
                    <div class="payment-badge-node" title="Mobile Money Routes Approved">
                        <i class="fa-solid fa-mobile-screen-button"></i> <span>Mobile Money Pay</span>
                    </div>
                </div>
            </div>

            <!-- Column Four: Physical Address & Coordination Details -->
            <div class="footer-matrix-col">
                <h4 class="footer-col-title">Contact & Location</h4>
                <ul class="footer-contact-coordinates">
                    <li>
                        <i class="fa-solid fa-location-dot coordinate-icon"></i>
                        <span>P.O box 76 Kampala,<br>Butabika Road, Bbina, Nakawa Division</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope-open coordinate-icon"></i>
                        <span>admissions@bbina.ac.ug</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-headset coordinate-icon"></i>
                        <span>+256 772605229<br></span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright & Legal Row Component -->
        <div class="footer-bottom-legal-row">
            <p> 2026 Bbina Islamic Primary School. All Rights Reserved. Developed by Edtech</p>
            <div class="legal-utilities-sub-links">
                <a href="#">Privacy Framework</a>
                <a href="#">Terms of Use</a>
                <a href="#">Web Portal Sitemap</a>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Widget Component -->
    <a href="https://wa.me/256772605229?text=Assalamu%20Alaikum,%20I%20am%20reaching%20out%20to%20Bbina%20Islamic%20Primary%20School%20regarding%20admission%20details..." 
       target="_blank" 
       class="whatsapp-floating-btn" 
       aria-label="Chat with Bbina Islamic School on WhatsApp">
        <span class="whatsapp-tooltip">Chat with us</span>
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</body>
</html>












