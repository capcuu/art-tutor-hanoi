<?php
/**
 * VietQR config per Fluent Form (forms with #qr_amount / #qr_img).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, mixed>
 */
function ath_fluent_qr_config() {
	$config = array(
		'bankImage'   => 'https://img.vietqr.io/image/VCB-9971263202-compact.png',
		'accountName' => 'Hoang Minh Anh',
		'usd2vnd'     => 26250,
		'forms'       => array(
			13 => array(
				'baseNote'   => 'TrialClass',
				'amountMode' => 'usd_total',
				'nameFields' => array( 'input_text' ),
			),
			14 => array(
				'baseNote'   => 'LifeDrawingModel',
				'amountMode' => 'booking_weekday',
				'amounts'    => array(
					'Friday'   => 850000,
					'Saturday' => 850000,
					'default'  => 750000,
				),
				'nameFields' => array( 'input_text' ),
			),
			21 => array(
				'baseNote'   => 'KidsClass',
				'amountMode' => 'usd_total',
				'nameFields' => array( 'names[first_name]', 'subject', 'input_text' ),
			),
			33 => array(
				'baseNote'   => 'ArtResidency',
				'amountMode' => 'usd_total',
				'nameFields' => array( 'input_text' ),
			),
			35 => array(
				'baseNote'   => 'AdultClass',
				'amountMode' => 'usd_total',
				'nameFields' => array( 'input_text' ),
			),
		),
	);

	return apply_filters( 'ath_fluent_qr_config', $config );
}

/**
 * Fluent Form IDs that use VietQR offline payment.
 *
 * @return int[]
 */
function ath_fluent_qr_form_ids() {
	$config = ath_fluent_qr_config();
	return array_map( 'intval', array_keys( $config['forms'] ) );
}
