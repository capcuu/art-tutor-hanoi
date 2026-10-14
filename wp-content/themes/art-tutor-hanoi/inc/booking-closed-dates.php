<?php
/**
 * Book page date picker: closed dates, Thursday closure, life drawing on Friday.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Studio dates that cannot be booked (Y-m-d).
 *
 * @return string[]
 */
function ath_booking_closed_dates() {
	$dates = array(
		'2026-10-17',
		'2026-12-25',
	);

	$dates = apply_filters( 'ath_booking_closed_dates', $dates );

	return array_values( array_filter( array_map( 'strval', (array) $dates ) ) );
}

/**
 * Config read by assets/js/booking-closed-dates.js.
 *
 * @return array<string, mixed>
 */
function ath_booking_closed_dates_config() {
	$config = array(
		'formIds'           => array( 39, 40, 21 ),
		'workshopFormId'    => 40,
		'lifeDrawingValue'  => 'Nude Life Drawing',
		'residencyValue'    => 'Art Residency',
		'programField'      => 'dropdown',
		'workshopField'     => 'dropdown_4',
	);

	return apply_filters( 'ath_booking_closed_dates_config', $config );
}

/**
 * Enqueue the book-page date rules.
 */
function ath_enqueue_booking_closed_dates() {
	if ( ! function_exists( 'ath_is_book_page' ) || ! ath_is_book_page() ) {
		return;
	}

	$path = ATH_THEME_DIR . '/assets/js/booking-closed-dates.js';
	$ver  = is_readable( $path ) ? (string) filemtime( $path ) : ATH_THEME_VERSION;

	wp_enqueue_script(
		'ath-booking-closed-dates',
		ATH_THEME_URI . '/assets/js/booking-closed-dates.js',
		array( 'jquery' ),
		$ver,
		true
	);

	wp_localize_script( 'ath-booking-closed-dates', 'athBookingClosedDates', ath_booking_closed_dates_config() );

	wp_add_inline_script(
		'ath-booking-closed-dates',
		'window.ATH_CLOSED_DATES=' . wp_json_encode( ath_booking_closed_dates() ) . ';',
		'before'
	);
}

add_action( 'wp_enqueue_scripts', 'ath_enqueue_booking_closed_dates', 30 );

/**
 * Fluent Forms date field "Custom config" for Nude Life Drawing is:
 *   return date.getDay() !== 6
 * which leaves only Saturday selectable. Friday is 5.
 *
 * @param string $config Raw flatpickr config from the date field.
 * @return string
 */
function ath_life_drawing_date_config_to_friday( $config ) {
	if ( ! is_string( $config ) || $config === '' ) {
		return $config;
	}

	$replacements = array(
		'date.getDay() !== 6' => 'date.getDay() !== 5',
		'date.getDay() != 6'  => 'date.getDay() != 5',
		'date.getDay() === 6' => 'date.getDay() === 5',
		'date.getDay() == 6'  => 'date.getDay() == 5',
	);

	return str_replace( array_keys( $replacements ), array_values( $replacements ), $config );
}

/**
 * Rewrite form 40's life-drawing date config before Fluent Forms prints it.
 *
 * @param object $form Form model.
 * @return object
 */
function ath_booking_form_life_drawing_friday( $form ) {
	if ( empty( $form->id ) || (int) $form->id !== 40 || empty( $form->form_fields ) || ! is_string( $form->form_fields ) ) {
		return $form;
	}

	$form->form_fields = ath_life_drawing_date_config_to_friday( $form->form_fields );

	return $form;
}

add_filter( 'fluentform/rendering_form', 'ath_booking_form_life_drawing_friday', 20 );

/**
 * Same replacement on the finished book-page HTML, in case the field config
 * is printed from a cached form object.
 *
 * @param string $html Page HTML.
 * @return string
 */
function ath_booking_page_life_drawing_friday_html( $html ) {
	if ( ! is_string( $html ) ) {
		return $html;
	}

	return ath_life_drawing_date_config_to_friday( $html );
}

add_action(
	'template_redirect',
	function () {
		if ( ! function_exists( 'ath_is_book_page' ) || ! ath_is_book_page() ) {
			return;
		}

		ob_start( 'ath_booking_page_life_drawing_friday_html' );
	},
	0
);
