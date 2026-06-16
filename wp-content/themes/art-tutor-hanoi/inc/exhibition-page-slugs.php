<?php
/**
 * Exhibition artist page slugs — normalize l2026exhibition_* → 2026exhibition_*.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * DB migration version for l-prefixed exhibition slugs.
 */
function ath_exhibition_slug_fix_version() {
	return 1;
}

/**
 * Strip erroneous "l" prefix from sheet-import exhibition slugs.
 *
 * @param string $slug Page slug.
 */
function ath_normalize_exhibition_page_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );

	if ( preg_match( '/^l(2026exhibition_.+)$/i', $slug, $matches ) ) {
		return sanitize_title( $matches[1] );
	}

	return $slug;
}

/**
 * Replace old exhibition slug references in post content and meta.
 *
 * @param string $old_slug Previous slug.
 * @param string $new_slug Normalized slug.
 */
function ath_replace_exhibition_slug_references( $old_slug, $new_slug ) {
	global $wpdb;

	$old_slug = sanitize_title( (string) $old_slug );
	$new_slug = sanitize_title( (string) $new_slug );

	if ( $old_slug === '' || $new_slug === '' || $old_slug === $new_slug ) {
		return;
	}

	$replacements = array(
		$old_slug,
		'/' . $old_slug . '/',
		home_url( '/' . $old_slug . '/' ),
	);

	$new_values = array(
		$new_slug,
		'/' . $new_slug . '/',
		home_url( '/' . $new_slug . '/' ),
	);

	foreach ( $replacements as $index => $search ) {
		$like = '%' . $wpdb->esc_like( $search ) . '%';

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
				$search,
				$new_values[ $index ],
				$like
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
				$search,
				$new_values[ $index ],
				$like
			)
		);
	}
}

/**
 * Rename exhibition pages whose slug starts with l2026exhibition.
 *
 * @return array{renamed: array<string, string>, skipped: string[]}
 */
function ath_fix_l_prefixed_exhibition_page_slugs() {
	global $wpdb;

	$renamed = array();
	$skipped = array();

	$page_ids = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status != 'trash' AND post_name LIKE 'l2026exhibition%'"
	);

	if ( ! is_array( $page_ids ) ) {
		$page_ids = array();
	}

	foreach ( $page_ids as $page_id ) {
		$page = get_post( (int) $page_id );
		if ( ! $page || $page->post_type !== 'page' ) {
			continue;
		}

		$new_slug = ath_normalize_exhibition_page_slug( $page->post_name );
		if ( $new_slug === $page->post_name ) {
			continue;
		}

		$conflict = get_page_by_path( $new_slug, OBJECT, 'page' );
		if ( $conflict && (int) $conflict->ID !== (int) $page->ID ) {
			$skipped[] = $page->post_name . ' → ' . $new_slug . ' (slug taken)';
			continue;
		}

		$result = wp_update_post(
			array(
				'ID'        => (int) $page->ID,
				'post_name' => $new_slug,
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			$skipped[] = $page->post_name . ' (' . $result->get_error_message() . ')';
			continue;
		}

		ath_replace_exhibition_slug_references( $page->post_name, $new_slug );
		$renamed[ $page->post_name ] = $new_slug;
	}

	if ( $renamed !== array() ) {
		flush_rewrite_rules( false );
	}

	return array(
		'renamed' => $renamed,
		'skipped' => $skipped,
	);
}

/**
 * One-time migration after theme deploy.
 */
function ath_maybe_fix_l_prefixed_exhibition_page_slugs() {
	if ( (int) get_option( 'ath_exhibition_l_prefix_fix', 0 ) >= ath_exhibition_slug_fix_version() ) {
		return;
	}

	ath_fix_l_prefixed_exhibition_page_slugs();
	update_option( 'ath_exhibition_l_prefix_fix', ath_exhibition_slug_fix_version(), false );
}

add_action( 'init', 'ath_maybe_fix_l_prefixed_exhibition_page_slugs', 5 );

/**
 * 301 redirect l2026exhibition_* → 2026exhibition_* (bookmarks, Google Sheet links).
 */
function ath_redirect_l_prefixed_exhibition_slugs() {
	if ( is_admin() ) {
		return;
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path = wp_parse_url( $uri, PHP_URL_PATH );

	if ( ! is_string( $path ) || $path === '' ) {
		return;
	}

	$path = trim( $path, '/' );
	if ( ! preg_match( '/^l(2026exhibition_.+)$/i', $path, $matches ) ) {
		return;
	}

	$target = home_url( '/' . sanitize_title( $matches[1] ) . '/' );
	$query  = wp_parse_url( $uri, PHP_URL_QUERY );

	if ( is_string( $query ) && $query !== '' ) {
		$target = $target . ( strpos( $target, '?' ) !== false ? '&' : '?' ) . $query;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}

add_action( 'template_redirect', 'ath_redirect_l_prefixed_exhibition_slugs', 0 );
