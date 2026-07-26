<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery · Uganda Martyrs Primary School, Namugongo</title>
  <meta name="description" content="Photos of school life at Uganda Martyrs Primary School, Namugongo — classrooms, sports, ceremonies, events and more.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- ============================================================
     PAGE HERO
     ============================================================ -->
<section class="page-hero">
  <div class="container reveal">
    <p class="breadcrumb"><a href="index.php">Home</a> / Gallery</p>
    <p class="eyebrow">Photo Gallery</p>
    <h1>Life at Uganda Martyrs, in pictures</h1>
    <p class="lead">From classroom mornings to the June pilgrimage, from inter-house sports days to the Primary Seven send-off Mass — a glimpse of what it looks like to be part of our school.</p>
  </div>
  <div class="hill-divider" style="color:var(--ink)">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
      <path d="M0,44 C240,4 480,0 720,18 C960,36 1200,40 1440,10 L1440,44 L0,44 Z" fill="currentColor"/>
    </svg>
  </div>
</section>

<!-- ============================================================
     GALLERY  (filter + masonry grid)
     ============================================================ -->
<section class="gallery-section">
  <div class="container">

    <div class="reveal">
      <p class="eyebrow">Browse by category</p>
      <h2>School life through the lens</h2>

      <div class="gallery-filter-bar" role="group" aria-label="Filter gallery by category">
        <button class="filter-btn active" data-filter="all">All photos</button>
        <button class="filter-btn" data-filter="classroom">Classroom</button>
        <button class="filter-btn" data-filter="sports">Sports</button>
        <button class="filter-btn" data-filter="events">Events</button>
        <button class="filter-btn" data-filter="ceremonies">Ceremonies</button>
        <button class="filter-btn" data-filter="facilities">Facilities</button>
      </div>
    </div>

    <div class="gallery-grid reveal">

      <!-- ===== CLASSROOM ===== -->
      <div class="gallery-item" data-category="classroom" data-caption="Morning lesson · Primary Four literacy class">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 320" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="320" fill="#16233D"/>
            <rect x="40" y="60" width="400" height="200" rx="4" fill="#2A3A5C"/>
            <!-- book lines -->
            <rect x="80" y="100" width="200" height="12" rx="3" fill="#F2B705" opacity="0.7"/>
            <rect x="80" y="122" width="280" height="8" rx="3" fill="#FBF3E6" opacity="0.3"/>
            <rect x="80" y="138" width="260" height="8" rx="3" fill="#FBF3E6" opacity="0.3"/>
            <rect x="80" y="154" width="240" height="8" rx="3" fill="#FBF3E6" opacity="0.3"/>
            <rect x="80" y="170" width="300" height="8" rx="3" fill="#FBF3E6" opacity="0.25"/>
            <rect x="80" y="186" width="220" height="8" rx="3" fill="#FBF3E6" opacity="0.25"/>
            <!-- pencil icon -->
            <g transform="translate(360,100) rotate(-30)">
              <rect x="-6" y="-40" width="12" height="50" rx="2" fill="#F2B705" opacity="0.8"/>
              <polygon points="-6,10 6,10 0,22" fill="#FBF3E6" opacity="0.8"/>
            </g>
            <path d="M0,290 C120,270 360,280 480,272 L480,320 L0,320 Z" fill="#204B37" opacity="0.5"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Morning lesson · Primary Four literacy class</span>
          </div>
        </div>
      </div>

      <!-- ===== SPORTS ===== -->
      <div class="gallery-item" data-category="sports" data-caption="Inter-house athletics day · Term Two">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 380" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="380" fill="#2F6B4F"/>
            <!-- running track oval -->
            <ellipse cx="240" cy="200" rx="180" ry="110" fill="none" stroke="#F2B705" stroke-width="8" opacity="0.4"/>
            <ellipse cx="240" cy="200" rx="130" ry="72" fill="none" stroke="#F2B705" stroke-width="4" opacity="0.25"/>
            <!-- centre circle -->
            <circle cx="240" cy="200" r="40" fill="none" stroke="#FBF3E6" stroke-width="3" opacity="0.3"/>
            <!-- running figures (simple) -->
            <g fill="#FBF3E6" opacity="0.7">
              <circle cx="150" cy="170" r="10"/>
              <path d="M145,180 L142,210 M150,180 L158,205 M142,210 L135,230 M142,210 L152,230 M158,205 L165,225 M158,205 L148,225"/>
              <circle cx="310" cy="175" r="10"/>
              <path d="M305,185 L302,215 M310,185 L318,210 M302,215 L295,235 M302,215 L312,235 M318,210 L325,230 M318,210 L308,230"/>
            </g>
            <path d="M0,340 C120,318 360,328 480,320 L480,380 L0,380 Z" fill="#204B37" opacity="0.6"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Inter-house athletics day · Term Two</span>
          </div>
        </div>
      </div>

      <!-- ===== EVENTS ===== -->
      <div class="gallery-item" data-category="events" data-caption="Martyrs' Day pilgrimage · 3 June 2026">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 500" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="500" fill="#A6402E"/>
            <!-- sun rays -->
            <g stroke="#F2B705" stroke-width="6" stroke-linecap="round" opacity="0.45" transform="translate(240,180)">
              <line x1="0" y1="-110" x2="0" y2="-80"/>
              <line x1="0" y1="80" x2="0" y2="110"/>
              <line x1="-110" y1="0" x2="-80" y2="0"/>
              <line x1="80" y1="0" x2="110" y2="0"/>
              <line x1="-78" y1="-78" x2="-57" y2="-57"/>
              <line x1="57" y1="57" x2="78" y2="78"/>
              <line x1="57" y1="-57" x2="78" y2="-78"/>
              <line x1="-78" y1="78" x2="-57" y2="57"/>
            </g>
            <circle cx="240" cy="180" r="68" fill="#F2B705"/>
            <!-- cross -->
            <g stroke="#A6402E" stroke-width="10" stroke-linecap="round" opacity="0.7">
              <line x1="240" y1="148" x2="240" y2="212"/>
              <line x1="208" y1="168" x2="272" y2="168"/>
            </g>
            <path d="M0,390 C80,360 200,370 320,368 C400,366 450,374 480,368 L480,500 L0,500 Z" fill="#16233D" opacity="0.65"/>
            <path d="M0,420 C120,398 320,410 480,400 L480,500 L0,500 Z" fill="#204B37" opacity="0.5"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Martyrs' Day pilgrimage · 3 June 2026</span>
          </div>
        </div>
      </div>

      <!-- ===== CLASSROOM ===== -->
      <div class="gallery-item" data-category="classroom" data-caption="Mathematics drill · Primary Six">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 300" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="300" fill="#204B37"/>
            <!-- chalkboard -->
            <rect x="60" y="40" width="360" height="180" rx="4" fill="#2F6B4F"/>
            <rect x="72" y="52" width="336" height="156" rx="3" fill="#204B37" opacity="0.5"/>
            <!-- maths writing -->
            <text x="100" y="100" font-family="monospace" font-size="22" fill="#F2B705" opacity="0.75">3x² + 7x - 4</text>
            <text x="100" y="132" font-family="monospace" font-size="18" fill="#FBF3E6" opacity="0.45">= (3x - 1)(x + 4)</text>
            <line x1="100" y1="148" x2="380" y2="148" stroke="#FBF3E6" stroke-width="1" opacity="0.3" stroke-dasharray="4 4"/>
            <text x="100" y="174" font-family="monospace" font-size="16" fill="#FBF3E6" opacity="0.4">x = ⅓  or  x = -4</text>
            <path d="M0,268 C160,250 320,258 480,252 L480,300 L0,300 Z" fill="#16233D" opacity="0.55"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Mathematics drill · Primary Six</span>
          </div>
        </div>
      </div>

      <!-- ===== CEREMONIES ===== -->
      <div class="gallery-item" data-category="ceremonies" data-caption="Primary Seven send-off Mass · December 2025">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 420" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="420" fill="#16233D"/>
            <!-- chapel arch -->
            <path d="M140,360 L140,180 Q240,80 340,180 L340,360 Z" fill="none" stroke="#F2B705" stroke-width="3" opacity="0.4"/>
            <!-- altar cloth -->
            <rect x="160" y="310" width="160" height="40" rx="3" fill="#A6402E" opacity="0.5"/>
            <!-- cross on altar -->
            <g stroke="#F2B705" stroke-width="6" stroke-linecap="round" opacity="0.8">
              <line x1="240" y1="260" x2="240" y2="310"/>
              <line x1="218" y1="278" x2="262" y2="278"/>
            </g>
            <!-- candle flames -->
            <ellipse cx="190" cy="296" rx="6" ry="10" fill="#F2B705" opacity="0.7"/>
            <ellipse cx="290" cy="296" rx="6" ry="10" fill="#F2B705" opacity="0.7"/>
            <rect x="186" y="302" width="8" height="22" rx="2" fill="#FBF3E6" opacity="0.4"/>
            <rect x="286" y="302" width="8" height="22" rx="2" fill="#FBF3E6" opacity="0.4"/>
            <!-- light rays from cross -->
            <circle cx="240" cy="285" r="30" fill="#F2B705" opacity="0.06"/>
            <circle cx="240" cy="285" r="50" fill="#F2B705" opacity="0.04"/>
            <path d="M0,380 C160,360 320,370 480,362 L480,420 L0,420 Z" fill="#2A3A5C" opacity="0.6"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Primary Seven send-off Mass · December 2025</span>
          </div>
        </div>
      </div>

      <!-- ===== SPORTS ===== -->
      <div class="gallery-item" data-category="sports" data-caption="Girls' netball practice · Term One">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 340" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="340" fill="#832F21"/>
            <!-- netball court lines -->
            <rect x="60" y="60" width="360" height="220" fill="none" stroke="#FBF3E6" stroke-width="2" opacity="0.3"/>
            <line x1="60" y1="170" x2="420" y2="170" stroke="#FBF3E6" stroke-width="2" opacity="0.3"/>
            <circle cx="240" cy="170" r="50" fill="none" stroke="#FBF3E6" stroke-width="2" opacity="0.25"/>
            <!-- goal posts -->
            <line x1="240" y1="60" x2="240" y2="20" stroke="#F2B705" stroke-width="4" opacity="0.6"/>
            <circle cx="240" cy="18" r="14" fill="none" stroke="#F2B705" stroke-width="3" opacity="0.6"/>
            <line x1="240" y1="280" x2="240" y2="320" stroke="#F2B705" stroke-width="4" opacity="0.6"/>
            <circle cx="240" cy="322" r="14" fill="none" stroke="#F2B705" stroke-width="3" opacity="0.6"/>
            <!-- ball -->
            <circle cx="310" cy="140" r="18" fill="#F2B705" opacity="0.75"/>
            <path d="M296,132 Q310,122 324,132" fill="none" stroke="#A6402E" stroke-width="2"/>
            <path d="M294,148 Q310,160 326,148" fill="none" stroke="#A6402E" stroke-width="2"/>
            <path d="M300,300 C100,282 80,296 0,288 L0,340 L480,340 L480,290 C400,298 380,285 300,300 Z" fill="#204B37" opacity="0.4"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Girls' netball practice · Term One</span>
          </div>
        </div>
      </div>

      <!-- ===== EVENTS ===== -->
      <div class="gallery-item" data-category="events" data-caption="Founder's Day celebrations · March 2026">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 300" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="300" fill="#F2B705"/>
            <!-- star burst / celebration -->
            <g transform="translate(240,140)">
              <g stroke="#16233D" stroke-width="3" stroke-linecap="round" opacity="0.5">
                <line x1="0" y1="-90" x2="0" y2="-65"/>
                <line x1="0" y1="65" x2="0" y2="90"/>
                <line x1="-90" y1="0" x2="-65" y2="0"/>
                <line x1="65" y1="0" x2="90" y2="0"/>
                <line x1="-64" y1="-64" x2="-46" y2="-46"/>
                <line x1="46" y1="46" x2="64" y2="64"/>
                <line x1="46" y1="-46" x2="64" y2="-64"/>
                <line x1="-64" y1="64" x2="-46" y2="46"/>
              </g>
              <circle r="50" fill="#A6402E" opacity="0.8"/>
              <text x="0" y="6" text-anchor="middle" font-family="serif" font-size="16" fill="#F2B705" font-weight="bold">59</text>
              <text x="0" y="-8" text-anchor="middle" font-family="sans-serif" font-size="7" fill="#FBF3E6" opacity="0.8">YEARS</text>
            </g>
            <path d="M0,265 C120,245 360,255 480,248 L480,300 L0,300 Z" fill="#832F21" opacity="0.5"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Founder's Day celebrations · March 2026</span>
          </div>
        </div>
      </div>

      <!-- ===== CLASSROOM ===== -->
      <div class="gallery-item" data-category="classroom" data-caption="Science practical · Primary Five">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 360" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="360" fill="#2A3A5C"/>
            <!-- beaker / flask icon -->
            <g transform="translate(240,170)">
              <path d="M-30,-70 L-30,-10 L-70,60 Q-70,80 -40,80 L40,80 Q70,80 70,60 L30,-10 L30,-70 Z" fill="none" stroke="#F2B705" stroke-width="3" opacity="0.6"/>
              <!-- liquid inside -->
              <path d="M-60,40 L-40,15 L40,15 L60,40 Q60,70 40,70 L-40,70 Q-60,70 -60,40 Z" fill="#2F6B4F" opacity="0.5"/>
              <!-- bubbles -->
              <circle cx="-20" cy="45" r="5" fill="#F2B705" opacity="0.4"/>
              <circle cx="15" cy="30" r="7" fill="#F2B705" opacity="0.3"/>
              <circle cx="-5" cy="58" r="4" fill="#F2B705" opacity="0.35"/>
              <!-- handle of flask -->
              <line x1="-30" y1="-70" x2="30" y2="-70" stroke="#F2B705" stroke-width="3" opacity="0.5"/>
            </g>
            <path d="M0,320 C160,300 320,310 480,304 L480,360 L0,360 Z" fill="#204B37" opacity="0.4"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Science practical · Primary Five</span>
          </div>
        </div>
      </div>

      <!-- ===== FACILITIES ===== -->
      <div class="gallery-item" data-category="facilities" data-caption="School chapel — morning prayer">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 400" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="400" fill="#FBF3E6"/>
            <!-- sky gradient suggestion -->
            <rect width="480" height="200" fill="#F3E6D2"/>
            <!-- chapel building -->
            <rect x="120" y="180" width="240" height="160" fill="#16233D"/>
            <!-- roof -->
            <polygon points="100,180 240,80 380,180" fill="#A6402E"/>
            <!-- windows -->
            <rect x="160" y="220" width="40" height="60" rx="20" fill="#F2B705" opacity="0.5"/>
            <rect x="280" y="220" width="40" height="60" rx="20" fill="#F2B705" opacity="0.5"/>
            <!-- door -->
            <rect x="210" y="280" width="60" height="60" rx="30" fill="#2A3A5C"/>
            <!-- cross on roof -->
            <g stroke="#F2B705" stroke-width="6" stroke-linecap="round">
              <line x1="240" y1="78" x2="240" y2="42"/>
              <line x1="222" y1="56" x2="258" y2="56"/>
            </g>
            <!-- ground -->
            <rect x="0" y="338" width="480" height="62" fill="#2F6B4F" opacity="0.35"/>
            <rect x="0" y="360" width="480" height="40" fill="#204B37" opacity="0.4"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">School chapel — morning prayer</span>
          </div>
        </div>
      </div>

      <!-- ===== CEREMONIES ===== -->
      <div class="gallery-item" data-category="ceremonies" data-caption="Choir at Martyrs' Day Mass · June 2026">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 320" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="320" fill="#2F6B4F"/>
            <!-- music notes scattered -->
            <g fill="#F2B705" opacity="0.55" font-family="serif" font-size="36">
              <text x="60" y="130">♪</text>
              <text x="160" y="90">♫</text>
              <text x="270" y="120">♩</text>
              <text x="370" y="88">♬</text>
              <text x="110" y="200">♫</text>
              <text x="330" y="195">♪</text>
            </g>
            <!-- staff lines -->
            <g stroke="#FBF3E6" stroke-width="1.5" opacity="0.2">
              <line x1="40" y1="140" x2="440" y2="140"/>
              <line x1="40" y1="156" x2="440" y2="156"/>
              <line x1="40" y1="172" x2="440" y2="172"/>
              <line x1="40" y1="188" x2="440" y2="188"/>
              <line x1="40" y1="204" x2="440" y2="204"/>
            </g>
            <path d="M0,285 C160,265 320,274 480,267 L480,320 L0,320 Z" fill="#204B37" opacity="0.6"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Choir at Martyrs' Day Mass · June 2026</span>
          </div>
        </div>
      </div>

      <!-- ===== SPORTS ===== -->
      <div class="gallery-item" data-category="sports" data-caption="Boys' football — House League final">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 380" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="380" fill="#204B37"/>
            <!-- football pitch -->
            <rect x="30" y="40" width="420" height="260" fill="none" stroke="#FBF3E6" stroke-width="2" opacity="0.25"/>
            <line x1="30" y1="170" x2="450" y2="170" stroke="#FBF3E6" stroke-width="2" opacity="0.2"/>
            <circle cx="240" cy="170" r="44" fill="none" stroke="#FBF3E6" stroke-width="2" opacity="0.2"/>
            <!-- goal areas -->
            <rect x="30" y="120" width="80" height="100" fill="none" stroke="#FBF3E6" stroke-width="2" opacity="0.2"/>
            <rect x="370" y="120" width="80" height="100" fill="none" stroke="#FBF3E6" stroke-width="2" opacity="0.2"/>
            <!-- football -->
            <circle cx="240" cy="170" r="20" fill="#FBF3E6" opacity="0.85"/>
            <path d="M228,158 L240,150 L252,158 L252,170 L240,178 L228,170 Z" fill="#16233D" opacity="0.4"/>
            <path d="M240,150 L240,140" stroke="#16233D" stroke-width="2" opacity="0.4"/>
            <path d="M252,158 L260,154" stroke="#16233D" stroke-width="2" opacity="0.4"/>
            <path d="M252,170 L260,174" stroke="#16233D" stroke-width="2" opacity="0.4"/>
            <path d="M228,170 L220,174" stroke="#16233D" stroke-width="2" opacity="0.4"/>
            <path d="M228,158 L220,154" stroke="#16233D" stroke-width="2" opacity="0.4"/>
            <path d="M0,335 C160,316 320,325 480,318 L480,380 L0,380 Z" fill="#16233D" opacity="0.5"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Boys' football — House League final</span>
          </div>
        </div>
      </div>

      <!-- ===== EVENTS ===== -->
      <div class="gallery-item" data-category="events" data-caption="Open Day — parents and pupils in the compound">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 340" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="340" fill="#F2B705"/>
            <!-- people silhouettes (simple) -->
            <g fill="#16233D" opacity="0.5">
              <!-- person 1 -->
              <circle cx="120" cy="160" r="22"/>
              <path d="M90,200 C90,182 104,172 120,172 C136,172 150,182 150,200 L150,240 L90,240 Z"/>
              <!-- person 2 -->
              <circle cx="240" cy="155" r="24"/>
              <path d="M208,196 C208,177 222,166 240,166 C258,166 272,177 272,196 L272,240 L208,240 Z"/>
              <!-- person 3 (child) -->
              <circle cx="185" cy="185" r="16"/>
              <path d="M162,210 C162,196 172,188 185,188 C198,188 208,196 208,210 L208,240 L162,240 Z"/>
              <!-- person 4 -->
              <circle cx="360" cy="162" r="22"/>
              <path d="M330,202 C330,184 344,174 360,174 C376,174 390,184 390,202 L390,240 L330,240 Z"/>
            </g>
            <!-- bunting / flags -->
            <polyline points="0,60 80,80 160,60 240,78 320,60 400,78 480,60" fill="none" stroke="#A6402E" stroke-width="3" opacity="0.6"/>
            <g fill="#A6402E" opacity="0.5">
              <polygon points="70,80 90,80 80,100"/>
              <polygon points="150,60 170,60 160,80"/>
              <polygon points="230,78 250,78 240,98"/>
              <polygon points="310,60 330,60 320,80"/>
              <polygon points="390,78 410,78 400,98"/>
            </g>
            <path d="M0,305 C100,285 380,296 480,288 L480,340 L0,340 Z" fill="#832F21" opacity="0.45"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Open Day — parents and pupils in the compound</span>
          </div>
        </div>
      </div>

      <!-- ===== FACILITIES ===== -->
      <div class="gallery-item" data-category="facilities" data-caption="School garden — Young Farmers Club harvest">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 300" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="300" fill="#E9F1E7"/>
            <!-- sky -->
            <rect width="480" height="160" fill="#FBF3E6"/>
            <!-- sun -->
            <circle cx="380" cy="70" r="36" fill="#F2B705" opacity="0.7"/>
            <!-- maize stalks -->
            <g stroke="#2F6B4F" stroke-width="4" stroke-linecap="round">
              <line x1="80" y1="280" x2="80" y2="140"/>
              <line x1="160" y1="280" x2="160" y2="130"/>
              <line x1="240" y1="280" x2="240" y2="145"/>
              <line x1="320" y1="280" x2="320" y2="138"/>
              <line x1="400" y1="280" x2="400" y2="150"/>
            </g>
            <!-- maize cob (simple) -->
            <g>
              <ellipse cx="80" cy="178" rx="12" ry="26" fill="#F2B705" opacity="0.75" transform="rotate(-15,80,178)"/>
              <ellipse cx="160" cy="168" rx="12" ry="26" fill="#F2B705" opacity="0.75" transform="rotate(10,160,168)"/>
              <ellipse cx="240" cy="175" rx="12" ry="26" fill="#F2B705" opacity="0.75" transform="rotate(-8,240,175)"/>
              <ellipse cx="320" cy="170" rx="12" ry="26" fill="#F2B705" opacity="0.75" transform="rotate(12,320,170)"/>
              <ellipse cx="400" cy="180" rx="12" ry="26" fill="#F2B705" opacity="0.75" transform="rotate(-10,400,180)"/>
            </g>
            <!-- leaves -->
            <g fill="#2F6B4F" opacity="0.5">
              <ellipse cx="96" cy="195" rx="22" ry="8" transform="rotate(-30,96,195)"/>
              <ellipse cx="176" cy="186" rx="22" ry="8" transform="rotate(25,176,186)"/>
              <ellipse cx="256" cy="192" rx="22" ry="8" transform="rotate(-20,256,192)"/>
            </g>
            <!-- soil -->
            <path d="M0,265 C100,252 380,260 480,255 L480,300 L0,300 Z" fill="#A6402E" opacity="0.35"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">School garden — Young Farmers Club harvest</span>
          </div>
        </div>
      </div>

      <!-- ===== CEREMONIES ===== -->
      <div class="gallery-item" data-category="ceremonies" data-caption="Prize-giving day · Primary Three and Four">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 360" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="360" fill="#16233D"/>
            <!-- trophy -->
            <g transform="translate(240,180)">
              <path d="M-50,-80 Q-60,-20 -40,10 Q-20,40 0,44 Q20,40 40,10 Q60,-20 50,-80 Z" fill="#F2B705" opacity="0.75"/>
              <!-- handles -->
              <path d="M-50,-60 Q-80,-50 -78,-20 Q-76,10 -40,10" fill="none" stroke="#F2B705" stroke-width="6" opacity="0.65"/>
              <path d="M50,-60 Q80,-50 78,-20 Q76,10 40,10" fill="none" stroke="#F2B705" stroke-width="6" opacity="0.65"/>
              <!-- stem -->
              <rect x="-10" y="44" width="20" height="28" fill="#F2B705" opacity="0.7"/>
              <rect x="-30" y="70" width="60" height="10" rx="4" fill="#F2B705" opacity="0.7"/>
              <!-- star inside -->
              <polygon points="0,-50 6,-34 24,-34 10,-24 16,-8 0,-18 -16,-8 -10,-24 -24,-34 -6,-34" fill="#A6402E" opacity="0.6"/>
            </g>
            <!-- confetti dots -->
            <g opacity="0.4">
              <circle cx="80" cy="80" r="6" fill="#F2B705"/><circle cx="140" cy="120" r="4" fill="#A6402E"/>
              <circle cx="320" cy="90" r="5" fill="#2F6B4F"/><circle cx="400" cy="130" r="6" fill="#F2B705"/>
              <circle cx="60" cy="200" r="4" fill="#A6402E"/><circle cx="420" cy="200" r="5" fill="#2F6B4F"/>
              <circle cx="100" cy="280" r="5" fill="#F2B705"/><circle cx="380" cy="270" r="4" fill="#A6402E"/>
            </g>
            <path d="M0,320 C160,300 320,312 480,304 L480,360 L0,360 Z" fill="#A6402E" opacity="0.4"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Prize-giving day · Primary Three and Four</span>
          </div>
        </div>
      </div>

      <!-- ===== CLASSROOM ===== -->
      <div class="gallery-item" data-category="classroom" data-caption="Reading time · Primary Two">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 280" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="280" fill="#A6402E"/>
            <!-- open book -->
            <g transform="translate(240,140)">
              <!-- left page -->
              <path d="M-120,-70 Q-110,-80 0,-70 L0,80 Q-110,90 -120,80 Z" fill="#FBF3E6" opacity="0.85"/>
              <!-- right page -->
              <path d="M120,-70 Q110,-80 0,-70 L0,80 Q110,90 120,80 Z" fill="#FBF3E6" opacity="0.75"/>
              <!-- spine -->
              <rect x="-4" y="-70" width="8" height="150" fill="#F3E6D2" opacity="0.5"/>
              <!-- text lines left -->
              <g stroke="#A6402E" stroke-width="2" opacity="0.3">
                <line x1="-100" y1="-44" x2="-14" y2="-44"/>
                <line x1="-100" y1="-28" x2="-14" y2="-28"/>
                <line x1="-100" y1="-12" x2="-14" y2="-12"/>
                <line x1="-100" y1="4" x2="-14" y2="4"/>
                <line x1="-100" y1="20" x2="-14" y2="20"/>
                <line x1="-100" y1="36" x2="-14" y2="36"/>
                <line x1="-100" y1="52" x2="-14" y2="52"/>
              </g>
              <!-- text lines right -->
              <g stroke="#A6402E" stroke-width="2" opacity="0.25">
                <line x1="14" y1="-44" x2="100" y2="-44"/>
                <line x1="14" y1="-28" x2="100" y2="-28"/>
                <line x1="14" y1="-12" x2="80" y2="-12"/>
                <line x1="14" y1="4" x2="100" y2="4"/>
                <line x1="14" y1="20" x2="90" y2="20"/>
                <line x1="14" y1="36" x2="100" y2="36"/>
              </g>
            </g>
            <path d="M0,250 C100,230 380,240 480,232 L480,280 L0,280 Z" fill="#16233D" opacity="0.5"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Reading time · Primary Two</span>
          </div>
        </div>
      </div>

      <!-- ===== EVENTS ===== -->
      <div class="gallery-item" data-category="events" data-caption="Drama club performance · Founder's Day">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 420" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="420" fill="#2A3A5C"/>
            <!-- stage curtains -->
            <path d="M0,0 C0,0 60,40 40,200 C30,280 20,340 0,420 Z" fill="#A6402E" opacity="0.55"/>
            <path d="M480,0 C480,0 420,40 440,200 C450,280 460,340 480,420 Z" fill="#A6402E" opacity="0.55"/>
            <!-- spotlight cone -->
            <path d="M240,0 L120,280 L360,280 Z" fill="#F2B705" opacity="0.08"/>
            <circle cx="240" cy="280" r="90" fill="#F2B705" opacity="0.07"/>
            <!-- comedy / tragedy masks -->
            <g transform="translate(180,180)">
              <circle r="38" fill="#FBF3E6" opacity="0.75"/>
              <!-- happy mouth -->
              <path d="M-18,10 Q0,26 18,10" fill="none" stroke="#16233D" stroke-width="3" stroke-linecap="round"/>
              <circle cx="-12" cy="-4" r="5" fill="#16233D" opacity="0.6"/>
              <circle cx="12" cy="-4" r="5" fill="#16233D" opacity="0.6"/>
            </g>
            <g transform="translate(300,185)">
              <circle r="38" fill="#F2B705" opacity="0.7"/>
              <!-- sad mouth -->
              <path d="M-18,16 Q0,4 18,16" fill="none" stroke="#16233D" stroke-width="3" stroke-linecap="round"/>
              <circle cx="-12" cy="-2" r="5" fill="#16233D" opacity="0.6"/>
              <circle cx="12" cy="-2" r="5" fill="#16233D" opacity="0.6"/>
            </g>
            <path d="M0,385 C160,365 320,375 480,368 L480,420 L0,420 Z" fill="#16233D" opacity="0.7"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">Drama club performance · Founder's Day</span>
          </div>
        </div>
      </div>

      <!-- ===== CEREMONIES ===== -->
      <div class="gallery-item" data-category="ceremonies" data-caption="New school year blessing · February 2026">
        <div class="gallery-item-inner">
          <svg class="gallery-placeholder" viewBox="0 0 480 300" xmlns="http://www.w3.org/2000/svg">
            <rect width="480" height="300" fill="#2F6B4F"/>
            <!-- dove / peace symbol suggestion -->
            <g transform="translate(240,130)">
              <circle r="52" fill="none" stroke="#F2B705" stroke-width="2" opacity="0.4"/>
              <!-- dove shape (simple) -->
              <path d="M0,-30 C20,-40 50,-30 60,-10 C70,10 50,30 20,28 L40,10 C20,20 -10,20 -30,10 C-50,0 -50,-20 -30,-28 C-10,-36 0,-30 0,-30 Z" fill="#FBF3E6" opacity="0.7"/>
              <!-- olive branch -->
              <path d="M20,28 L-10,56" stroke="#FBF3E6" stroke-width="2" opacity="0.6" stroke-linecap="round"/>
              <ellipse cx="6" cy="42" rx="7" ry="4" fill="#F2B705" opacity="0.5" transform="rotate(-40,6,42)"/>
              <ellipse cx="-4" cy="52" rx="7" ry="4" fill="#F2B705" opacity="0.4" transform="rotate(-50,-4,52)"/>
            </g>
            <!-- rays -->
            <g stroke="#F2B705" stroke-width="2" opacity="0.15">
              <line x1="240" y1="40" x2="240" y2="10"/>
              <line x1="340" y1="85" x2="360" y2="70"/>
              <line x1="380" y1="170" x2="410" y2="170"/>
              <line x1="140" y1="85" x2="120" y2="70"/>
              <line x1="100" y1="170" x2="70" y2="170"/>
            </g>
            <path d="M0,265 C160,245 320,255 480,248 L480,300 L0,300 Z" fill="#204B37" opacity="0.55"/>
          </svg>
          <div class="gallery-overlay">
            <span class="gallery-cap-text">New school year blessing · February 2026</span>
          </div>
        </div>
      </div>

    </div><!-- /.gallery-grid -->
  </div>
</section>

<!-- ============================================================
     LIGHTBOX (hidden until a gallery-item is clicked)
     ============================================================ -->
<div class="lightbox-overlay" id="galleryLightbox" role="dialog" aria-modal="true" aria-label="Photo lightbox">
  <div class="lightbox-content">
    <button class="lightbox-close" aria-label="Close photo">&times;</button>
    <div class="lightbox-img-wrap" id="lightboxSvgWrap"></div>
    <p class="lightbox-caption" id="lightboxCaption"></p>
  </div>
</div>

<!-- ============================================================
     CTA BAND
     ============================================================ -->
<section class="cta-band">
  <div class="hill-divider" style="color:var(--leaf-tint)">
    <svg viewBox="0 0 1440 44" preserveAspectRatio="none">
      <path d="M0,30 C240,0 480,4 720,22 C960,40 1200,36 1440,12 L1440,44 L0,44 Z" fill="currentColor"/>
    </svg>
  </div>
  <div class="container reveal">
    <h2>Ready to make your own memories here?</h2>
    <p>The best way to see the school is to walk through the gate on a school morning. Come and say hello.</p>
    <div class="cta-actions">
      <a href="admissions.php" class="btn btn-primary">Begin Admissions</a>
      <a href="contact.php" class="btn btn-on-dark">Plan a Visit</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
