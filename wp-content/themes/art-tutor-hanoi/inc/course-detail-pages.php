<?php
/**
 * Adult course detail pages — /courses/{slug}/ (Gutenberg-editable).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

require_once ATH_THEME_DIR . '/inc/course-gutenberg.php';

define( 'ATH_ADULT_COURSE_PAGES_SETUP_VERSION', '2' );

/**
 * Adult course rows from data file (with galleries merged).
 *
 * @return array<string, array<string, mixed>>
 */
function ath_adult_course_data() {
	static $data = null;

	if ( $data === null ) {
		$data = array();
		foreach ( array_keys( require ATH_THEME_DIR . '/data/course-pages.php' ) as $slug ) {
			$course = v2_course_by_slug( $slug );
			if ( $course ) {
				$data[ $slug ] = $course;
			}
		}
	}

	return $data;
}

/**
 * Adult course slugs.
 *
 * @return string[]
 */
function ath_adult_course_slugs() {
	return array_keys( ath_adult_course_data() );
}

/**
 * Courses hub page (parent for detail pages).
 *
 * @return WP_Post|null
 */
function ath_courses_hub_page() {
	$page = get_page_by_path( 'courses' );

	return ( $page && $page->post_type === 'page' ) ? $page : null;
}

/**
 * Adult course detail page by slug.
 *
 * @param string $slug Course slug.
 * @return WP_Post|null
 */
function ath_adult_course_page_by_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );
	if ( $slug === '' || $slug === 'courses' ) {
		return null;
	}

	$page = get_page_by_path( 'courses/' . $slug );

	return ( $page && $page->post_type === 'page' ) ? $page : null;
}

/**
 * Whether a page is an adult course detail page.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_adult_course_detail_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	$parent = (int) $post->post_parent;
	if ( ! $parent ) {
		return false;
	}

	$parent_post = get_post( $parent );

	return $parent_post && $parent_post->post_name === 'courses';
}

/**
 * Create child course pages under the Courses hub (idempotent).
 */
function ath_ensure_adult_course_pages() {
	$parent = ath_courses_hub_page();

	if ( ! $parent ) {
		return;
	}

	$parent_id   = (int) $parent->ID;
	$needs_flush = false;

	foreach ( ath_adult_course_data() as $slug => $course ) {
		$page = ath_adult_course_page_by_slug( $slug );

		if ( ! $page ) {
			$page_id = ath_insert_gutenberg_page(
				array(
					'post_title'  => $course['title'],
					'post_name'   => $slug,
					'post_parent' => $parent_id,
				)
			);

			if ( is_wp_error( $page_id ) || ! $page_id ) {
				continue;
			}

			$needs_flush = true;
			continue;
		}

		$page_id = (int) $page->ID;

		if ( (int) $page->post_parent !== $parent_id ) {
			wp_update_post(
				array(
					'ID'          => $page_id,
					'post_parent' => $parent_id,
				)
			);
			$needs_flush = true;
		}

		if ( $page->post_status !== 'publish' ) {
			wp_update_post(
				array(
					'ID'          => $page_id,
					'post_status' => 'publish',
				)
			);
		}

		ath_use_default_page_template( $page_id );

		if ( $page->post_title !== $course['title'] ) {
			wp_update_post(
				array(
					'ID'         => $page_id,
					'post_title' => $course['title'],
				)
			);
		}
	}

	$stored = get_option( 'ath_adult_course_pages_version', '' );
	if ( $needs_flush || $stored !== ATH_ADULT_COURSE_PAGES_SETUP_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'ath_adult_course_pages_version', ATH_ADULT_COURSE_PAGES_SETUP_VERSION, false );
	}
}

/**
 * Migrate adult course pages to native Gutenberg blocks.
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_adult_course_pages( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	ath_ensure_adult_course_pages();

	foreach ( ath_adult_course_slugs() as $slug ) {
		$page = ath_adult_course_page_by_slug( $slug );
		if ( ! $page ) {
			$results['errors'][] = sprintf( 'Course page "%s" not found.', $slug );
			continue;
		}

		if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
			$results['skipped'][] = 'courses/' . $slug;
			continue;
		}

		$course = v2_course_by_slug( $slug );
		if ( ! $course ) {
			$results['errors'][] = sprintf( 'No course data for "%s".', $slug );
			continue;
		}

		$block_content = ath_course_to_gutenberg_blocks( $course );
		if ( $block_content === '' ) {
			$results['errors'][] = sprintf( 'No blocks built for course "%s".', $slug );
			continue;
		}

		ath_prepare_gutenberg_page( $page->ID, $block_content );
		$results['updated'][] = 'courses/' . $slug;
	}

	return $results;
}

add_action( 'after_switch_theme', 'ath_ensure_adult_course_pages' );

add_action(
	'init',
	function () {
		ath_ensure_adult_course_pages();
	},
	5
);

/**
 * Seed adult course pages into Gutenberg on first admin visit after deploy.
 */
function ath_maybe_migrate_adult_course_pages() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'ath_adult_course_gutenberg_version' ) === '2' ) {
		return;
	}

	ath_migrate_adult_course_pages( false );
	update_option( 'ath_adult_course_gutenberg_version', '2', false );
	update_option( 'ath_content_mode', 'hybrid', false );
}

add_action( 'admin_init', 'ath_maybe_migrate_adult_course_pages', 102 );
