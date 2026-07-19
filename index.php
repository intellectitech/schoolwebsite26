<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/datastore.php';

$page_title = SITE_NAME . " | " . SITE_MOTTO;
$active_page = 'home';

// Latest 3 published news posts
$allNews = ds_read('news');
$allNews = array_values(array_filter($allNews, function ($p) { return !empty($p['is_published']); }));
usort($allNews, function ($a, $b) { return strtotime($b['published_at']) - strtotime($a['published_at']); });
$latestNews = array_slice($allNews, 0, 3);

// Gallery preview (6 most recent images)
$allGallery = ds_read('gallery');
usort($allGallery, function ($a, $b) { return strtotime($b['uploaded_at'] ?? 'now') - strtotime($a['uploaded_at'] ?? 'now'); });
$galleryPreview = array_slice($allGallery, 0, 6);

require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<!-- HERO -->
<section class="hero" style="background-image: linear-gradient(135deg, rgba(11,45,92,0.85), rgba(20,80,163,0.75)), url('assets/images/school.jpg');">

    <div class="container hero-inner">
        <div class="hero-content">
            <span class="hero-eyebrow">
                Kindergarten · Primary · Day & Boarding
            </span>
            <h1>
                Welcome to <span>Mbuya Parents' School</span>
            </h1>
            <p class="lead">
                A top pre-primary and primary school in Kampala, nurturing every learner's God-given talents through holistic, practical, values-driven education.
                <?php echo SITE_MOTTO; ?>.
            </p>
            <div class="hero-actions">
                <a href="admissions.php" class="btn btn-primary">
                    Apply for Admission
                </a>
                <a href="about.php" class="btn btn-outline">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats-strip">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item"><div class="stat-number">1000+</div><div class="stat-label">Pupils</div></div>
      <div class="stat-item"><div class="stat-number">2</div><div class="stat-label">Swimming Pools</div></div>
      <div class="stat-item"><div class="stat-number">Top</div><div class="stat-label">PLE Performers</div></div>
      <div class="stat-item"><div class="stat-number">K&ndash;P7</div><div class="stat-label">Kindergarten to Primary 7</div></div>
    </div>
  </div>
</div>

<!-- WELCOME / ABOUT SNAPSHOT -->
<section>
  <div class="container split">
    <div class="split-art">
      <svg viewBox="0 0 400 320" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="320" rx="16" fill="#ffffff"/>

    <image 
      href="assets/images/students.jpg"
      x="20"
      y="20"
      width="360"
      height="280"
      preserveAspectRatio="xMidYMid slice"
      clip-path="inset(0 round 16px)"
    />
  </svg>
</div>
    </div>
    <div class="split-copy">
      <span class="eyebrow">Who We Are</span>
      <h2>Nurturing Talent, Building Character</h2>
      <p>Mbuya Parents' School is a mixed day and boarding Pre-primary and Primary school located at Plot 22-25 Nadiope Lane, off Ismael Road, Mbuya II Parish, Nakawa Division, Kampala. We provide holistic and practical education that enables our pupils to develop their unique, God-given talents and abilities alongside excellent academic grades.</p>
      <ul class="checklist">
        <li>Conducive classrooms and a spacious, secure campus</li>
        <li>ICT Laboratory and well-stocked Library</li>
        <li>Two swimming pools and other sports facilities</li>
        <li>Consistently top-performing PLE candidates</li>
      </ul>
      <a href="about.php" class="btn btn-blue">More About Our School</a>
    </div>
  </div>
</section>

<!-- HEAD TEACHER MESSAGE -->
<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">From the Head Teacher</span>
      <h2>A Warm Welcome to Our School Community</h2>
    </div>
    <div class="message-block">
      "Education is the most powerful weapon one can use to change the world." It is with this conviction that Mbuya Parents' School devotedly and professionally nurtures every pupil into a virtuous and competent person, ready to provide transformative leadership for our country and the world. We employ a holistic education model built around each learner's unique talents, preparing them to thrive in today's fast-changing world.
    </div>
  </div>
</section>

<!-- FACILITIES / PROGRAMMES -->
<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Why Choose Us</span>
      <h2>Facilities &amp; Programmes</h2>
      <p>State-of-the-art infrastructure supporting academic excellence and holistic growth.</p>
    </div>
    <div class="grid grid-4">
      <div class="card"><div class="card-body"><h3>Conducive Classrooms</h3><p>Spacious, well-resourced classrooms designed for focused learning from Kindergarten to P7.</p></div></div>
      <div class="card"><div class="card-body"><h3>ICT Laboratory</h3><p>A modern computer lab that builds digital literacy skills from an early age.</p></div></div>
      <div class="card"><div class="card-body"><h3>Two Swimming Pools</h3><p>Dedicated pools supporting our swimming programme, fitness, and water safety.</p></div></div>
      <div class="card"><div class="card-body"><h3>Library</h3><p>A well-stocked library nurturing a lifelong love for reading and research.</p></div></div>
    </div>
  </div>
</section>

<!-- NEWS PREVIEW -->
<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Latest Updates</span>
      <h2>News &amp; Blog</h2>
      <p>Stay up to date with school news, events and parenting tips.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($latestNews as $post):
          $title = $post['title'];
          $excerpt = $post['excerpt'];
          $cover = $post['cover_image'];
          $category = $post['category'] ?? 'News';
          $date = date('d M Y', strtotime($post['published_at']));
          $slug = $post['slug'];
      ?>
      <div class="card">
        <img src="<?php echo htmlspecialchars($cover); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="card-img">
        <div class="card-body">
          <div class="news-meta"><?php echo htmlspecialchars($category); ?><span class="news-date"><?php echo $date; ?></span></div>
          <h3><?php echo htmlspecialchars($title); ?></h3>
          <p><?php echo htmlspecialchars($excerpt); ?></p>
          <a href="news.php?slug=<?php echo urlencode($slug); ?>" class="read-more">Read More &rarr;</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center" style="margin-top:34px;">
      <a href="news.php" class="btn btn-blue">View All News &amp; Blog Posts</a>
    </div>
  </div>
</section>

<!-- GALLERY PREVIEW -->
<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Life at Mbuya</span>
      <h2>Photo Gallery</h2>
      <p>Glimpses of academics, sports, ICT and school celebrations.</p>
    </div>
    <div class="gallery-grid">
      <?php foreach ($galleryPreview as $img): ?>
      <div class="gallery-item" data-category="preview">
        <img src="<?php echo htmlspecialchars($img['file_path']); ?>" alt="<?php echo htmlspecialchars($img['title']); ?>">
        <div class="gallery-caption"><?php echo htmlspecialchars($img['title']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center" style="margin-top:34px;">
      <a href="gallery.php" class="btn btn-blue">View Full Gallery</a>
    </div>
  </div>
</section>

<!-- CTA -->
<section>
  <div class="cta-banner">
    <h2>Ready to Join the Mbuya Parents' School Family?</h2>
    <p>Admissions are open for Kindergarten through Primary Seven, Day and Boarding.</p>
    <a href="admissions.php" class="btn btn-primary">Start Your Application</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
