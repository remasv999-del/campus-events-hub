<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Registrations';
$activePage = 'registrations';
$registrations = readCsvFile(REGISTRATIONS_FILE);
$registrations = array_reverse($registrations);

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">Registration Records</p>
            <h1>Submitted Registrations</h1>
            <p>Registration data is read from the CSV file and displayed using a PHP loop.</p>
        </div>
    </section>

    <section class="section" aria-labelledby="registration-table-title">
        <div class="container">
            <div class="section-heading">
                <h2 id="registration-table-title">Registration List</h2>
                <p><?= count($registrations) ?> record<?= count($registrations) === 1 ? '' : 's' ?></p>
            </div>

            <?php if ($registrations === []): ?>
                <div class="empty-state">
                    <h3>No registrations yet</h3>
                    <p>Submitted registrations will appear here.</p>
                    <a class="button primary" href="register.php">Register for an Event</a>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <caption>Students registered for campus events</caption>
                        <thead>
                            <tr>
                                <th scope="col">Date Submitted</th>
                                <th scope="col">Student Name</th>
                                <th scope="col">Student ID</th>
                                <th scope="col">Email</th>
                                <th scope="col">Event</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $registration): ?>
                                <tr>
                                    <td><?= e($registration['timestamp'] ?? '') ?></td>
                                    <td><?= e($registration['name'] ?? '') ?></td>
                                    <td><?= e($registration['student_id'] ?? '') ?></td>
                                    <td><?= e($registration['email'] ?? '') ?></td>
                                    <td><?= e($registration['event_title'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
