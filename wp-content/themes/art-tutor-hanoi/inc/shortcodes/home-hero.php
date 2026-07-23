<?php
/**
 * Homepage hero shortcode — [ath_home_hero]
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default attribute values for the home hero shortcode.
 *
 * @return array<string, string>
 */
function ath_home_hero_default_atts() {
	return array(
		'aria_label'        => 'Art studio',
		'poster'            => 'hero2.jpg',
		'video_url'         => '',
		'video_id'          => 'Banner_tmdhl6',
		'video_version'     => 'v1780352977',
		'video_width'       => '1920',
		'cloudinary_folder' => 'video',
		'brand_h1'          => 'Art Tutor Hanoi',
		'autoplay'          => '1',
		'muted'             => '1',
		'loop'              => '1',
		'playsinline'       => '1',
		'preload'           => 'auto',
	);
}

/**
 * When true, home partial outputs a shortcode block instead of HTML.
 *
 * @param bool|null $set Toggle export mode.
 */
function ath_home_hero_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode block markup for migration export.
 */
function ath_home_hero_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_hero_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Build a shortcode string with explicit attributes.
 *
 * @param array<string, mixed> $overrides Optional attribute overrides.
 */
function ath_home_hero_shortcode_string( array $overrides = array() ) {
	$atts  = array_merge( ath_home_hero_default_atts(), $overrides );
	$parts = array();

	foreach ( $atts as $key => $value ) {
		$value = (string) $value;
		if ( $value === '' ) {
			continue;
		}
		$parts[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
	}

	return '[ath_home_hero ' . implode( ' ', $parts ) . ']';
}

/**
 * Resolve poster image URL (theme asset path or absolute URL).
 *
 * @param string $poster Relative path under assets/images/ or full URL.
 */
function ath_resolve_hero_poster_url( $poster ) {
	$poster = trim( (string) $poster );
	if ( $poster === '' ) {
		return '';
	}

	if ( preg_match( '#^https?://#i', $poster ) ) {
		return $poster;
	}

	return ath_asset_url( $poster );
}

/**
 * Resolve hero background video URL.
 *
 * @param array<string, string> $atts Shortcode attributes.
 */
function ath_resolve_hero_video_url( array $atts ) {
	$video_url = trim( (string) ( $atts['video_url'] ?? '' ) );
	if ( $video_url !== '' && preg_match( '#^https?://#i', $video_url ) ) {
		return $video_url;
	}

	$video_id = trim( (string) ( $atts['video_id'] ?? '' ) );
	if ( $video_id === '' ) {
		return cld_home_hero_video_url();
	}

	$version = trim( (string) ( $atts['video_version'] ?? '' ) );
	$width   = max( 480, (int) ( $atts['video_width'] ?? 1920 ) );
	$path    = $version !== '' ? $version . '/' . $video_id : $video_id;

	return sprintf(
		'https://res.cloudinary.com/%s/video/upload/q_auto,w_%d,c_limit/f_auto/%s.mp4',
		CLD_CLOUD,
		$width,
		$path
	);
}

/**
 * Whether a shortcode flag is enabled.
 *
 * @param string $value Attribute value.
 */
function ath_shortcode_flag_enabled( $value ) {
	return ! in_array( strtolower( (string) $value ), array( '0', 'false', 'no', 'off' ), true );
}

/**
 * Render the homepage hero block.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 */
function ath_render_home_hero_block( array $args = array() ) {
	$args = wp_parse_args( $args, ath_home_hero_default_atts() );

	$aria_label        = (string) $args['aria_label'];
	$poster_url        = ath_resolve_hero_poster_url( $args['poster'] );
	$video_url         = ath_resolve_hero_video_url( $args );
	$cloudinary_folder = (string) $args['cloudinary_folder'];
	$brand_h1          = (string) ( $args['brand_h1'] ?? 'Art Tutor Hanoi' );
	$autoplay          = ath_shortcode_flag_enabled( $args['autoplay'] );
	$muted             = ath_shortcode_flag_enabled( $args['muted'] );
	$loop              = ath_shortcode_flag_enabled( $args['loop'] );
	$playsinline       = ath_shortcode_flag_enabled( $args['playsinline'] );
	$preload           = in_array( $args['preload'], array( 'auto', 'metadata', 'none' ), true ) ? $args['preload'] : 'auto';

	ob_start();
	require ATH_THEME_DIR . '/partials/home-hero.php';

	return (string) ob_get_clean();
}

/**
 * Render hero in PHP partial or export shortcode during migration.
 */
function ath_render_home_hero() {
	if ( ath_home_hero_export_mode() ) {
		echo ath_home_hero_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_hero_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Shortcode callback — [ath_home_hero].
 *
 * @param array<string, string>|string $atts Shortcode attributes.
 */
function ath_home_hero_shortcode( $atts ) {
	$atts = shortcode_atts( ath_home_hero_default_atts(), $atts, 'ath_home_hero' );

	return ath_render_home_hero_block( $atts );
}

add_shortcode( 'ath_home_hero', 'ath_home_hero_shortcode' );
