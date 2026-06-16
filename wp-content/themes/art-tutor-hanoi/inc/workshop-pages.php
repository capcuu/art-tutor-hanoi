<?php
/**
 * Workshop pages — WordPress pages at /workshops/{slug}/ (Gutenberg-editable).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

require_once ATH_THEME_DIR . '/inc/gutenberg-blocks.php';

define( 'ATH_WORKSHOP_PAGES_SETUP_VERSION', '4' );

/**
 * Workshop content from data file.
 *
 * @return array<string, array<string, mixed>>
 */
function ath_workshop_data() {
	static $data = null;

	if ( $data === null ) {
		$data = require ATH_THEME_DIR . '/data/experience-pages.php';
	}

	return $data;
}

/**
 * Workshop page slugs.
 *
 * @return string[]
 */
function ath_workshop_slugs() {
	return array_keys( ath_workshop_data() );
}

/**
 * Workshop slugs listed on the hub page and nav submenu (excludes trial class).
 *
 * @return string[]
 */
function ath_workshop_hub_slugs() {
	return array( 'life-drawing', 'silk-painting', 'artist-residency' );
}

/**
 * Workshops hub page (/workshops/).
 *
 * @return WP_Post|null
 */
function ath_workshops_hub_page() {
	$page = get_page_by_path( 'workshops' );

	return ( $page && $page->post_type === 'page' ) ? $page : null;
}

/**
 * Permalink for the workshops hub page.
 */
function ath_workshops_hub_url() {
	$page = ath_workshops_hub_page();
	if ( $page && $page->post_status === 'publish' ) {
		return get_permalink( $page );
	}

	return home_url( '/workshops/' );
}

/**
 * Whether the current page is the workshops hub (/workshops/).
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_workshops_hub_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	return $post->post_name === 'workshops' && ! (int) $post->post_parent;
}

/**
 * Default seed data for the workshops hub page (Gutenberg migration source).
 *
 * @return array<string, mixed>
 */
function ath_workshops_hub_data() {
	static $data = null;

	if ( $data === null ) {
		$data = require ATH_THEME_DIR . '/data/workshops-hub.php';
	}

	return $data;
}

/**
 * Build native Gutenberg blocks for the /workshops/ hub page.
 */
function ath_workshops_hub_to_gutenberg_blocks() {
	$hub    = ath_workshops_hub_data();
	$blocks = array();

	if ( ! empty( $hub['hero']['subtitle'] ) ) {
		$blocks[] = ath_gutenberg_paragraph( (string) $hub['hero']['subtitle'], 'courses-hero__subtitle' );
	}

	foreach ( (array) ( $hub['intro'] ?? array() ) as $index => $paragraph ) {
		if ( (int) $index === 1 ) {
			$blocks[] = ath_gutenberg_paragraph_html(
				'Looking for weekly classes instead? Explore our <a href="' . esc_url( ath_page_url( 'courses' ) ) . '">adult art courses</a> or '
				. '<a href="' . esc_url( ath_book_url( 'adult' ) ) . '">book a trial session</a> before you commit.'
			);
			continue;
		}
		$blocks[] = ath_gutenberg_paragraph( (string) $paragraph );
	}

	if ( ! empty( $hub['schedule']['items'] ) && is_array( $hub['schedule']['items'] ) ) {
		$blocks[] = ath_gutenberg_heading( (string) ( $hub['schedule']['heading'] ?? 'Schedule' ), 2 );
		$blocks[] = ath_gutenberg_list( $hub['schedule']['items'] );
	}

	foreach ( (array) ( $hub['workshops'] ?? array() ) as $workshop ) {
		$section = array(
			ath_gutenberg_heading( (string) $workshop['title'], 3, 'workshops-hub__card-title' ),
			ath_gutenberg_paragraph( (string) $workshop['desc'], 'workshops-hub__card-desc' ),
		);

		if ( ! empty( $workshop['duration'] ) ) {
			$section[] = ath_gutenberg_paragraph_html(
				'<strong>Duration:</strong> ' . esc_html( (string) $workshop['duration'] ),
				'workshops-hub__card-meta'
			);
		}

		if ( ! empty( $workshop['schedule'] ) ) {
			$section[] = ath_gutenberg_paragraph_html(
				'<strong>When:</strong> ' . esc_html( (string) $workshop['schedule'] ),
				'workshops-hub__card-meta'
			);
		}

		$section[] = ath_gutenberg_paragraph_html(
			'<a href="' . esc_url( ath_experience_url( (string) $workshop['slug'] ) ) . '">Learn more</a>',
			'workshops-hub__card-link'
		);

		$blocks[] = ath_gutenberg_group( implode( "\n\n", $section ), 'workshops-hub__card' );
	}

	if ( ! empty( $hub['cta']['text'] ) ) {
		$blocks[] = ath_gutenberg_paragraph( (string) $hub['cta']['text'], 'courses-cta__text' );
	}

	if ( ! empty( $hub['cta']['button'] ) ) {
		$blocks[] = ath_gutenberg_btn(
			(string) $hub['cta']['button'],
			ath_book_url( (string) ( $hub['cta']['book'] ?? 'adult' ) ),
			'cta',
			'courses-cta__btn'
		);
	}

	$courses_url = ath_page_url( 'courses' );
	$blocks[]    = ath_gutenberg_paragraph_html(
		'<a href="' . esc_url( ath_page_url( 'pricing' ) ) . '">View pricing</a> '
		. '<span aria-hidden="true">·</span> <a href="' . esc_url( ath_page_url( 'calendar' ) ) . '">Weekly calendar</a> '
		. '<span aria-hidden="true">·</span> <a href="' . esc_url( $courses_url ) . '">Art classes</a>',
		'courses-cta__links'
	);

	$blocks[] = ath_gutenberg_paragraph_html(
		'<strong>Explore:</strong> <a href="' . esc_url( $courses_url ) . '">Art classes</a> '
		. '<span aria-hidden="true">·</span> <a href="' . esc_url( ath_page_url( 'kids-courses' ) ) . '">Kids classes</a> '
		. '<span aria-hidden="true">·</span> <a href="' . esc_url( ath_book_url( 'adult' ) ) . '">Book a trial</a>',
		'commercial-crosslinks__links'
	);

	return trim( implode( "\n\n", $blocks ) );
}

/**
 * Migrate the workshops hub page to Gutenberg (default page.php template).
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_workshops_hub_page( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	ath_ensure_workshop_pages();

	$page = ath_workshops_hub_page();
	if ( ! $page ) {
		$results['errors'][] = 'Workshops hub page not found.';
		return $results;
	}

	if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
		$results['skipped'][] = 'workshops';
		return $results;
	}

	$block_content = ath_workshops_hub_to_gutenberg_blocks();
	if ( $block_content === '' ) {
		$results['errors'][] = 'No blocks built for workshops hub.';
		return $results;
	}

	delete_post_meta( $page->ID, '_wp_page_template' );
	ath_clear_invalid_page_template( $page->ID );

	ath_prepare_gutenberg_page( $page->ID, $block_content );
	$results['updated'][] = 'workshops';

	return $results;
}

/**
 * Workshop child page by slug.
 *
 * @param string $slug Workshop slug.
 * @return WP_Post|null
 */
function ath_workshop_page_by_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );
	if ( $slug === '' ) {
		return null;
	}

	$page = get_page_by_path( 'workshops/' . $slug );

	return ( $page && $page->post_type === 'page' ) ? $page : null;
}

/**
 * Whether a page is a workshop detail page.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_workshop_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	$parent = (int) $post->post_parent;
	if ( ! $parent ) {
		return false;
	}

	$parent_post = get_post( $parent );

	return $parent_post && $parent_post->post_name === 'workshops';
}

/**
 * Create parent + child workshop pages (idempotent).
 */
function ath_ensure_workshop_pages() {
	$needs_flush = false;

	$parent = get_page_by_path( 'workshops' );
	if ( ! $parent ) {
		$parent_id = ath_insert_gutenberg_page(
			array(
				'post_title' => 'Workshops',
				'post_name'  => 'workshops',
			)
		);

		if ( is_wp_error( $parent_id ) || ! $parent_id ) {
			return;
		}

		$needs_flush = true;
	} else {
		$parent_id = (int) $parent->ID;

		if ( $parent->post_status !== 'publish' ) {
			wp_update_post(
				array(
					'ID'          => $parent_id,
					'post_status' => 'publish',
				)
			);
			$needs_flush = true;
		}

		delete_post_meta( $parent_id, '_wp_page_template' );
		ath_clear_invalid_page_template( $parent_id );

		if ( $parent->post_title !== 'Workshops' ) {
			wp_update_post(
				array(
					'ID'         => $parent_id,
					'post_title' => 'Workshops',
				)
			);
		}
	}

	foreach ( ath_workshop_data() as $slug => $experience ) {
		$page = ath_workshop_page_by_slug( $slug );

		if ( ! $page ) {
			$page_id = ath_insert_gutenberg_page(
				array(
					'post_title'  => $experience['title'],
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

		if ( $page->post_title !== $experience['title'] ) {
			wp_update_post(
				array(
					'ID'         => $page_id,
					'post_title' => $experience['title'],
				)
			);
		}
	}

	$stored = get_option( 'ath_workshop_pages_version', '' );
	if ( $needs_flush || $stored !== ATH_WORKSHOP_PAGES_SETUP_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'ath_workshop_pages_version', ATH_WORKSHOP_PAGES_SETUP_VERSION, false );
	}
}

/**
 * Whether a workshop detail page should render from the block editor.
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_should_use_workshop_gutenberg( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || ! ath_is_workshop_page( $post ) ) {
		return false;
	}

	if ( ath_content_mode() === 'legacy' ) {
		return false;
	}

	return ath_page_has_editor_content( $post );
}

/**
 * Migrate workshop pages to native Gutenberg blocks (heading, paragraph, list, image).
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_workshop_pages( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	ath_ensure_workshop_pages();

	foreach ( ath_workshop_slugs() as $slug ) {
		$page = ath_workshop_page_by_slug( $slug );
		if ( ! $page ) {
			$results['errors'][] = sprintf( 'Workshop page "%s" not found.', $slug );
			continue;
		}

		if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
			$results['skipped'][] = 'workshops/' . $slug;
			continue;
		}

		$experience = v2_experience_by_slug( $slug );
		if ( ! $experience ) {
			$results['errors'][] = sprintf( 'No experience data for "%s".', $slug );
			continue;
		}

		$block_content = ath_experience_to_gutenberg_blocks( $experience );
		if ( $block_content === '' ) {
			$results['errors'][] = sprintf( 'No blocks built for workshop "%s".', $slug );
			continue;
		}

		ath_prepare_gutenberg_page( $page->ID, $block_content );
		$results['updated'][] = 'workshops/' . $slug;
	}

	return $results;
}

add_action( 'after_switch_theme', 'ath_ensure_workshop_pages' );

add_action(
	'init',
	function () {
		ath_ensure_workshop_pages();
	},
	5
);

/**
 * Seed / upgrade workshop pages — mark editor pages; never overwrite existing content.
 */
function ath_maybe_migrate_workshop_pages() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$version = get_option( 'ath_workshop_gutenberg_version', '' );

	if ( $version === '5' ) {
		return;
	}

	ath_ensure_workshop_pages();

	foreach ( ath_workshop_slugs() as $slug ) {
		$page = ath_workshop_page_by_slug( $slug );
		if ( ! $page ) {
			continue;
		}

		if ( ath_page_has_editor_content( $page ) ) {
			ath_mark_page_gutenberg( (int) $page->ID );
			continue;
		}

		// Empty page — seed once from theme data.
		$experience = v2_experience_by_slug( $slug );
		if ( ! $experience ) {
			continue;
		}

		$block_content = ath_experience_to_gutenberg_blocks( $experience );
		if ( $block_content === '' ) {
			continue;
		}

		ath_prepare_gutenberg_page( (int) $page->ID, $block_content );
	}

	$hub = ath_workshops_hub_page();
	if ( $hub && ath_page_has_editor_content( $hub ) ) {
		ath_mark_page_gutenberg( (int) $hub->ID );
	}

	update_option( 'ath_workshop_gutenberg_version', '5', false );
}

add_action( 'admin_init', 'ath_maybe_migrate_workshop_pages', 101 );

/**
 * Keep workshop detail pages on Gutenberg after editor saves.
 *
 * @param int $post_id Post ID.
 */
function ath_sync_workshop_page_meta( $post_id ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'page' || ! ath_is_workshop_page( $post ) ) {
		return;
	}

	if ( ! ath_page_has_editor_content( $post ) ) {
		return;
	}

	ath_mark_page_gutenberg( $post_id );
}

add_action( 'save_post_page', 'ath_sync_workshop_page_meta', 20 );
