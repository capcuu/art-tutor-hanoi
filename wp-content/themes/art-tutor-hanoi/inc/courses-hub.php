<?php
/**
 * Adult courses hub — flat pathway block detection and accordion shortcode swap.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

define( 'ATH_COURSES_HUB_GUTENBERG_VERSION', '3' );

/**
 * Whether post content has flat pathway blocks (no accordion markup).
 *
 * @param string $content Raw post content.
 */
function ath_courses_has_flat_pathway_blocks( $content ) {
	if ( strpos( $content, 'pathway__header' ) !== false ) {
		return false;
	}

	return strpos( $content, 'pathways-section' ) !== false
		|| strpos( $content, 'pathway__title' ) !== false
		|| strpos( $content, 'pathway__num' ) !== false
		|| (bool) preg_match( '/<!-- wp:group[^\n]*"className":"pathway"/', $content );
}

/**
 * Pathways incorrectly nested inside a Gutenberg courses-cta group.
 *
 * @param string $content Raw post content.
 */
function ath_courses_has_pathways_in_cta_group( $content ) {
	return (bool) preg_match( '/courses-cta[\s\S]*?(pathway__|pathways-section|"className":"pathway")/s', $content );
}

/**
 * Whether /courses/ body should be rebuilt as hero intro + hub shortcode.
 *
 * @param string $content Raw post content.
 */
function ath_courses_needs_hub_shortcode_swap( $content ) {
	if ( ath_courses_has_pathways_in_cta_group( $content ) ) {
		return true;
	}

	return ath_courses_has_flat_pathway_blocks( $content );
}

/**
 * Swap broken flat body for accordion shortcode; keep hero intro paragraphs only.
 *
 * @param string $content Post content.
 */
function ath_courses_editor_pathways_content( $content ) {
	if ( ! is_page( 'courses' ) || ! ath_courses_needs_hub_shortcode_swap( $content ) ) {
		return $content;
	}

	$intro = ath_courses_hub_hero_blocks_from_content( $content );
	if ( $intro === '' ) {
		return $content;
	}

	$shortcode = "<!-- wp:shortcode -->\n[ath_courses_hub]\n<!-- /wp:shortcode -->";

	return $intro . "\n\n" . $shortcode;
}

add_filter( 'the_content', 'ath_courses_editor_pathways_content', 7 );

/**
 * Auto-upgrade courses hub when theme version bumps (non-destructive DB fix).
 */
function ath_maybe_migrate_courses_hub_page() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'ath_courses_hub_gutenberg_version', '' ) === ATH_COURSES_HUB_GUTENBERG_VERSION ) {
		return;
	}

	$page = ath_courses_hub_page();
	if ( $page ) {
		$page_content = (string) $page->post_content;
		if ( ath_courses_needs_hub_shortcode_swap( $page_content ) || ath_courses_has_pathways_in_cta_group( $page_content ) ) {
			ath_migrate_courses_hub_page( true );
		}
	}

	update_option( 'ath_courses_hub_gutenberg_version', ATH_COURSES_HUB_GUTENBERG_VERSION, false );
}

add_action( 'admin_init', 'ath_maybe_migrate_courses_hub_page', 102 );

/**
 * Ensure migrated meta when the hub page is saved from the block editor.
 *
 * @param int $post_id Post ID.
 */
function ath_sync_courses_hub_meta( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	$page = get_post( $post_id );
	if ( ! $page || $page->post_type !== 'page' || $page->post_name !== 'courses' ) {
		return;
	}

	if ( strpos( (string) $page->post_content, '[ath_courses_hub]' ) === false ) {
		return;
	}

	ath_mark_page_gutenberg( $post_id );
}

add_action( 'save_post_page', 'ath_sync_courses_hub_meta', 20 );
