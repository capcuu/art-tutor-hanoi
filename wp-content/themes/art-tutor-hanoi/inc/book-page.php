<?php
/**
 * Book page — canonical /book/ URL, page setup, redirects.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Canonical booking page base URL (always /book/).
 */
function ath_book_base_url() {
	return user_trailingslashit( home_url( '/book' ) );
}

/**
 * Is this the booking page?
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_book_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	if ( in_array( $post->post_name, array( 'book', 'book-a-class' ), true ) ) {
		return true;
	}

	return get_page_template_slug( $post->ID ) === 'page-templates/book.php';
}

/**
 * Create or update the Book a Class page (runs once after theme activation).
 */
function ath_ensure_book_page() {
	$template = 'page-templates/book.php';
	$page     = get_page_by_path( 'book' );
	$legacy   = get_page_by_path( 'book-a-class' );

	if ( ! $page && $legacy && $legacy->post_status === 'publish' ) {
		wp_update_post(
			array(
				'ID'         => $legacy->ID,
				'post_name'  => 'book',
				'post_title' => 'Book a Class',
			)
		);
		update_post_meta( $legacy->ID, '_wp_page_template', $template );
		$page = get_post( $legacy->ID );
	}

	if ( ! $page ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => 'Book a Class',
				'post_name'    => 'book',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
			$page = get_post( $page_id );
		}
	}

	if ( $page ) {
		update_post_meta( $page->ID, '_wp_page_template', $template );
		if ( $page->post_title !== 'Book a Class' ) {
			wp_update_post(
				array(
					'ID'         => $page->ID,
					'post_title' => 'Book a Class',
				)
			);
		}
	}

	if ( $page && $legacy && (int) $legacy->ID !== (int) $page->ID && $legacy->post_status === 'publish' ) {
		wp_trash_post( (int) $legacy->ID );
	}

	if ( $page ) {
		flush_rewrite_rules( false );
	}
}

add_action( 'after_switch_theme', 'ath_ensure_book_page' );

add_action(
	'init',
	function () {
		if ( get_option( 'ath_book_page_setup' ) === '3' ) {
			return;
		}
		ath_ensure_book_page();
		update_option( 'ath_book_page_setup', '3' );
	},
	5
);

add_filter(
	'page_link',
	function ( $link, $post_id ) {
		if ( ath_is_book_page( $post_id ) ) {
			return ath_book_base_url();
		}
		return $link;
	},
	10,
	2
);

add_filter(
	'redirect_canonical',
	function ( $redirect_url, $requested_url ) {
		if ( is_string( $requested_url ) && preg_match( '#(/book/?)(\?|$)#', $requested_url ) ) {
			return false;
		}
		return $redirect_url;
	},
	10,
	2
);

add_action(
	'init',
	function () {
		$book   = get_page_by_path( 'book' );
		$legacy = get_page_by_path( 'book-a-class' );
		$target = $book ? 'book' : ( $legacy ? 'book-a-class' : 'book' );
		add_rewrite_rule( '^book/?$', 'index.php?pagename=' . $target, 'top' );
	},
	6
);

add_action(
	'template_redirect',
	function () {
		if ( ! is_page( 'book-a-class' ) ) {
			return;
		}

		$target = ath_book_base_url();
		if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$tab = sanitize_key( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $tab !== '' ) {
				$target = add_query_arg( 'tab', $tab, $target );
			}
		}

		wp_safe_redirect( $target, 301 );
		exit;
	},
	1
);

add_filter(
	'wp_nav_menu_objects',
	function ( $items ) {
		foreach ( $items as $item ) {
			if ( ! empty( $item->url ) && strpos( $item->url, 'book-a-class' ) !== false ) {
				$item->url = preg_replace( '#/book-a-class/?#', '/book/', $item->url );
			}
		}
		return $items;
	}
);
