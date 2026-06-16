<?php
/**
 * Homepage Studio News carousel — [ath_home_news]
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default shortcode attributes.
 *
 * @return array<string, string>
 */
function ath_home_news_default_atts() {
	return array(
		'source'       => 'home-news',
		'label'        => '',
		'heading'      => '',
		'limit'        => '',
		'btn_label'    => '',
		'footer_label' => '',
		'aria_label'   => 'Studio news',
	);
}

/**
 * Export mode toggle.
 *
 * @param bool|null $set Toggle.
 */
function ath_home_news_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode block for migration export.
 */
function ath_home_news_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_news_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Load config from data file.
 *
 * @param string $source Basename without .php.
 * @return array<string, mixed>
 */
function ath_home_news_load_data( $source ) {
	$source = sanitize_file_name( (string) $source );
	$source = preg_replace( '/\.php$/i', '', $source );
	if ( $source === '' ) {
		$source = 'home-news';
	}

	$path = ATH_THEME_DIR . '/data/' . $source . '.php';
	if ( ! is_readable( $path ) ) {
		return array();
	}

	$data = require $path;

	return is_array( $data ) ? $data : array();
}

/**
 * Resolve merged config from shortcode atts + data file.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return array<string, mixed>
 */
function ath_home_news_resolve_config( array $atts ) {
	$data = ath_home_news_load_data( $atts['source'] );

	$limit = $atts['limit'] !== '' ? max( 1, (int) $atts['limit'] ) : max( 1, (int) ( $data['limit'] ?? 6 ) );

	return array(
		'label'        => $atts['label'] !== '' ? $atts['label'] : (string) ( $data['label'] ?? 'Art Tutor Hanoi' ),
		'heading'      => $atts['heading'] !== '' ? $atts['heading'] : (string) ( $data['heading'] ?? 'From Our Studio' ),
		'limit'        => $limit,
		'btn_label'    => $atts['btn_label'] !== '' ? $atts['btn_label'] : (string) ( $data['btn_label'] ?? 'Read More' ),
		'footer_label' => $atts['footer_label'] !== '' ? $atts['footer_label'] : (string) ( $data['footer_label'] ?? 'View all news' ),
		'aria_label'   => (string) $atts['aria_label'],
		'posts'        => ath_studio_news_posts( $limit ),
		'view_all_url' => ath_studio_news_category_url(),
	);
}

/**
 * Build shortcode string with attributes.
 *
 * @param array<string, mixed> $overrides Optional overrides.
 */
function ath_home_news_shortcode_string( array $overrides = array() ) {
	$defaults = ath_home_news_default_atts();
	$data     = ath_home_news_load_data( $defaults['source'] );

	$atts = array_merge(
		$defaults,
		array(
			'label'        => (string) ( $data['label'] ?? '' ),
			'heading'      => (string) ( $data['heading'] ?? '' ),
			'limit'        => (string) ( $data['limit'] ?? 6 ),
			'btn_label'    => (string) ( $data['btn_label'] ?? '' ),
			'footer_label' => (string) ( $data['footer_label'] ?? '' ),
		),
		$overrides
	);

	$parts = array();
	foreach ( $atts as $key => $value ) {
		$value = (string) $value;
		if ( $value === '' ) {
			continue;
		}
		$parts[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
	}

	return '[ath_home_news ' . implode( ' ', $parts ) . ']';
}

/**
 * Render Studio News carousel section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 */
function ath_render_home_news_block( array $args = array() ) {
	$atts   = wp_parse_args( $args, ath_home_news_default_atts() );
	$config = ath_home_news_resolve_config( $atts );

	if ( empty( $config['posts'] ) ) {
		return '';
	}

	$news_label        = $config['label'];
	$news_heading      = $config['heading'];
	$news_posts        = $config['posts'];
	$news_btn_label    = $config['btn_label'];
	$news_footer_label = $config['footer_label'];
	$news_view_all_url = $config['view_all_url'];
	$news_aria_label   = $config['aria_label'];

	ob_start();
	require ATH_THEME_DIR . '/partials/home-news.php';

	return (string) ob_get_clean();
}

/**
 * Render in partial or export shortcode.
 */
function ath_render_home_news() {
	if ( ath_home_news_export_mode() ) {
		echo ath_home_news_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_news_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Shortcode callback.
 *
 * @param array<string, string>|string $atts Attributes.
 */
function ath_home_news_shortcode( $atts ) {
	$atts = shortcode_atts( ath_home_news_default_atts(), $atts, 'ath_home_news' );

	return ath_render_home_news_block( $atts );
}

add_shortcode( 'ath_home_news', 'ath_home_news_shortcode' );
