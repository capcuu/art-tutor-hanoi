<?php
/**
 * More links page — v2 layout, page setup.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is this the more links hub page?
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_links_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	if ( $post->post_name === 'link' ) {
		return true;
	}

	return false;
}

/**
 * Create or update the More Links page (slug: link).
 */
function ath_ensure_links_page() {
	$page = get_page_by_path( 'link' );

	if ( ! $page ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => 'Links',
				'post_name'    => 'link',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			$page = get_post( $page_id );
		}
	}

	if ( $page ) {
		delete_post_meta( $page->ID, '_wp_page_template' );
	}
}

add_action( 'after_switch_theme', 'ath_ensure_links_page' );

add_action(
	'init',
	function () {
		if ( get_option( 'ath_links_page_setup' ) === '2' ) {
			return;
		}
		ath_ensure_links_page();
		update_option( 'ath_links_page_setup', '2' );
	},
	5
);
