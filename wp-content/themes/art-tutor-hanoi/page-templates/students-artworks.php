<?php
/**
 * Template Name: Students' Artworks
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$page = get_queried_object();
$title = ( $page instanceof WP_Post && $page->post_title !== '' )
	? $page->post_title . ' — Art Tutor Hanoi'
	: 'Students\' Artworks — Art Tutor Hanoi';

ath_render_page(
	static function () {
		require ATH_THEME_DIR . '/partials/students-artworks-content.php';
	},
	$title
);
