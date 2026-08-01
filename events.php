<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';

$page_title = "Events | " . SITE_NAME;
$active_page = 'events';
require_once __DIR__ . '/includes/header.php';

$upcomingEvents = [];
$pastEvents = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
        $upcomingEvents = $stmt->fetchAll();
    } catch (Exception $e) { $upcomingEvents = []; }
    try {
        $stmt = $pdo->query("SELECT * FROM events WHERE event_date < CURDATE() ORDER BY event_date DESC LIMIT 6");
        $pastEvents = $stmt->fetchAll();
    } catch (Exception $e) { $pastEvents = []; }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <h1>School Events</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / Events</div>
  </div>
</div>

<section>
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Save the Date</span>
      <h2>Upcoming Events</h2>
      <p>Key dates on the Mbuya Parents' School calendar &mdash; sports days, celebrations, parent meetings and more.</p>
    </div>

    <?php if (empty($upcomingEvents)): ?>
      <div class="empty-state" style="text-align:center; padding:40px 20px; color:var(--text-light);">
        <p>No upcoming events are scheduled right now. Please check back soon, or follow our <a href="<?php echo SITE_INSTAGRAM; ?>" target="_blank" rel="noopener" style="color:var(--blue); font-weight:700;">Instagram page</a> for the latest updates.</p>
      </div>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($upcomingEvents as $ev):
            $day = date('d', strtotime($ev['event_date']));
            $mon = date('M', strtotime($ev['event_date']));
            $year = date('Y', strtotime($ev['event_date']));
            $weekday = date('l', strtotime($ev['event_date']));
        ?>
        <div class="card">
          <div class="card-body" style="display:flex; gap:16px; align-items:flex-start;">
            <div style="flex-shrink:0; text-align:center; background:var(--navy); color:var(--white); border-radius:10px; padding:10px 14px; min-width:64px;">
              <div style="font-size:1.4rem; font-weight:800; line-height:1;"><?php echo $day; ?></div>
              <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--gold); font-weight:700;"><?php echo $mon; ?></div>
              <div style="font-size:0.7rem; color:var(--sky-2); margin-top:2px;"><?php echo $year; ?></div>
            </div>
            <div>
              <div class="news-meta" style="margin-bottom:4px;"><?php echo htmlspecialchars($weekday); ?></div>
              <h3 style="margin-bottom:6px;"><?php echo htmlspecialchars($ev['title']); ?></h3>
              <?php if (!empty($ev['description'])): ?>
                <p style="margin-bottom:8px; font-size:0.88rem;"><?php echo htmlspecialchars($ev['description']); ?></p>
              <?php endif; ?>
              <?php if (!empty($ev['location'])): ?>
                <p style="margin-bottom:0; font-size:0.85rem; color:var(--text-light);"> <?php echo htmlspecialchars($ev['location']); ?></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if (!empty($pastEvents)): ?>
<section class="bg-sky">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Looking Back</span>
      <h2>Recent Past Events</h2>
      <p>A look at what our school community has recently celebrated together.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($pastEvents as $ev): ?>
      <div class="card">
        <div class="card-body">
          <div class="news-meta"><?php echo date('d M Y', strtotime($ev['event_date'])); ?></div>
          <h3><?php echo htmlspecialchars($ev['title']); ?></h3>
          <?php if (!empty($ev['location'])): ?>
            <p>&#128205; <?php echo htmlspecialchars($ev['location']); ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section>
  <div class="cta-banner">
    <h2>Never Miss a Moment</h2>
    <p>Follow us on Facebook or check back here regularly for the latest school events and updates.</p>
    <a href="<?php echo SITE_FACEBOOK; ?>" target="_blank" rel="noopener" class="btn btn-primary">Follow on Facebook</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
