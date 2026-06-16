<?php
/**
 * One-time SEO playbook sync — ghi /seo/ từ theme lên database.
 *
 * Usage:
 * 1. Deploy theme (inc/seo-page.php phải có Bước 1.9).
 * 2. Log in as admin, visit: https://yoursite.com/sync-seo-once.php
 * 3. Delete this file from server root after success.
 *
 * @package Art_Tutor_Hanoi
 */

require __DIR__ . '/wp-load.php';

if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Admin login required.' );
}

if ( ! function_exists( 'ath_ensure_seo_page' ) ) {
	wp_die( 'Theme art-tutor-hanoi not active or seo-page.php missing.' );
}

$result  = ath_ensure_seo_page( true );
$url     = function_exists( 'ath_seo_page_url' ) ? ath_seo_page_url() : home_url( '/seo/' );
$version = function_exists( 'ath_seo_page_sync_version' ) ? ath_seo_page_sync_version() : '?';

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<title>Sync SEO playbook</title>
	<style>
		body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
		.ok { color: #0a7; }
		.err { color: #c00; }
		code { background: #f4f4f4; padding: 0.1em 0.35em; border-radius: 3px; }
	</style>
</head>
<body>
	<h1>Sync SEO playbook</h1>
	<?php if ( $result['updated'] && $result['id'] ) : ?>
		<p class="ok"><strong>Đã cập nhật</strong> trang SEO (ID <?php echo (int) $result['id']; ?>, playbook v<?php echo esc_html( $version ); ?>).</p>
		<p>Kiểm tra: <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $url ); ?></a> — tìm <strong>Bước 1.9 — Dọn plugin &amp; Code Snippets</strong>.</p>
		<p class="err"><strong>Xóa</strong> <code>sync-seo-once.php</code> khỏi thư mục gốc WordPress.</p>
	<?php elseif ( $result['id'] ) : ?>
		<p class="ok">Trang SEO (ID <?php echo (int) $result['id']; ?>) đã khớp theme v<?php echo esc_html( $version ); ?>.</p>
		<p><a href="<?php echo esc_url( $url ); ?>">Mở /seo/</a></p>
	<?php else : ?>
		<p class="err">Không tạo/cập nhật được trang SEO. Kiểm tra theme và quyền admin.</p>
	<?php endif; ?>
</body>
</html>
