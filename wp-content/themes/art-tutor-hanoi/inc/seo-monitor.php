<?php
/**
 * Sprint 4 — GSC monitoring baseline, title A/B variants, perf tweaks.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * GSC watch URLs with baseline metrics (from doc/seo export).
 *
 * @return array<int, array<string, mixed>>
 */
function ath_seo_gsc_watchlist() {
	static $rows = null;

	if ( $rows === null ) {
		$path = ATH_THEME_DIR . '/data/seo-gsc-baseline.php';
		$rows = is_readable( $path ) ? require $path : array();
	}

	return is_array( $rows ) ? $rows : array();
}

/**
 * Homepage title variants for manual A/B in Rank Math.
 *
 * @return array<string, string> label => title
 */
function ath_seo_home_title_variants() {
	return array(
		'A — current (theme seed)' => 'Art Tutor Hanoi | English Art Classes & Workshops',
		'B — commercial CTR test'    => 'Book Art Classes in Hanoi | Art Tutor Hanoi',
		'C — workshops angle'        => 'Art Classes & Workshops in Hanoi | Art Tutor Hanoi',
		'D — Tay Ho local'           => 'English Art Studio in Tay Ho | Art Tutor Hanoi',
	);
}

/**
 * Homepage meta description variants.
 *
 * @return array<string, string>
 */
function ath_seo_home_description_variants() {
	return array(
		'A — current' => 'Book a trial art class in Tay Ho, Hanoi. English-speaking instructors, adult & kids courses, life drawing, silk painting workshops.',
		'B — CTA'     => 'Trial class $40. Weekly art courses & 1-day workshops in Tay Ho. English-friendly studio — book online today.',
	);
}

/**
 * Defer front-end JS; preconnect Cloudinary (already in assets.php).
 */
function ath_seo_enqueue_perf_tweaks() {
	if ( ! ath_is_v2_layout() ) {
		return;
	}

	wp_script_add_data( 'ath-main', 'strategy', 'defer' );
}

add_action( 'wp_enqueue_scripts', 'ath_seo_enqueue_perf_tweaks', 101 );

/**
 * Sprint 4 — enable perf tweaks flag (runs once on admin action).
 */
function ath_apply_seo_monitor_sprint() {
	update_option( 'ath_seo_monitor_sprint_version', '1', false );
	update_option( 'ath_seo_perf_defer_js', '1', false );

	ath_ensure_seo_page( true );

	return array(
		'updated' => array( 'defer_js', 'gsc_baseline', 'seo_playbook' ),
		'skipped' => array(),
		'errors'  => array(),
	);
}
