<?php

declare(strict_types=1);

require_once __DIR__ . '/cloudinary.php';
require_once __DIR__ . '/helpers.php';

function ap_render_gallery_item(array $item, string $alt, bool $active = false): void
{
    $type = ($item['type'] ?? 'image') === 'video' ? 'video' : 'image';
    $thumb = trim((string) ($item['thumb'] ?? $item['src'] ?? ''));
    $media = trim((string) ($item['media'] ?? $item['src'] ?? ''));

    if ($thumb === '') {
        return;
    }

    $classes = 'gallery__item';

    if ($type === 'video') {
        $classes .= ' gallery__item--video';
    }

    if ($active) {
        $classes .= ' is-active';
    }
    ?>
    <article
      class="<?php echo ap_esc($classes); ?>"
      <?php if ($type === 'video') : ?>
        data-media-type="video"
        data-media-src="<?php echo ap_esc($media); ?>"
      <?php endif; ?>
    >
      <img src="<?php echo ap_esc($thumb); ?>" alt="<?php echo ap_esc($alt); ?>" loading="lazy" decoding="async" onerror="this.closest('.gallery__item')?.remove()">
      <?php if ($type === 'video') : ?>
        <span class="gallery__play" aria-hidden="true"></span>
      <?php endif; ?>
    </article>
    <?php
}

function ap_artwork_section_folders(array $artist): array
{
    $sections = [];

    for ($i = 1; $i <= 6; $i++) {
        $value = trim($artist['artwork_' . $i] ?? '');

        if ($value !== '') {
            $sections[] = $value;
        }
    }

    return $sections;
}

function ap_section_layout(string $slug, string $folder_name): array
{
    static $sections = null;

    if ($sections === null) {
        $sections = require dirname(__DIR__) . '/data/sections.php';
    }

    if (isset($sections[$slug])) {
        return $sections[$slug];
    }

    $label = ucwords(str_replace(['-', '_'], ' ', $slug));

    return [
        'label' => $label,
        'gallery_class' => 'gallery--studies',
    ];
}

function ap_fallback_gallery_items(string $artist_id, string $folder_name): array
{
    static $galleries = null;

    if ($galleries === null) {
        $path = dirname(__DIR__) . '/data/galleries.php';
        $galleries = is_readable($path) ? require $path : [];
    }

    $artist_key = strtoupper(trim($artist_id));
    $slug = ap_section_slug_from_path($folder_name);

    return $galleries[$artist_key][$slug]['items'] ?? [];
}

function ap_build_gallery(array $artist, string $folder_name, bool $force = false): ?array
{
    $folder_path = ap_artwork_folder_path($folder_name);

    if ($folder_path === '') {
        return null;
    }

    if (!ap_is_artwork_folder_path($folder_path) && trim($artist['cloudinary_folder'] ?? '') === '') {
        return null;
    }

    $slug = ap_section_slug_from_path($folder_path);
    $layout = ap_section_layout($slug, $folder_path);
    $cloudinary_folder = trim($artist['cloudinary_folder'] ?? '');
    $fetch = ap_cloudinary_fetch_artwork_section($cloudinary_folder, $folder_path, $force);
    $items = $fetch['items'] ?? [];
    $source = 'cloudinary';

    if ($items === []) {
        $items = ap_fallback_gallery_items((string) ($artist['id'] ?? ''), $folder_path);
        $source = 'fallback';
    }

    if ($items === []) {
        return null;
    }

    return array_merge($layout, [
        'items' => $items,
        'asset_folder' => $fetch['folder'] ?? ap_cloudinary_artwork_folder($cloudinary_folder, $folder_path),
        'source' => $source,
    ]);
}

function ap_collect_galleries(array $artist, array $artwork_folders, bool $force = false): array
{
    $galleries = [];

    foreach ($artwork_folders as $folder_name) {
        $gallery = ap_build_gallery($artist, $folder_name, $force);

        if ($gallery !== null) {
            $galleries[] = $gallery;
        }
    }

    return $galleries;
}

function ap_closing_gallery_items(array $artist, bool $force = false): array
{
    $folder = ap_artwork_folder_path($artist['gallery'] ?? '');

    if ($folder !== '') {
        $cloudinary_folder = trim($artist['cloudinary_folder'] ?? '');

        if (!ap_is_artwork_folder_path($folder) && $cloudinary_folder !== '') {
            $folder = ap_cloudinary_artwork_folder($cloudinary_folder, $folder);
        }

        $fetch = ap_cloudinary_gallery_fetch($folder, $force);
        $items = $fetch['items'] ?? [];

        if ($items !== []) {
            return $items;
        }
    }

    $portrait = trim($artist['portrait_2'] ?? '');

    if ($portrait === '') {
        return [];
    }

    return [[
        'type' => 'image',
        'title' => '',
        'alt' => '',
        'src' => $portrait,
        'media' => $portrait,
        'thumb' => $portrait,
    ]];
}

function ap_gallery_debug_report(array $artist): array
{
    $cloudinary_folder = trim($artist['cloudinary_folder'] ?? '');
    $folders = [];

    foreach (ap_artwork_section_folders($artist) as $section_folder) {
        $fetch = ap_cloudinary_fetch_artwork_section($cloudinary_folder, $section_folder);

        $folders[] = [
            'section' => $section_folder,
            'folder' => $fetch['folder'] ?? ap_cloudinary_artwork_folder($cloudinary_folder, $section_folder),
            'count' => count($fetch['items'] ?? []),
            'fallback_count' => count(ap_fallback_gallery_items((string) ($artist['id'] ?? ''), $section_folder)),
            'error' => $fetch['debug']['error'] ?? null,
            'source' => $fetch['debug']['source'] ?? null,
        ];
    }

    return [
        'artist_id' => $artist['id'] ?? '',
        'cloudinary_folder' => $cloudinary_folder,
        'cloud_name' => ap_cloudinary_config()['cloud_name'] ?? '',
        'credentials_ready' => ap_cloudinary_credentials_ready(),
        'credential_source' => ap_cloudinary_config()['credential_source'] ?? 'none',
        'folders' => $folders,
    ];
}
