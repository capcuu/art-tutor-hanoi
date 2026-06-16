<?php
/**
 * Kids course page helpers.
 */
function v2_kids_course_url( $slug ) {
	return 'kids-course.php?slug=' . rawurlencode( $slug );
}

function v2_kids_course_by_slug( $slug ) {
	static $courses = null;
	if ( $courses === null ) {
		$courses = require dirname( __DIR__ ) . '/data/kids-course-pages.php';
	}
	return isset( $courses[ $slug ] ) ? $courses[ $slug ] : null;
}
