<?php
/**
 * Custom rewrite rules for course / workshop detail pages.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		add_rewrite_tag( '%ath_course%', '([^&]+)' );
		add_rewrite_tag( '%ath_kids_course%', '([^&]+)' );
		add_rewrite_tag( '%ath_experience%', '([^&]+)' );

		add_rewrite_rule(
			'^courses/([^/]+)/?$',
			'index.php?ath_course=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^kids-courses/([^/]+)/?$',
			'index.php?ath_kids_course=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^workshops/([^/]+)/?$',
			'index.php?ath_experience=$matches[1]',
			'top'
		);
	}
);

add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'ath_course';
		$vars[] = 'ath_kids_course';
		$vars[] = 'ath_experience';
		return $vars;
	}
);

add_filter(
	'template_include',
	function ( $template ) {
		$course = get_query_var( 'ath_course' );
		if ( $course ) {
			$custom = ATH_THEME_DIR . '/templates/course-detail.php';
			if ( is_readable( $custom ) ) {
				return $custom;
			}
		}

		$kids = get_query_var( 'ath_kids_course' );
		if ( $kids ) {
			$custom = ATH_THEME_DIR . '/templates/kids-course-detail.php';
			if ( is_readable( $custom ) ) {
				return $custom;
			}
		}

		$experience = get_query_var( 'ath_experience' );
		if ( $experience ) {
			$custom = ATH_THEME_DIR . '/templates/experience-detail.php';
			if ( is_readable( $custom ) ) {
				return $custom;
			}
		}

		return $template;
	}
);

add_action(
	'after_switch_theme',
	function () {
		flush_rewrite_rules();
	}
);
