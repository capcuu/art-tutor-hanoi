<?php
/**
 * Custom rewrite rules for kids course detail pages (adult courses use WP pages).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		add_rewrite_tag( '%ath_kids_course%', '([^&]+)' );

		add_rewrite_rule(
			'^kids-courses/([^/]+)/?$',
			'index.php?ath_kids_course=$matches[1]',
			'top'
		);
	}
);

add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'ath_kids_course';
		return $vars;
	}
);

add_filter(
	'template_include',
	function ( $template ) {
		$kids = get_query_var( 'ath_kids_course' );
		if ( $kids ) {
			$custom = ATH_THEME_DIR . '/templates/course-detail.php';
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
