<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/datastore.php';

$page_title = "Academics | " . SITE_NAME;
$active_page = 'academics';

$allEvents = ds_read('events');
$today = date('Y-m-d');
$events = array_values(array_filter($allEvents, function ($e) use ($today) { return $e['event_date'] >= $today; }));
usort($events, function ($a, $b) { return strtotime($a['event_date']) - strtotime($b['event_date']); });
$events = array_slice($events, 0, 5);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <h1>Academics</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / Academics</div>
  </div>
</div>

<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Curriculum</span>
      <h2>From Kindergarten to Primary Seven</h2>
      <p>A structured, holistic academic journey built on the national curriculum and enriched with practical, talent-based learning.</p>
    </div>
    <div class="grid grid-3">
      <div class="card"><div class="card-body"><h3>Pre-Primary (Kindergarten)</h3><p>A nurturing introduction to numeracy, literacy, play-based learning and social skills for our youngest learners.</p></div></div>
      <div class="card"><div class="card-body"><h3>Lower Primary (P1&ndash;P3)</h3><p>Building strong foundations in literacy, numeracy and life skills through the national curriculum.</p></div></div>
      <div class="card"><div class="card-body"><h3>Upper Primary (P4&ndash;P7)</h3><p>Deepened subject mastery and rigorous PLE preparation, alongside co-curricular talent development.</p></div></div>
    </div>
  </div>
</section>

<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Track Record</span>
      <h2>Consistent PLE Excellence</h2>
    </div>
    <div class="message-block">
      We have consistently excelled in the national Primary Leaving Examinations (PLE), with our candidates individually and collectively standing out as top performers in not only Kampala District, but also in the whole country. Our practical and holistic education curriculum nurtures creativity, hard work and talent development among pupils, preparing them to favourably compete in today's fast-changing, competitive world.
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Beyond the Classroom</span>
      <h2>Co-Curricular Activities</h2>
    </div>
    <div class="grid grid-4">
      <div class="card"><div class="card-body"><h3>Swimming</h3><p>Lessons and galas across our two swimming pools building confidence and fitness.</p></div></div>
      <div class="card"><div class="card-body"><h3>Sports &amp; Athletics</h3><p>Football, athletics and inter-house competitions on our sports grounds.</p></div></div>
      <div class="card"><div class="card-body"><h3>Music, Dance &amp; Drama</h3><p>Talent development through performance, creativity and self-expression.</p></div></div>
      <div class="card"><div class="card-body"><h3>ICT Skills</h3><p>Hands-on computer lessons in our ICT Laboratory from an early age.</p></div></div>
    </div>
  </div>
</section>

<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Academic Calendar</span>
      <h2>Upcoming Events</h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($events as $event):
          $title = $event['title'];
          $date = date('d M Y', strtotime($event['event_date']));
          $location = $event['location'];
      ?>
      <div class="card">
        <div class="card-body">
          <div class="news-meta"><?php echo $date; ?></div>
          <h3><?php echo htmlspecialchars($title); ?></h3>
          <p><?php echo htmlspecialchars($location); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section>
  <div class="cta-banner">
    <h2>Give Your Child a Strong Academic Foundation</h2>
    <p>Enrol today and be part of a school with a proven record of PLE excellence.</p>
    <a href="admissions.php" class="btn btn-primary">Apply for Admission</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
