<?php
/**
 * Legacy URL 301 redirects — old slugs → current theme URLs.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Map legacy path (no leading/trailing slash) → absolute redirect URL.
 *
 * @return array<string, string>
 */
function ath_legacy_redirect_paths() {
	$map = array(
		'book-a-trial-art-session-art-tutor-hanoi' => ath_page_url( 'book' ),
		'meet-the-artists'                         => ath_page_url( 'about' ),
		'art-tutor-fine-art-courses-hanoi'         => ath_page_url( 'courses' ),
		'art-class-pricing-in-hanoi'               => ath_page_url( 'pricing' ),
		'art-tutor-for-kids-eng'                   => ath_page_url( 'kids-courses' ),
		'nude-drawing-class'                       => ath_experience_url( 'life-drawing' ),
		'nude-model-drawing-class-hanoi'           => ath_experience_url( 'life-drawing' ),
		'dare-to-draw-nude-drawing-class'          => ath_experience_url( 'life-drawing' ),
		'life-drawing-in-hanoi-join-live-model-session' => ath_experience_url( 'life-drawing' ),
		'art-classes-in-hanoi'                     => ath_page_url( 'courses' ),
		'the-best-english-speaking-art-classes-in-hanoi-2025' => ath_page_url( 'courses' ),
		'best-art-classes-in-hanoi-for-beginners-expats-2026-guide' => ath_page_url( 'courses' ),
		'1-day-creative-workshops-in-hanoi'        => ath_page_url( 'workshops' ),
		'top-5-art-workshops-in-hanoi-for-expats-travelers' => ath_page_url( 'workshops' ),
		'art-workshops-in-hanoi-for-travelers-2025' => ath_page_url( 'workshops' ),
	);

	return apply_filters( 'ath_legacy_redirect_paths', $map );
}

/**
 * Redirect legacy single-segment paths (301).
 */
function ath_handle_legacy_redirects() {
	if ( is_admin() ) {
		return;
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path = wp_parse_url( $uri, PHP_URL_PATH );

	if ( ! is_string( $path ) || $path === '' ) {
		return;
	}

	$path = trim( $path, '/' );
	if ( $path === '' ) {
		return;
	}

	$map = ath_legacy_redirect_paths();
	if ( ! isset( $map[ $path ] ) ) {
		return;
	}

	$target = $map[ $path ];
	if ( ! $target || ! is_string( $target ) ) {
		return;
	}

	$query = wp_parse_url( $uri, PHP_URL_QUERY );
	if ( is_string( $query ) && $query !== '' ) {
		$target = $target . ( strpos( $target, '?' ) !== false ? '&' : '?' ) . $query;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}

add_action( 'template_redirect', 'ath_handle_legacy_redirects', 1 );

/**
 * Redirect legacy Learner's artworks category archives → /students-artworks/.
 */
function ath_redirect_learner_artworks_category_archive() {
	if ( is_admin() ) {
		return;
	}

	$uri    = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path   = wp_parse_url( $uri, PHP_URL_PATH );
	$target = null;

	if ( is_string( $path ) && $path !== '' ) {
		$path = trim( $path, '/' );
		foreach ( ath_learner_artwork_category_slugs() as $slug ) {
			$pattern = '#^category/' . preg_quote( $slug, '#' ) . '(?:/page/\d+)?$#';
			if ( $path === 'category/' . $slug || preg_match( $pattern, $path ) ) {
				$target = ath_students_artworks_url();
				break;
			}
		}
	}

	if ( ! $target && is_category() && ath_is_learner_artwork_category() ) {
		$target = ath_students_artworks_url();
	}

	if ( ! $target ) {
		return;
	}

	$query = wp_parse_url( $uri, PHP_URL_QUERY );
	if ( is_string( $query ) && $query !== '' ) {
		$target = $target . ( strpos( $target, '?' ) !== false ? '&' : '?' ) . $query;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}

add_action( 'template_redirect', 'ath_redirect_learner_artworks_category_archive', 0 );
