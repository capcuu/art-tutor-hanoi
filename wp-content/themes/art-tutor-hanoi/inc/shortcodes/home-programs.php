<?php
/**
 * Homepage structured programs — [ath_home_programs]
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default shortcode attributes.
 *
 * @return array<string, string>
 */
function ath_home_programs_default_atts() {
	return array(
		'source'       => 'home-programs',
		'label'        => '',
		'heading'      => '',
		'courses_page' => '',
		'footer_label' => '',
		'aria_label'   => 'Structured programs',
	);
}

/**
 * Export mode toggle.
 *
 * @param bool|null $set Toggle.
 */
function ath_home_programs_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode block for migration export.
 */
function ath_home_programs_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_programs_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Build shortcode string.
 *
 * @param array<string, mixed> $overrides Optional overrides.
 */
function ath_home_programs_shortcode_string( array $overrides = array() ) {
	$defaults = ath_home_programs_default_atts();
	$data     = ath_home_programs_load_data( $defaults['source'] );

	$atts = array_merge(
		$defaults,
		array(
			'label'        => (string) ( $data['label'] ?? '' ),
			'heading'      => (string) ( $data['heading'] ?? '' ),
			'courses_page' => (string) ( $data['courses_page'] ?? 'courses' ),
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

	return '[ath_home_programs ' . implode( ' ', $parts ) . ']';
}

/**
 * Load programs config from data file.
 *
 * @param string $source Basename without .php.
 * @return array<string, mixed>
 */
function ath_home_programs_load_data( $source ) {
	$source = sanitize_file_name( (string) $source );
	$source = preg_replace( '/\.php$/i', '', $source );
	if ( $source === '' ) {
		$source = 'home-programs';
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
function ath_home_programs_resolve_config( array $atts ) {
	$data = ath_home_programs_load_data( $atts['source'] );

	$config = array(
		'label'        => $atts['label'] !== '' ? $atts['label'] : (string) ( $data['label'] ?? 'Our courses' ),
		'heading'      => $atts['heading'] !== '' ? $atts['heading'] : (string) ( $data['heading'] ?? '' ),
		'courses_page' => $atts['courses_page'] !== '' ? $atts['courses_page'] : (string) ( $data['courses_page'] ?? 'courses' ),
		'footer_label' => $atts['footer_label'] !== '' ? $atts['footer_label'] : (string) ( $data['footer_label'] ?? 'Explore all courses' ),
		'aria_label'   => (string) $atts['aria_label'],
		'items'        => isset( $data['items'] ) && is_array( $data['items'] ) ? array_values( $data['items'] ) : array(),
	);

	$config['courses_url'] = ath_page_url( $config['courses_page'] );

	return $config;
}

/**
 * Row URL — courses page + optional #anchor.
 *
 * @param string $courses_url Base courses URL.
 * @param array  $item        Row from data file.
 */
function ath_home_programs_row_url( $courses_url, array $item ) {
	$url = (string) $courses_url;
	$anchor = sanitize_title( (string) ( $item['anchor'] ?? '' ) );

	if ( $anchor !== '' ) {
		$url .= '#' . $anchor;
	}

	return $url;
}

/**
 * Render structured programs section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 */
function ath_render_home_programs_block( array $args = array() ) {
	$atts   = wp_parse_args( $args, ath_home_programs_default_atts() );
	$config = ath_home_programs_resolve_config( $atts );

	$label        = $config['label'];
	$heading      = $config['heading'];
	$footer_label = $config['footer_label'];
	$courses_url  = $config['courses_url'];
	$aria_label   = $config['aria_label'];
	$items        = $config['items'];

	ob_start();
	require ATH_THEME_DIR . '/partials/home-programs.php';

	return (string) ob_get_clean();
}

/**
 * Render in partial or export shortcode.
 */
function ath_render_home_programs() {
	if ( ath_home_programs_export_mode() ) {
		echo ath_home_programs_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_programs_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Shortcode callback.
 *
 * @param array<string, string>|string $atts Attributes.
 */
function ath_home_programs_shortcode( $atts ) {
	$atts = shortcode_atts( ath_home_programs_default_atts(), $atts, 'ath_home_programs' );

	return ath_render_home_programs_block( $atts );
}

add_shortcode( 'ath_home_programs', 'ath_home_programs_shortcode' );
