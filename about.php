<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'About Us — ' . getSetting($pdo, 'school_name');

// Department heads / staff directory (see database_patch.sql for
// the vw_staff_directory view — it isn't in the raw dump).
$leadership = $pdo->query(
    'SELECT * FROM vw_staff_directory WHERE is_active = 1 ORDER BY is_management DESC, sort_order ASC'
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us · Uganda Martyrs Primary School, Namugongo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <?php include("includes/header.php"); ?>

    <main>
        <section class="about-hero">
            <div class="container reveal">
                <p class="breadcrumb">
                    <a href="index.php">Home</a> / About Us
                </p>
                <div class="hero-top">
                    <div class="hero-copy">
                        <p class="eyebrow">About Uganda Martyrs Primary School</p>
                        <h1>A school raised in the shadow of the shrines</h1>
                        <p class="lead">Every June, hundreds of thousands of pilgrims walk the road past our gate to
                            Namugongo. We're here the other 364 days too — teaching Primary One through Primary Seven
                            pupils to read, reason and pray, on ground that has held Ugandan faith for well over a
                            century.</p>
                    </div>
                    <img src="assets/images/Aerial_view_of_Uganda_martyrs_Basilica_Namugongo_in_Uganda.jpg"
                        alt="basilica" style="max-width: 60%">
                </div>
            </div>
        </section>

        <section class="story">
            <div class="container story-grid reveal">
                <div class="story-copy">
                    <p class="eyebrow">Where the Mission Began</p>
                    <h2>From a martyrs' witness to a school</h2>
                    <p>The story of Uganda Martyrs Namugongo starts with courage, not curriculum. In 1886, a group of
                        young men attached to the royal court chose their faith over their lives at this very site.
                        Pilgrims have walked here every third of June since, and in 1964 twenty-two of the Catholic
                        martyrs were declared saints by Pope Paul VI.</p>
                    <p>In 1967, four friends turned that legacy into a school, believing the martyrs' courage was worth
                        teaching as much as remembering. What began as a handful of classrooms grew over the decades
                        into a fuller institution — a primary section feeding into secondary school, and eventually a
                        nursery too, all raising children within a few hundred metres of the shrine that gave the school
                        its name.</p>
                    <p>That's still the shape of things today. Our pupils walk past the Basilica on their way to school
                        and learn their times tables in classrooms built on ground their great-grandparents may have
                        walked as pilgrims. We think that proximity should mean something in how we teach, not just
                        where we happen to be located.</p>
                    <div class="motto-block">
                        <p class="motto-label">Our Motto</p>
                        <p class="motto-text">"Courage to Learn, Faith to Rise"</p>
                    </div>
                </div>
                <div class="quick-facts">
                    <div class="quick-fact"><span class="fl">Namesake</span><span class="fv">The Uganda Martyrs of
                            1886</span></div>
                    <div class="quick-fact"><span class="fl">Location</span><span class="fv">Namugongo, Kira
                            Municipality, Wakiso District</span></div>
                    <div class="quick-fact"><span class="fl">Grades taught</span><span class="fv">Primary One to Primary
                            Seven</span></div>
                    <div class="quick-fact"><span class="fl">Foundation</span><span class="fv">Catholic heritage,
                            parish-linked</span></div>
                </div>
            </div>
        </section>

        <section class="history">
            <div class="hill-divider" style="color:var(--cream)">
                <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
                    <path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor" />
                </svg>
            </div>
            <div class="container reveal">
                <p class="eyebrow">Our History</p>
                <h2>How Namugongo's story became a school</h2>
                <div class="timeline-vertical">
                    <div class="timeline-item">
                        <span class="timeline-year">1886</span>
                        <h3>Witness at Namugongo</h3>
                        <p>Thirty-two young men attached to the royal court — Anglican and Catholic — are put to death
                            here for refusing to renounce their faith.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">1964</span>
                        <h3>Sainthood</h3>
                        <p>Pope Paul VI canonizes twenty-two of the Catholic martyrs, cementing Namugongo's place in the
                            global Catholic calendar.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">1967</span>
                        <h3>A school is founded</h3>
                        <p>Four friends open the doors of Uganda Martyrs Namugongo, believing the martyrs' witness was
                            worth building an education around.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">In the years since</span>
                        <h3>Growing with the parish</h3>
                        <p>A primary section, and later a nursery, take shape to serve Namugongo's youngest children
                            from their very first years of school.</p>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">Today</span>
                        <h3>Seven grades, one gate</h3>
                        <p>Primary One to Primary Seven pupils study within walking distance of the Basilica and the
                            Anglican Shrine, in a school shaped by the mission that built it.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mission-vision">
            <div class="hill-divider" style="color:var(--leaf-tint)">
                <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
                    <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
                </svg>
            </div>
            <div class="container reveal">
                <p class="eyebrow">What We're Working Toward</p>
                <h2>Mission & vision</h2>
                <div class="mv-grid">
                    <div class="mv-card">
                        <h3>Our Mission</h3>
                        <p>To teach every Primary One to Primary Seven pupil to read, reason and pray with confidence —
                            regardless of where they started — inside a school shaped by Catholic faith and Namugongo's
                            legacy of quiet courage.</p>
                    </div>
                    <div class="mv-card">
                        <h3>Our Vision</h3>
                        <p>A Namugongo where every child who passes through our gate leaves able to think clearly, work
                            honestly, and hold their faith without needing to be reminded to.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="values">
            <div class="container reveal">
                <p class="eyebrow">What Guides Us</p>
                <h2>Our core values</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <svg class="value-icon" viewBox="0 0 40 40">
                            <path d="M20 4 L14 16 L26 16 Z" fill="#A6402E" />
                            <rect x="16" y="16" width="8" height="18" fill="#A6402E" />
                            <line x1="12" y1="10" x2="28" y2="10" stroke="#A6402E" stroke-width="2" />
                        </svg>
                        <h3>Faith</h3>
                        <p>Morning prayer, hymns and scripture are woven through the week, not set apart from it.</p>
                    </div>
                    <div class="value-card">
                        <svg class="value-icon" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="14" fill="none" stroke="#F2B705" stroke-width="3" />
                            <path d="M14 20 l4 4 8 -8" stroke="#F2B705" stroke-width="3" fill="none"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <h3>Excellence</h3>
                        <p>Small-group catch-up lessons and steady, structured PLE preparation from Primary Five.</p>
                    </div>
                    <div class="value-card">
                        <svg class="value-icon" viewBox="0 0 40 40">
                            <path d="M10 16 h20 l-3 16 h-14 z" fill="none" stroke="#2F6B4F" stroke-width="2" />
                            <line x1="14" y1="16" x2="14" y2="8" stroke="#2F6B4F" stroke-width="2" />
                            <line x1="26" y1="16" x2="26" y2="8" stroke="#2F6B4F" stroke-width="2" />
                        </svg>
                        <h3>Service</h3>
                        <p>Pupils help tend the school garden and compound — care practiced, not just taught.</p>
                    </div>
                    <div class="value-card">
                        <svg class="value-icon" viewBox="0 0 40 40">
                            <circle cx="14" cy="26" r="6" fill="none" stroke="#A6402E" stroke-width="2" />
                            <circle cx="26" cy="26" r="6" fill="none" stroke="#A6402E" stroke-width="2" />
                            <circle cx="20" cy="14" r="6" fill="none" stroke="#A6402E" stroke-width="2" />
                        </svg>
                        <h3>Community</h3>
                        <p>Parents, teachers and parish walk the same road, including on pilgrimage days.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="faith">
            <div class="hill-divider" style="color:var(--cream)">
                <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
                    <path d="M0,10 C240,42 480,40 720,20 C960,0 1200,4 1440,24 L1440,44 L0,44 Z" fill="currentColor" />
                </svg>
            </div>
            <div class="container reveal">
                <p class="eyebrow eyebrow-light">Faith & Heritage</p>
                <h2>Faith held close, not set apart</h2>
                <div class="faith-grid">
                    <div class="faith-copy">
                        <p>The Basilica of the Uganda Martyrs and the Anglican Uganda Martyrs Shrine sit close enough
                            that pupils can see their rooftops from the school compound. Every 3 June, when Namugongo
                            fills with pilgrims from across East Africa for Martyrs' Day, our pupils don't watch from a
                            distance — many walk the route themselves, alongside their families and parish.</p>
                        <p>Morning prayer opens each school day. Religious Education sits on the timetable like any
                            other subject, and our chapel choir leads hymns pupils will recognise from Sunday Mass. We
                            don't treat faith as separate from academics — we've found the two tend to reinforce each
                            other.</p>
                    </div>
                    <div class="faith-facts">
                        <div class="faith-fact">
                            <h3>Martyrs' Day</h3>
                            <p>3 June is a national holiday in Uganda, marked by one of East Africa's largest annual
                                pilgrimages, right outside our gate.</p>
                        </div>
                        <div class="faith-fact">
                            <h3>Daily rhythm</h3>
                            <p>Morning assembly, prayer and hymns open the school day before the first lesson bell.</p>
                        </div>
                        <div class="faith-fact">
                            <h3>Guided visits</h3>
                            <p>Classes take turns walking to the shrines, connecting history lessons to the ground they
                                happened on.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="facilities">
            <div class="hill-divider" style="color:var(--ink)">
                <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
                    <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
                </svg>
            </div>
            <div class="container reveal">
                <p class="eyebrow">Around the Compound</p>
                <h2>What's on the grounds</h2>
                <div class="facilities-grid">
                    <div class="facility-card">
                        <svg class="facility-icon" viewBox="0 0 40 40">
                            <rect x="6" y="12" width="28" height="20" rx="2" fill="none" stroke="#A6402E"
                                stroke-width="2" />
                            <line x1="6" y1="20" x2="34" y2="20" stroke="#A6402E" stroke-width="2" />
                        </svg>
                        <h3>Classrooms</h3>
                        <p>Dedicated classrooms for each grade, Primary One through Primary Seven.</p>
                    </div>
                    <div class="facility-card">
                        <svg class="facility-icon" viewBox="0 0 40 40">
                            <rect x="10" y="8" width="20" height="24" rx="1" fill="none" stroke="#2F6B4F"
                                stroke-width="2" />
                            <line x1="14" y1="14" x2="26" y2="14" stroke="#2F6B4F" stroke-width="1.5" />
                            <line x1="14" y1="19" x2="26" y2="19" stroke="#2F6B4F" stroke-width="1.5" />
                            <line x1="14" y1="24" x2="22" y2="24" stroke="#2F6B4F" stroke-width="1.5" />
                        </svg>
                        <h3>Library Corner</h3>
                        <p>Shared reading shelves stocked for early readers through to Primary Seven revision.</p>
                    </div>
                    <div class="facility-card">
                        <svg class="facility-icon" viewBox="0 0 40 40">
                            <rect x="17" y="14" width="6" height="20" fill="#A6402E" />
                            <polygon points="20,4 12,16 28,16" fill="#A6402E" />
                        </svg>
                        <h3>School Chapel</h3>
                        <p>A dedicated space for morning prayer, weekday Mass and the pupil choir.</p>
                    </div>
                    <div class="facility-card">
                        <svg class="facility-icon" viewBox="0 0 40 40">
                            <rect x="6" y="18" width="28" height="14" rx="1" fill="none" stroke="#F2B705"
                                stroke-width="2" />
                            <line x1="12" y1="18" x2="12" y2="12" stroke="#F2B705" stroke-width="2" />
                            <line x1="28" y1="18" x2="28" y2="12" stroke="#F2B705" stroke-width="2" />
                        </svg>
                        <h3>Dining Hall</h3>
                        <p>A hot midday meal is served to pupils every school day.</p>
                    </div>
                    <div class="facility-card">
                        <svg class="facility-icon" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="14" fill="none" stroke="#2F6B4F" stroke-width="3" />
                            <path d="M6 20 H34 M20 6 V34" stroke="#2F6B4F" stroke-width="2" />
                        </svg>
                        <h3>Sports Field</h3>
                        <p>Open ground for football, netball and athletics practice and inter-house games.</p>
                    </div>
                    <div class="facility-card">
                        <svg class="facility-icon" viewBox="0 0 40 40">
                            <path d="M20 6 v10 M15 11 h10" stroke="#A6402E" stroke-width="3" stroke-linecap="round" />
                            <rect x="8" y="16" width="24" height="18" rx="2" fill="none" stroke="#A6402E"
                                stroke-width="2" />
                        </svg>
                        <h3>Sick Bay</h3>
                        <p>A first-aid room with a nurse on call for minor injuries and check-ups.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="leadership">
            <div class="hill-divider" style="color:var(--cream)">
                <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
                    <path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor" />
                </svg>
            </div>
            <div class="container reveal">
                <p class="eyebrow">Who Runs the School</p>
                <h2>Leadership & staff</h2>
                <p class="lead-note">Day to day, the school is guided by a small leadership team supported by our
                    classroom teachers. <em>Names and photos to be added by the school office.</em></p>
                <div class="leader-grid">
                    <div class="leader-card">
                        <div class="leader-avatar" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
                            </svg>
                        </div>
                        <h3>Head Teacher</h3>
                        <span class="leader-role">School Leadership</span>
                        <p>Oversees the school's academic direction, staffing and its relationship with the parish and
                            parents.</p>
                    </div>
                    <div class="leader-card">
                        <div class="leader-avatar" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
                            </svg>
                        </div>
                        <h3>Deputy Head Teacher</h3>
                        <span class="leader-role">Academics</span>
                        <p>Manages the timetable, exam preparation and academic standards across Primary One to Seven.
                        </p>
                    </div>
                    <div class="leader-card">
                        <div class="leader-avatar" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
                            </svg>
                        </div>
                        <h3>Deputy Head Teacher</h3>
                        <span class="leader-role">Welfare</span>
                        <p>Looks after pupil wellbeing, discipline and the day-to-day running of the compound.</p>
                    </div>
                    <div class="leader-card">
                        <div class="leader-avatar" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
                            </svg>
                        </div>
                        <h3>Director of Studies</h3>
                        <span class="leader-role">Curriculum</span>
                        <p>Coordinates lesson planning and teacher development across every subject and grade.</p>
                    </div>
                    <div class="leader-card">
                        <div class="leader-avatar" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
                            </svg>
                        </div>
                        <h3>Senior Woman Teacher</h3>
                        <span class="leader-role">Pastoral Care</span>
                        <p>A first point of contact for girls' welfare, guidance and counselling matters.</p>
                    </div>
                    <div class="leader-card">
                        <div class="leader-avatar" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
                            </svg>
                        </div>
                        <h3>Chaplain</h3>
                        <span class="leader-role">Faith Life</span>
                        <p>Leads morning prayer, weekday Mass and the school's link with the parish and shrines.</p>
                    </div>
                </div>

                <?php if ($leadership): ?>
                    <p class="lead-note" style="margin-top:2.5rem">Meet some of our staff:</p>
                    <div class="leader-grid">
                        <?php foreach ($leadership as $person): ?>
                            <div class="leader-card">
                                <div class="leader-avatar" aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="8" r="4" />
                                        <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
                                    </svg>
                                </div>
                                <h3><?= htmlspecialchars($person['full_name']) ?></h3>
                                <span
                                    class="leader-role"><?= htmlspecialchars($person['role']) ?><?= $person['department_name'] ? ' · ' . htmlspecialchars($person['department_name']) : '' ?></span>
                                <?php if ($person['bio']): ?>
                                    <p><?= htmlspecialchars($person['bio']) ?></p><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="cta-band">
            <div class="hill-divider" style="color:var(--leaf-tint)">
                <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
                    <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
                </svg>
            </div>
            <div class="container reveal">
                <h2>Come see it for yourself</h2>
                <p>The best way to know the school is to walk the compound, meet a teacher, and hear the morning hymns
                    for yourself.</p>
                <div class="cta-actions">
                    <a href="admissions.php" class="btn btn-primary">Begin Admissions</a>
                    <a href="contact.php#find-us" class="btn btn-on-soil">Plan a Visit</a>
                </div>
            </div>
        </section>
    </main>

    <?php include("includes/footer.php"); ?>

</body>

</html>