<?php
/**
 * Homepage artist community — [ath_home_community]
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default shortcode attributes.
 *
 * @return array<string, string>
 */
function ath_home_community_default_atts() {
	return array(
		'source'          => 'home-community',
		'stat'            => '',
		'section_title'   => '',
		'limit'           => '',
		'always_visible'  => '',
		'panel_open'      => '',
		'footer_label'    => '',
		'view_all_page'   => '',
		'toggle_show'     => '',
		'toggle_hide'     => '',
		'panel_id'        => 'community-panel',
		'toggle_id'       => 'community-toggle',
		'aria_label'      => 'Artist community',
	);
}

/**
 * Export mode toggle.
 *
 * @param bool|null $set Toggle.
 */
function ath_home_community_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode block for migration export.
 */
function ath_home_community_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_community_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Load community config from data file.
 *
 * @param string $source Basename without .php.
 * @return array<string, mixed>
 */
function ath_home_community_load_data( $source ) {
	$source = sanitize_file_name( (string) $source );
	$source = preg_replace( '/\.php$/i', '', $source );
	if ( $source === '' ) {
		$source = 'home-community';
	}

	$path = ATH_THEME_DIR . '/data/' . $source . '.php';
	if ( ! is_readable( $path ) ) {
		return array();
	}

	$data = require $path;

	return is_array( $data ) ? $data : array();
}

/**
 * Stat lines → safe HTML with &lt;br&gt;.
 *
 * @param array<int, string>|string $lines Array of lines or pipe-separated string.
 */
function ath_home_community_stat_html( $lines ) {
	if ( is_string( $lines ) ) {
		$lines = array_filter( array_map( 'trim', explode( '|', $lines ) ) );
	}

	if ( ! is_array( $lines ) ) {
		return '';
	}

	$parts = array();
	foreach ( $lines as $line ) {
		$line = trim( (string) $line );
		if ( $line !== '' ) {
			$parts[] = esc_html( $line );
		}
	}

	if ( empty( $parts ) ) {
		return '';
	}

	return implode( '<br>', $parts );
}

/**
 * Resolve merged config from shortcode atts + data file.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return array<string, mixed>
 */
function ath_home_community_resolve_config( array $atts ) {
	$data = ath_home_community_load_data( $atts['source'] );

	$stat_lines = isset( $data['stat_lines'] ) && is_array( $data['stat_lines'] ) ? $data['stat_lines'] : array();
	if ( $atts['stat'] !== '' ) {
		$stat_lines = $atts['stat'];
	}

	$limit = $atts['limit'] !== '' ? max( 1, (int) $atts['limit'] ) : max( 1, (int) ( $data['limit'] ?? 6 ) );

	if ( $atts['always_visible'] !== '' ) {
		$always_visible = in_array( strtolower( $atts['always_visible'] ), array( '1', 'true', 'yes' ), true );
	} else {
		$always_visible = ! empty( $data['always_visible'] );
	}

	if ( $atts['panel_open'] !== '' ) {
		$panel_open = in_array( strtolower( $atts['panel_open'] ), array( '1', 'true', 'yes' ), true );
	} else {
		$panel_open = ! empty( $data['panel_open'] );
	}

	$view_all_page = $atts['view_all_page'] !== '' ? $atts['view_all_page'] : (string) ( $data['view_all_page'] ?? 'students-artworks' );
	$view_all_url  = $view_all_page !== '' ? ath_page_url( $view_all_page ) : ath_students_artworks_url();
	if ( $view_all_url === '' ) {
		$view_all_url = ath_students_artworks_url();
	}

	return array(
		'stat_html'              => ath_home_community_stat_html( $stat_lines ),
		'section_title'          => $atts['section_title'] !== '' ? $atts['section_title'] : (string) ( $data['section_title'] ?? '' ),
		'limit'                  => $limit,
		'always_visible'         => $always_visible,
		'panel_open'             => $panel_open,
		'footer_label'           => $atts['footer_label'] !== '' ? $atts['footer_label'] : (string) ( $data['footer_label'] ?? 'View all' ),
		'view_all_url'           => $view_all_url,
		'toggle_show'            => $atts['toggle_show'] !== '' ? $atts['toggle_show'] : (string) ( $data['toggle_show'] ?? 'View Community' ),
		'toggle_hide'            => $atts['toggle_hide'] !== '' ? $atts['toggle_hide'] : (string) ( $data['toggle_hide'] ?? 'Hide Community' ),
		'panel_id'               => sanitize_html_class( $atts['panel_id'] !== '' ? $atts['panel_id'] : 'community-panel' ),
		'toggle_id'              => sanitize_html_class( $atts['toggle_id'] !== '' ? $atts['toggle_id'] : 'community-toggle' ),
		'aria_label'             => (string) $atts['aria_label'],
	);
}

/**
 * Build shortcode string with attributes.
 *
 * @param array<string, mixed> $overrides Optional overrides.
 */
function ath_home_community_shortcode_string( array $overrides = array() ) {
	$defaults = ath_home_community_default_atts();
	$data     = ath_home_community_load_data( $defaults['source'] );

	$stat = '';
	if ( isset( $data['stat_lines'] ) && is_array( $data['stat_lines'] ) ) {
		$stat = implode( '|', array_map( 'strval', $data['stat_lines'] ) );
	}

	$always_visible = ! empty( $data['always_visible'] ) ? '1' : '0';

	$atts = array_merge(
		$defaults,
		array(
			'stat'           => $stat,
			'limit'          => (string) ( $data['limit'] ?? 6 ),
			'always_visible' => $always_visible,
			'footer_label'   => (string) ( $data['footer_label'] ?? 'View all' ),
			'view_all_page'  => (string) ( $data['view_all_page'] ?? 'students-artworks' ),
			'toggle_show'    => (string) ( $data['toggle_show'] ?? 'View Community' ),
			'toggle_hide'    => (string) ( $data['toggle_hide'] ?? 'Hide Community' ),
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

	return '[ath_home_community ' . implode( ' ', $parts ) . ']';
}

/**
 * Render community banner with resolved config.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 */
function ath_render_home_community_block( array $args = array() ) {
	$atts   = wp_parse_args( $args, ath_home_community_default_atts() );
	$config = ath_home_community_resolve_config( $atts );

	$community_section_title  = $config['section_title'] !== '' ? $config['section_title'] : null;
	$community_always_visible = $config['always_visible'];
	$community_panel_open     = $config['panel_open'];
	$community_panel_id       = $config['panel_id'];
	$community_toggle_id      = $config['toggle_id'];
	$community_limit          = $config['limit'];
	$community_stat_text      = $config['stat_html'];
	$community_footer_label   = $config['footer_label'];
	$community_view_all_url   = $config['view_all_url'];
	$community_toggle_show    = $config['toggle_show'];
	$community_toggle_hide    = $config['toggle_hide'];
	$community_aria_label     = $config['aria_label'];

	ob_start();
	require ATH_THEME_DIR . '/partials/community-banner.php';

	return (string) ob_get_clean();
}

/**
 * Render in partial or export shortcode.
 */
function ath_render_home_community() {
	if ( ath_home_community_export_mode() ) {
		echo ath_home_community_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_community_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Shortcode callback.
 *
 * @param array<string, string>|string $atts Attributes.
 */
function ath_home_community_shortcode( $atts ) {
	$atts = shortcode_atts( ath_home_community_default_atts(), $atts, 'ath_home_community' );

	return ath_render_home_community_block( $atts );
}

add_shortcode( 'ath_home_community', 'ath_home_community_shortcode' );
