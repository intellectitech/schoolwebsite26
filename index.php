<?php

session_start();
require_once 'config/database.php';
require_once './includes/functions.php';

$pageTitle = getSetting($pdo, 'school_name') . ' — Home';

// ── FETCH DATA FROM DATABASE ──────────────────────────────────

// Latest 3 published news articles
$stmt = $pdo->query(
  'SELECT n.id, n.title, n.slug, n.excerpt, n.featured_image,
            n.published_at,
            nc.name  AS cat_name,
            nc.color AS cat_color
     FROM news n
     LEFT JOIN news_categories nc ON nc.id = n.category_id
     WHERE n.is_published = 1
     ORDER BY n.published_at DESC
     LIMIT 3'
);
$latestNews = $stmt->fetchAll();

// Upcoming events (today or future, max 4)
$events = $pdo->query(
  'SELECT * FROM events
     WHERE is_published = 1
       AND event_date >= CURDATE()
     ORDER BY event_date ASC
     LIMIT 4'
)->fetchAll();

// Published testimonials
$testimonials = $pdo->query(
  'SELECT * FROM testimonials
     WHERE is_published = 1
     ORDER BY sort_order ASC
     LIMIT 3'
)->fetchAll();

// Page content blocks
$heroTitle = getSetting($pdo, 'hero_title');
$heroSubtitle = getSetting($pdo, 'hero_subtitle');
$foundedYear = getSetting($pdo, 'founded_year');
$totalStudents = getSetting($pdo, 'total_students');
$contactPhone = getSetting($pdo, 'contact_phone') ?: '+256 700 000 000';
$contactEmail = getSetting($pdo, 'contact_email') ?: 'info@ugandamartyrsnamugongo.sc.ug';
?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Uganda Martyrs Primary School · Namugongo</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet" />

</head>

<body>
  <!-- ================= HEADER ================= -->
  <?php include("includes/header.php"); ?>

  <!-- ================= HERO ================= -->
  <section class="hero" id="top">
    <div class="hero-inner">
      <p class="eyebrow">Kira Municipality · Wakiso District, Uganda</p>
      <h1><?= htmlspecialchars($heroTitle ?: getSetting($pdo, 'school_name')) ?></h1>

      <div class="slider">
        <div class="background bg1">
          <!-- <p class="content">UGANDA MARTYRS</p> -->
        </div>
        <div class="background bg2">
          <!-- <p class="content">PRIMARY AND NURSERY SCHOOL</p> -->
        </div>
        <div class="background bg3">
          <!-- <p class="content">NAMUGONGO</p> -->
        </div>
        <!-- <div class="background bg4">
                    <p class="content">and ability</p>
                </div> -->
        <p class="hero-sub content">
          A short walk from the Namugongo shrines, we teach Primary One through
          Primary Seven to read the world with steady minds and unshaken faith.
          <br>
          <span class="sub">Courage to Learn, Faith to Rise.</span>
        </p>

      </div>

      <div class="hero-actions">
        <a href="admissions.php" class="btn btn-primary">Begin Admissions</a>
        <a href="contact.php#find-us" class="btn btn-ghost">Plan a Visit</a>
      </div>
    </div>
  </section>

  <!-- ================= WELCOME / FACTS ================= -->
  <section class="welcome">
    <div class="hill-divider" style="color: var(--ink)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,44 C240,4 480,0 720,18 C960,36 1200,40 1440,10 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="section-intro">
        Karibu — welcome. Uganda Martyrs Primary School sits minutes from the
        Basilica and the Anglican Shrine that draw pilgrims from across East
        Africa every third of June. That heritage of quiet endurance shapes
        how we teach: patiently, thoroughly, and with faith held close.
      </p>
      <div class="facts-grid">
        <div class="fact-card">
          <span class="fact-label">Grades taught</span><span class="fact-value">P1 – P7</span>
        </div>
        <div class="fact-card">
          <span class="fact-label">Where we stand</span><span class="fact-value">Namugongo, Kira Municipality</span>
        </div>
        <div class="fact-card">
          <span class="fact-label">Foundation</span><span class="fact-value">Catholic heritage</span>
        </div>
        <div class="fact-card">
          <span class="fact-label">Leaving exam</span><span class="fact-value">UNEB PLE, Primary Seven</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= ABOUT ================= -->
  <section class="about" id="about">
    <div class="container about-grid reveal">
      <div class="about-copy">
        <p class="eyebrow">Our Story</p>
        <h2>Built on ground the martyrs made sacred</h2>
        <p>
          Namugongo has drawn the faithful for well over a century — first as
          a place of witness, now as a place of pilgrimage. Our school grew
          out of that same soil: a Catholic-founded primary school raising
          Primary One to Primary Seven pupils within sight of the shrine
          spires.
        </p>
        <p>
          We are small enough that no child goes unnoticed, and serious enough
          that Primary Seven candidates leave ready for whatever secondary
          school asks of them. Faith is not a subject on the timetable here —
          it is the timetable's foundation.
        </p>
        <div class="motto-block">
          <p class="motto-label">Our Motto</p>
          <p class="motto-text">"Courage to Learn, Faith to Rise"</p>
        </div>
      </div>
      <ul class="values-list">
        <li>
          <h3>Faith</h3>
          <p>
            Morning prayer, hymns and scripture are woven through the week,
            not set apart from it.
          </p>
        </li>
        <li>
          <h3>Excellence</h3>
          <p>
            Small-group catch-up lessons and steady, structured PLE
            preparation from Primary Five.
          </p>
        </li>
        <li>
          <h3>Service</h3>
          <p>
            Pupils help tend the school garden and compound — care practiced,
            not just taught.
          </p>
        </li>
        <li>
          <h3>Community</h3>
          <p>
            Parents, teachers and parish walk the same road, including on
            pilgrimage days.
          </p>
        </li>
      </ul>
    </div>
  </section>

  <!-- ================= LEARNING JOURNEY ================= -->
  <section class="journey" id="journey">
    <div class="hill-divider" style="color: var(--cream)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow">Academics</p>
      <h2>Seven years, one steady climb</h2>
      <div class="timeline">
        <div class="rung">
          <span class="rung-no">P1</span>
          <h3>Foundations</h3>
          <p>
            Literacy, numeracy and Luganda alongside English, built through
            play and song.
          </p>
        </div>
        <div class="rung">
          <span class="rung-no">P2</span>
          <h3>Reading fluency</h3>
          <p>
            Independent reading takes hold, with daily numeracy drills to
            match.
          </p>
        </div>
        <div class="rung">
          <span class="rung-no">P3</span>
          <h3>Widening subjects</h3>
          <p>
            Science and Social Studies join the timetable in their own right.
          </p>
        </div>
        <div class="rung">
          <span class="rung-no">P4</span>
          <h3>Study habits</h3>
          <p>
            Homework routines, group projects and the first real class
            assessments.
          </p>
        </div>
        <div class="rung">
          <span class="rung-no">P5</span>
          <h3>Depth and rigor</h3>
          <p>
            Subjects deepen; Religious Education and Social Studies take
            fuller shape.
          </p>
        </div>
        <div class="rung">
          <span class="rung-no">P6</span>
          <h3>Mock preparation</h3>
          <p>
            First mock assessments begin, with structured PLE preparation
            underway.
          </p>
        </div>
        <div class="rung">
          <span class="rung-no">P7</span>
          <h3>PLE & send-off</h3>
          <p>
            Primary Leaving Examinations with UNEB, followed by a send-off
            Mass.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= LIFE AT SCHOOL ================= -->
  <section class="life" id="life">
    <div class="hill-divider" style="color: var(--leaf-tint)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow">Beyond the Classroom</p>
      <h2>A full day, well spent</h2>
      <div class="life-grid">
        <div class="life-card">
          <svg class="life-icon" viewBox="0 0 40 40">
            <path d="M20 4 L14 16 L26 16 Z" fill="#A6402E" />
            <rect x="16" y="16" width="8" height="18" fill="#A6402E" />
            <line x1="12" y1="10" x2="28" y2="10" stroke="#A6402E" stroke-width="2" />
          </svg>
          <h3>Chapel & Choir</h3>
          <p>
            Weekday prayer, Sunday Mass attendance and a pupil choir that
            leads the school's hymns.
          </p>
        </div>
        <div class="life-card">
          <svg class="life-icon" viewBox="0 0 40 40">
            <circle cx="20" cy="20" r="14" fill="none" stroke="#2F6B4F" stroke-width="3" />
            <path d="M6 20 H34 M20 6 V34" stroke="#2F6B4F" stroke-width="2" />
          </svg>
          <h3>Games & Athletics</h3>
          <p>
            Football, netball and athletics practice, with inter-house
            competitions each term.
          </p>
        </div>
        <div class="life-card">
          <svg class="life-icon" viewBox="0 0 40 40">
            <circle cx="14" cy="28" r="5" fill="#F2B705" />
            <circle cx="28" cy="24" r="5" fill="#F2B705" />
            <path d="M19 28 V10 L33 6 V24" stroke="#F2B705" stroke-width="2" fill="none" />
          </svg>
          <h3>Music, Dance & Drama</h3>
          <p>
            Traditional dance, drumming and drama, performed at Founder's Day
            and open days.
          </p>
        </div>
        <div class="life-card">
          <svg class="life-icon" viewBox="0 0 40 40">
            <rect x="8" y="10" width="24" height="18" rx="2" fill="none" stroke="#A6402E" stroke-width="2" />
            <line x1="8" y1="16" x2="32" y2="16" stroke="#A6402E" stroke-width="2" />
          </svg>
          <h3>Clubs & Societies</h3>
          <p>
            Debate, Young Farmers and Wildlife clubs meet weekly, run largely
            by the pupils themselves.
          </p>
        </div>
        <div class="life-card">
          <svg class="life-icon" viewBox="0 0 40 40">
            <path d="M10 16 h20 l-3 16 h-14 z" fill="none" stroke="#2F6B4F" stroke-width="2" />
            <line x1="14" y1="16" x2="14" y2="8" stroke="#2F6B4F" stroke-width="2" />
            <line x1="26" y1="16" x2="26" y2="8" stroke="#2F6B4F" stroke-width="2" />
          </svg>
          <h3>Meals & Care</h3>
          <p>
            A hot midday meal and a nurse on call, so learning is never fought
            against an empty stomach.
          </p>
        </div>
        <div class="life-card">
          <svg class="life-icon" viewBox="0 0 40 40">
            <rect x="17" y="14" width="6" height="20" fill="#A6402E" />
            <polygon points="20,4 12,16 28,16" fill="#A6402E" />
          </svg>
          <h3>Shrine Pilgrimage Visits</h3>
          <p>
            Guided walks to the Namugongo shrines connect classroom history to
            the ground it happened on.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= ADMISSIONS ================= -->
  <section class="admissions" id="admissions">
    <div class="hill-divider" style="color: var(--cream)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,10 C240,42 480,40 720,20 C960,0 1200,4 1440,24 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow eyebrow-light">Admissions</p>
      <h2>Four steps to a place at Uganda Martyrs</h2>
      <div class="admissions-grid">
        <div class="steps">
          <div class="step">
            <span class="step-no">1</span>
            <div>
              <h3>Inquire</h3>
              <p>
                Visit the school office or call the admissions line, Monday to
                Friday, 8am–4pm.
              </p>
            </div>
          </div>
          <div class="step">
            <span class="step-no">2</span>
            <div>
              <h3>Visit & assess</h3>
              <p>
                Tour the compound and sit a short, friendly placement check
                for the grade you're joining.
              </p>
            </div>
          </div>
          <div class="step">
            <span class="step-no">3</span>
            <div>
              <h3>Submit documents</h3>
              <p>
                Birth certificate, immunisation record, latest report card and
                two passport photos.
              </p>
            </div>
          </div>
          <div class="step">
            <span class="step-no">4</span>
            <div>
              <h3>Welcome</h3>
              <p>
                Collect the uniform list and term calendar, and meet your
                child's class teacher.
              </p>
            </div>
          </div>
        </div>
        <div class="requirements">
          <h3>What to bring</h3>
          <ul>
            <li>Birth certificate or extract</li>
            <li>Immunisation card</li>
            <li>Previous school report (if transferring)</li>
            <li>Two passport photographs</li>
            <li>First term fees or agreed payment plan</li>
          </ul>
          <p class="note">
            Admissions run year-round as places allow. Primary One intake is
            heaviest in the term before each new school year begins.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= NEWS & EVENTS ================= -->
  <?php if ($latestNews || $events): ?>
    <section class="news-teaser" id="news">
      <div class="hill-divider" style="color: var(--leaf-tint)">
        <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
          <path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor" />
        </svg>
      </div>
      <div class="container reveal">
        <p class="eyebrow">What's Happening</p>
        <h2>News & upcoming events</h2>
        <div class="news-events-grid">

          <?php if ($latestNews): ?>
            <div class="news-teaser-col">
              <h3 class="news-teaser-heading">Latest news</h3>
              <div class="news-grid-3">
                <?php foreach ($latestNews as $article): ?>
                  <div class="news-card">
                    <div class="news-card-img">
                      <?php if ($article['featured_image'] && file_exists(__DIR__ . '/' . $article['featured_image'])): ?>
                        <img class="card-img" src="<?= htmlspecialchars($article['featured_image']) ?>"
                          alt="<?= htmlspecialchars($article['title']) ?>">
                      <?php else: ?>
                        <svg viewBox="0 0 400 240" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                          <rect width="400" height="240" fill="<?= htmlspecialchars($article['cat_color'] ?? '#16233D') ?>" />
                          <path d="M0,180 C100,150 260,150 400,175 L400,240 L0,240 Z" fill="#FBF3E6" opacity="0.15" />
                        </svg>
                      <?php endif; ?>
                    </div>
                    <div class="news-card-body">
                      <span class="news-tag news-tag-leaf"
                        style="background:<?= htmlspecialchars($article['cat_color'] ?? '#1565C0') ?>"><?= htmlspecialchars($article['cat_name'] ?? 'News') ?></span>
                      <h3><?= htmlspecialchars($article['title']) ?></h3>
                      <p><?= htmlspecialchars(excerpt($article['excerpt'] ?? '', 100)) ?></p>
                    </div>
                    <div class="card-footer">
                      <a href="article.php?slug=<?= urlencode($article['slug']) ?>" class="news-read-more">Read more →</a>
                      <span><?= date('d M Y', strtotime($article['published_at'])) ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <p style="margin-top:1.6rem"><a href="news.php" class="btn btn-ghost">View all news</a></p>
            </div>
          <?php endif; ?>

          <?php if ($events): ?>
            <div class="news-teaser-col">
              <h3 class="news-teaser-heading">Upcoming events</h3>
              <ul class="events-list">
                <?php foreach ($events as $event): ?>
                  <li class="event-item">
                    <div class="event-date">
                      <span class="event-day"><?= date('d', strtotime($event['event_date'])) ?></span>
                      <span class="event-month"><?= date('M Y', strtotime($event['event_date'])) ?></span>
                    </div>
                    <div class="event-body">
                      <h4><?= htmlspecialchars($event['title']) ?></h4>
                      <?php if (!empty($event['location'])): ?>
                        <p class="event-location">📍<?= htmlspecialchars($event['location']) ?></p><?php endif; ?>
                      <?php if ($event['start_time']): ?>
                        🕗 <?= date('g:i A', strtotime($event['start_time'])) ?>
                      <?php endif; ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- ================= VOICES ================= -->
  <section class="voices" id="voices">
    <div class="hill-divider" style="color: var(--ink)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal in">
      <p class="eyebrow">From Our Community</p>
      <h2 class="section-title">What people say</h2>
      <?php if ($testimonials): ?>
        <div class="voices-grid voices">
          <?php foreach ($testimonials as $t): ?>
            <div class="voice-card">
              <p class="voice-quote"><?= htmlspecialchars($t['content']) ?></p>
              <div class="voice-attr"><?= htmlspecialchars($t['author_name']) ?></div>
              <div class="testimonial-role"><?= htmlspecialchars($t['author_role'] ?? '') ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="voice-quote">"We chose it for the faith. We have stayed for how far behind they refuse to leave any
          child."</p>
        <p class="voice-attr">Parent, Primary 1</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ================= CONTACT ================= -->
  <section class="contact" id="contact">
    <div class="hill-divider" style="color: var(--cream)">
      <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
        <path d="M0,10 C240,40 480,44 720,24 C960,4 1200,0 1440,20 L1440,44 L0,44 Z" fill="currentColor" />
      </svg>
    </div>
    <div class="container reveal">
      <p class="eyebrow">Visit Us</p>
      <h2>Find us near the shrines</h2>
      <div class="contact-grid">
        <div class="info-block">
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 21s7-7.2 7-12a7 7 0 1 0-14 0c0 4.8 7 12 7 12z" />
              <circle cx="12" cy="9" r="2.5" />
            </svg>
            <div>
              <h3>Address</h3>
              <p>
                Namugongo, Kira Municipality, Wakiso District — near the
                Basilica of the Uganda Martyrs, about 12km northeast of
                Kampala.
              </p>
            </div>
          </div>
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path
                d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8 9.7a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2z" />
            </svg>
            <div>
              <h3>Phone</h3>
              <p><?= htmlspecialchars($contactPhone) ?> (office, Mon–Fri, 8am–5pm)</p>
            </div>
          </div>
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="4" width="20" height="16" rx="2" />
              <path d="m22 6-10 7L2 6" />
            </svg>
            <div>
              <h3>Email</h3>
              <p><?= htmlspecialchars($contactEmail) ?></p>
            </div>
          </div>
          <div class="info-item">
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <path d="M12 6v6l4 2" />
            </svg>
            <div>
              <h3>Office hours</h3>
              <p>
                Monday – Friday, 8:00am – 5:00pm. Weekend tours by
                appointment.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="hero-actions">
      <a href="contact.php#contactForm" class="btn btn-ghost">CONTACT US</a>
    </div>
  </section>


  <!-- ================= FOOTER ================= -->
  <?php include("includes/footer.php"); ?>

</body>

</html>