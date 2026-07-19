<?php
require_once __DIR__ . '/includes/admin_auth.php';

$editing = false;
$event = ['id' => null, 'title' => '', 'description' => '', 'event_date' => date('Y-m-d'), 'location' => ''];

$allEvents = ds_read('events');

if (!empty($_GET['id'])) {
    $found = ds_find($allEvents, $_GET['id']);
    if ($found) { $event = $found; $editing = true; }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $existingId = $_POST['id'] ?: null;

    if ($title === '') { $errors[] = "Event title is required."; }
    if ($event_date === '') { $errors[] = "Event date is required."; }

    if (empty($errors)) {
        $data = ['title' => $title, 'description' => $description, 'event_date' => $event_date, 'location' => $location];

        if ($existingId) {
            $allEvents = ds_update($allEvents, $existingId, $data);
            $flashMsg = 'Event updated successfully.';
        } else {
            $data['id'] = ds_next_id($allEvents);
            $allEvents[] = $data;
            $flashMsg = 'Event added successfully.';
        }

        if (ds_write('events', $allEvents)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => $flashMsg];
            header('Location: events.php');
            exit;
        } else {
            $errors[] = "Could not save the event — check that the /data folder is writable.";
        }
    }

    $event = array_merge($event, ['title' => $title, 'description' => $description, 'event_date' => $event_date, 'location' => $location, 'id' => $existingId]);
    if ($existingId) { $editing = true; }
}

$page_title = $editing ? "Edit Event" : "Add Event";
$active_nav = "events";
require_once __DIR__ . '/includes/admin_header.php';
?>

<?php foreach ($errors as $err): ?>
  <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
<?php endforeach; ?>

<div class="panel">
  <div class="panel-header">
    <h2><?php echo $editing ? 'Edit Event' : 'Add New Event'; ?></h2>
    <a href="events.php" class="btn btn-outline btn-sm">&larr; Back to Events</a>
  </div>
  <div class="panel-body">
    <form method="POST" action="events_edit.php<?php echo $editing ? '?id=' . $event['id'] : ''; ?>">
      <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">

      <div class="form-grid">
        <div class="field field-full">
          <label for="title">Event Title *</label>
          <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($event['title']); ?>">
        </div>
        <div class="field">
          <label for="event_date">Event Date *</label>
          <input type="date" id="event_date" name="event_date" required value="<?php echo htmlspecialchars($event['event_date']); ?>">
        </div>
        <div class="field">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" placeholder="e.g. School Main Hall">
        </div>
        <div class="field field-full">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-gold"><?php echo $editing ? 'Save Changes' : 'Add Event'; ?></button>
        <a href="events.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
