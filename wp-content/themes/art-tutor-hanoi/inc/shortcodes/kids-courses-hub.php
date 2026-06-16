<?php
/**
 * Kids courses hub body — pathways, schedule, CTA (for Gutenberg /kids-courses/ page).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render kids courses pathways accordion (schedule/pricing from editor blocks).
 */
function ath_render_kids_courses_hub_block() {
	ob_start();
	require ATH_THEME_DIR . '/partials/kids-courses-hub-pathways.php';

	return (string) ob_get_clean();
}

/**
 * Shortcode callback — [ath_kids_courses_hub]
 */
function ath_kids_courses_hub_shortcode() {
	return ath_render_kids_courses_hub_block();
}

add_shortcode( 'ath_kids_courses_hub', 'ath_kids_courses_hub_shortcode' );
