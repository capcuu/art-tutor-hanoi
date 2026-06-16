<?php
/**
 * Homepage gallery shortcode — [ath_home_gallery]
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default attribute values for the home gallery shortcode.
 *
 * @return array<string, mixed>
 */
function ath_home_gallery_default_atts() {
	return array(
		'intro'             => 'Art Tutor Hanoi is a collective of academically trained artists specializing in fine art',
		'source'            => 'frontpage-gallery',
		'cloudinary_folder' => 'website/image/frontpage',
		'visible'           => '3',
		'load_more'         => '0',
		'shuffle'           => '0',
		'button'            => 'Load more photos',
	);
}

/**
 * Build a shortcode string with explicit attributes (for migration export / editor).
 *
 * @param array<string, mixed> $overrides Optional attribute overrides.
 */
function ath_home_gallery_shortcode_string( array $overrides = array() ) {
	$atts = array_merge( ath_home_gallery_default_atts(), $overrides );
	$parts = array();

	foreach ( $atts as $key => $value ) {
		// Intro is theme-managed; do not bake into exported shortcode attributes.
		if ( $key === 'intro' ) {
			continue;
		}
		$value = (string) $value;
		if ( $value === '' ) {
			continue;
		}
		$parts[] = sprintf(
			'%s="%s"',
			$key,
			esc_attr( $value )
		);
	}

	return '[ath_home_gallery ' . implode( ' ', $parts ) . ']';
}

/**
 * Load gallery image rows from a theme data file.
 *
 * @param string $source Basename of data/{source}.php (no path).
 * @return array<int, array<string, mixed>>
 */
function ath_home_gallery_load_items( $source ) {
	$source = sanitize_file_name( (string) $source );
	$source = preg_replace( '/\.php$/i', '', $source );
	if ( $source === '' ) {
		$source = 'frontpage-gallery';
	}

	$path = ATH_THEME_DIR . '/data/' . $source . '.php';
	if ( ! is_readable( $path ) ) {
		return array();
	}

	$items = require $path;

	return is_array( $items ) ? array_values( $items ) : array();
}

/**
 * Render the homepage gallery block.
 *
 * @param array<string, mixed> $args intro, source, cloudinary_folder, visible, load_more, shuffle, button.
 */
function ath_render_home_gallery_block( array $args = array() ) {
	$args = wp_parse_args( $args, ath_home_gallery_default_atts() );

	$intro             = (string) $args['intro'];
	$source            = (string) $args['source'];
	$cloudinary_folder = (string) $args['cloudinary_folder'];
	$visible           = max( 1, (int) $args['visible'] );
	$load_more         = max( 0, (int) $args['load_more'] );
	$shuffle           = ! in_array( strtolower( (string) $args['shuffle'] ), array( '0', 'false', 'no', 'off' ), true );
	$button_label      = (string) $args['button'];

	$items           = ath_home_gallery_load_items( $source );
	$gallery_batches = ath_prepare_frontpage_gallery_batches( $items, $visible, $load_more, $shuffle );
	$gallery_has_more = ! empty( $gallery_batches[2] );

	ob_start();
	require ATH_THEME_DIR . '/partials/home-gallery.php';

	return (string) ob_get_clean();
}

/**
 * Shortcode callback — [ath_home_gallery].
 *
 * @param array<string, string>|string $atts Shortcode attributes.
 */
function ath_home_gallery_shortcode( $atts ) {
	$defaults = ath_home_gallery_default_atts();
	$atts     = shortcode_atts( $defaults, $atts, 'ath_home_gallery' );
	// Theme-managed — ignore values baked in migrated editor content.
	$atts['intro']   = $defaults['intro'];
	$atts['shuffle'] = $defaults['shuffle'];
	$atts['visible'] = $defaults['visible'];

	return ath_render_home_gallery_block( $atts );
}

add_shortcode( 'ath_home_gallery', 'ath_home_gallery_shortcode' );
