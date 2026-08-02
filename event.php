<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Event Details';
$activePage = 'events';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$event = $id ? findEventById($id) : null;

if ($event === null) {
    http_response_code(404);
}

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <?php if ($event === null): ?>
        <section class="section">
            <div class="container narrow">
                <div class="empty-state">
                    <h1>Event Not Found</h1>
                    <p>The requested event does not exist or the event ID is invalid.</p>
                    <a class="button primary" href="events.php">Return to Events</a>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="page-banner">
            <div class="container">
                <p class="eyebrow"><?= e($event['category']) ?></p>
                <h1><?= e($event['title']) ?></h1>
                <p>Review the event information and register online.</p>
            </div>
        </section>

        <section class="section">
            <div class="container event-detail-grid">
                <div class="event-detail-image">
                    <img src="assets/images/<?= e($event['image']) ?>" alt="<?= e($event['title']) ?> illustration">
                </div>
                <article class="detail-card">
                    <h2>Event Information</h2>
                    <dl class="detail-list">
                        <div><dt>Date</dt><dd><?= e(formatEventDate($event['date'])) ?></dd></div>
                        <div><dt>Time</dt><dd><?= e(formatEventTime($event['time'])) ?></dd></div>
                        <div><dt>Location</dt><dd><?= e($event['location']) ?></dd></div>
                        <div><dt>Category</dt><dd><?= e($event['category']) ?></dd></div>
                    </dl>
                    <h2>Description</h2>
                    <p><?= e($event['description']) ?></p>
                    <div class="button-group">
                        <a class="button primary" href="register.php?event=<?= (int) $event['id'] ?>">Register for This Event</a>
                        <a class="button secondary" href="events.php">Back to Events</a>
                    </div>
                </article>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
