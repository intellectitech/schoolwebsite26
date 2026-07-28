<?php
// ============================================================
//  admin/event-form.php — Add / Edit a School Event
//  ?edit=ID  → edit that event
//  (no param) → new event
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$event = null;

if ($editId) {
  $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
  $stmt->execute([$editId]);
  $event = $stmt->fetch();
  if (!$event) {
    setFlashErrors(['That event could not be found.']);
    redirectTo('events.php');
  }
}

// ── HANDLE SAVE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlashErrors(['Your session expired before the form was submitted. Please try again.']);
    redirectTo($editId ? "event-form.php?edit={$editId}" : 'event-form.php');
  }

  $title = clean($_POST['title'] ?? '');
  $description = clean($_POST['description'] ?? '');
  $location = clean($_POST['location'] ?? '');
  $eventDate = clean($_POST['event_date'] ?? '');
  $startTime = clean($_POST['start_time'] ?? '');
  $endTime = clean($_POST['end_time'] ?? '');
  $isPublished = isset($_POST['is_published']) ? 1 : 0;
  $removeImage = isset($_POST['remove_image']);

  $errors = [];

  if ($title === '')
    $errors[] = 'Please give the event a title.';
  if (mb_strlen($title) > 290)
    $errors[] = 'That title is too long.';
  if ($description === '')
    $errors[] = 'Please add a short description.';
  if ($location === '')
    $errors[] = 'Please add a location.';
  if ($eventDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate))
    $errors[] = 'Please choose a valid event date.';
  if ($startTime === '' || !preg_match('/^\d{2}:\d{2}$/', $startTime))
    $errors[] = 'Please choose a start time.';
  if ($endTime === '' || !preg_match('/^\d{2}:\d{2}$/', $endTime))
    $errors[] = 'Please choose an end time.';
  if (!$errors && $endTime <= $startTime)
    $errors[] = 'The end time must be after the start time.';

  if (!empty($errors)) {
    setFlashErrors($errors);
    setOldInput([
      'title' => $title,
      'description' => $description,
      'location' => $location,
      'event_date' => $eventDate,
      'start_time' => $startTime,
      'end_time' => $endTime,
      'is_published' => $isPublished,
    ]);
    redirectTo($editId ? "event-form.php?edit={$editId}" : 'event-form.php');
  }

  // Only touch the filesystem once every other field has already passed.
  $upload = uploadEventImage('featured_img');
  if ($upload['error']) {
    setFlashErrors([$upload['error']]);
    setOldInput([
      'title' => $title,
      'description' => $description,
      'location' => $location,
      'event_date' => $eventDate,
      'start_time' => $startTime,
      'end_time' => $endTime,
      'is_published' => $isPublished,
    ]);
    redirectTo($editId ? "event-form.php?edit={$editId}" : 'event-form.php');
  }

  $imagePath = $editId ? $event['featured_img'] : '';
  if ($removeImage) {
    deleteEventImageFile($imagePath);
    $imagePath = '';
  }
  if ($upload['path']) {
    deleteEventImageFile($imagePath); // replace: drop the old file
    $imagePath = $upload['path'];
  }

  $startDateTime = $eventDate . ' ' . $startTime . ':00';
  $endDateTime = $eventDate . ' ' . $endTime . ':00';

  if ($editId) {
    $pdo->prepare(
      'UPDATE events SET title = ?, description = ?, location = ?, event_date = ?,
                start_time = ?, end_time = ?, featured_img = ?, is_published = ?
             WHERE id = ?'
    )->execute([
          $title,
          $description,
          $location,
          $eventDate,
          $startDateTime,
          $endDateTime,
          $imagePath,
          $isPublished,
          $editId,
        ]);
    auditLog($pdo, $_SESSION['admin_id'], 'update', 'events', $editId, 'Updated event: ' . $title);
    setFlashSuccess('"' . $title . '" has been updated.');
  } else {
    $pdo->prepare(
      'INSERT INTO events (title, description, location, event_date, start_time, end_time, featured_img, is_published, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([
          $title,
          $description,
          $location,
          $eventDate,
          $startDateTime,
          $endDateTime,
          $imagePath,
          $isPublished,
          $_SESSION['admin_id'],
        ]);
    $newId = $pdo->lastInsertId();
    auditLog($pdo, $_SESSION['admin_id'], 'insert', 'events', $newId, 'Created event: ' . $title);
    setFlashSuccess('"' . $title . '" has been ' . ($isPublished ? 'added and published' : 'saved as a draft') . '.');
  }

  redirectTo('events.php');
}

$flash = getFlash();

// Values used to fill the form: old input (after a validation error) > existing event (edit mode) > blank (new)
$f = $flash['old'];
$v = function ($key, $default = '') use ($f, $event) {
  if ($f)
    return $f[$key] ?? $default;
  if ($event)
    return $event[$key] ?? $default;
  return $default;
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit Event' : 'New Event' ?> · Admin · Uganda Martyrs Primary School</title>
  <meta name="robots" content="noindex, nofollow">
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,400..600&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="admin-body">
  <div class="admin-shell">
    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
      <header class="admin-topbar">
        <h1><?= $editId ? 'Edit Event' : 'New Event' ?></h1>
        <p><a href="events.php">← Back to all events</a></p>
      </header>

      <section class="admin-panel">
        <?php if ($flash['errors']): ?>
          <div class="alert alert-error">
            <ul><?php foreach ($flash['errors'] as $err): ?>
                <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="event-form.php<?= $editId ? '?edit=' . $editId : '' ?>"
          enctype="multipart/form-data" class="admin-form">
          <?= csrfField() ?>

          <div class="field">
            <label for="title">Event title</label>
            <input id="title" name="title" type="text" required maxlength="290"
              value="<?= htmlspecialchars($v('title')) ?>" placeholder="e.g. Annual Sports Day">
          </div>

          <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"
              required><?= htmlspecialchars($v('description')) ?></textarea>
          </div>

          <div class="field">
            <label for="location">Location</label>
            <input id="location" name="location" type="text" required maxlength="200"
              value="<?= htmlspecialchars($v('location')) ?>" placeholder="e.g. School Grounds">
          </div>

          <div class="field-row">
            <div class="field">
              <label for="event_date">Date</label>
              <input id="event_date" name="event_date" type="date" required
                value="<?= htmlspecialchars($v('event_date', $event ? date('Y-m-d', strtotime($event['event_date'])) : date('Y-m-d'))) ?>">
            </div>
            <div class="field">
              <label for="start_time">Start time</label>
              <input id="start_time" name="start_time" type="time" required
                value="<?= htmlspecialchars($v('start_time', $event ? date('H:i', strtotime($event['start_time'])) : '09:00')) ?>">
            </div>
            <div class="field">
              <label for="end_time">End time</label>
              <input id="end_time" name="end_time" type="time" required
                value="<?= htmlspecialchars($v('end_time', $event ? date('H:i', strtotime($event['end_time'])) : '17:00')) ?>">
            </div>
          </div>

          <div class="field">
            <label for="featured_img">Photo <span style="text-transform:none;letter-spacing:normal">(optional — JPG, PNG
                or WEBP, up to 3MB)</span></label>
            <?php if ($editId && $event['featured_img'] && file_exists(__DIR__ . '/../' . $event['featured_img'])): ?>
              <div class="admin-image-preview">
                <img src="../<?= htmlspecialchars($event['featured_img']) ?>" alt="Current event photo">
                <label class="admin-checkbox-inline">
                  <input type="checkbox" name="remove_image" value="1"> Remove this photo
                </label>
              </div>
            <?php endif; ?>
            <input id="featured_img" name="featured_img" type="file"
              accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
          </div>

          <label class="admin-checkbox-inline">
            <input type="checkbox" name="is_published" value="1" <?= $v('is_published', $event ? $event['is_published'] : 1) ? 'checked' : '' ?>>
            Published (visible on the site)
          </label>

          <div class="admin-actions">
            <button type="submit" class="btn btn-primary"><?= $editId ? 'Save Changes' : 'Save Event' ?></button>
            <a href="events.php" class="btn btn-ghost">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>

</html>