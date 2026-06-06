<?php
/**
 * Kids course page helpers.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

function v2_kids_course_url( $slug ) {
	return ath_kids_course_url( $slug );
}

function v2_kids_course_by_slug( $slug ) {
	static $courses = null;
	if ( $courses === null ) {
		$courses = require ATH_THEME_DIR . '/data/kids-course-pages.php';
	}
	return isset( $courses[ $slug ] ) ? $courses[ $slug ] : null;
}
