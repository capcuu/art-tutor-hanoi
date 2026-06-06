<?php
/**
 * Generic page.php — editor content and slug-routed partials.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * WordPress slug for the weekly calendar page.
 */
function ath_calendar_page_slug() {
	return 'weekly-calendar';
}

/**
 * Page slugs rendered via a theme partial (through page.php).
 *
 * @return array<string, string> slug => partial filename under partials/
 */
function ath_routed_page_partials() {
	$map = array(
		'link'            => 'links-content.php',
		'thank-you'       => 'thank-you-content.php',
		'weekly-calendar' => 'calendar-content.php',
		'pricing'         => 'pricing-content.php',
		'courses'         => 'courses-content.php',
		'kids-courses'    => 'kids-courses-content.php',
	);

	return apply_filters( 'ath_routed_page_partials', $map );
}

/**
 * Absolute path to a routed partial, if any.
 *
 * @param string $slug Page slug.
 */
function ath_routed_page_partial_path( $slug ) {
	$partials = ath_routed_page_partials();

	if ( ! isset( $partials[ $slug ] ) ) {
		return null;
	}

	$path = ATH_THEME_DIR . '/partials/' . $partials[ $slug ];

	return is_readable( $path ) ? $path : null;
}

/**
 * Variables required by calendar-content.php.
 *
 * @return array<string, string>
 */
function ath_calendar_page_vars() {
	$now    = new DateTime( 'now', wp_timezone() );
	$monday = ( clone $now )->modify( '-' . ( (int) $now->format( 'N' ) - 1 ) . ' days' );
	$sunday = ( clone $monday )->modify( '+6 days' );
	$fmt    = static function ( DateTime $d ) {
		return $d->format( 'M j, Y' );
	};

	$week_range        = 'From ' . $fmt( $monday ) . ' to ' . $fmt( $sunday );
	$art_calendar_html = v2_art_calendar_html();
	$table_html        = v2_tablepress_table( 2 );

	if ( $table_html === '' ) {
		$table_html = v2_tablepress_table_from_live( 2 );
	}

	$table_html = v2_prepare_tablepress_html( $table_html );

	return array(
		'week_range'        => $week_range,
		'art_calendar_html' => $art_calendar_html,
		'table_html'        => $table_html,
	);
}

/**
 * Page slugs that use page.php with editor content only.
 *
 * @return string[]
 */
function ath_generic_v2_page_slugs() {
	$slugs = array(
		'faq',
		'hanoi-art-supply-map',
		'art-tutorials',
		'free-art-feedback',
		'join-our-art-community',
		'exhibitionlivingcolors2025',
		'meet-the-artists',
		'art-tutor-for-kids-eng',
		'art-tutor-fine-art-courses-hanoi',
		'call-for-artists',
		'practice-at-home',
		'workshops',
		'discount-code',
		'quotation-table',
		'certificate_form',
		'nude-drawing-class',
		'programs',
		'beginner-friendly-art-class-in-hanoi',
		'artist-residency-in-hanoi',
		'book-a-class',
		'book-a-trial-art-session-art-tutor-hanoi',
	);

	return apply_filters( 'ath_generic_v2_page_slugs', $slugs );
}

/**
 * Slugs that must keep a dedicated page-templates/* assignment.
 *
 * @return array<string, string> slug => template path
 */
function ath_dedicated_v2_page_templates() {
	$map = array(
		'about'             => 'page-templates/about.php',
		'students-artworks' => 'page-templates/students-artworks.php',
		'book'              => 'page-templates/book.php',
	);

	return apply_filters( 'ath_dedicated_v2_page_templates', $map );
}

/**
 * All page slugs that should use default page.php (no page-templates/* meta).
 *
 * @return string[]
 */
function ath_page_php_slugs() {
	return array_values(
		array_unique(
			array_merge(
				array_keys( ath_routed_page_partials() ),
				ath_generic_v2_page_slugs()
			)
		)
	);
}

/**
 * Whether this page uses the generic page.php layout.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_generic_v2_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	$dedicated = ath_dedicated_v2_page_templates();
	if ( isset( $dedicated[ $post->post_name ] ) ) {
		return false;
	}

	if ( in_array( $post->post_name, ath_page_php_slugs(), true ) ) {
		return true;
	}

	$template = get_page_template_slug( $post->ID );
	if ( $template === '' || $template === 'default' ) {
		return true;
	}

	return false;
}

/**
 * Display title for page.php pages (fallback when WP title is empty).
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_page_display_title( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$title = get_the_title( $post );
	if ( $title !== '' ) {
		return $title;
	}

	$fallbacks = array(
		'hanoi-art-supply-map' => 'Hanoi Art Supply Map',
	);

	if ( isset( $fallbacks[ $post->post_name ] ) ) {
		return $fallbacks[ $post->post_name ];
	}

	return ucwords( str_replace( array( '-', '_' ), ' ', $post->post_name ) );
}

/**
 * Ensure weekly-calendar page uses page.php.
 */
function ath_ensure_calendar_page() {
	$page = get_page_by_path( ath_calendar_page_slug() );

	if ( ! $page ) {
		return;
	}

	delete_post_meta( $page->ID, '_wp_page_template' );

	if ( $page->post_title === '' || $page->post_title === 'Calendar' ) {
		wp_update_post(
			array(
				'ID'         => $page->ID,
				'post_title' => 'Weekly Calendar',
			)
		);
	}
}

/**
 * Assign default template to page.php pages; keep dedicated templates intact.
 */
function ath_ensure_generic_v2_pages() {
	ath_ensure_calendar_page();

	$dedicated = ath_dedicated_v2_page_templates();

	foreach ( ath_page_php_slugs() as $slug ) {
		if ( isset( $dedicated[ $slug ] ) ) {
			continue;
		}

		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}

		$current = get_page_template_slug( $page->ID );
		if ( $current !== '' && $current !== 'default' ) {
			delete_post_meta( $page->ID, '_wp_page_template' );
		}
	}

	foreach ( $dedicated as $slug => $template ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}

		if ( get_page_template_slug( $page->ID ) !== $template ) {
			update_post_meta( $page->ID, '_wp_page_template', $template );
		}
	}

	$supplies = get_page_by_path( 'hanoi-art-supply-map' );
	if ( $supplies && $supplies->post_title === '' ) {
		wp_update_post(
			array(
				'ID'         => $supplies->ID,
				'post_title' => 'Hanoi Art Supply Map',
			)
		);
	}
}

add_action( 'after_switch_theme', 'ath_ensure_generic_v2_pages' );

add_filter(
	'template_include',
	function ( $template ) {
		if ( ! is_singular( 'page' ) || ! ath_is_generic_v2_page() ) {
			return $template;
		}

		$page_php = ATH_THEME_DIR . '/page.php';

		return is_readable( $page_php ) ? $page_php : $template;
	},
	20
);

add_action(
	'init',
	function () {
		if ( get_option( 'ath_generic_v2_pages_setup' ) === '5' ) {
			return;
		}
		ath_ensure_generic_v2_pages();
		update_option( 'ath_generic_v2_pages_setup', '5' );
	},
	5
);
