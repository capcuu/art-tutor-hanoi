<?php
/**
 * One-time fix — rename l2026exhibition_* pages and update internal links.
 *
 * Usage:
 * 1. Deploy theme (inc/exhibition-page-slugs.php).
 * 2. Log in as admin, visit: https://arttutorhanoi.com/rename-exhibition-slug-once.php
 * 3. Delete this file from server root after success.
 *
 * @package Art_Tutor_Hanoi
 */

require __DIR__ . '/wp-load.php';

if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Admin login required.' );
}

if ( ! function_exists( 'ath_fix_l_prefixed_exhibition_page_slugs' ) ) {
	wp_die( 'Theme art-tutor-hanoi not active or exhibition-page-slugs.php missing.' );
}

$result = ath_fix_l_prefixed_exhibition_page_slugs();
update_option( 'ath_exhibition_l_prefix_fix', ath_exhibition_slug_fix_version(), false );

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<title>Rename exhibition slugs</title>
	<style>
		body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
		.ok { color: #0a7; }
		.warn { color: #a60; }
		code { background: #f4f4f4; padding: 0.1em 0.35em; border-radius: 3px; }
		ul { padding-left: 1.25rem; }
	</style>
</head>
<body>
	<h1>Exhibition slug fix</h1>
	<?php if ( $result['renamed'] !== array() ) : ?>
		<p class="ok"><strong>Đã đổi slug:</strong></p>
		<ul>
			<?php foreach ( $result['renamed'] as $old => $new ) : ?>
				<li><code><?php echo esc_html( $old ); ?></code> → <a href="<?php echo esc_url( home_url( '/' . $new . '/' ) ); ?>"><code><?php echo esc_html( $new ); ?></code></a></li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="ok">Không còn trang <code>l2026exhibition_*</code> cần đổi (hoặc đã chạy trước đó).</p>
	<?php endif; ?>
	<?php if ( $result['skipped'] !== array() ) : ?>
		<p class="warn"><strong>Bỏ qua:</strong></p>
		<ul>
			<?php foreach ( $result['skipped'] as $note ) : ?>
				<li><?php echo esc_html( $note ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<p>Ví dụ: <a href="<?php echo esc_url( home_url( '/2026exhibition_caophanthuygiang/' ) ); ?>">/2026exhibition_caophanthuygiang/</a></p>
	<p class="warn"><strong>Xóa</strong> <code>rename-exhibition-slug-once.php</code> khỏi thư mục gốc WordPress.</p>
</body>
</html>
