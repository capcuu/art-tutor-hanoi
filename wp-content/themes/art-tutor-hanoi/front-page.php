<?php
/**
 * Homepage template.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

ath_render_page(
	static function () {
		if ( have_posts() ) {
			the_post();

			if ( ath_should_use_home_gutenberg() ) {
				the_content();
				return;
			}
		}

		require ATH_THEME_DIR . '/partials/home-content.php';
	},
	'Art Tutor Hanoi'
);
