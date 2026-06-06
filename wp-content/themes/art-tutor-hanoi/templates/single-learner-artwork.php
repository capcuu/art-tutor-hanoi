<?php
/**
 * Single post — Learner's artworks (student profile).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

if ( ! have_posts() ) {
	ath_render_not_found(
		'Post not found',
		'We couldn\'t find that student profile.',
		ath_students_artworks_url(),
		'View all student work'
	);
}

the_post();

$student_title = ath_student_post_title();
$doc_title     = $student_title . ' — Art Tutor Hanoi';

ath_render_page(
	static function () {
		require ATH_THEME_DIR . '/partials/single-learner-artwork-content.php';
	},
	$doc_title
);
