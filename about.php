<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About & Contact';
$activePage = 'about';
$errors = [];
$successMessage = '';
$formData = ['contact_name' => '', 'contact_email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($formData) as $key) {
        $formData[$key] = isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'The form session expired. Please refresh the page and try again.';
    }

    if ($formData['contact_name'] === '') {
        $errors['contact_name'] = 'Name is required.';
    } elseif (stringLength($formData['contact_name']) > 80) {
        $errors['contact_name'] = 'Name must not exceed 80 characters.';
    }

    if ($formData['contact_email'] === '') {
        $errors['contact_email'] = 'Email is required.';
    } elseif (!filter_var($formData['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['contact_email'] = 'Enter a valid email address.';
    }

    if ($formData['subject'] === '') {
        $errors['subject'] = 'Subject is required.';
    } elseif (stringLength($formData['subject']) > 120) {
        $errors['subject'] = 'Subject must not exceed 120 characters.';
    }

    if ($formData['message'] === '') {
        $errors['message'] = 'Message is required.';
    } elseif (stringLength($formData['message']) < 10 || stringLength($formData['message']) > 1000) {
        $errors['message'] = 'Message must be between 10 and 1,000 characters.';
    }

    if ($errors === []) {
        $successMessage = 'Your message passed validation successfully. No email was sent, as required by the project instructions.';
        $formData = ['contact_name' => '', 'contact_email' => '', 'subject' => '', 'message' => ''];
    }
}

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">Our Team</p>
            <h1>About the Project</h1>
            <p>Campus Events Hub provides one organized place for students to browse and register for university activities.</p>
        </div>
    </section>

    <section class="section">
        <div class="container two-column">
            <div>
                <h2>Project Purpose</h2>
                <p>
                    The website publishes upcoming workshops, seminars, competitions, and trips.
                    Students can search events, review complete details, register online, and view stored registrations.
                </p>

                <h2 class="spaced-heading">Team Members and Contributions</h2>
                <div class="team-grid">
                    <article class="team-card">
                        <h3>Remas Aldossari</h3>
                        <p class="student-id">S230050712</p>
                        <p>Selected the project theme and developed the home page and navigation bar.</p>
                    </article>
                    <article class="team-card">
                        <h3>Layan Alenzi</h3>
                        <p class="student-id">220031080</p>
                        <p>Designed the events page and prepared the initial event information and images.</p>
                    </article>
                    <article class="team-card">
                        <h3>Wejdan Alghamdi</h3>
                        <p class="student-id">220020628</p>
                        <p>Created the registration and event details structures and assisted with responsive CSS.</p>
                    </article>
                    <article class="team-card">
                        <h3>Joory Alghamdi</h3>
                        <p class="student-id">230048477</p>
                        <p>Developed the About and Contact page, organized GitHub, and reviewed page consistency.</p>
                    </article>
                </div>

                <h2 class="spaced-heading">Site Map</h2>
                <a class="sitemap-link" href="docs/site-map.png" target="_blank" rel="noopener">
                    <img src="docs/site-map.png" alt="Campus Events Hub site map showing all pages and navigation flow">
                    <span>Open the full site map image</span>
                </a>
            </div>

            <form class="form-card" method="POST" action="about.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <h2>Contact Us</h2>

                <?php if ($successMessage !== ''): ?>
                    <div class="alert success" role="status"><?= e($successMessage) ?></div>
                <?php endif; ?>
                <?php if (isset($errors['form'])): ?>
                    <div class="alert error" role="alert"><?= e($errors['form']) ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="contact_name">Name</label>
                    <input id="contact_name" name="contact_name" type="text" maxlength="80" autocomplete="name" required value="<?= old('contact_name', $formData) ?>">
                    <small class="error-message"><?= e($errors['contact_name'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="contact_email">Email</label>
                    <input id="contact_email" name="contact_email" type="email" maxlength="120" autocomplete="email" required value="<?= old('contact_email', $formData) ?>">
                    <small class="error-message"><?= e($errors['contact_email'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" type="text" maxlength="120" required value="<?= old('subject', $formData) ?>">
                    <small class="error-message"><?= e($errors['subject'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="7" maxlength="1000" required><?= old('message', $formData) ?></textarea>
                    <small class="error-message"><?= e($errors['message'] ?? '') ?></small>
                </div>

                <button class="button primary full-width" type="submit">Validate Message</button>
            </form>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
