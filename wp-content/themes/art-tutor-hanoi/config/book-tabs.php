<?php
/**
 * Book a Class — tab definitions and Fluent Forms IDs.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fluent Form IDs — set after creating forms in WP Admin → Fluent Forms → All Forms.
 * Override in functions.php via ath_adult_booking_form_id / ath_kids_booking_form_id filters.
 */
if ( ! defined( 'ATH_ADULT_BOOKING_FORM_ID' ) ) {
	define( 'ATH_ADULT_BOOKING_FORM_ID', 40 );
}
if ( ! defined( 'ATH_KIDS_BOOKING_FORM_ID' ) ) {
	define( 'ATH_KIDS_BOOKING_FORM_ID', 21 );
}

/**
 * Legacy book tab slugs mapped to the current two-tab layout.
 *
 * @return array<string, string>
 */
function ath_book_tab_legacy_map() {
	return array(
		'trial'      => 'adult',
		'workshops'  => 'adult',
		'adults'     => 'adult',
		'residency'  => 'adult',
	);
}

/**
 * @return array<string, array{
 *   label: string,
 *   form_id: int|null,
 *   intro: string
 * }>
 */
function ath_book_tabs() {
	$adult_form_id = (int) apply_filters( 'ath_adult_booking_form_id', ATH_ADULT_BOOKING_FORM_ID );
	$kids_form_id  = (int) apply_filters( 'ath_kids_booking_form_id', ATH_KIDS_BOOKING_FORM_ID );

	$tabs = array(
		'adult' => array(
			'label'   => 'Adult',
			'form_id' => $adult_form_id,
			'intro'   => 'Book workshops, trial classes, weekly courses, or artist residency for adults.',
		),
		'kids'  => array(
			'label'   => 'Kids',
			'form_id' => $kids_form_id,
			'intro'   => 'Enrol children aged 5–12 in drawing or contemporary art programs.',
		),
	);

	return apply_filters( 'ath_book_tabs', $tabs );
}

/**
 * Active tab from ?tab= query string.
 */
function ath_book_active_tab() {
	$tabs   = ath_book_tabs();
	$legacy = ath_book_tab_legacy_map();
	$tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'adult'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( isset( $legacy[ $tab ] ) ) {
		$tab = $legacy[ $tab ];
	}

	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'adult';
	}

	return $tab;
}

/**
 * Book page URL with optional tab.
 *
 * @param string $tab Tab slug.
 */
function ath_book_url( $tab = 'adult' ) {
	$tabs   = ath_book_tabs();
	$legacy = ath_book_tab_legacy_map();

	if ( isset( $legacy[ $tab ] ) ) {
		$tab = $legacy[ $tab ];
	}

	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'adult';
	}

	return add_query_arg( 'tab', $tab, ath_book_base_url() );
}

/**
 * Render Fluent Forms embed.
 *
 * @param int $form_id Form ID.
 */
function ath_render_fluent_form( $form_id ) {
	$form_id = (int) $form_id;

	if ( $form_id <= 0 ) {
		ath_render_book_form_fallback();
		return;
	}

	if ( shortcode_exists( 'fluentform' ) ) {
		echo do_shortcode( '[fluentform id="' . $form_id . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	ath_render_book_form_fallback();
}

/**
 * Fallback when Fluent Forms is unavailable.
 */
function ath_render_book_form_fallback() {
	echo '<p class="book-form-section__fallback">';
	echo esc_html__( 'Booking form is not available.', 'art-tutor-hanoi' );
	echo ' ';
	echo '<a href="tel:+84988288302">' . esc_html__( 'Call (+84) 98 828 8302', 'art-tutor-hanoi' ) . '</a>';
	echo ' ' . esc_html__( 'or', 'art-tutor-hanoi' ) . ' ';
	echo '<a href="mailto:contact@arttutorhanoi.com">contact@arttutorhanoi.com</a>.';
	echo '</p>';
}
