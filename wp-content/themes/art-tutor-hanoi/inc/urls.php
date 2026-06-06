<?php
/**
 * URL helpers — maps v2 paths to WordPress permalinks.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default page slugs (override via WP pages when they exist).
 *
 * @return array<string, string>
 */
function ath_page_slugs() {
	return array(
		'home'         => '',
		'about'        => 'about',
		'courses'      => 'courses',
		'kids-courses' => 'kids-courses',
		'pricing'      => 'pricing',
		'calendar'     => 'weekly-calendar',
		'book'         => 'book',
		'thank-you'    => 'thank-you',
		'faq'          => 'faq',
		'art-supplies' => 'hanoi-art-supply-map',
		'art-tutorials'=> 'art-tutorials',
		'art-feedback' => 'free-art-feedback',
		'community'    => 'join-our-art-community',
		'exhibition'   => 'exhibitionlivingcolors2025',
		'links'        => 'link',
		'students-artworks' => 'students-artworks',
		'about-artists'=> 'meet-the-artists',
		'kids-portfolio' => 'art-tutor-for-kids-eng',
		'adults-portfolio' => 'art-tutor-fine-art-courses-hanoi',
	);
}

/**
 * Permalink for a logical site page.
 *
 * @param string $key Page key from ath_page_slugs().
 */
function ath_page_url( $key ) {
	static $cache = array();

	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$slugs = ath_page_slugs();

	if ( $key === 'home' ) {
		$cache[ $key ] = home_url( '/' );
		return $cache[ $key ];
	}

	if ( ! isset( $slugs[ $key ] ) ) {
		$cache[ $key ] = home_url( '/' );
		return $cache[ $key ];
	}

	$slug = $slugs[ $key ];

	if ( $key === 'book' ) {
		$cache[ $key ] = ath_book_base_url();
		return $cache[ $key ];
	}

	if ( $key === 'thank-you' ) {
		$cache[ $key ] = ath_thank_you_base_url();
		return $cache[ $key ];
	}

	$page = get_page_by_path( $slug );

	if ( $page ) {
		$cache[ $key ] = get_permalink( $page );
		return $cache[ $key ];
	}

	$cache[ $key ] = home_url( '/' . trim( $slug, '/' ) . '/' );
	return $cache[ $key ];
}

/**
 * Theme asset URL (images under assets/images/).
 *
 * @param string $path Relative path, e.g. gallery/HTT_1546.jpg
 */
function ath_asset_url( $path ) {
	return ATH_THEME_URI . '/assets/images/' . ltrim( $path, '/' );
}

/**
 * Convert legacy v2 relative URLs to WordPress URLs.
 *
 * @param string $url Raw URL from data files or partials.
 */
function ath_resolve_url( $url ) {
	if ( $url === '' || $url === '#' ) {
		return $url;
	}

	$book_tabs = array( 'trial', 'workshops', 'adults', 'kids', 'residency' );
	if ( in_array( $url, $book_tabs, true ) ) {
		return ath_book_url( $url );
	}

	if ( preg_match( '#^https?://#i', $url ) ) {
		if ( strpos( $url, 'book-a-trial-art-session' ) !== false ) {
			return ath_book_url( 'trial' );
		}
		if ( strpos( $url, 'book-a-class' ) !== false ) {
			return ath_book_url( 'trial' );
		}
		return $url;
	}

	if ( preg_match( '#^experience\.php\?slug=([^&]+)#', $url, $m ) ) {
		return ath_experience_url( $m[1] );
	}

	if ( preg_match( '#^course\.php\?slug=([^&]+)#', $url, $m ) ) {
		return ath_course_url( $m[1] );
	}

	if ( preg_match( '#^kids-course\.php\?slug=([^&]+)#', $url, $m ) ) {
		return ath_kids_course_url( $m[1] );
	}

	$map = array(
		'about.php'        => 'about',
		'courses.php'      => 'courses',
		'kids-courses.php' => 'kids-courses',
		'pricing.php'      => 'pricing',
		'calendar.php'     => 'calendar',
		'index.php'        => 'home',
	);

	$base = strtok( $url, '?' );
	$hash = '';
	if ( strpos( $url, '#' ) !== false ) {
		list( $base, $hash ) = explode( '#', $url, 2 );
		$hash = '#' . $hash;
	}

	if ( isset( $map[ $base ] ) ) {
		return ath_page_url( $map[ $base ] ) . $hash;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

/**
 * Adult course detail URL.
 *
 * @param string $slug Course slug.
 */
function ath_course_url( $slug ) {
	return home_url( '/courses/' . rawurlencode( $slug ) . '/' );
}

/**
 * Kids course detail URL.
 *
 * @param string $slug Course slug.
 */
function ath_kids_course_url( $slug ) {
	return home_url( '/kids-courses/' . rawurlencode( $slug ) . '/' );
}

/**
 * Workshop / experience URL.
 *
 * @param string $slug Experience slug.
 */
function ath_experience_url( $slug ) {
	return home_url( '/workshops/' . rawurlencode( $slug ) . '/' );
}

/**
 * Whether the current request uses the v2 layout.
 */
function ath_is_v2_layout() {
	if ( is_front_page() ) {
		return true;
	}

	if ( is_singular( 'post' ) && ath_is_learner_artwork_post() ) {
		return true;
	}

	foreach ( array( 'ath_course', 'ath_kids_course', 'ath_experience' ) as $var ) {
		if ( get_query_var( $var ) ) {
			return true;
		}
	}

	if ( ! is_page() ) {
		$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
		if ( is_string( $path ) && preg_match( '#^/(courses|kids-courses|workshops)/[^/]+/?$#', $path ) ) {
			return true;
		}
		return false;
	}

	$template = get_page_template_slug( get_queried_object_id() );
	if ( $template && strpos( $template, 'page-templates/' ) === 0 ) {
		return true;
	}

	return ath_is_generic_v2_page();
}
