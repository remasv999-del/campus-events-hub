<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Register';
$activePage = 'register';
$events = getEvents();
$errors = [];
$successMessage = '';
$formData = [
    'name' => '',
    'student_id' => '',
    'email' => '',
    'event_id' => '',
    'consent' => '',
];

$requestedEvent = filter_input(INPUT_GET, 'event', FILTER_VALIDATE_INT);
if ($requestedEvent && findEventById($requestedEvent)) {
    $formData['event_id'] = (string) $requestedEvent;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($formData) as $key) {
        $formData[$key] = isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'The form session expired. Please refresh the page and try again.';
    }

    if ($formData['name'] === '') {
        $errors['name'] = 'Full name is required.';
    } elseif (stringLength($formData['name']) < 3 || stringLength($formData['name']) > 80) {
        $errors['name'] = 'Full name must be between 3 and 80 characters.';
    }

    if ($formData['student_id'] === '') {
        $errors['student_id'] = 'Student ID is required.';
    } elseif (preg_match('/^S?\d{9}$/i', $formData['student_id']) !== 1) {
        $errors['student_id'] = 'Enter nine digits, optionally beginning with S.';
    }

    if ($formData['email'] === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    $selectedEventId = filter_var($formData['event_id'], FILTER_VALIDATE_INT);
    $selectedEvent = $selectedEventId ? findEventById($selectedEventId) : null;
    if ($selectedEvent === null) {
        $errors['event_id'] = 'Select a valid event.';
    }

    if ($formData['consent'] !== 'yes') {
        $errors['consent'] = 'You must confirm that the submitted information is correct.';
    }

    if ($errors === [] && $selectedEvent !== null) {
        $saved = saveRegistration([
            'timestamp' => date('Y-m-d H:i:s'),
            'name' => $formData['name'],
            'student_id' => strtoupper($formData['student_id']),
            'email' => strtolower($formData['email']),
            'event_id' => (string) $selectedEvent['id'],
            'event_title' => $selectedEvent['title'],
        ]);

        if ($saved) {
            $successMessage = 'Registration completed successfully. Your information has been saved.';
            $formData = ['name' => '', 'student_id' => '', 'email' => '', 'event_id' => '', 'consent' => ''];
        } else {
            $errors['form'] = 'The registration could not be saved. Check that the data folder is writable.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">Student Registration</p>
            <h1>Register for an Event</h1>
            <p>Complete the form below. All fields are required.</p>
        </div>
    </section>

    <section class="section">
        <div class="container narrow">
            <form class="form-card" method="POST" action="register.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                <?php if ($successMessage !== ''): ?>
                    <div class="alert success" role="status"><?= e($successMessage) ?></div>
                <?php endif; ?>

                <?php if (isset($errors['form'])): ?>
                    <div class="alert error" role="alert"><?= e($errors['form']) ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" maxlength="80" autocomplete="name" required value="<?= old('name', $formData) ?>" aria-describedby="name-error">
                    <small id="name-error" class="error-message"><?= e($errors['name'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input id="student_id" name="student_id" type="text" maxlength="10" placeholder="Example: S230050712" required value="<?= old('student_id', $formData) ?>" aria-describedby="student-id-error">
                    <small id="student-id-error" class="error-message"><?= e($errors['student_id'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="email">University email</label>
                    <input id="email" name="email" type="email" maxlength="120" autocomplete="email" required value="<?= old('email', $formData) ?>" aria-describedby="email-error">
                    <small id="email-error" class="error-message"><?= e($errors['email'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="event_id">Selected event</label>
                    <select id="event_id" name="event_id" required aria-describedby="event-error">
                        <option value="">Choose an event</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?= (int) $event['id'] ?>" <?= $formData['event_id'] === (string) $event['id'] ? 'selected' : '' ?>>
                                <?= e($event['title']) ?> — <?= e(formatEventDate($event['date'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small id="event-error" class="error-message"><?= e($errors['event_id'] ?? '') ?></small>
                </div>

                <div class="checkbox-group">
                    <input id="consent" name="consent" type="checkbox" value="yes" <?= $formData['consent'] === 'yes' ? 'checked' : '' ?>>
                    <label for="consent">I confirm that the information is correct.</label>
                </div>
                <small class="error-message"><?= e($errors['consent'] ?? '') ?></small>

                <button class="button primary full-width" type="submit">Submit Registration</button>
            </form>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
