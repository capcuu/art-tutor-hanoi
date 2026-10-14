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
