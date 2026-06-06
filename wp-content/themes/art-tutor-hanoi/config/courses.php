<?php
/**
 * Course page helpers.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

function v2_course_url( $slug ) {
	return ath_course_url( $slug );
}

function v2_course_by_slug( $slug ) {
	static $courses = null;
	if ( $courses === null ) {
		$courses   = require ATH_THEME_DIR . '/data/course-pages.php';
		$galleries = require ATH_THEME_DIR . '/data/course-galleries.php';
		foreach ( $galleries as $gallery_slug => $gallery ) {
			if ( isset( $courses[ $gallery_slug ] ) ) {
				$courses[ $gallery_slug ]['gallery'] = $gallery;
			}
		}
	}
	return isset( $courses[ $slug ] ) ? $courses[ $slug ] : null;
}
