<?php
/**
 * Homepage testimonials — [ath_home_testimonials]
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default shortcode attributes.
 *
 * @return array<string, string>
 */
function ath_home_testimonials_default_atts() {
	return array(
		'source'     => 'home-testimonials',
		'label'      => '',
		'heading'    => '',
		'photo'      => '',
		'photo_alt'  => '',
		'track_id'   => 'testimonials-track',
		'dots_id'    => 'testimonials-dots',
		'prev_label' => 'Previous review',
		'next_label' => 'Next review',
		'aria_label' => 'Testimonials',
	);
}

/**
 * Export mode toggle.
 *
 * @param bool|null $set Toggle.
 */
function ath_home_testimonials_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode block for migration export.
 */
function ath_home_testimonials_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_testimonials_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Load testimonials config from data file.
 *
 * @param string $source Basename without .php.
 * @return array<string, mixed>
 */
function ath_home_testimonials_load_data( $source ) {
	$source = sanitize_file_name( (string) $source );
	$source = preg_replace( '/\.php$/i', '', $source );
	if ( $source === '' ) {
		$source = 'home-testimonials';
	}

	$path = ATH_THEME_DIR . '/data/' . $source . '.php';
	if ( ! is_readable( $path ) ) {
		return array();
	}

	$data = require $path;

	return is_array( $data ) ? $data : array();
}

/**
 * Resolve photo URL (theme asset path or absolute URL).
 *
 * @param string $photo Relative path under assets/images/ or full URL.
 */
function ath_home_testimonials_photo_url( $photo ) {
	$photo = trim( (string) $photo );
	if ( $photo === '' ) {
		return '';
	}

	if ( preg_match( '#^https?://#i', $photo ) ) {
		return $photo;
	}

	return ath_asset_url( $photo );
}

/**
 * Resolve merged config from shortcode atts + data file.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return array<string, mixed>
 */
function ath_home_testimonials_resolve_config( array $atts ) {
	$data = ath_home_testimonials_load_data( $atts['source'] );

	$photo = $atts['photo'] !== '' ? $atts['photo'] : (string) ( $data['photo'] ?? '' );

	return array(
		'label'      => $atts['label'] !== '' ? $atts['label'] : (string) ( $data['label'] ?? 'Testimonials' ),
		'heading'    => $atts['heading'] !== '' ? $atts['heading'] : (string) ( $data['heading'] ?? '' ),
		'photo_url'  => ath_home_testimonials_photo_url( $photo ),
		'photo_alt'  => $atts['photo_alt'] !== '' ? $atts['photo_alt'] : (string) ( $data['photo_alt'] ?? '' ),
		'track_id'   => sanitize_html_class( $atts['track_id'] !== '' ? $atts['track_id'] : 'testimonials-track' ),
		'dots_id'    => sanitize_html_class( $atts['dots_id'] !== '' ? $atts['dots_id'] : 'testimonials-dots' ),
		'prev_label' => (string) $atts['prev_label'],
		'next_label' => (string) $atts['next_label'],
		'aria_label' => (string) $atts['aria_label'],
		'items'      => isset( $data['items'] ) && is_array( $data['items'] ) ? array_values( $data['items'] ) : array(),
	);
}

/**
 * Build shortcode string with attributes.
 *
 * @param array<string, mixed> $overrides Optional overrides.
 */
function ath_home_testimonials_shortcode_string( array $overrides = array() ) {
	$defaults = ath_home_testimonials_default_atts();
	$data     = ath_home_testimonials_load_data( $defaults['source'] );

	$atts = array_merge(
		$defaults,
		array(
			'label'     => (string) ( $data['label'] ?? '' ),
			'heading'   => (string) ( $data['heading'] ?? '' ),
			'photo'     => (string) ( $data['photo'] ?? '' ),
			'photo_alt' => (string) ( $data['photo_alt'] ?? '' ),
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

	return '[ath_home_testimonials ' . implode( ' ', $parts ) . ']';
}

/**
 * Render testimonials section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 */
function ath_render_home_testimonials_block( array $args = array() ) {
	$atts   = wp_parse_args( $args, ath_home_testimonials_default_atts() );
	$config = ath_home_testimonials_resolve_config( $atts );

	$label      = $config['label'];
	$heading    = $config['heading'];
	$photo_url  = $config['photo_url'];
	$photo_alt  = $config['photo_alt'];
	$track_id   = $config['track_id'];
	$dots_id    = $config['dots_id'];
	$prev_label = $config['prev_label'];
	$next_label = $config['next_label'];
	$aria_label = $config['aria_label'];
	$items      = $config['items'];

	ob_start();
	require ATH_THEME_DIR . '/partials/home-testimonials.php';

	return (string) ob_get_clean();
}

/**
 * Render in partial or export shortcode.
 */
function ath_render_home_testimonials() {
	if ( ath_home_testimonials_export_mode() ) {
		echo ath_home_testimonials_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_testimonials_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Shortcode callback.
 *
 * @param array<string, string>|string $atts Attributes.
 */
function ath_home_testimonials_shortcode( $atts ) {
	$atts = shortcode_atts( ath_home_testimonials_default_atts(), $atts, 'ath_home_testimonials' );

	return ath_render_home_testimonials_block( $atts );
}

add_shortcode( 'ath_home_testimonials', 'ath_home_testimonials_shortcode' );
