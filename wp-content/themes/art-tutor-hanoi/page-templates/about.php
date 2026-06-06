<?php
/**
 * Template Name: About
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

ath_render_page(
	static function () {
		require ATH_THEME_DIR . '/partials/about-content.php';
	},
	'About — Art Tutor Hanoi'
);
