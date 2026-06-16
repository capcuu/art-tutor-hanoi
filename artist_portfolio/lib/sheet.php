<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

function ap_normalize_header(string $header): string
{
    $key = strtolower(trim($header));
    $key = preg_replace('/[^a-z0-9]+/', '_', $key) ?? $key;

    return trim($key, '_');
}

function ap_should_refresh_sheet_cache(): bool
{
    return isset($_GET['refresh']) && (string) $_GET['refresh'] === '1';
}

function ap_invalidate_sheet_cache(): void
{
    foreach (['sheet.csv', 'teacher.csv'] as $file) {
        $path = dirname(__DIR__) . '/cache/' . $file;

        if (is_file($path)) {
            @unlink($path);
        }
    }
}

function ap_fetch_cached_csv(string $url, string $cache_file, bool $force = false): string
{
    $cache_dir = dirname($cache_file);

    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }

    if (
        !$force
        && is_readable($cache_file)
        && (time() - (int) filemtime($cache_file)) < AP_CACHE_TTL
    ) {
        $cached = file_get_contents($cache_file);

        if ($cached !== false && $cached !== '') {
            return $cached;
        }
    }

    $fetch_url = $url . (strpos($url, '?') !== false ? '&' : '?') . '_=' . time();

    $context = stream_context_create([
        'http' => [
            'timeout' => 20,
            'header' => "User-Agent: ArtTutorPortfolio/1.0\r\nCache-Control: no-cache\r\n",
        ],
    ]);

    $csv = @file_get_contents($fetch_url, false, $context);

    if ($csv === false || trim($csv) === '') {
        if (is_readable($cache_file)) {
            $fallback = file_get_contents($cache_file);

            if ($fallback !== false && $fallback !== '') {
                return $fallback;
            }
        }

        return '';
    }

    file_put_contents($cache_file, $csv);

    return $csv;
}

function ap_fetch_sheet_csv(bool $force = false): string
{
    return ap_fetch_cached_csv(
        AP_SHEET_EXPORT_URL,
        dirname(__DIR__) . '/cache/sheet.csv',
        $force
    );
}

function ap_fetch_teacher_sheet_csv(bool $force = false): string
{
    return ap_fetch_cached_csv(
        AP_TEACHER_SHEET_EXPORT_URL,
        dirname(__DIR__) . '/cache/teacher.csv',
        $force
    );
}

function ap_parse_csv_rows(string $csv): array
{
    if ($csv === '') {
        return [];
    }

    $stream = fopen('php://memory', 'r+');

    if ($stream === false) {
        return [];
    }

    fwrite($stream, $csv);
    rewind($stream);

    $headers = fgetcsv($stream);

    if ($headers === false) {
        fclose($stream);

        return [];
    }

    $headers = array_map('ap_normalize_header', $headers);
    $rows = [];

    while (($row = fgetcsv($stream)) !== false) {
        if ($row === [null] || $row === false) {
            continue;
        }

        $assoc = [];

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $assoc[$header] = isset($row[$index]) ? trim((string) $row[$index]) : '';
        }

        $rows[] = $assoc;
    }

    fclose($stream);

    return $rows;
}

function ap_parse_sheet_rows(bool $force = false): array
{
    $rows = [];

    foreach (ap_parse_csv_rows(ap_fetch_sheet_csv($force)) as $row) {
        if (($row['id'] ?? '') === '') {
            continue;
        }

        $rows[] = $row;
    }

    return $rows;
}

function ap_parse_teacher_rows(bool $force = false): array
{
    return ap_parse_csv_rows(ap_fetch_teacher_sheet_csv($force));
}

function ap_find_teacher_by_name(string $name, bool $force = false): ?array
{
    require_once __DIR__ . '/helpers.php';

    $needle = ap_normalize_person_name($name);

    if ($needle === '') {
        return null;
    }

    $teachers = ap_parse_teacher_rows($force);
    $partial_match = null;

    foreach ($teachers as $teacher) {
        $candidate = ap_normalize_person_name((string) ($teacher['teacher_name'] ?? ''));

        if ($candidate === '') {
            continue;
        }

        if ($candidate === $needle) {
            return $teacher;
        }

        if (
            $partial_match === null
            && (strpos($candidate, $needle) !== false || strpos($needle, $candidate) !== false)
        ) {
            $partial_match = $teacher;
        }
    }

    return $partial_match;
}

function ap_find_artist_by_id(string $id, bool $force = false): ?array
{
    $needle = strtoupper(trim($id));

    if ($needle === '') {
        return null;
    }

    foreach (ap_parse_sheet_rows($force) as $row) {
        if (strtoupper(trim($row['id'] ?? '')) === $needle) {
            return $row;
        }
    }

    return null;
}
