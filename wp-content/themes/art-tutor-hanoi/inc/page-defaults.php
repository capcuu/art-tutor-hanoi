<?php
/**
 * Default page policy — page.php + Gutenberg for all new content pages.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Slugs allowed to keep a dedicated page-templates/* file (dynamic PHP only).
 *
 * @return array<string, string> slug => template path
 */
function ath_dedicated_page_template_slugs() {
	return ath_dedicated_v2_page_templates();
}

/**
 * Whether a page may use a custom page template.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_page_allows_custom_template( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	$dedicated = ath_dedicated_page_template_slugs();

	return isset( $dedicated[ $post->post_name ] );
}

/**
 * Force a page onto default page.php (clear custom template meta).
 *
 * @param int $post_id Page ID.
 */
function ath_use_default_page_template( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return;
	}

	delete_post_meta( $post_id, '_wp_page_template' );
	ath_clear_invalid_page_template( $post_id );
}

/**
 * Mark a page as Gutenberg-managed (hybrid mode).
 *
 * @param int $post_id Page ID.
 */
function ath_mark_page_gutenberg( $post_id ) {
	update_post_meta( (int) $post_id, '_ath_gutenberg_migrated', '1' );
	delete_post_meta( (int) $post_id, '_ath_gutenberg_full_layout' );
}

/**
 * Apply default template + Gutenberg meta after content is set.
 *
 * @param int    $post_id       Page ID.
 * @param string $block_content Optional block editor content.
 */
function ath_prepare_gutenberg_page( $post_id, $block_content = null ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return;
	}

	ath_use_default_page_template( $post_id );

	if ( $block_content !== null && $block_content !== '' ) {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $block_content,
			),
			true
		);
	}

	ath_mark_page_gutenberg( $post_id );
}

/**
 * Insert a new page using default template + Gutenberg.
 *
 * @param array<string, mixed> $args post_title, post_name, post_parent, post_content, post_status.
 * @return int|WP_Error Page ID or error.
 */
function ath_insert_gutenberg_page( array $args ) {
	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_title'   => (string) ( $args['post_title'] ?? '' ),
			'post_name'    => (string) ( $args['post_name'] ?? '' ),
			'post_parent'  => (int) ( $args['post_parent'] ?? 0 ),
			'post_content' => (string) ( $args['post_content'] ?? '' ),
			'post_status'  => (string) ( $args['post_status'] ?? 'publish' ),
		),
		true
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return $page_id;
	}

	$content = isset( $args['post_content'] ) ? (string) $args['post_content'] : null;
	ath_prepare_gutenberg_page( (int) $page_id, $content !== '' ? $content : null );

	return (int) $page_id;
}

/**
 * One-time sweep: every page → page.php except Book and Students' Artworks.
 *
 * @return array{cleared: int[], dedicated: int[]}
 */
function ath_sweep_all_pages_to_v2_template() {
	$dedicated = ath_dedicated_page_template_slugs();
	$cleared   = array();
	$kept      = array();

	$page_ids = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $page_ids as $page_id ) {
		$page_id = (int) $page_id;
		$post    = get_post( $page_id );
		if ( ! $post ) {
			continue;
		}

		ath_clear_invalid_page_template( $page_id );

		if ( isset( $dedicated[ $post->post_name ] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $dedicated[ $post->post_name ] );
			$kept[] = $page_id;
			continue;
		}

		ath_use_default_page_template( $page_id );
		$cleared[] = $page_id;
	}

	return array(
		'cleared'   => $cleared,
		'dedicated' => $kept,
	);
}

add_action(
	'init',
	function () {
		if ( get_option( 'ath_v2_template_sweep_v2' ) === '1' ) {
			return;
		}

		ath_sweep_all_pages_to_v2_template();
		update_option( 'ath_v2_template_sweep_v2', '1', false );
	},
	7
);

/**
 * Clear custom templates from pages that should use default page.php.
 */
function ath_ensure_default_page_templates() {
	foreach ( ath_workshop_slugs() as $slug ) {
		$page = ath_workshop_page_by_slug( $slug );
		if ( $page ) {
			ath_use_default_page_template( $page->ID );
		}
	}

	$hub = ath_workshops_hub_page();
	if ( $hub ) {
		ath_use_default_page_template( $hub->ID );
	}

	foreach ( ath_adult_course_slugs() as $slug ) {
		$page = ath_adult_course_page_by_slug( $slug );
		if ( $page ) {
			ath_use_default_page_template( $page->ID );
		}
	}
}

add_action( 'init', 'ath_ensure_default_page_templates', 6 );

/**
 * New and updated pages must use default page.php unless dedicated (book, students-artworks).
 *
 * @param int $post_id Page ID.
 */
function ath_enforce_default_page_template_on_save( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'page' ) {
		return;
	}

	if ( ath_page_allows_custom_template( $post ) ) {
		return;
	}

	ath_use_default_page_template( $post_id );
}

add_action( 'save_post_page', 'ath_enforce_default_page_template_on_save', 20 );
