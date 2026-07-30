<?php
// Include inside the <body> of every admin page.
// The page must set $currentAdminPage (e.g. 'dashboard', 'news', 'events', ...)
$newEnquiryCount  = (int) $pdo->query("SELECT COUNT(*) FROM admission_enquiries WHERE status = 'new'")->fetchColumn();
$unreadMsgCount   = (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();

$adminLinks = [
    'dashboard'    => ['Dashboard',      'dashboard.php', 0],
    'news'         => ['News Articles',  'news.php', 0],
    'events'       => ['Events',         'events.php', 0],
    'enquiries'    => ['Admissions',     'enquiries.php', $newEnquiryCount],
    'testimonials' => ['Testimonials',   'testimonials.php', 0],
    'gallery'      => ['Gallery',        'gallery.php', 0],
    'messages'     => ['Messages',       'messages.php', $unreadMsgCount],
    'settings'     => ['Site Settings',  'settings.php', 0],
];
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <?= htmlspecialchars(getSetting($pdo, 'school_name')) ?>
        <span>Admin Panel</span>
    </div>
    <nav class="admin-nav">
        <?php foreach ($adminLinks as $key => [$label, $href, $badge]):
            $active = ($currentAdminPage === $key) ? 'active' : ''; ?>
        <a href="<?= $href ?>" class="admin-nav-link <?= $active ?>"><?= $label ?><?php if ($badge > 0): ?><span class="admin-nav-badge"><?= $badge ?></span><?php endif; ?></a>
        <?php endforeach; ?>
        <a href="../index.php" class="admin-nav-link" target="_blank">View Site &#8599;</a>
        <a href="logout.php" class="admin-nav-link admin-nav-logout">Log Out</a>
    </nav>
</aside>
