<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Bbina Islamic Primary School</title>
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
            color: #fff !important;
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
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .whatsapp-floating-btn i {
            font-size: 32px;
        }

        /* Interactive Notification Badge */
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

        .whatsapp-tooltip::after {
            content: '';
            position: absolute;
            right: -6px;
            top: 50%;
            transform: translateY(-50%);
            border-width: 6px 0 6px 6px;
            border-style: solid;
            border-color: transparent transparent transparent #111;
        }

        .whatsapp-floating-btn:hover .whatsapp-tooltip {
            opacity: 1;
            visibility: visible;
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
                <li><a href="admmissions.php">Admmissions</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
                <li><a href="news.php">News & Gallery</a></li>
                <li><a href="admmissions.php" class="btn-apply">Apply Now</a></li>
            </ul>
        </nav>
    </header>

    <!-- Inner Page Banner -->
     <section style="background-image: url('images/hero5.jpg');" class="hero-banner">
        <div class="hero-content">
            <h2>GET IN TOUCH</h2>
            <p>Have any questions? Reach out to our administration office today. We are here to help!</p>
        </div>
    </section>

    <!-- Main Container Content Layout Split -->
    <div class="main-layout">
        
        <!-- Left Column: Detailed Contact Form & Map -->
        <main>
            
            <!-- Welcome/Introduction box -->
            <section class="welcome-box">
                <h3 class="section-title">Send Us a Direct Message</h3>
                <p>For admissions, school fees structures, employment opportunities, or general feedback, please fill out the form below. Our administrative desk will review your submission and reply within 24 to 48 working hours.</p>
                
                <!-- Expanded Contact Form -->
                <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Your message has been received! We will contact you shortly.');" style="margin-top: 25px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Your Full Name <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" required placeholder="John Doe" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Email Address <span style="color: red;">*</span></label>
                            <input type="email" class="form-control" required placeholder="johndoe@gmail.com" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Phone Number <span style="color: red;">*</span></label>
                            <input type="tel" class="form-control" required placeholder="+256 700 000000" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">I am a: <span style="color: red;">*</span></label>
                            <select class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">-- Select Option --</option>
                                <option value="Parent">Parent / Guardian</option>
                                <option value="Student">Prospective Student</option>
                                <option value="Alumni">Alumnus</option>
                                <option value="Visitor">General Enquirer</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 5px;">Subject <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" required placeholder="Admission Inquiry / School Fees / Other" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 5px;">Your Message <span style="color: red;">*</span></label>
                        <textarea class="form-control" rows="6" required placeholder="How can we assist you? Please share details here..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                    </div>

                    <button type="submit" class="btn-apply" style="border: none; padding: 12px 30px; font-size: 1rem; cursor: pointer; display: inline-block;">
                        Submit Inquiry <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
                    </button>
                </form>
            </section>

            <!-- Location Map Container -->
            <section style="margin-top: 40px;">
                <h3 class="section-title">Our Physical Location</h3>
                <p style="margin-bottom: 20px;">We are easily accessible in Kampala, Nakawa Division. Visit our administrative blocks along Butabika Road during our operational hours for physically guided tours.</p>
                <div class="map-wrapper" style="border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); height: 350px;">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15959.028784711319!2d32.62884175!3d0.32363065!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177db96f7c107bf3%3A0xe9f79bd91ffda81b!2sBbina%2C%20Kampala!5e0!3m2!1sen!2sug!4v1710000000000" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </section>

        </main>

        <!-- Right Column: Sidebar Office Coordinates & FAQs -->
        <aside>
            
            <!-- Quick Contact Coordinates Card -->
            <div class="sidebar-widget" style="background-color: #fafafa; border-top: 4px solid var(--primary-color, #104e3b);">
                <h4 class="sidebar-title">Direct Contacts</h4>
                <ul class="footer-contact-coordinates" style="padding-left: 0; list-style: none;">
                    <li style="margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-location-dot" style="color: var(--primary-color, #104e3b); font-size: 1.2rem; margin-top: 3px;"></i>
                        <span><strong>Main Campus:</strong><br>P.O box 76 Kampala,<br>Butabika Road, Bbina,<br>Nakawa Division, Uganda</span>
                    </li>
                    <li style="margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-phone" style="color: var(--primary-color, #104e3b); font-size: 1.2rem; margin-top: 3px;"></i>
                        <span><strong>Phone Support:</strong><br>+256 772605229<br>+256 701605229</span>
                    </li>
                    <li style="margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-envelope" style="color: var(--primary-color, #104e3b); font-size: 1.2rem; margin-top: 3px;"></i>
                        <span><strong>Official Emails:</strong><br>admissions@bbina.ac.ug<br>infob@bbina.ac.ug</span>
                    </li>
                </ul>
            </div>

            <!-- Office Operations Hours Card -->
            <div class="sidebar-widget">
                <h4 class="sidebar-title">Working Hours</h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px 0; font-weight: 600;">Monday - Friday</td>
                        <td style="padding: 8px 0; text-align: right;">8:00 AM - 5:00 PM</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px 0; font-weight: 600;">Saturday</td>
                        <td style="padding: 8px 0; text-align: right;">9:00 AM - 1:00 PM</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #d9534f;">Sundays & Holidays</td>
                        <td style="padding: 8px 0; text-align: right; color: #d9534f; font-weight: 600;">Closed</td>
                    </tr>
                </table>
            </div>

            <!-- FAQ Help Card -->
            <div class="sidebar-widget" style="background-color: var(--primary-color, #104e3b); color: #fff; border-radius: 8px; padding: 20px;">
                <h4 class="sidebar-title" style="color: #fff; border-bottom: 1px solid rgba(255,255,255,0.2); margin-bottom: 15px;">Admission Notice</h4>
                <p style="font-size: 0.9rem; line-height: 1.6; margin-bottom: 12px;">Looking to enroll your child for the upcoming school term? Please check our requirements sheet or download structural templates directly.</p>
                <a href="#" class="btn-apply" style="display: block; text-align: center; background-color: var(--accent-color, #e2b13c); color: #111; text-decoration: none; padding: 10px; border-radius: 4px; font-weight: bold; font-size: 0.9rem;">
                    Download Requirement Sheet <i class="fa-solid fa-download" style="margin-left: 5px;"></i>
                </a>
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
    <a href="https://wa.me/256772605229?text=Assalamu%20Alaikum,%20I%20am%20reaching%20out%20to%20Bbina%20Islamic%20Primary%20School%20regarding..." 
       target="_blank" 
       class="whatsapp-floating-btn" 
       aria-label="Chat with Bbina Islamic School on WhatsApp">
        <span class="whatsapp-tooltip">Chat with us</span>
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</body>
</html>