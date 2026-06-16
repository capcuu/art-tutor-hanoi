<?php
/**
 * Pricing page accordion — [ath_pricing] shortcode.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render pricing accordion markup.
 */
function ath_render_pricing_accordion_block() {
	ob_start();
	require ATH_THEME_DIR . '/partials/pricing-accordion.php';

	return (string) ob_get_clean();
}

/**
 * Shortcode callback — [ath_pricing]
 */
function ath_pricing_shortcode() {
	return ath_render_pricing_accordion_block();
}

add_shortcode( 'ath_pricing', 'ath_pricing_shortcode' );
