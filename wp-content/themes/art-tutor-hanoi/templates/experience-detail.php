<?php
/**
 * Workshop / experience detail — /workshops/{slug}/
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$slug       = sanitize_title( (string) get_query_var( 'ath_experience' ) );
$experience = v2_experience_by_slug( $slug );

if ( ! $experience ) {
	ath_render_not_found(
		'Page not found',
		'Browse our workshops and experiences to find your next session.',
		ath_page_url( 'pricing' ),
		'View programs'
	);
}

ath_render_page(
	static function () use ( $experience ) {
		require ATH_THEME_DIR . '/partials/experience-content.php';
	},
	$experience['title'] . ' — Art Tutor Hanoi'
);
