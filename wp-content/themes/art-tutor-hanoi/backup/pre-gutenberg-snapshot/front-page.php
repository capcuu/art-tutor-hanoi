<?php
/**
 * Homepage template.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

ath_render_page(
	static function () {
		require ATH_THEME_DIR . '/partials/home-content.php';
	},
	'Art Tutor Hanoi'
);
