<?php
/**
 * Shared helper functions for the Campus Events Hub project.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const DATA_DIRECTORY = __DIR__ . '/../data';
const EVENTS_FILE = DATA_DIRECTORY . '/events.csv';
const REGISTRATIONS_FILE = DATA_DIRECTORY . '/registrations.csv';

/** Escape output before printing it into HTML. */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Read a CSV file and return each row as an associative array. */
function readCsvFile(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        return [];
    }

    $handle = fopen($path, 'r');
    if ($handle === false) {
        return [];
    }

    $header = fgetcsv($handle);
    if ($header === false) {
        fclose($handle);
        return [];
    }

    $rows = [];
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) !== count($header)) {
            continue;
        }
        $rows[] = array_combine($header, $row);
    }

    fclose($handle);
    return $rows;
}

/** Return all campus events. */
function getEvents(): array
{
    static $events = null;
    if ($events === null) {
        $events = readCsvFile(EVENTS_FILE);
        usort($events, static function (array $a, array $b): int {
            return strcmp($a['date'] . ' ' . $a['time'], $b['date'] . ' ' . $b['time']);
        });
    }
    return $events;
}

/** Find a single event by its numeric ID. */
function findEventById(int $id): ?array
{
    foreach (getEvents() as $event) {
        if ((int) $event['id'] === $id) {
            return $event;
        }
    }
    return null;
}


/** Return the length of a UTF-8 string, with a safe fallback. */
function stringLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

/** Format an ISO date for display. */
function formatEventDate(string $date): string
{
    $timestamp = strtotime($date);
    return $timestamp ? date('F j, Y', $timestamp) : $date;
}

/** Format a 24-hour time for display. */
function formatEventTime(string $time): string
{
    $timestamp = strtotime($time);
    return $timestamp ? date('g:i A', $timestamp) : $time;
}

/** Create or return a CSRF token stored in the session. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Verify a submitted CSRF token. */
function verifyCsrfToken(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Prevent spreadsheet-formula injection when data is later opened in Excel.
 */
function safeCsvValue(string $value): string
{
    $value = trim($value);
    if ($value !== '' && preg_match('/^[=+\-@]/', $value) === 1) {
        return "'" . $value;
    }
    return $value;
}

/** Append one registration record to registrations.csv using a file lock. */
function saveRegistration(array $registration): bool
{
    if (!is_dir(DATA_DIRECTORY) && !mkdir(DATA_DIRECTORY, 0775, true) && !is_dir(DATA_DIRECTORY)) {
        return false;
    }

    $isNewFile = !is_file(REGISTRATIONS_FILE) || filesize(REGISTRATIONS_FILE) === 0;
    $handle = fopen(REGISTRATIONS_FILE, 'ab');
    if ($handle === false) {
        return false;
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }

    if ($isNewFile) {
        fputcsv($handle, ['timestamp', 'name', 'student_id', 'email', 'event_id', 'event_title']);
    }

    $saved = fputcsv($handle, [
        $registration['timestamp'],
        safeCsvValue($registration['name']),
        safeCsvValue($registration['student_id']),
        safeCsvValue($registration['email']),
        $registration['event_id'],
        safeCsvValue($registration['event_title']),
    ]) !== false;

    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    return $saved;
}

/** Return submitted form data safely when repopulating a field. */
function old(string $key, array $source = []): string
{
    return isset($source[$key]) && is_string($source[$key]) ? e($source[$key]) : '';
}
