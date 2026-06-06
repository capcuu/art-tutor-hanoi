<?php
/**
 * Book a Class — tab definitions and Fluent Forms IDs.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, array{
 *   label: string,
 *   form_id: int|null,
 *   intro: string,
 *   workshops?: array<int, array{title: string, desc: string, url: string}>
 * }>
 */
function ath_book_tabs() {
	$tabs = array(
		'trial' => array(
			'label'   => 'Trial Class',
			'form_id' => 13,
			'intro'   => 'Try a guided 2-hour session for complete beginners. Personal feedback from instructors trained at Vietnam University of Fine Arts.',
		),
		'workshops' => array(
			'label'   => 'Workshops',
			'form_id' => null,
			'intro'   => 'One-off studio experiences — life drawing with a live model, silk painting, and seasonal workshops.',
			'workshops' => array(
				array(
					'title' => 'Life Drawing',
					'desc'  => 'Draw from a live model with guided support. Saturday afternoons in our Tay Ho studio.',
					'url'   => ath_experience_url( 'life-drawing' ),
				),
				array(
					'title' => 'Silk Painting',
					'desc'  => 'Advanced silk painting workshop with Dr. Le Xuan Dzung.',
					'url'   => ath_experience_url( 'silk-painting' ),
				),
			),
		),
		'adults' => array(
			'label'   => 'Adult Classes',
			'form_id' => 35,
			'intro'   => 'Register for fine-art programs — pencil, charcoal, colour, oil painting, and structured courses for adults.',
		),
		'kids' => array(
			'label'   => 'Kids Classes',
			'form_id' => 21,
			'intro'   => 'Enrol children aged 5–12 in drawing or contemporary art programs.',
		),
		'residency' => array(
			'label'   => 'Art Residency',
			'form_id' => 33,
			'intro'   => 'Reserve independent studio time for 1–4 weeks. Work at your own pace with optional guidance.',
		),
	);

	return apply_filters( 'ath_book_tabs', $tabs );
}

/**
 * Active tab from ?tab= query string.
 */
function ath_book_active_tab() {
	$tabs = ath_book_tabs();
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'trial'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'trial';
	}

	return $tab;
}

/**
 * Book page URL with optional tab.
 *
 * @param string $tab Tab slug.
 */
function ath_book_url( $tab = 'trial' ) {
	$tabs = ath_book_tabs();
	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'trial';
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
