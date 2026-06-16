<?php

declare(strict_types=1);

function ap_esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function ap_text(string $value): string
{
    return nl2br(ap_esc($value));
}

function ap_parse_timeline(string $text): array
{
    $text = trim(str_replace(["\r\n", "\r"], "\n", $text));

    if ($text === '') {
        return [];
    }

    $blocks = preg_split('/\n\s*\n/', $text) ?: [];
    $items = [];

    foreach ($blocks as $block) {
        $lines = array_values(array_filter(array_map('trim', explode("\n", trim($block)))));

        if ($lines === []) {
            continue;
        }

        $items[] = [
            'range' => $lines[0] ?? '',
            'title' => $lines[1] ?? '',
            'text' => $lines[2] ?? '',
        ];
    }

    return $items;
}

function ap_parse_paragraphs(string $text): array
{
    $text = trim(str_replace(["\r\n", "\r"], "\n", $text));

    if ($text === '') {
        return [];
    }

    $parts = preg_split('/\n\s*\n/', $text) ?: [];

    return array_values(array_filter(array_map('trim', $parts)));
}

function ap_parse_contact(string $text): array
{
    $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text) ?: [])));
    $contact = [
        'email' => '',
        'phone' => '',
        'lines' => [],
    ];

    foreach ($lines as $line) {
        if ($contact['email'] === '' && filter_var($line, FILTER_VALIDATE_EMAIL)) {
            $contact['email'] = $line;
            continue;
        }

        if ($contact['phone'] === '' && preg_match('/^(\+?\d[\d\s().-]{6,}\d)$/', $line)) {
            $contact['phone'] = $line;
            continue;
        }

        $contact['lines'][] = $line;
    }

    return $contact;
}

function ap_phone_href(string $phone): string
{
    $digits = preg_replace('/[^\d+]/', '', $phone) ?? '';

    return $digits;
}

function ap_normalize_person_name(string $name): string
{
    $name = trim($name);

    if ($name === '') {
        return '';
    }

    if (class_exists('Transliterator')) {
        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');

        if ($transliterator instanceof Transliterator) {
            $name = $transliterator->transliterate($name);
        }
    }

    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9]+/', ' ', $name) ?? $name;
    $name = preg_replace('/\s+/', ' ', $name) ?? $name;

    return trim($name);
}

function ap_normalize_section_slug(string $value): string
{
    $slug = strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? $slug;

    return trim($slug, '-');
}

function ap_section_slug_from_path(string $folder_path): string
{
    $folder_path = trim(str_replace('\\', '/', $folder_path), '/');

    if ($folder_path === '') {
        return '';
    }

    if (strpos($folder_path, '/') === false) {
        return ap_normalize_section_slug($folder_path);
    }

    return ap_normalize_section_slug(basename($folder_path));
}

function ap_artwork_folder_path(string $value): string
{
    return trim(str_replace('\\', '/', $value), '/');
}

function ap_is_artwork_folder_path(string $value): bool
{
    return strpos(ap_artwork_folder_path($value), '/') !== false;
}
