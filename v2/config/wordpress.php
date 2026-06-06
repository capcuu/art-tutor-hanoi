<?php
/**
 * Bootstrap WordPress for TablePress shortcodes in v2 templates.
 */
function v2_wp_load() {
	static $loaded = false;
	if ( $loaded ) {
		return true;
	}

	$wp_load = dirname( __DIR__, 2 ) . '/wp-load.php';
	if ( ! is_readable( $wp_load ) ) {
		return false;
	}

	define( 'WP_USE_THEMES', false );
	require_once $wp_load;

	// Auth must be ready before shortcodes that check current_user_can().
	wp_get_current_user();

	$loaded = true;

	return true;
}

/**
 * Render [art_calendar] for editors/admins only (matches Code Snippets behavior).
 */
function v2_art_calendar_html() {
	if ( ! v2_wp_load() || ! current_user_can( 'edit_pages' ) ) {
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
	if ( ! v2_wp_load() ) {
		return '';
	}

	return do_shortcode( '[table id=' . (int) $table_id . ' /]' );
}

/**
 * Fetch TablePress table HTML from the live weekly calendar page.
 *
 * @param int $table_id TablePress table ID.
 */
function v2_tablepress_table_from_live( $table_id ) {
	$table_id = (int) $table_id;
	$endpoint = 'https://arttutorhanoi.com/wp-json/wp/v2/pages?slug=weekly-calendar&_fields=content.rendered';

	if ( function_exists( 'wp_remote_get' ) ) {
		$response = wp_remote_get( $endpoint, array( 'timeout' => 15 ) );
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return '';
		}
		$json = wp_remote_retrieve_body( $response );
	} else {
		$context = stream_context_create( array( 'http' => array( 'timeout' => 15 ) ) );
		$json    = @file_get_contents( $endpoint, false, $context ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	if ( ! is_string( $json ) || $json === '' ) {
		return '';
	}

	$data = json_decode( $json, true );
	if ( ! is_array( $data ) || empty( $data[0]['content']['rendered'] ) ) {
		return '';
	}

	$html = (string) $data[0]['content']['rendered'];
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
