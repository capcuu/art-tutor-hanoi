<?php
/**
 * VietQR config per Fluent Form (forms with #qr_amount / #qr_img).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

define( 'ATH_QR_LOGIC_VERSION', '20260610c' );

/**
 * Default VietQR field map for adult booking forms.
 *
 * @return array<string, mixed>
 */
function ath_fluent_qr_default_adult_form_config() {
	return array(
		'baseNote'   => 'AdultBooking',
		'amountMode' => 'usd_total',
		'nameFields' => array( 'first_name' ),
	);
}

/**
 * Default VietQR field map for kids booking forms.
 *
 * @return array<string, mixed>
 */
function ath_fluent_qr_default_kids_form_config() {
	return array(
		'baseNote'   => 'KidsClass',
		'amountMode' => 'usd_total',
		'nameFields' => array( 'names[first_name]', 'subject', 'input_text' ),
	);
}

/**
 * @return array<string, mixed>
 */
function ath_fluent_qr_config() {
	$adult_form_id = defined( 'ATH_ADULT_BOOKING_FORM_ID' ) ? (int) ATH_ADULT_BOOKING_FORM_ID : 40;
	$kids_form_id  = defined( 'ATH_KIDS_BOOKING_FORM_ID' ) ? (int) ATH_KIDS_BOOKING_FORM_ID : 21;

	$config = array(
		'bankImage'     => 'https://img.vietqr.io/image/VCB-9971263202-compact.png',
		'accountName'   => 'Hoang Minh Anh',
		'accountNumber' => '9971263202',
		'bankName'      => 'VIETCOMBANK',
		'usd2vnd'       => 26500,
		'forms'         => array(
			$adult_form_id => array(
				'baseNote'   => 'AdultBooking',
				'amountMode' => 'usd_total',
				'nameFields' => array( 'first_name' ),
			),
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
			$kids_form_id => array(
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

	if ( function_exists( 'ath_book_tabs' ) ) {
		foreach ( ath_book_tabs() as $slug => $tab ) {
			$tab_form_id = (int) ( $tab['form_id'] ?? 0 );
			if ( ! $tab_form_id ) {
				continue;
			}
			$config['forms'][ $tab_form_id ] = 'kids' === $slug
				? ath_fluent_qr_default_kids_form_config()
				: ath_fluent_qr_default_adult_form_config();
		}
	}

	foreach ( array( 39, 40 ) as $adult_form_id ) {
		if ( ! isset( $config['forms'][ $adult_form_id ] ) ) {
			$config['forms'][ $adult_form_id ] = ath_fluent_qr_default_adult_form_config();
		}
	}

	return apply_filters( 'ath_fluent_qr_config', $config );
}

/**
 * Resolve QR config for a submission (handles local/server form ID mismatch).
 *
 * @param int                  $form_id  Fluent Form ID.
 * @param array<string, mixed> $response Submission response.
 * @param string               $source   Book tab slug from ?from= (adult|kids).
 */
function ath_fluent_qr_form_config_for_submission( $form_id, array $response, $source = '' ) {
	$form_cfg = ath_fluent_qr_form_config( $form_id );

	if ( $form_cfg ) {
		return $form_cfg;
	}

	if ( function_exists( 'ath_book_tabs' ) ) {
		foreach ( ath_book_tabs() as $slug => $tab ) {
			if ( (int) ( $tab['form_id'] ?? 0 ) === (int) $form_id ) {
				return 'kids' === $slug
					? ath_fluent_qr_default_kids_form_config()
					: ath_fluent_qr_default_adult_form_config();
			}
		}
	}

	if ( 'kids' === $source ) {
		return ath_fluent_qr_default_kids_form_config();
	}

	if ( 'adult' === $source || isset( $response['first_name'] ) ) {
		return ath_fluent_qr_default_adult_form_config();
	}

	if ( isset( $response['names'] ) && is_array( $response['names'] ) ) {
		return ath_fluent_qr_default_kids_form_config();
	}

	return null;
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

/**
 * Fluent Forms offline payment method values that use bank QR (e.g. "Bank QR code").
 *
 * @return string[]
 */
function ath_fluent_qr_offline_payment_methods() {
	return apply_filters( 'ath_fluent_qr_offline_payment_methods', array( 'test' ) );
}

/**
 * Form config for a Fluent Form ID, if VietQR-enabled.
 *
 * @param int $form_id Form ID.
 */
function ath_fluent_qr_form_config( $form_id ) {
	$config = ath_fluent_qr_config();
	$form_id = (int) $form_id;

	return isset( $config['forms'][ $form_id ] ) ? $config['forms'][ $form_id ] : null;
}

/**
 * Sanitize transfer note (matches fluent-qr.js).
 *
 * @param string $text Raw text.
 */
function ath_fluent_qr_sanitize_add_info( $text ) {
	$text = remove_accents( (string) $text );
	$text = preg_replace( '/[^a-zA-Z0-9 \-_.]/', '', $text );
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );

	return substr( $text, 0, 60 );
}

/**
 * Build VietQR image URL.
 *
 * @param int    $amount_vnd Amount in VND.
 * @param string $add_info   Transfer description.
 */
function ath_fluent_qr_build_image_url( $amount_vnd, $add_info ) {
	$config = ath_fluent_qr_config();
	$url    = wp_parse_url( $config['bankImage'] );

	if ( empty( $url['scheme'] ) || empty( $url['host'] ) ) {
		return '';
	}

	$base = $url['scheme'] . '://' . $url['host'] . ( $url['path'] ?? '' );
	$args = array(
		'amount'      => max( 0, (int) $amount_vnd ),
		'addInfo'       => ath_fluent_qr_sanitize_add_info( $add_info ),
		'accountName'   => $config['accountName'],
	);

	return add_query_arg( $args, $base );
}

/**
 * Read a field from decoded submission response (supports names[first_name] keys).
 *
 * @param array<string, mixed> $response   Submission response.
 * @param string               $field_name Field name.
 */
function ath_fluent_qr_response_value( array $response, $field_name ) {
	if ( isset( $response[ $field_name ] ) && ! is_array( $response[ $field_name ] ) ) {
		return (string) $response[ $field_name ];
	}

	if ( preg_match( '/^(.+)\[(.+)\]$/', $field_name, $matches ) ) {
		$group = $matches[1];
		$key   = $matches[2];

		if ( isset( $response[ $group ] ) && is_array( $response[ $group ] ) && isset( $response[ $group ][ $key ] ) ) {
			return (string) $response[ $group ][ $key ];
		}
	}

	return '';
}

/**
 * Extract payer name from submission for QR transfer note.
 *
 * @param array<string, mixed> $response Submission response.
 * @param array<string, mixed> $form_cfg Form QR config.
 */
function ath_fluent_qr_name_from_response( array $response, array $form_cfg ) {
	$fields = $form_cfg['nameFields'] ?? array();

	foreach ( $fields as $field_name ) {
		$value = trim( ath_fluent_qr_response_value( $response, $field_name ) );
		if ( $value !== '' ) {
			return $value;
		}
	}

	return '';
}

/**
 * Load a Fluent Forms submission (model or DB fallback).
 *
 * @param int $entry_id Submission ID.
 */
function ath_fluent_get_submission( $entry_id ) {
	$entry_id = (int) $entry_id;

	if ( $entry_id <= 0 ) {
		return null;
	}

	if ( class_exists( '\FluentForm\App\Models\Submission' ) ) {
		$row = \FluentForm\App\Models\Submission::find( $entry_id );
		if ( $row ) {
			return $row;
		}
	}

	global $wpdb;

	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}fluentform_submissions WHERE id = %d",
			$entry_id
		)
	);
}

/**
 * Parse USD from numeric values or labels like "Trial Class ($20)".
 *
 * @param mixed $value Raw field value.
 */
function ath_fluent_qr_parse_usd_value( $value ) {
	if ( is_numeric( $value ) ) {
		return (float) $value;
	}

	$text = trim( (string) $value );
	if ( $text === '' ) {
		return 0.0;
	}

	if ( preg_match( '/\(\s*\$?\s*(\d+(?:\.\d+)?)\s*\)/', $text, $matches ) ) {
		return (float) $matches[1];
	}

	return 0.0;
}

/**
 * Sum USD using Fluent Form select calc_value (dropdown stores label text, not 20).
 *
 * @param int                  $form_id  Fluent Form ID.
 * @param array<string, mixed> $response Submission response.
 */
function ath_fluent_qr_usd_from_form_field_calc( $form_id, array $response ) {
	$form_id = (int) $form_id;
	if ( $form_id <= 0 ) {
		return 0.0;
	}

	$form = null;
	if ( class_exists( '\FluentForm\App\Models\Form' ) ) {
		$form = \FluentForm\App\Models\Form::find( $form_id );
	}

	if ( ! $form || empty( $form->form_fields ) ) {
		return 0.0;
	}

	$fields = json_decode( (string) $form->form_fields, true );
	if ( ! is_array( $fields ) ) {
		return 0.0;
	}

	$usd          = 0.0;
	$select_names = array( 'dropdown', 'dropdown_1', 'dropdown_2', 'dropdown_3' );

	foreach ( $fields as $field ) {
		if ( ( $field['element'] ?? '' ) !== 'select' ) {
			continue;
		}

		$name = (string) ( $field['attributes']['name'] ?? '' );
		if ( ! in_array( $name, $select_names, true ) || ! isset( $response[ $name ] ) ) {
			continue;
		}

		$selected = trim( (string) $response[ $name ] );
		if ( $selected === '' ) {
			continue;
		}

		$matched = false;
		foreach ( $field['settings']['advanced_options'] ?? array() as $option ) {
			$value = trim( (string) ( $option['value'] ?? '' ) );
			$label = trim( (string) ( $option['label'] ?? '' ) );
			if ( $selected === $value || $selected === $label ) {
				$usd    += (float) ( $option['calc_value'] ?? 0 );
				$matched = true;
				break;
			}
		}

		if ( ! $matched ) {
			$usd += ath_fluent_qr_parse_usd_value( $selected );
		}
	}

	return $usd;
}

/**
 * USD total from a Fluent Forms submission.
 *
 * @param object               $submission Submission row.
 * @param array<string, mixed> $response   Decoded response.
 */
function ath_fluent_qr_usd_from_submission( $submission, array $response ) {
	$form_id = isset( $submission->form_id ) ? (int) $submission->form_id : 0;

	if ( isset( $response['payment_input'] ) ) {
		$parsed = ath_fluent_qr_parse_usd_value( $response['payment_input'] );
		if ( $parsed > 0 ) {
			return $parsed;
		}
	}

	$form_calc = ath_fluent_qr_usd_from_form_field_calc( $form_id, $response );
	if ( $form_calc > 0 ) {
		return $form_calc;
	}

	$total = isset( $submission->payment_total ) ? (float) $submission->payment_total : 0.0;

	if ( $total <= 0 && ! empty( $response['payment_total'] ) && is_numeric( $response['payment_total'] ) ) {
		$total = (float) $response['payment_total'];
	}

	if ( $total > 0 ) {
		$currency = isset( $submission->currency ) ? strtoupper( (string) $submission->currency ) : 'USD';
		if ( 'USD' === $currency ) {
			return $total >= 100 ? $total / 100 : $total;
		}
		return $total;
	}

	return 0.0;
}

/**
 * VND amount for a submission using form QR rules.
 *
 * @param object               $submission Submission row.
 * @param array<string, mixed> $response   Decoded response.
 * @param array<string, mixed> $form_cfg   Form QR config.
 */
function ath_fluent_qr_vnd_from_submission( $submission, array $response, array $form_cfg ) {
	$mode = $form_cfg['amountMode'] ?? 'usd_total';

	if ( 'booking_weekday' === $mode ) {
		$amounts = $form_cfg['amounts'] ?? array();
		$weekday = ath_fluent_qr_response_value( $response, 'booking_weekday' );

		if ( $weekday && isset( $amounts[ $weekday ] ) ) {
			return (int) $amounts[ $weekday ];
		}

		return isset( $amounts['default'] ) ? (int) $amounts['default'] : 0;
	}

	$config = ath_fluent_qr_config();
	$usd    = ath_fluent_qr_usd_from_submission( $submission, $response );

	return $usd > 0 ? (int) round( $usd * (int) $config['usd2vnd'] ) : 0;
}

/**
 * Whether submission used offline bank QR payment.
 *
 * @param object               $submission Submission row.
 * @param array<string, mixed> $response   Decoded response.
 */
function ath_fluent_qr_submission_is_offline_bank( $submission, array $response ) {
	$offline = ath_fluent_qr_offline_payment_methods();
	$method  = '';

	if ( ! empty( $submission->payment_method ) ) {
		$method = (string) $submission->payment_method;
	} elseif ( ! empty( $response['payment_method'] ) ) {
		$method = (string) $response['payment_method'];
	}

	if ( ! in_array( $method, $offline, true ) ) {
		return false;
	}

	if ( ! empty( $submission->payment_status ) && 'paid' === $submission->payment_status ) {
		return false;
	}

	return true;
}

/**
 * VietQR display data for a Fluent Forms entry, or null when not applicable.
 *
 * @param int    $entry_id Submission ID.
 * @param string $source   Optional book tab slug from ?from= (adult|kids).
 */
function ath_fluent_qr_thank_you_data( $entry_id, $source = '' ) {
	$entry_id = (int) $entry_id;
	$source   = sanitize_key( $source );

	if ( $entry_id <= 0 ) {
		return null;
	}

	$submission = ath_fluent_get_submission( $entry_id );

	if ( ! $submission || empty( $submission->form_id ) ) {
		return null;
	}

	$form_id = (int) $submission->form_id;

	$response = json_decode( (string) $submission->response, true );

	if ( ! is_array( $response ) ) {
		$response = array();
	}

	if ( ! ath_fluent_qr_submission_is_offline_bank( $submission, $response ) ) {
		// Pending bank transfer may not have payment_method stored yet.
		$method = strtolower( (string) ( $submission->payment_method ?? ( $response['payment_method'] ?? '' ) ) );
		$online = array( 'stripe', 'paypal', 'mollie', 'razorpay' );
		if ( in_array( $method, $online, true ) ) {
			return null;
		}
	}

	$form_cfg = ath_fluent_qr_form_config_for_submission( $form_id, $response, $source );

	if ( ! $form_cfg ) {
		return null;
	}

	$amount_vnd = ath_fluent_qr_vnd_from_submission( $submission, $response, $form_cfg );

	if ( $amount_vnd <= 0 ) {
		return null;
	}

	$name     = ath_fluent_qr_name_from_response( $response, $form_cfg );
	$base     = $form_cfg['baseNote'] ?? 'Booking';
	$add_info = $name ? $base . ' - ' . $name : $base;
	$config   = ath_fluent_qr_config();

	return array(
		'entry_id'       => $entry_id,
		'form_id'        => $form_id,
		'amount_vnd'     => $amount_vnd,
		'amount_label'   => number_format_i18n( $amount_vnd ) . ' VND',
		'add_info'       => ath_fluent_qr_sanitize_add_info( $add_info ),
		'qr_image_url'   => ath_fluent_qr_build_image_url( $amount_vnd, $add_info ),
		'bank_name'      => $config['bankName'] ?? 'VIETCOMBANK',
		'account_number' => $config['accountNumber'] ?? '',
		'account_name'   => $config['accountName'] ?? '',
	);
}
