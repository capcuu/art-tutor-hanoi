<?php
/**
 * Plugin Name: ARTH – Send Invoice by Email
 * Description: AJAX: render ảnh invoice (ScreenshotOne) và gửi email kèm ảnh qua wp_mail() + FluentSMTP.
 * Version: 1.1.0
 */

if (!defined('ABSPATH')) exit;

// TODO: ĐỔI chuỗi bí mật này và dùng GIỐNG HỆT ở inv.php
define('ARTH_SEND_INVOICE_SECRET', 'asdfgLkjhzxcvbMn');
// TODO: ĐỔI access key của ScreenshotOne (API key của bạn)
define('ARTH_SCREENSHOTONE_KEY', 'kSx5vPZaw0koCQ');

add_action('wp_ajax_arth_send_invoice_ss1', 'arth_send_invoice_ss1_cb');
add_action('wp_ajax_nopriv_arth_send_invoice_ss1', 'arth_send_invoice_ss1_cb');

function arth_send_invoice_ss1_cb() {
    header('Content-Type: application/json; charset=UTF-8');

    // 1) Validate
    $secret  = isset($_POST['secret']) ? sanitize_text_field($_POST['secret']) : '';
    if ($secret !== ARTH_SEND_INVOICE_SECRET) {
        wp_send_json_error(['msg'=>'Unauthorized'], 401);
    }
    $email   = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $name    = isset($_POST['name'])  ? sanitize_text_field($_POST['name'])  : '';
    $pageUrl = isset($_POST['pageUrl']) ? esc_url_raw($_POST['pageUrl'])     : '';
    if (!is_email($email) || empty($pageUrl)) {
        wp_send_json_error(['msg'=>'Invalid email or pageUrl'], 400);
    }

    // (Tuỳ chọn) giới hạn cùng host để an toàn
    $site_host = parse_url(home_url('/'), PHP_URL_HOST);
    $url_host  = parse_url($pageUrl, PHP_URL_HOST);
    if ($site_host && $url_host && $site_host !== $url_host) {
        wp_send_json_error(['msg'=>'Host not allowed'], 400);
    }

    // 2) Gọi ScreenshotOne để chụp ảnh PNG
    $qs = [
        'access_key'                 => ARTH_SCREENSHOTONE_KEY,
        'url'                        => $pageUrl,
        'viewport_width'             => 850,
        'device_scale_factor'        => 2,
        'selector'                   => '.container',
        'selector_scroll_into_view'  => 'true',
        'capture_beyond_viewport'    => 'true',
        'block_cookie_banners'       => 'true',
        'block_chats'                => 'true',
        'block_ads'                  => 'true',
    ];
    $ss1_url = 'https://api.screenshotone.com/take?' . http_build_query($qs);

    $resp = wp_remote_get($ss1_url, ['timeout'=>60]);
    if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) {
        wp_send_json_error(['msg'=>'Screenshot failed'], 500);
    }
    $imgBody = wp_remote_retrieve_body($resp);
    if (!$imgBody) wp_send_json_error(['msg'=>'Empty image body'], 500);

    // 3) Lưu file tạm trong uploads
    $uploads = wp_upload_dir();
    $dir = trailingslashit($uploads['basedir']).'invoices-temp';
    if (!file_exists($dir)) wp_mkdir_p($dir);

    $filename = 'invoice-'.time().'-'.wp_generate_password(6,false).'.png';
    $filepath = trailingslashit($dir).$filename;
    if (file_put_contents($filepath, $imgBody) === false) {
        wp_send_json_error(['msg'=>'Cannot write temp file'], 500);
    }

    // 4) Gửi mail (FluentSMTP đã hook vào wp_mail)
    $subject = 'Your Invoice from Art Tutor Hanoi';
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    $headers[] = 'Bcc: arthnbk03@gmail.com';

    $message = '<p>Hi '.esc_html($name).',</p>'
             . '<p>Attached is your invoice image (PNG).</p>'
             . '<p>Art Tutor Hanoi</p>';

    $sent = wp_mail($email, $subject, $message, $headers, [$filepath]);

    // 5) Dọn file
    @unlink($filepath);

    if (!$sent) {
        wp_send_json_error(['msg'=>'Email not sent (check SMTP)'], 500);
    }
    wp_send_json_success(['msg'=>'Email sent']);
}
