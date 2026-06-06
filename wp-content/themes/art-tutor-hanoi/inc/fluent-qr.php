<?php
/**
 * Enqueue shared VietQR script for Fluent Forms with offline payment.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

require_once ATH_THEME_DIR . '/config/fluent-qr.php';

/**
 * Register fluent-qr.js.
 */
function ath_register_fluent_qr_assets() {
	$path = ATH_THEME_DIR . '/assets/js/fluent-qr.js';
	$ver  = is_readable( $path ) ? (string) filemtime( $path ) : ATH_THEME_VERSION;

	wp_register_script(
		'ath-fluent-qr',
		ATH_THEME_URI . '/assets/js/fluent-qr.js',
		array( 'jquery' ),
		$ver,
		true
	);

	wp_localize_script( 'ath-fluent-qr', 'athFluentQr', ath_fluent_qr_config() );
}

add_action( 'wp_enqueue_scripts', 'ath_register_fluent_qr_assets', 20 );

/**
 * Enqueue when a VietQR-enabled form renders.
 *
 * @param object $form Fluent Forms model.
 */
function ath_enqueue_fluent_qr_assets( $form ) {
	if ( ! $form || empty( $form->id ) ) {
		return;
	}

	if ( ! in_array( (int) $form->id, ath_fluent_qr_form_ids(), true ) ) {
		return;
	}

	wp_enqueue_script( 'ath-fluent-qr' );
}

add_action( 'fluentform/before_form_render', 'ath_enqueue_fluent_qr_assets', 10, 1 );
