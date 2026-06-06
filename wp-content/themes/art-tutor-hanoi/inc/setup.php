<?php
/**
 * Theme setup — page templates and activation helpers.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
	}
);

/**
 * Create core pages on theme activation (idempotent).
 */
function ath_seed_pages() {
	$templated = array(
		'about'             => array( 'About', 'page-templates/about.php' ),
		'students-artworks' => array( 'Students\' Artworks', 'page-templates/students-artworks.php' ),
	);

	$plain = array(
		'courses'      => 'Courses',
		'kids-courses' => 'Kids Courses',
		'pricing'      => 'Programs & Pricing',
		'weekly-calendar' => 'Weekly Calendar',
		'thank-you'    => 'Thank You',
	);

	foreach ( $templated as $slug => $meta ) {
		list( $title, $template ) = $meta;

		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			if ( get_page_template_slug( $existing->ID ) !== $template ) {
				update_post_meta( $existing->ID, '_wp_page_template', $template );
			}
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
		}
	}

	foreach ( $plain as $slug => $title ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);
	}

	ath_ensure_book_page();
	ath_ensure_thank_you_page();
	ath_ensure_students_artworks_page();
	ath_ensure_links_page();
	ath_ensure_generic_v2_pages();
}

add_action( 'after_switch_theme', 'ath_seed_pages' );
