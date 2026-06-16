<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

function ap_env(string $key, ?string $default = null): ?string
{
    static $file_vars = null;

    if ($file_vars === null) {
        $file_vars = [];
        $env_file = dirname(__DIR__) . '/.env';

        if (is_readable($env_file)) {
            foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);

                if ($line === '' || $line[0] === '#') {
                    continue;
                }

                if (preg_match('/^([A-Za-z_][A-Za-z0-9_]*)=(.*)$/', $line, $matches)) {
                    $file_vars[$matches[1]] = trim($matches[2], " \t\"'");
                }
            }
        }
    }

    $env_value = getenv($key);

    if ($env_value !== false && $env_value !== '') {
        return $env_value;
    }

    if (array_key_exists($key, $file_vars)) {
        return $file_vars[$key];
    }

    return $default;
}

function ap_parse_cloudinary_url(string $url): ?array
{
    $url = trim($url);

    if ($url === '') {
        return null;
    }

    $url = preg_replace('/^CLOUDINARY_URL=/', '', $url);

    if (!preg_match('#^cloudinary://([^:]+):([^@]+)@([^/\s]+)#', $url, $matches)) {
        return null;
    }

    return [
        'api_key' => $matches[1],
        'api_secret' => $matches[2],
        'cloud_name' => $matches[3],
    ];
}

function ap_cloudinary_url_from_wp_options(): string
{
    if (!function_exists('get_option')) {
        return '';
    }

    $connect = get_option('cloudinary_connect', []);

    if (is_array($connect) && !empty($connect['cloudinary_url'])) {
        return (string) $connect['cloudinary_url'];
    }

    $direct = get_option('cloudinary_url', '');

    return is_string($direct) ? $direct : '';
}

function ap_cloudinary_credentials_from_wp(): ?array
{
    static $credentials = null;
    static $checked = false;

    if ($checked) {
        return $credentials;
    }

    $checked = true;
    $credentials = null;

    $wp_load_candidates = [
        dirname(__DIR__, 2) . '/wp-load.php',
        dirname(__DIR__, 3) . '/wp-load.php',
    ];

    foreach ($wp_load_candidates as $wp_load) {
        if (!is_readable($wp_load)) {
            continue;
        }

        require_once $wp_load;

        $url = ap_cloudinary_url_from_wp_options();
        $credentials = ap_parse_cloudinary_url($url);

        if ($credentials !== null) {
            break;
        }
    }

    return $credentials;
}

function ap_cloudinary_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = [
        'cloud_name' => AP_CLOUDINARY_CLOUD_NAME,
        'api_key' => ap_env('CLOUDINARY_API_KEY', '') ?? '',
        'api_secret' => ap_env('CLOUDINARY_API_SECRET', '') ?? '',
        'credential_source' => 'none',
    ];

    $env_cloud_name = ap_env('CLOUDINARY_CLOUD_NAME', '') ?? '';

    if ($env_cloud_name !== '' && strcasecmp($env_cloud_name, AP_CLOUDINARY_CLOUD_NAME) !== 0) {
        $config['cloud_name_mismatch'] = $env_cloud_name;
    }

    $defined_urls = [];

    if (defined('CLOUDINARY_CONNECTION_STRING')) {
        $defined_urls[] = (string) CLOUDINARY_CONNECTION_STRING;
    }

    if (defined('CLOUDINARY_URL')) {
        $defined_urls[] = (string) CLOUDINARY_URL;
    }

    foreach ($defined_urls as $defined_url) {
        $parsed = ap_parse_cloudinary_url($defined_url);

        if ($parsed !== null && strcasecmp($parsed['cloud_name'], AP_CLOUDINARY_CLOUD_NAME) === 0) {
            $config['api_key'] = $parsed['api_key'];
            $config['api_secret'] = $parsed['api_secret'];
            $config['credential_source'] = 'wp-config';
            break;
        }
    }

    if ($config['api_key'] === '' || $config['api_secret'] === '') {
        $url_credentials = ap_parse_cloudinary_url(ap_env('CLOUDINARY_URL', '') ?? '');

        if ($url_credentials !== null && strcasecmp($url_credentials['cloud_name'], AP_CLOUDINARY_CLOUD_NAME) === 0) {
            $config['api_key'] = $url_credentials['api_key'];
            $config['api_secret'] = $url_credentials['api_secret'];
            $config['credential_source'] = 'env';
        }
    }

    if ($config['api_key'] === '' || $config['api_secret'] === '') {
        $wp_credentials = ap_cloudinary_credentials_from_wp();

        if ($wp_credentials !== null && strcasecmp($wp_credentials['cloud_name'], AP_CLOUDINARY_CLOUD_NAME) === 0) {
            $config['api_key'] = $wp_credentials['api_key'];
            $config['api_secret'] = $wp_credentials['api_secret'];
            $config['credential_source'] = 'wordpress';
        }
    }

    if ($config['api_key'] !== '' && $config['api_secret'] !== '' && $config['credential_source'] === 'none') {
        $config['credential_source'] = 'env-keys';
    }

    return $config;
}

function ap_cloudinary_credentials_ready(): bool
{
    $config = ap_cloudinary_config();

    return $config['api_key'] !== '' && $config['api_secret'] !== '';
}
