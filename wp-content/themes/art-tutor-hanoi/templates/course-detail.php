<?php
/**
 * Kids course detail — /kids-courses/{slug}/ (adult courses use WP pages).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$kids_slug = sanitize_title( (string) get_query_var( 'ath_kids_course' ) );
$course    = v2_kids_course_by_slug( $kids_slug );

if ( ! $course ) {
	ath_render_not_found(
		'Course not found',
		'We couldn\'t find that course. Browse our kids programs to see what\'s available.',
		ath_page_url( 'kids-courses' ),
		'View kids courses'
	);
}

ath_render_page(
	static function () use ( $course ) {
		require ATH_THEME_DIR . '/partials/course-content.php';
	},
	$course['title'] . ' — Art Tutor Hanoi'
);
