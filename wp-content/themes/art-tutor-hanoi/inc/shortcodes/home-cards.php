<?php
/**
 * Homepage cards shortcode — [ath_home_cards]
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default shortcode attributes.
 *
 * @return array<string, string>
 */
function ath_home_cards_default_atts() {
	return array(
		'source'     => 'home-cards',
		'button'     => 'View Courses',
		'aria_label' => 'Featured programs',
	);
}

/**
 * Export mode — output shortcode block instead of HTML.
 *
 * @param bool|null $set Toggle.
 */
function ath_home_cards_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode block for migration export.
 */
function ath_home_cards_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_cards_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Build shortcode string with attributes.
 *
 * @param array<string, mixed> $overrides Optional overrides.
 */
function ath_home_cards_shortcode_string( array $overrides = array() ) {
	$atts  = array_merge( ath_home_cards_default_atts(), $overrides );
	$parts = array();

	foreach ( $atts as $key => $value ) {
		$value = (string) $value;
		if ( $value === '' ) {
			continue;
		}
		$parts[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
	}

	return '[ath_home_cards ' . implode( ' ', $parts ) . ']';
}

/**
 * Load card rows from data/{source}.php.
 *
 * @param string $source Data file basename.
 * @return array<int, array<string, string>>
 */
function ath_home_cards_load_items( $source ) {
	$source = sanitize_file_name( (string) $source );
	$source = preg_replace( '/\.php$/i', '', $source );
	if ( $source === '' ) {
		$source = 'home-cards';
	}

	$path = ATH_THEME_DIR . '/data/' . $source . '.php';
	if ( ! is_readable( $path ) ) {
		return array();
	}

	$items = require $path;

	return is_array( $items ) ? array_values( $items ) : array();
}

/**
 * Resolve card link URL.
 *
 * @param array<string, string> $card Card row.
 */
function ath_home_cards_resolve_link( array $card ) {
	return ath_resolve_theme_link(
		(string) ( $card['link'] ?? '' ),
		(string) ( $card['link_type'] ?? 'url' )
	);
}

/**
 * Render homepage cards section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 */
function ath_render_home_cards_block( array $args = array() ) {
	$args = wp_parse_args( $args, ath_home_cards_default_atts() );

	$source       = (string) $args['source'];
	$button_label = (string) $args['button'];
	$aria_label   = (string) $args['aria_label'];
	$cards        = ath_home_cards_load_items( $source );

	ob_start();
	require ATH_THEME_DIR . '/partials/home-cards.php';

	return (string) ob_get_clean();
}

/**
 * Render in partial or export shortcode.
 */
function ath_render_home_cards() {
	if ( ath_home_cards_export_mode() ) {
		echo ath_home_cards_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_cards_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Shortcode callback.
 *
 * @param array<string, string>|string $atts Attributes.
 */
function ath_home_cards_shortcode( $atts ) {
	$atts = shortcode_atts( ath_home_cards_default_atts(), $atts, 'ath_home_cards' );

	return ath_render_home_cards_block( $atts );
}

add_shortcode( 'ath_home_cards', 'ath_home_cards_shortcode' );
