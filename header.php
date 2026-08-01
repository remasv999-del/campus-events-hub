<?php
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Campus Events Hub';
$activePage = $activePage ?? '';
$fullTitle = $pageTitle === 'Campus Events Hub'
    ? $pageTitle
    : $pageTitle . ' | Campus Events Hub';

$navigation = [
    'home' => ['label' => 'Home', 'url' => 'index.php'],
    'events' => ['label' => 'Events', 'url' => 'events.php'],
    'register' => ['label' => 'Register', 'url' => 'register.php'],
    'registrations' => ['label' => 'Registrations', 'url' => 'registrations.php'],
    'about' => ['label' => 'About & Contact', 'url' => 'about.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Campus Events Hub helps students discover and register for university events.">
    <title><?= e($fullTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/app.js" defer></script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="site-header">
    <div class="container nav-wrapper">
        <a class="brand" href="index.php" aria-label="Campus Events Hub home">
            <span class="brand-mark" aria-hidden="true">CE</span>
            <span>Campus Events Hub</span>
        </a>

        <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="main-navigation">
            <span aria-hidden="true">☰</span>
        </button>

        <nav id="main-navigation" class="main-nav" aria-label="Main navigation">
            <?php foreach ($navigation as $key => $item): ?>
                <a href="<?= e($item['url']) ?>" <?= $activePage === $key ? 'aria-current="page" class="active"' : '' ?>>
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
