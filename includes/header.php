<title><?= htmlspecialchars($pageTitle ?? 'School Website') ?></title>
<meta name="description" content="<?= htmlspecialchars(getSettings($pdo, 'meta_description')) ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/body.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/hero.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/stats-bar.css">