<?php
/**
 * WordPress shortcode helpers for calendar page.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render [art_calendar] for editors/admins only (matches Code Snippets behavior).
 */
function v2_art_calendar_html() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return '';
	}

	return v2_prepare_art_calendar_html( do_shortcode( '[art_calendar]' ) );
}

/**
 * Sanitize admin calendar embed — keep fetch script, use v2 CSS for table styles.
 *
 * @param string $html Shortcode output HTML.
 */
function v2_prepare_art_calendar_html( $html ) {
	if ( $html === '' ) {
		return '';
	}

	$html = preg_replace( '#<style\b[^>]*>.*?</style>#is', '', $html );
	$html = preg_replace( '#<link\b[^>]*>#i', '', $html );
	$html = preg_replace( '#<!--.*?-->#s', '', $html );

	return trim( $html );
}

/**
 * Render a TablePress table by ID.
 *
 * @param int $table_id TablePress table ID.
 */
function v2_tablepress_table( $table_id ) {
	return do_shortcode( '[table id=' . (int) $table_id . ' /]' );
}

/**
 * Fetch TablePress table HTML from the weekly calendar page via REST.
 *
 * @param int $table_id TablePress table ID.
 */
function v2_tablepress_table_from_live( $table_id ) {
	$table_id = (int) $table_id;

	$page = get_page_by_path( ath_calendar_page_slug() );
	if ( $page ) {
		$html    = apply_filters( 'the_content', $page->post_content );
		$pattern = '#<table[^>]*\btablepress-id-' . $table_id . '\b[^>]*>.*?</table>#is';
		if ( preg_match( $pattern, $html, $match ) ) {
			return $match[0];
		}
	}

	$endpoint = rest_url( 'wp/v2/pages?slug=' . rawurlencode( ath_calendar_page_slug() ) . '&_fields=content.rendered' );
	$response = wp_remote_get( $endpoint, array( 'timeout' => 15 ) );
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
		return '';
	}

	$json = wp_remote_retrieve_body( $response );
	$data = json_decode( $json, true );
	if ( ! is_array( $data ) || empty( $data[0]['content']['rendered'] ) ) {
		return '';
	}

	$html    = (string) $data[0]['content']['rendered'];
	$pattern = '#<table[^>]*\btablepress-id-' . $table_id . '\b[^>]*>.*?</table>#is';
	if ( ! preg_match( $pattern, $html, $match ) ) {
		return '';
	}

	return $match[0];
}

/**
 * Sanitize embedded table HTML for v2 (no scripts/styles; v2-scoped markup).
 *
 * @param string $html Table HTML.
 */
function v2_prepare_tablepress_html( $html ) {
	if ( $html === '' ) {
		return '';
	}

	$html = preg_replace( '#<style\b[^>]*>.*?</style>#is', '', $html );
	$html = preg_replace( '#<script\b[^>]*>.*?</script>#is', '', $html );
	$html = preg_replace( '#<link\b[^>]*>#i', '', $html );
	$html = preg_replace( '#<!--.*?-->#s', '', $html );
	$html = preg_replace( '#\sstyle="[^"]*"#i', '', $html );
	$html = preg_replace( '#<thead>\s*<tr>\s*(?:<td>\s*</td>\s*){1,6}\s*</tr>\s*</thead>#is', '', $html );
	$html = preg_replace( '#<tr>\s*(?:<td>\s*</td>\s*){1,6}\s*</tr>#is', '', $html );
	$html = preg_replace( '#<caption[^>]*>.*?</caption>#is', '', $html );

	if ( ! preg_match( '#<table\b#i', $html ) ) {
		return '';
	}

	if ( strpos( $html, 'calendar-schedule__table-wrap' ) === false ) {
		$html = preg_replace(
			'/<table\b/i',
			'<div class="calendar-schedule__table-wrap"><table class="calendar-schedule__table"',
			$html,
			1
		);
		$html = preg_replace( '/<\/table>/i', '</table></div>', $html, 1 );
	}

	return trim( $html );
}
