<?php
/**
 * Course page helpers.
 */
function v2_course_url( $slug ) {
	return 'course.php?slug=' . rawurlencode( $slug );
}

function v2_course_by_slug( $slug ) {
	static $courses = null;
	if ( $courses === null ) {
		$courses  = require dirname( __DIR__ ) . '/data/course-pages.php';
		$galleries = require dirname( __DIR__ ) . '/data/course-galleries.php';
		foreach ( $galleries as $gallery_slug => $gallery ) {
			if ( isset( $courses[ $gallery_slug ] ) ) {
				$courses[ $gallery_slug ]['gallery'] = $gallery;
			}
		}
	}
	return isset( $courses[ $slug ] ) ? $courses[ $slug ] : null;
}
