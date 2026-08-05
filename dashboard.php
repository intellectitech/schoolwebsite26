<?php
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$eventMessage = '';
$messageCount = 0;
$newsCount = 0;
$eventCount = 0;
$applicationCount = 0;
$messages = [];
$newsPosts = [];
$events = [];

if ($pdo instanceof PDO) {
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_title'])) {
            $title = trim($_POST['event_title'] ?? '');
            $description = trim($_POST['event_description'] ?? '');
            $location = trim($_POST['event_location'] ?? '');
            $date = trim($_POST['event_date'] ?? '');

            if ($title === '' || $description === '' || $location === '' || $date === '') {
                $eventMessage = 'Please complete all event fields before saving.';
            } else {
                $insert = $pdo->prepare('INSERT INTO events (title, description, location, event_date) VALUES (?, ?, ?, ?)');
                $eventMessage = $insert->execute([$title, $description, $location, $date])
                    ? 'Event created successfully.'
                    : 'Unable to save the event. Please try again.';
            }
        }

        $messageCount = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
        $newsCount = (int) $pdo->query('SELECT COUNT(*) FROM news_posts')->fetchColumn();
        $eventCount = (int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
        $applicationCount = (int) $pdo->query('SELECT COUNT(*) FROM admission_applications')->fetchColumn();

        $messages = $pdo->query('SELECT name, email, message, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
        $newsPosts = $pdo->query('SELECT title, content, created_at FROM news_posts ORDER BY created_at DESC LIMIT 5')->fetchAll();
        $events = $pdo->query('SELECT title, description, location, event_date FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5')->fetchAll();
    } catch (PDOException $e) {
        $dbError = 'A database error occurred while loading the dashboard.';
    }
}

$pageTitle = 'Admin Dashboard';
$pageDescription = 'Staff dashboard for Namugongo Model Primary School.';
require_once __DIR__ . '/includes/header.php';

$maxCount = max(1, $messageCount, $newsCount, $eventCount, $applicationCount);
?>
        <section class="page-hero">
            <div class="container">
                <h1>School Dashboard</h1>
                <p>Welcome back, <?php echo h($adminName); ?>. Track enquiries, admission applications, news and upcoming events at a glance.</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <?php if (isset($dbError)) : ?>
                    <p class="notice notice-error"><?php echo h($dbError); ?></p>
                <?php endif; ?>

                <div class="dashboard-grid">
                    <div class="dashboard-card reveal">
                        <h3>Messages</h3>
                        <div class="dashboard-number"><?php echo $messageCount; ?></div>
                        <p>Contact submissions received</p>
                    </div>
                    <div class="dashboard-card reveal">
                        <h3>Admission Applications</h3>
                        <div class="dashboard-number"><?php echo $applicationCount; ?></div>
                        <p>Parent applications received</p>
                    </div>
                    <div class="dashboard-card reveal">
                        <h3>News Items</h3>
                        <div class="dashboard-number"><?php echo $newsCount; ?></div>
                        <p>Published updates</p>
                    </div>
                    <div class="dashboard-card reveal">
                        <h3>Upcoming Events</h3>
                        <div class="dashboard-number"><?php echo $eventCount; ?></div>
                        <p>Scheduled activities</p>
                    </div>
                </div>

                <!-- Simple CSS bar-chart overview (no external chart library needed) -->
                <div class="table-card reveal" style="margin-bottom:24px;">
                    <h3>Activity Overview</h3>
                    <div style="display:flex; align-items:flex-end; gap:22px; height:160px; padding-top:10px;">
                        <?php
                        $bars = [
                            'Messages' => $messageCount,
                            'Applications' => $applicationCount,
                            'News' => $newsCount,
                            'Events' => $eventCount,
                        ];
                        foreach ($bars as $label => $count) :
                            $heightPct = max(6, (int) round(($count / $maxCount) * 100));
                        ?>
                            <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <div style="width:100%; max-width:56px; height:130px; background:rgba(56,189,248,0.12); border-radius:8px; display:flex; align-items:flex-end; overflow:hidden;">
                                    <div style="width:100%; height:<?php echo $heightPct; ?>%; background:linear-gradient(180deg, var(--sky), var(--sky-strong)); border-radius:8px 8px 0 0;"></div>
                                </div>
                                <span style="font-size:0.78rem; color:var(--muted); text-align:center;"><?php echo h($label); ?><br><strong style="color:#fff;"><?php echo $count; ?></strong></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="highlight-panel reveal">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                        <div>
                            <h3>School Events This Week</h3>
                            <p>Sports day, club leadership training, and a parent-teacher meeting are scheduled this week to keep our school strong and connected.</p>
                        </div>
                        <a class="btn" href="logout.php">Log Out</a>
                    </div>
                </div>

                <div class="form-card reveal">
                    <h3>Create New Event</h3>
                    <?php if ($eventMessage) : ?>
                        <p class="notice <?php echo strpos($eventMessage, 'successfully') !== false ? 'notice-success' : 'notice-error'; ?>"><?php echo h($eventMessage); ?></p>
                    <?php endif; ?>
                    <form method="POST" action="dashboard.php">
                        <label for="event_title">Event Title</label>
                        <input type="text" id="event_title" name="event_title" required>
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" required>
                        <label for="event_location">Location</label>
                        <input type="text" id="event_location" name="event_location" required>
                        <label for="event_description">Description</label>
                        <textarea id="event_description" name="event_description" required></textarea>
                        <button type="submit" class="btn">Add Event</button>
                    </form>
                </div>

                <div class="table-card reveal">
                    <h3>Recent Messages</h3>
                    <table class="data-table">
                        <thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php if (empty($messages)) : ?>
                            <tr><td colspan="4">No messages yet.</td></tr>
                        <?php else : foreach ($messages as $message) : ?>
                            <tr>
                                <td><?php echo h($message['name']); ?></td>
                                <td><?php echo h($message['email']); ?></td>
                                <td><?php echo h(substr($message['message'], 0, 60)); ?></td>
                                <td><?php echo h($message['created_at']); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-card reveal">
                    <h3>Latest News</h3>
                    <table class="data-table">
                        <thead><tr><th>Title</th><th>Summary</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php if (empty($newsPosts)) : ?>
                            <tr><td colspan="3">No news posted yet.</td></tr>
                        <?php else : foreach ($newsPosts as $post) : ?>
                            <tr>
                                <td><?php echo h($post['title']); ?></td>
                                <td><?php echo h(substr($post['content'], 0, 70)); ?></td>
                                <td><?php echo h($post['created_at']); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-card reveal">
                    <h3>Upcoming Events</h3>
                    <table class="data-table">
                        <thead><tr><th>Event</th><th>Date</th><th>Location</th><th>Description</th></tr></thead>
                        <tbody>
                        <?php if (empty($events)) : ?>
                            <tr><td colspan="4">No upcoming events are scheduled at the moment.</td></tr>
                        <?php else : foreach ($events as $event) : ?>
                            <tr>
                                <td><?php echo h($event['title']); ?></td>
                                <td><?php echo h($event['event_date']); ?></td>
                                <td><?php echo h($event['location']); ?></td>
                                <td><?php echo h(substr($event['description'], 0, 80)); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
