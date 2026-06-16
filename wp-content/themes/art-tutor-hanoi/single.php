<?php
/**
 * Posts — single article and all post list views (v2 layout).
 *
 * Learner artwork singles use templates/single-learner-artwork.php instead.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$doc_title = 'Art Tutor Hanoi';

if ( is_singular( 'post' ) ) {
	if ( ! have_posts() ) {
		ath_render_not_found(
			'Post not found',
			'We couldn\'t find that article.',
			home_url( '/' ),
			'Back to home'
		);
	}

	the_post();
	$doc_title = get_the_title() . ' — Art Tutor Hanoi';
} else {
	$doc_title = ath_post_archive_doc_title();
}

ath_render_page(
	static function () {
		require ATH_THEME_DIR . '/partials/post-content.php';
	},
	$doc_title
);
