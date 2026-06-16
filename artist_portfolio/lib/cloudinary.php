<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';
require_once dirname(__DIR__) . '/config.php';

function ap_cloudinary_public_id_title(string $public_id): string
{
    $basename = basename(str_replace('\\', '/', $public_id));
    $basename = preg_replace('/\.[A-Za-z0-9]+$/', '', $basename) ?? $basename;
    $basename = preg_replace('/[_-]+/', ' ', $basename) ?? $basename;
    $basename = preg_replace('/\s+/', ' ', trim($basename)) ?? $basename;

    return $basename === '' ? 'Untitled' : strtoupper($basename);
}

function ap_cloudinary_resource_title(array $resource): string
{
    if (!empty($resource['display_name'])) {
        return strtoupper((string) $resource['display_name']);
    }

    $context = $resource['context'] ?? [];

    if (!empty($context['custom']['title'])) {
        return strtoupper((string) $context['custom']['title']);
    }

    if (!empty($context['caption'])) {
        return strtoupper((string) $context['caption']);
    }

    return ap_cloudinary_public_id_title((string) ($resource['public_id'] ?? ''));
}

function ap_cloudinary_strip_transformations(string $secure_url): string
{
    if ($secure_url === '' || strpos($secure_url, '/upload/') === false) {
        return $secure_url;
    }

    if (!preg_match('#^(https://res\.cloudinary\.com/[^/]+/(?:image|video)/upload/)(.+)$#', $secure_url, $matches)) {
        return $secure_url;
    }

    $rest = $matches[2];

    if (preg_match('#(v\d+/.+)$#', $rest, $asset)) {
        return $matches[1] . $asset[1];
    }

    return $secure_url;
}

function ap_cloudinary_build_url(string $secure_url, string $transformations): string
{
    $base_url = ap_cloudinary_strip_transformations($secure_url);

    if ($base_url === '' || strpos($base_url, '/upload/') === false) {
        return $secure_url;
    }

    if ($transformations === '') {
        return $base_url;
    }

    return preg_replace('#/upload/#', '/upload/' . $transformations . '/', $base_url, 1) ?? $secure_url;
}

function ap_cloudinary_gallery_thumb_url(string $secure_url, int $width = 640): string
{
    $width = max(120, min(1600, $width));

    return ap_cloudinary_build_url(
        $secure_url,
        'c_limit,w_' . $width . ',q_auto:best,f_auto,e_trim'
    );
}

function ap_cloudinary_gallery_full_url(string $secure_url): string
{
    return ap_cloudinary_build_url(
        $secure_url,
        'c_limit,w_1600,q_auto:best,f_auto,e_trim'
    );
}

function ap_cloudinary_resource_is_video(array $resource): bool
{
    return ($resource['resource_type'] ?? 'image') === 'video';
}

function ap_cloudinary_video_poster_url(string $secure_url, int $width = 640): string
{
    $base_url = ap_cloudinary_strip_transformations($secure_url);

    if ($base_url === '' || strpos($base_url, '/video/upload/') === false) {
        return $base_url;
    }

    $width = max(120, min(1600, $width));
    $transform = 'so_0,c_limit,w_' . $width . ',q_auto:best,f_auto';
    $poster = preg_replace('#/video/upload/#', '/video/upload/' . $transform . '/', $base_url, 1) ?? $base_url;

    return preg_replace('/\.(mp4|mov|webm|m4v)$/i', '.jpg', $poster) ?? $poster;
}

function ap_cloudinary_video_full_url(string $secure_url): string
{
    return ap_cloudinary_build_url($secure_url, 'q_auto:best,f_auto');
}

function ap_cloudinary_gallery_thumb_for_resource(array $resource): string
{
    $url = (string) ($resource['secure_url'] ?? '');

    if ($url === '') {
        return '';
    }

    if (ap_cloudinary_resource_is_video($resource)) {
        return ap_cloudinary_video_poster_url($url);
    }

    return ap_cloudinary_gallery_thumb_url($url);
}

function ap_cloudinary_gallery_full_for_resource(array $resource): string
{
    $url = (string) ($resource['secure_url'] ?? '');

    if ($url === '') {
        return '';
    }

    if (ap_cloudinary_resource_is_video($resource)) {
        return ap_cloudinary_video_full_url($url);
    }

    return ap_cloudinary_gallery_full_url($url);
}

function ap_cloudinary_api_request(string $method, string $endpoint, array $params = []): array
{
    $config = ap_cloudinary_config();

    if ($config['api_key'] === '' || $config['api_secret'] === '') {
        return [
            'ok' => false,
            'error' => 'Missing Cloudinary API credentials',
            'data' => null,
        ];
    }

    if (!function_exists('curl_init')) {
        return [
            'ok' => false,
            'error' => 'PHP cURL extension is not available',
            'data' => null,
        ];
    }

    $url = 'https://api.cloudinary.com/v1_1/' . rawurlencode($config['cloud_name']) . '/' . ltrim($endpoint, '/');
    $method = strtoupper($method);
    $curl = curl_init($url);

    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => $config['api_key'] . ':' . $config['api_secret'],
        CURLOPT_TIMEOUT => 15,
    ];

    if ($method === 'GET' && $params !== []) {
        curl_setopt($curl, CURLOPT_URL, $url . '?' . http_build_query($params));
    } elseif ($method === 'POST') {
        $payload = json_encode($params);
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $payload;
        $options[CURLOPT_HTTPHEADER] = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ];
    }

    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    if ($response === false) {
        return [
            'ok' => false,
            'error' => $curl_error !== '' ? $curl_error : 'Cloudinary request failed',
            'data' => null,
        ];
    }

    $data = json_decode($response, true);

    if ($status < 200 || $status >= 300) {
        $message = is_array($data) ? ($data['error']['message'] ?? $response) : $response;

        return [
            'ok' => false,
            'error' => 'Cloudinary API HTTP ' . $status . ': ' . $message,
            'data' => is_array($data) ? $data : null,
        ];
    }

    return [
        'ok' => true,
        'error' => null,
        'data' => is_array($data) ? $data : null,
    ];
}

function ap_cloudinary_cache_path(string $key): string
{
    $cache_dir = dirname(__DIR__) . '/cache';

    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }

    return $cache_dir . '/gallery-' . preg_replace('/[^a-z0-9_-]+/i', '-', $key) . '.json';
}

function ap_cloudinary_cache_delete(string $key): void
{
    $path = ap_cloudinary_cache_path($key);

    if (is_file($path)) {
        @unlink($path);
    }
}

function ap_invalidate_gallery_cache(): void
{
    $cache_dir = dirname(__DIR__) . '/cache';

    if (!is_dir($cache_dir)) {
        return;
    }

    foreach (glob($cache_dir . '/gallery-*.json') ?: [] as $path) {
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

function ap_cloudinary_cache_read(string $key, int $ttl): ?array
{
    $path = ap_cloudinary_cache_path($key);

    if (!is_readable($path)) {
        return null;
    }

    if ($ttl > 0 && (time() - (int) filemtime($path)) > $ttl) {
        return null;
    }

    $data = json_decode((string) file_get_contents($path), true);

    return is_array($data) ? $data : null;
}

function ap_cloudinary_cache_write(string $key, array $data): void
{
    file_put_contents(
        ap_cloudinary_cache_path($key),
        json_encode($data, JSON_UNESCAPED_SLASHES)
    );
}

function ap_cloudinary_sort_resources(array $resources): array
{
    usort($resources, static function (array $left, array $right): int {
        $left_name = strtolower((string) ($left['display_name'] ?? $left['public_id'] ?? ''));
        $right_name = strtolower((string) ($right['display_name'] ?? $right['public_id'] ?? ''));

        return $left_name <=> $right_name;
    });

    return $resources;
}

function ap_cloudinary_filter_gallery_resources(array $resources): array
{
    $items = [];

    foreach ($resources as $resource) {
        if (empty($resource['secure_url'])) {
            continue;
        }

        $type = $resource['resource_type'] ?? 'image';

        if ($type !== 'image' && $type !== 'video') {
            continue;
        }

        $items[] = $resource;
    }

    return ap_cloudinary_sort_resources($items);
}

function ap_cloudinary_filter_image_resources(array $resources): array
{
    return ap_cloudinary_filter_gallery_resources($resources);
}

function ap_cloudinary_resources_by_asset_folder(string $folder): array
{
    $folder = ap_cloudinary_resolve_folder($folder);
    $resources = [];
    $next_cursor = null;

    do {
        $params = [
            'asset_folder' => $folder,
            'max_results' => 100,
        ];

        if ($next_cursor !== null) {
            $params['next_cursor'] = $next_cursor;
        }

        $response = ap_cloudinary_api_request('GET', 'resources/by_asset_folder', $params);

        if (!$response['ok']) {
            return [
                'resources' => null,
                'error' => $response['error'],
            ];
        }

        foreach ($response['data']['resources'] ?? [] as $resource) {
            $resources[] = $resource;
        }

        $next_cursor = $response['data']['next_cursor'] ?? null;
    } while ($next_cursor !== null);

    return [
        'resources' => ap_cloudinary_filter_gallery_resources($resources),
        'error' => null,
    ];
}

function ap_cloudinary_search_asset_folder(string $folder): array
{
    $expression = 'asset_folder="' . str_replace('"', '\\"', $folder) . '"';
    $resources = [];
    $next_cursor = null;

    do {
        $params = [
            'expression' => $expression,
            'max_results' => 100,
        ];

        if ($next_cursor !== null) {
            $params['next_cursor'] = $next_cursor;
        }

        $response = ap_cloudinary_api_request('POST', 'resources/search', $params);

        if (!$response['ok']) {
            return [
                'resources' => null,
                'error' => $response['error'],
                'expression' => $expression,
            ];
        }

        foreach ($response['data']['resources'] ?? [] as $resource) {
            $type = $resource['resource_type'] ?? 'image';

            if (!empty($resource['secure_url']) && ($type === 'image' || $type === 'video')) {
                $resources[] = $resource;
            }
        }

        $next_cursor = $response['data']['next_cursor'] ?? null;
    } while ($next_cursor !== null);

    return [
        'resources' => ap_cloudinary_sort_resources($resources),
        'error' => null,
        'expression' => $expression,
    ];
}

function ap_cloudinary_list_folder_resources(string $folder, ?string &$method = null, bool $force = false): ?array
{
    $config = ap_cloudinary_config();
    $folder = ap_cloudinary_resolve_folder($folder);
    $cache_key = md5($config['cloud_name'] . '-media-v2-' . $folder);

    if ($force) {
        ap_cloudinary_cache_delete($cache_key);
    } elseif (($cached = ap_cloudinary_cache_read($cache_key, AP_CLOUDINARY_CACHE_TTL)) !== null) {
        $method = 'cache';

        return $cached;
    }

    $by_folder = ap_cloudinary_resources_by_asset_folder($folder);

    if ($by_folder['resources'] === null) {
        return null;
    }

    if ($by_folder['resources'] !== []) {
        $method = 'by_asset_folder';
        ap_cloudinary_cache_write($cache_key, $by_folder['resources']);

        return $by_folder['resources'];
    }

    $search = ap_cloudinary_search_asset_folder($folder);

    if ($search['resources'] === null) {
        return null;
    }

    if ($search['resources'] !== []) {
        $method = 'search';
        ap_cloudinary_cache_write($cache_key, $search['resources']);

        return $search['resources'];
    }

    $method = 'by_asset_folder';

    return [];
}

function ap_cloudinary_resources_to_items(array $resources): array
{
    $items = [];

    foreach ($resources as $resource) {
        $url = (string) ($resource['secure_url'] ?? '');

        if ($url === '') {
            continue;
        }

        $title = ap_cloudinary_resource_title($resource);
        $is_video = ap_cloudinary_resource_is_video($resource);
        $media = ap_cloudinary_gallery_full_for_resource($resource);
        $thumb = ap_cloudinary_gallery_thumb_for_resource($resource);

        $items[] = [
            'type' => $is_video ? 'video' : 'image',
            'title' => $title,
            'alt' => $title,
            'src' => $is_video ? $thumb : $media,
            'media' => $media,
            'thumb' => $thumb,
        ];
    }

    return $items;
}

function ap_cloudinary_resolve_folder(string $folder): string
{
    return trim($folder, '/');
}

function ap_cloudinary_gallery_fetch(string $folder, bool $force = false): array
{
    $folder = ap_cloudinary_resolve_folder($folder);
    $config = ap_cloudinary_config();
    $api_configured = ($config['api_key'] !== '' && $config['api_secret'] !== '');

    $debug = [
        'folder' => $folder,
        'count' => 0,
        'error' => null,
        'api_configured' => $api_configured,
        'credential_source' => $config['credential_source'] ?? 'none',
        'source' => null,
    ];

    if (!$api_configured) {
        $debug['error'] = 'Missing Cloudinary API credentials';

        return [
            'items' => [],
            'debug' => $debug,
        ];
    }

    $method = null;
    $resources = ap_cloudinary_list_folder_resources($folder, $method, $force);

    if ($resources === null) {
        $debug['error'] = 'Cloudinary API request failed';

        return [
            'items' => [],
            'debug' => $debug,
        ];
    }

    $debug['source'] = $method ?? 'unknown';

    $items = ap_cloudinary_resources_to_items($resources);
    $debug['count'] = count($items);

    if ($items === []) {
        $debug['error'] = 'No images found in asset folder "' . $folder . '"';
    }

    return [
        'items' => $items,
        'debug' => $debug,
    ];
}

function ap_artwork_folder_candidates(string $cloudinary_folder, string $section_folder): array
{
    $section = trim($section_folder, '/');

    if ($section === '') {
        return [];
    }

    if (strpos($section, '/') !== false) {
        $paths = [$section];
        $parent = dirname(str_replace('\\', '/', $section));
        $basename = basename(str_replace('\\', '/', $section));

        if ($basename !== '' && str_contains($basename, '_')) {
            $spaced = str_replace('_', ' ', $basename);
            $paths[] = ($parent !== '.' && $parent !== '' ? $parent . '/' : '') . $spaced;
        }

        return array_values(array_unique($paths));
    }

    $artist = trim($cloudinary_folder, '/');
    $bases = array_values(array_unique([
        trim(AP_CLOUDINARY_BASE, '/'),
        'Adult_portfolio',
    ]));

    $paths = [];

    foreach ($bases as $base) {
        $paths[] = $base . '/' . $artist . '/artwork/' . $section;
        $paths[] = $base . '/' . $artist . '/' . $section;
    }

    return array_values(array_unique($paths));
}

function ap_cloudinary_artwork_folder(string $cloudinary_folder, string $section_folder): string
{
    return ap_artwork_folder_candidates($cloudinary_folder, $section_folder)[0];
}

function ap_cloudinary_fetch_artwork_section(string $cloudinary_folder, string $section_folder, bool $force = false): array
{
    static $memory = [];

    $memory_key = strtolower(trim($cloudinary_folder) . '|' . trim($section_folder) . '|' . ($force ? '1' : '0'));

    if (!$force && isset($memory[$memory_key])) {
        return $memory[$memory_key];
    }

    $last_debug = [
        'folder' => '',
        'count' => 0,
        'error' => 'No matching Cloudinary folder',
        'api_configured' => ap_cloudinary_credentials_ready(),
        'credential_source' => ap_cloudinary_config()['credential_source'] ?? 'none',
        'source' => null,
    ];

    foreach (ap_artwork_folder_candidates($cloudinary_folder, $section_folder) as $folder) {
        $fetch = ap_cloudinary_gallery_fetch($folder, $force);

        if ($fetch['items'] !== []) {
            $memory[$memory_key] = $fetch;
            $memory[$memory_key]['folder'] = $folder;

            return $memory[$memory_key];
        }

        $last_debug = $fetch['debug'];
        $last_debug['folder'] = $folder;
    }

    $memory[$memory_key] = [
        'items' => [],
        'folder' => ap_cloudinary_artwork_folder($cloudinary_folder, $section_folder),
        'debug' => $last_debug,
    ];

    return $memory[$memory_key];
}
