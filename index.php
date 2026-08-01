<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Home';
$activePage = 'home';
$upcomingEvents = array_slice(getEvents(), 0, 3);

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Technology and Innovation Club</p>
                <h1>Discover, learn, and connect on campus</h1>
                <p class="lead">
                    Explore workshops, seminars, competitions, and university trips.
                    Register online and join activities that support your academic and professional growth.
                </p>
                <div class="button-group">
                    <a class="button primary" href="events.php">Explore Events</a>
                    <a class="button secondary" href="register.php">Register Now</a>
                </div>
            </div>
            <div class="hero-visual">
                <img src="assets/images/campus-events.svg" alt="Campus Events Hub illustration">
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="next-events-title">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Coming Soon</p>
                    <h2 id="next-events-title">Next Three Events</h2>
                </div>
                <a class="text-link" href="events.php">View all events <span aria-hidden="true">→</span></a>
            </div>

            <div class="event-grid">
                <?php foreach ($upcomingEvents as $event): ?>
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
        </div>
    </section>

    <section class="section section-muted" aria-labelledby="benefits-title">
        <div class="container">
            <h2 id="benefits-title" class="visually-hidden">Student Benefits</h2>
            <div class="feature-grid">
                <article class="feature-card">
                    <h3>Learn New Skills</h3>
                    <p>Attend practical workshops delivered by students, faculty members, and industry guests.</p>
                </article>
                <article class="feature-card">
                    <h3>Meet Other Students</h3>
                    <p>Build valuable connections with students who share your academic and professional interests.</p>
                </article>
                <article class="feature-card">
                    <h3>Join Competitions</h3>
                    <p>Take part in challenges that improve teamwork, creativity, communication, and technical ability.</p>
                </article>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
