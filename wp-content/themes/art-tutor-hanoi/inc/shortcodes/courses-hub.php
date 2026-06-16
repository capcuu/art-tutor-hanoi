<?php
/**
 * Courses hub body — pathways, benefits, CTA (for Gutenberg /courses/ page).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render courses hub body (pathways + benefits + CTA).
 */
function ath_render_courses_hub_block() {
	ob_start();
	require ATH_THEME_DIR . '/partials/courses-hub-body.php';

	return (string) ob_get_clean();
}

/**
 * Shortcode callback — [ath_courses_hub]
 */
function ath_courses_hub_shortcode() {
	return ath_render_courses_hub_block();
}

add_shortcode( 'ath_courses_hub', 'ath_courses_hub_shortcode' );
