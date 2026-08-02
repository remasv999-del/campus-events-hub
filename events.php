<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Events';
$activePage = 'events';
$events = getEvents();

$query = trim((string) ($_GET['q'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$categories = array_values(array_unique(array_column($events, 'category')));
sort($categories);

$filteredEvents = array_values(array_filter($events, static function (array $event) use ($query, $category): bool {
    $matchesQuery = $query === '' || stripos(
        $event['title'] . ' ' . $event['description'] . ' ' . $event['location'],
        $query
    ) !== false;
    $matchesCategory = $category === '' || $event['category'] === $category;
    return $matchesQuery && $matchesCategory;
}));

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">Campus Activities</p>
            <h1>Upcoming Events</h1>
            <p>Search and filter the available workshops, seminars, competitions, and trips.</p>
        </div>
    </section>

    <section class="section" aria-labelledby="events-list-title">
        <div class="container">
            <form class="filter-form" method="GET" action="events.php" role="search">
                <div class="form-group">
                    <label for="q">Search events</label>
                    <input id="q" name="q" type="search" value="<?= e($query) ?>" placeholder="Search by title, location, or keyword">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $categoryOption): ?>
                            <option value="<?= e($categoryOption) ?>" <?= $category === $categoryOption ? 'selected' : '' ?>>
                                <?= e($categoryOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-actions">
                    <button class="button primary" type="submit">Apply Filters</button>
                    <a class="button secondary" href="events.php">Reset</a>
                </div>
            </form>

            <div class="results-summary" aria-live="polite">
                <h2 id="events-list-title"><?= count($filteredEvents) ?> Event<?= count($filteredEvents) === 1 ? '' : 's' ?> Found</h2>
            </div>

            <?php if ($filteredEvents === []): ?>
                <div class="empty-state">
                    <h3>No matching events</h3>
                    <p>Try a different search word or choose another category.</p>
                </div>
            <?php else: ?>
                <div class="event-grid">
                    <?php foreach ($filteredEvents as $event): ?>
                        <article class="event-card">
                            <img src="assets/images/<?= e($event['image']) ?>" alt="<?= e($event['title']) ?> illustration">
                            <div class="event-card-body">
                                <p class="event-category"><?= e($event['category']) ?></p>
                                <h3><?= e($event['title']) ?></h3>
                                <dl class="event-meta">
                                    <div><dt>Date</dt><dd><?= e(formatEventDate($event['date'])) ?></dd></div>
                                    <div><dt>Time</dt><dd><?= e(formatEventTime($event['time'])) ?></dd></div>
                                    <div><dt>Location</dt><dd><?= e($event['location']) ?></dd></div>
                                </dl>
                                <p><?= e($event['description']) ?></p>
                                <a class="button small" href="event.php?id=<?= (int) $event['id'] ?>">View Details</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
