<?php
// ============================================================
//  admin/settings.php — Manage Site Settings (school_info table)
//  Covers the small facts that appear all over the site: school
//  name, motto, contact details, homepage hero text, and stats.
// ============================================================
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin();

// Friendly labels + input type for the settings we already know
// about. Anything not listed here (e.g. a custom setting someone
// added) falls back to a generic text field using its own
// description as the label.
$fieldMeta = [
  'school_name' => ['label' => 'School Name', 'type' => 'text'],
  'school_motto' => ['label' => 'School Motto', 'type' => 'text'],
  'contact_phone' => ['label' => 'Contact Phone', 'type' => 'tel'],
  'contact_email' => ['label' => 'Contact Email', 'type' => 'email'],
  'school_address' => ['label' => 'School Address', 'type' => 'textarea'],
  'hero_title' => ['label' => 'Homepage Hero Title', 'type' => 'text'],
  'hero_subtitle' => ['label' => 'Homepage Hero Subtitle', 'type' => 'textarea'],
  'founded_year' => ['label' => 'Founded Year', 'type' => 'number'],
  'total_students' => ['label' => 'Total Students', 'type' => 'number'],
];

// ── HANDLE SAVE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
  $formAction = $_POST['form_action'] ?? 'update_settings';

  if ($formAction === 'update_settings') {
    $submitted = $_POST['settings'] ?? [];
    $errors = [];
    $changedCount = 0;

    // Look up each row's key so we can validate by type.
    $rows = $pdo->query('SELECT id, setting_key FROM school_info')->fetchAll();
    $keyById = [];
    foreach ($rows as $r) {
      $keyById[$r['id']] = $r['setting_key'];
    }

    foreach ($submitted as $id => $rawValue) {
      $id = (int) $id;
      if (!isset($keyById[$id]))
        continue;
      $key = $keyById[$id];
      $value = clean($rawValue);

      if ($key === 'contact_email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid contact email address.';
        continue;
      }
      if (in_array($key, ['founded_year', 'total_students'], true) && $value !== '' && !ctype_digit($value)) {
        $errors[] = 'Founded year and total students must be numbers.';
        continue;
      }

      if (updateSetting($pdo, $id, $value, $_SESSION['admin_id'])) {
        $changedCount++;
        auditLog($pdo, $_SESSION['admin_id'], 'update', 'school_info', $id, 'Updated setting: ' . $key);
      }
    }

    if ($errors) {
      setFlashErrors(array_unique($errors));
    } elseif ($changedCount > 0) {
      setFlashSuccess($changedCount === 1 ? 'Setting updated.' : "{$changedCount} settings updated.");
    } else {
      setFlashSuccess('No changes to save.');
    }
    redirectTo('settings.php');
  }

  if ($formAction === 'add_setting') {
    $newKey = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]+/', '_', $_POST['setting_key'] ?? '')));
    $newValue = clean($_POST['setting_value'] ?? '');
    $newDescription = clean($_POST['description'] ?? '');

    if ($newKey === '') {
      setFlashErrors(['Please give the new setting a key (letters, numbers, underscores only).']);
      redirectTo('settings.php');
    }

    $exists = $pdo->prepare('SELECT id FROM school_info WHERE setting_key = ?');
    $exists->execute([$newKey]);
    if ($exists->fetch()) {
      setFlashErrors(["A setting called \"{$newKey}\" already exists."]);
      redirectTo('settings.php');
    }

    $pdo->prepare(
      'INSERT INTO school_info (setting_key, setting_value, description, updated_by) VALUES (?, ?, ?, ?)'
    )->execute([$newKey, $newValue, $newDescription, $_SESSION['admin_id']]);
    $newId = $pdo->lastInsertId();
    auditLog($pdo, $_SESSION['admin_id'], 'insert', 'school_info', $newId, 'Added setting: ' . $newKey);
    setFlashSuccess('New setting added.');
    redirectTo('settings.php');
  }

  if ($formAction === 'delete_setting') {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT setting_key FROM school_info WHERE id = ?');
    $stmt->execute([$id]);
    $key = $stmt->fetchColumn();
    // Guard: the known settings above are relied on across the site,
    // so only custom (unlisted) settings can be deleted from here.
    if ($key && !isset($fieldMeta[$key])) {
      $pdo->prepare('DELETE FROM school_info WHERE id = ?')->execute([$id]);
      auditLog($pdo, $_SESSION['admin_id'], 'delete', 'school_info', $id, 'Deleted setting: ' . $key);
      setFlashSuccess('Setting removed.');
    } else {
      setFlashErrors(['That setting is used across the site and cannot be deleted.']);
    }
    redirectTo('settings.php');
  }
}

$flash = getFlash();

// ── LOAD SETTINGS ─────────────────────────────────────────
$settings = $pdo->query('SELECT * FROM school_info ORDER BY id ASC')->fetchAll();

// Split into "known" settings (shown first, in a friendly fixed
// order) and any custom ones someone has added on top.
$knownOrder = array_keys($fieldMeta);
$known = [];
$custom = [];
foreach ($settings as $row) {
  if (isset($fieldMeta[$row['setting_key']])) {
    $known[$row['setting_key']] = $row;
  } else {
    $custom[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Site Settings · Admin · Uganda Martyrs Primary School</title>
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
        <div>
          <h1>Site Settings</h1>
          <p>These values feed the school name, contact details, and homepage text shown across the site.</p>
        </div>
      </header>

      <?php if ($flash['success']): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash['success']) ?></div><?php endif; ?>
      <?php if ($flash['errors']): ?>
        <div class="alert alert-error">
          <ul><?php foreach ($flash['errors'] as $err): ?>
              <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <section class="admin-panel">
        <form method="POST" action="settings.php" class="admin-form">
          <?= csrfField() ?>
          <input type="hidden" name="form_action" value="update_settings">

          <?php foreach ($knownOrder as $key): ?>
            <?php if (!isset($known[$key]))
              continue; ?>
            <?php $row = $known[$key];
            $meta = $fieldMeta[$key]; ?>
            <div class="field">
              <label for="setting_<?= $row['id'] ?>"><?= htmlspecialchars($meta['label']) ?></label>
              <?php if ($meta['type'] === 'textarea'): ?>
                <textarea id="setting_<?= $row['id'] ?>" name="settings[<?= $row['id'] ?>]"
                  rows="2"><?= htmlspecialchars($row['setting_value']) ?></textarea>
              <?php else: ?>
                <input id="setting_<?= $row['id'] ?>" name="settings[<?= $row['id'] ?>]"
                  type="<?= $meta['type'] === 'number' ? 'number' : ($meta['type'] === 'email' ? 'email' : ($meta['type'] === 'tel' ? 'tel' : 'text')) ?>"
                  value="<?= htmlspecialchars($row['setting_value']) ?>">
              <?php endif; ?>
              <?php if ($row['description']): ?>
                <p class="field-hint"><?= htmlspecialchars($row['description']) ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <div class="admin-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
          </div>
        </form>
      </section>

      <section class="admin-panel admin-panel-narrow">
        <div class="admin-panel-head">
          <h2>Custom Settings</h2>
        </div>
        <?php if ($custom): ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Key</th>
                <th>Value</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($custom as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['setting_key']) ?></td>
                  <td><?= htmlspecialchars(excerpt($row['setting_value'], 60)) ?></td>
                  <td class="admin-table-actions">
                    <form method="POST" action="settings.php" onsubmit="return confirm('Delete this setting?');">
                      <?= csrfField() ?>
                      <input type="hidden" name="form_action" value="delete_setting">
                      <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                      <button type="submit" class="link-btn link-btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="admin-empty">No custom settings yet.</p>
        <?php endif; ?>

        <form method="POST" action="settings.php" class="admin-inline-form">
          <?= csrfField() ?>
          <input type="hidden" name="form_action" value="add_setting">
          <input type="text" name="setting_key" placeholder="Setting key (e.g. facebook_url)" maxlength="100" required>
          <input type="text" name="setting_value" placeholder="Value" maxlength="500">
          <input type="text" name="description" placeholder="Description (optional)" maxlength="300">
          <button type="submit" class="btn btn-ghost">Add Setting</button>
        </form>
      </section>
    </main>
  </div>
</body>

</html>