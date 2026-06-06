<?php
/**
 * Adult course detail — /courses/{slug}/
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$slug   = sanitize_title( (string) get_query_var( 'ath_course' ) );
$course = v2_course_by_slug( $slug );

if ( ! $course ) {
	ath_render_not_found(
		'Course not found',
		'We couldn\'t find that course. Browse our learning pathways to see all programs.',
		ath_page_url( 'courses' ),
		'View all courses'
	);
}

ath_render_page(
	static function () use ( $course ) {
		require ATH_THEME_DIR . '/partials/course-content.php';
	},
	$course['title'] . ' — Art Tutor Hanoi'
);
