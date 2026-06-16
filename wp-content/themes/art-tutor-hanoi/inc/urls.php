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
		'workshops'    => 'workshops',
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
		'exhibition'   => '2026-exhibition-goihe',
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
 * Resolve theme data URLs to WordPress permalinks.
 *
 * @param string $url Raw URL from data files or partials.
 */
function ath_resolve_url( $url ) {
	if ( $url === '' || $url === '#' ) {
		return $url;
	}

	$book_tabs = array_merge( array_keys( ath_book_tabs() ), array_keys( ath_book_tab_legacy_map() ) );
	if ( in_array( $url, $book_tabs, true ) ) {
		return ath_book_url( $url );
	}

	if ( preg_match( '#^https?://#i', $url ) ) {
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

	$page_keys = array_flip( ath_page_slugs() );
	$base      = strtok( $url, '?' );
	$hash      = '';

	if ( strpos( $url, '#' ) !== false ) {
		list( $base, $hash ) = explode( '#', $url, 2 );
		$hash = '#' . $hash;
	}

	if ( isset( $page_keys[ $base ] ) ) {
		return ath_page_url( $page_keys[ $base ] ) . $hash;
	}

	$v2_files = array(
		'about.php'        => 'about',
		'courses.php'      => 'courses',
		'kids-courses.php' => 'kids-courses',
		'pricing.php'      => 'pricing',
		'calendar.php'     => 'calendar',
		'index.php'        => 'home',
	);

	if ( isset( $v2_files[ $base ] ) ) {
		return ath_page_url( $v2_files[ $base ] ) . $hash;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

/**
 * Resolve a theme link from shortcode / data (experience, page, book tab, or URL).
 *
 * @param string $link      Slug, page key, book tab, or URL.
 * @param string $link_type experience | page | book | url
 */
function ath_resolve_theme_link( $link, $link_type = 'url' ) {
	$link      = trim( (string) $link );
	$link_type = strtolower( trim( (string) $link_type ) );

	if ( $link === '' ) {
		return '#';
	}

	if ( $link_type === 'experience' ) {
		return ath_experience_url( $link );
	}

	if ( $link_type === 'page' ) {
		return ath_page_url( $link );
	}

	if ( $link_type === 'book' ) {
		return ath_book_url( $link );
	}

	if ( preg_match( '#^https?://#i', $link ) ) {
		return $link;
	}

	return ath_resolve_url( $link );
}

/**
 * Adult course detail URL.
 *
 * @param string $slug Course slug.
 */
function ath_course_url( $slug ) {
	$page = ath_adult_course_page_by_slug( $slug );
	if ( $page && $page->post_status === 'publish' ) {
		return get_permalink( $page );
	}

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
	$page = ath_workshop_page_by_slug( $slug );
	if ( $page && $page->post_status === 'publish' ) {
		return get_permalink( $page );
	}

	return home_url( '/workshops/' . rawurlencode( $slug ) . '/' );
}

/**
 * Whether the current request uses the v2 layout.
 */
function ath_is_v2_layout() {
	if ( is_front_page() ) {
		return true;
	}

	if ( is_singular( 'post' ) || is_category() || is_tag() || ath_uses_post_template() ) {
		return true;
	}

	foreach ( array( 'ath_kids_course' ) as $var ) {
		if ( get_query_var( $var ) ) {
			return true;
		}
	}

	if ( is_page() ) {
		return true;
	}

	$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
	if ( is_string( $path ) && preg_match( '#^/(courses|kids-courses|workshops)/[^/]+/?$#', $path ) ) {
		return true;
	}

	return false;
}
