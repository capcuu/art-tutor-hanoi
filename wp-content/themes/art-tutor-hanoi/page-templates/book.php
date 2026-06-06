<?php
/**
 * Template Name: Book a Class
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

ath_render_page(
	static function () {
		require ATH_THEME_DIR . '/partials/book-content.php';
	},
	'Book a Class — Art Tutor Hanoi'
);
