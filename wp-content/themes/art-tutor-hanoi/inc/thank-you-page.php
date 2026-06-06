<?php
/**
 * Thank you page — v2 layout, page setup, Fluent Forms redirects.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fluent Forms IDs that should redirect to the thank you page.
 *
 * @return int[]
 */
function ath_booking_form_ids() {
	$ids = array( 14 );

	foreach ( ath_book_tabs() as $tab ) {
		if ( ! empty( $tab['form_id'] ) ) {
			$ids[] = (int) $tab['form_id'];
		}
	}

	$ids = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );

	return apply_filters( 'ath_booking_form_ids', $ids );
}

/**
 * Map booking form ID to book tab slug (for ?from= on thank you page).
 *
 * @param int $form_id Fluent Forms ID.
 */
function ath_booking_form_source( $form_id ) {
	$form_id = (int) $form_id;

	foreach ( ath_book_tabs() as $slug => $tab ) {
		if ( ! empty( $tab['form_id'] ) && (int) $tab['form_id'] === $form_id ) {
			return $slug;
		}
	}

	if ( 14 === $form_id ) {
		return 'life-drawing';
	}

	return '';
}

/**
 * Thank you page base URL.
 */
function ath_thank_you_base_url() {
	return user_trailingslashit( home_url( '/thank-you' ) );
}

/**
 * Thank you page URL with optional source tab.
 *
 * @param string $from Book tab slug or life-drawing.
 */
function ath_thank_you_url( $from = '' ) {
	$url = ath_thank_you_base_url();

	if ( $from !== '' ) {
		$url = add_query_arg( 'from', sanitize_key( $from ), $url );
	}

	return $url;
}

/**
 * Is this the thank you page?
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_thank_you_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	if ( in_array( $post->post_name, array( 'thank-you', 'thank-you-for-booking' ), true ) ) {
		return true;
	}

	return false;
}

/**
 * Create or update the Thank You page.
 */
function ath_ensure_thank_you_page() {
	$page   = get_page_by_path( 'thank-you' );
	$legacy = get_page_by_path( 'thank-you-for-booking' );

	if ( ! $page && $legacy && $legacy->post_status === 'publish' ) {
		wp_update_post(
			array(
				'ID'         => $legacy->ID,
				'post_name'  => 'thank-you',
				'post_title' => 'Thank You',
			)
		);
		delete_post_meta( $legacy->ID, '_wp_page_template' );
		$page = get_post( $legacy->ID );
	}

	if ( ! $page ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => 'Thank You',
				'post_name'    => 'thank-you',
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
		if ( $page->post_title !== 'Thank You' ) {
			wp_update_post(
				array(
					'ID'         => $page->ID,
					'post_title' => 'Thank You',
				)
			);
		}
	}

	if ( $page && $legacy && (int) $legacy->ID !== (int) $page->ID && $legacy->post_status === 'publish' ) {
		wp_trash_post( (int) $legacy->ID );
	}
}

add_action( 'after_switch_theme', 'ath_ensure_thank_you_page' );

add_action(
	'init',
	function () {
		if ( get_option( 'ath_thank_you_page_setup' ) === '2' ) {
			return;
		}
		ath_ensure_thank_you_page();
		update_option( 'ath_thank_you_page_setup', '2' );
	},
	5
);

add_filter(
	'page_link',
	function ( $link, $post_id ) {
		if ( ath_is_thank_you_page( $post_id ) ) {
			return ath_thank_you_base_url();
		}
		return $link;
	},
	10,
	2
);

add_action(
	'template_redirect',
	function () {
		if ( ! is_page( 'thank-you-for-booking' ) ) {
			return;
		}

		$target = ath_thank_you_base_url();
		if ( isset( $_GET['from'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$from = sanitize_key( wp_unslash( $_GET['from'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $from !== '' ) {
				$target = add_query_arg( 'from', $from, $target );
			}
		}

		wp_safe_redirect( $target, 301 );
		exit;
	},
	1
);

/**
 * Redirect booking forms to the thank you page after submit.
 */
add_filter(
	'fluentform/form_submission_confirmation',
	function ( $confirmation, $form_data, $form ) {
		$form_id = isset( $form->id ) ? (int) $form->id : 0;

		if ( ! in_array( $form_id, ath_booking_form_ids(), true ) ) {
			return $confirmation;
		}

		$source = ath_booking_form_source( $form_id );

		$confirmation['redirectTo'] = 'customUrl';
		$confirmation['customUrl']  = ath_thank_you_url( $source );

		return $confirmation;
	},
	10,
	3
);

/**
 * Persist redirect URLs in Fluent Forms settings (runs once).
 */
function ath_sync_booking_form_redirects() {
	if ( ! class_exists( '\FluentForm\App\Models\FormMeta' ) ) {
		return;
	}

	foreach ( ath_booking_form_ids() as $form_id ) {
		$settings = \FluentForm\App\Models\FormMeta::retrieve( 'formSettings', $form_id );

		if ( ! is_array( $settings ) ) {
			continue;
		}

		$source = ath_booking_form_source( $form_id );
		$url    = ath_thank_you_url( $source );

		if (
			isset( $settings['confirmation']['redirectTo'], $settings['confirmation']['customUrl'] )
			&& 'customUrl' === $settings['confirmation']['redirectTo']
			&& $settings['confirmation']['customUrl'] === $url
		) {
			continue;
		}

		if ( ! isset( $settings['confirmation'] ) || ! is_array( $settings['confirmation'] ) ) {
			$settings['confirmation'] = array();
		}

		$settings['confirmation']['redirectTo']           = 'customUrl';
		$settings['confirmation']['customUrl']            = $url;
		$settings['confirmation']['samePageFormBehavior'] = 'hide_form';

		\FluentForm\App\Models\FormMeta::persist( $form_id, 'formSettings', $settings );
	}
}

add_action(
	'init',
	function () {
		if ( get_option( 'ath_booking_form_redirects_synced' ) === '1' ) {
			return;
		}
		ath_sync_booking_form_redirects();
		update_option( 'ath_booking_form_redirects_synced', '1' );
	},
	20
);
