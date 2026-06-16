<?php
/**
 * Gutenberg migration — export legacy partials, seed pages, rollback.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove page template meta when it no longer exists in the active theme.
 *
 * Prevents wp_update_post() failing with "Invalid page template." on legacy pages.
 *
 * @param int $post_id Page ID.
 */
function ath_clear_invalid_page_template( $post_id ) {
	$post_id  = (int) $post_id;
	$template = get_page_template_slug( $post_id );

	if ( $template === '' ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'page' ) {
		return;
	}

	$valid = wp_get_theme()->get_page_templates( $post, 'page' );
	if ( ! isset( $valid[ $template ] ) ) {
		delete_post_meta( $post_id, '_wp_page_template' );
	}
}

/**
 * Wrap captured HTML as a Gutenberg Custom HTML block.
 *
 * @param string $html Raw HTML.
 */
function ath_html_to_gutenberg_block( $html ) {
	$html = trim( $html );
	if ( $html === '' ) {
		return '';
	}

	return "<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->";
}

/**
 * Build block content for home export — split HTML vs shortcode blocks.
 *
 * @param string $html Captured home HTML (shortcode positions hold block markup).
 */
function ath_home_html_to_gutenberg_blocks( $html ) {
	$html = trim( $html );
	if ( $html === '' ) {
		return '';
	}

	$pattern = '/(<!-- wp:shortcode -->\s*\[ath_home_[^\]]+\]\s*<!-- \/wp:shortcode -->)/s';
	$parts   = preg_split( $pattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

	if ( ! is_array( $parts ) || count( $parts ) === 1 ) {
		return ath_html_to_gutenberg_block( $html );
	}

	$blocks = array();
	foreach ( $parts as $part ) {
		$part = trim( $part );
		if ( $part === '' ) {
			continue;
		}

		if ( strpos( $part, '<!-- wp:shortcode -->' ) === 0 ) {
			$blocks[] = $part;
			continue;
		}

		$blocks[] = ath_html_to_gutenberg_block( $part );
	}

	return implode( "\n\n", $blocks );
}

/**
 * Capture rendered HTML from a routed partial.
 *
 * @param string $slug Page slug.
 */
function ath_capture_routed_partial_html( $slug ) {
	$partial = ath_routed_page_partial_path( $slug );
	if ( ! $partial ) {
		return '';
	}

	if ( $slug === ath_calendar_page_slug() ) {
		extract( ath_calendar_page_vars(), EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	}

	ob_start();
	require $partial;

	return trim( (string) ob_get_clean() );
}

/**
 * Capture homepage partial HTML (gallery → shortcode, not static HTML).
 */
function ath_capture_home_partial_html() {
	ath_home_hero_export_mode( true );
	ath_home_intro_export_mode( true );
	ath_home_cards_export_mode( true );
	ath_home_gallery_export_mode( true );
	ath_home_programs_export_mode( true );
	ath_home_community_export_mode( true );
	ath_home_testimonials_export_mode( true );
	ath_home_news_export_mode( true );
	ob_start();
	require ATH_THEME_DIR . '/partials/home-content.php';
	ath_home_news_export_mode( false );
	ath_home_testimonials_export_mode( false );
	ath_home_community_export_mode( false );
	ath_home_programs_export_mode( false );
	ath_home_gallery_export_mode( false );
	ath_home_cards_export_mode( false );
	ath_home_intro_export_mode( false );
	ath_home_hero_export_mode( false );

	return trim( (string) ob_get_clean() );
}

/**
 * Backup current page editor contents before migration.
 *
 * @return array<int, array{slug: string, title: string, content: string, meta: array<string, mixed>}>
 */
function ath_backup_page_editor_contents() {
	$backup = array();

	$slugs = array_merge(
		ath_gutenberg_migratable_page_slugs(),
		ath_generic_v2_page_slugs()
	);

	$front = (int) get_option( 'page_on_front' );
	if ( $front ) {
		$front_post = get_post( $front );
		if ( $front_post ) {
			$slugs[] = $front_post->post_name;
		}
	}

	$slugs = array_values( array_unique( $slugs ) );

	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}

		$backup[ $page->ID ] = array(
			'slug'    => $page->post_name,
			'title'   => $page->post_title,
			'content' => $page->post_content,
			'meta'    => array(
				'_ath_gutenberg_migrated'   => get_post_meta( $page->ID, '_ath_gutenberg_migrated', true ),
				'_ath_gutenberg_full_layout' => get_post_meta( $page->ID, '_ath_gutenberg_full_layout', true ),
			),
		);
	}

	foreach ( ath_workshop_slugs() as $slug ) {
		$page = ath_workshop_page_by_slug( $slug );
		if ( ! $page ) {
			continue;
		}

		$backup[ $page->ID ] = array(
			'slug'    => 'workshops/' . $page->post_name,
			'title'   => $page->post_title,
			'content' => $page->post_content,
			'meta'    => array(
				'_ath_gutenberg_migrated'   => get_post_meta( $page->ID, '_ath_gutenberg_migrated', true ),
				'_ath_gutenberg_full_layout' => get_post_meta( $page->ID, '_ath_gutenberg_full_layout', true ),
			),
		);
	}

	foreach ( ath_adult_course_slugs() as $slug ) {
		$page = ath_adult_course_page_by_slug( $slug );
		if ( ! $page ) {
			continue;
		}

		$backup[ $page->ID ] = array(
			'slug'    => 'courses/' . $page->post_name,
			'title'   => $page->post_title,
			'content' => $page->post_content,
			'meta'    => array(
				'_ath_gutenberg_migrated'   => get_post_meta( $page->ID, '_ath_gutenberg_migrated', true ),
				'_ath_gutenberg_full_layout' => get_post_meta( $page->ID, '_ath_gutenberg_full_layout', true ),
			),
		);
	}

	$hub = ath_workshops_hub_page();
	if ( $hub ) {
		$backup[ $hub->ID ] = array(
			'slug'    => 'workshops',
			'title'   => $hub->post_title,
			'content' => $hub->post_content,
			'meta'    => array(
				'_ath_gutenberg_migrated'   => get_post_meta( $hub->ID, '_ath_gutenberg_migrated', true ),
				'_ath_gutenberg_full_layout' => get_post_meta( $hub->ID, '_ath_gutenberg_full_layout', true ),
			),
		);
	}

	update_option( 'ath_gutenberg_content_backup', $backup, false );

	$path = ATH_THEME_DIR . '/backup/db-content-backup.json';
	wp_mkdir_p( dirname( $path ) );
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	file_put_contents( $path, wp_json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

	return $backup;
}

/**
 * Write post_content bypassing page-template validation (home migration fallback).
 *
 * @param int    $post_id Page ID.
 * @param string $content Block editor content.
 */
function ath_force_page_content( $post_id, $content ) {
	global $wpdb;

	$updated = $wpdb->update(
		$wpdb->posts,
		array(
			'post_content' => $content,
		),
		array( 'ID' => (int) $post_id ),
		array( '%s' ),
		array( '%d' )
	);

	if ( $updated === false ) {
		return new WP_Error( 'db_update_failed', __( 'Could not update page content in the database.' ) );
	}

	clean_post_cache( (int) $post_id );

	return (int) $post_id;
}

/**
 * Migrate the static front page to Gutenberg (home partial export).
 *
 * @param bool $force Overwrite existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_homepage_to_gutenberg( $force = true ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	$front_id = (int) get_option( 'page_on_front' );
	if ( ! $front_id ) {
		$results['errors'][] = 'No static homepage set (Settings → Reading).';
		return $results;
	}

	$front = get_post( $front_id );
	if ( ! $front || $front->post_type !== 'page' ) {
		$results['errors'][] = 'Homepage page not found.';
		return $results;
	}

	if ( ! $force && ath_page_has_editor_content( $front ) ) {
		$results['skipped'][] = 'home (' . $front->post_name . ')';
		return $results;
	}

	$html = ath_capture_home_partial_html();
	if ( $html === '' ) {
		$results['errors'][] = 'No HTML captured for homepage.';
		return $results;
	}

	// Front page must use theme front-page.php — clear any legacy template meta.
	delete_post_meta( $front_id, '_wp_page_template' );
	ath_clear_invalid_page_template( $front_id );

	$block_content = ath_home_html_to_gutenberg_blocks( $html );
	$updated       = wp_update_post(
		array(
			'ID'           => $front_id,
			'post_content' => $block_content,
		),
		true
	);

	if ( is_wp_error( $updated ) ) {
		$updated = ath_force_page_content( $front_id, $block_content );
	}

	if ( is_wp_error( $updated ) ) {
		$results['errors'][] = sprintf( 'home: %s', $updated->get_error_message() );
		return $results;
	}

	update_post_meta( $front_id, '_ath_gutenberg_migrated', '1' );
	update_post_meta( $front_id, '_ath_gutenberg_full_layout', '1' );
	$results['updated'][] = 'home (' . $front->post_name . ')';

	return $results;
}

/**
 * Seed Gutenberg content from legacy partials.
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_seed_gutenberg_from_legacy( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	ath_backup_page_editor_contents();

	$hub_block_slugs = array( 'courses', 'kids-courses', 'pricing' );

	foreach ( ath_gutenberg_migratable_page_slugs() as $slug ) {
		if ( in_array( $slug, $hub_block_slugs, true ) ) {
			continue;
		}
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$results['errors'][] = sprintf( 'Page "%s" not found.', $slug );
			continue;
		}

		if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
			$results['skipped'][] = $slug;
			continue;
		}

		$html = ath_capture_routed_partial_html( $slug );
		if ( $html === '' ) {
			$results['errors'][] = sprintf( 'No HTML captured for "%s".', $slug );
			continue;
		}

		ath_clear_invalid_page_template( $page->ID );

		$block_content = ath_html_to_gutenberg_block( $html );
		$updated = wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => $block_content,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			$results['errors'][] = sprintf( '%s: %s', $slug, $updated->get_error_message() );
			continue;
		}

		update_post_meta( $page->ID, '_ath_gutenberg_migrated', '1' );
		update_post_meta( $page->ID, '_ath_gutenberg_full_layout', '1' );
		$results['updated'][] = $slug;
	}

	$home_results = ath_migrate_homepage_to_gutenberg( $overwrite );
	$results['updated'] = array_merge( $results['updated'], $home_results['updated'] );
	$results['skipped'] = array_merge( $results['skipped'], $home_results['skipped'] );
	$results['errors']  = array_merge( $results['errors'], $home_results['errors'] );

	$hub_results      = ath_migrate_workshops_hub_page( $overwrite );
	$child_results    = ath_migrate_workshop_pages( $overwrite );
	$workshop_results = array(
		'updated' => array_merge( $hub_results['updated'], $child_results['updated'] ),
		'skipped' => array_merge( $hub_results['skipped'], $child_results['skipped'] ),
		'errors'  => array_merge( $hub_results['errors'], $child_results['errors'] ),
	);
	$results['updated'] = array_merge( $results['updated'], $workshop_results['updated'] );
	$results['skipped'] = array_merge( $results['skipped'], $workshop_results['skipped'] );
	$results['errors']  = array_merge( $results['errors'], $workshop_results['errors'] );

	$course_results = ath_migrate_adult_course_pages( $overwrite );
	$results['updated'] = array_merge( $results['updated'], $course_results['updated'] );
	$results['skipped'] = array_merge( $results['skipped'], $course_results['skipped'] );
	$results['errors']  = array_merge( $results['errors'], $course_results['errors'] );

	$courses_hub_results = ath_migrate_courses_hub_page( $overwrite );
	$results['updated']  = array_merge( $results['updated'], $courses_hub_results['updated'] );
	$results['skipped']  = array_merge( $results['skipped'], $courses_hub_results['skipped'] );
	$results['errors']   = array_merge( $results['errors'], $courses_hub_results['errors'] );

	$kids_hub_results   = ath_migrate_kids_courses_hub_page( $overwrite );
	$results['updated'] = array_merge( $results['updated'], $kids_hub_results['updated'] );
	$results['skipped'] = array_merge( $results['skipped'], $kids_hub_results['skipped'] );
	$results['errors']  = array_merge( $results['errors'], $kids_hub_results['errors'] );

	$pricing_results    = ath_migrate_pricing_page( $overwrite );
	$results['updated'] = array_merge( $results['updated'], $pricing_results['updated'] );
	$results['skipped'] = array_merge( $results['skipped'], $pricing_results['skipped'] );
	$results['errors']  = array_merge( $results['errors'], $pricing_results['errors'] );

	update_option( 'ath_content_mode', 'hybrid', false );
	update_option( 'ath_gutenberg_migration_version', '3', false );

	return $results;
}

/**
 * Restore editor contents from the last backup.
 *
 * @return array{restored: string[], errors: string[]}
 */
function ath_restore_gutenberg_backup() {
	$backup  = get_option( 'ath_gutenberg_content_backup', array() );
	$results = array(
		'restored' => array(),
		'errors'   => array(),
	);

	if ( ! is_array( $backup ) || empty( $backup ) ) {
		$path = ATH_THEME_DIR . '/backup/db-content-backup.json';
		if ( is_readable( $path ) ) {
			$decoded = json_decode( file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( is_array( $decoded ) ) {
				$backup = $decoded;
			}
		}
	}

	foreach ( $backup as $page_id => $row ) {
		$page_id = (int) $page_id;
		if ( ! $page_id || ! is_array( $row ) ) {
			continue;
		}

		$updated = wp_update_post(
			array(
				'ID'           => $page_id,
				'post_content' => $row['content'] ?? '',
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			$results['errors'][] = sprintf( '%s: %s', $row['slug'] ?? $page_id, $updated->get_error_message() );
			continue;
		}

		$meta = $row['meta'] ?? array();
		if ( ! empty( $meta['_ath_gutenberg_migrated'] ) ) {
			update_post_meta( $page_id, '_ath_gutenberg_migrated', $meta['_ath_gutenberg_migrated'] );
		} else {
			delete_post_meta( $page_id, '_ath_gutenberg_migrated' );
		}

		if ( ! empty( $meta['_ath_gutenberg_full_layout'] ) ) {
			update_post_meta( $page_id, '_ath_gutenberg_full_layout', $meta['_ath_gutenberg_full_layout'] );
		} else {
			delete_post_meta( $page_id, '_ath_gutenberg_full_layout' );
		}

		$results['restored'][] = $row['slug'] ?? (string) $page_id;
	}

	return $results;
}

/**
 * Full rollback to legacy PHP rendering.
 */
function ath_rollback_to_legacy_mode() {
	update_option( 'ath_content_mode', 'legacy', false );
}

/**
 * Admin tools screen.
 */
function ath_register_gutenberg_migration_page() {
	add_management_page(
		'ATH Content Migration',
		'ATH Content',
		'manage_options',
		'ath-content-migration',
		'ath_render_gutenberg_migration_page'
	);
}

add_action( 'admin_menu', 'ath_register_gutenberg_migration_page' );

/**
 * Handle migration form actions.
 */
function ath_handle_gutenberg_migration_actions() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_POST['ath_content_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	check_admin_referer( 'ath_content_migration' );

	$action = sanitize_key( wp_unslash( $_POST['ath_content_action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( $action === 'set_mode' ) {
		$mode = sanitize_key( wp_unslash( $_POST['ath_content_mode'] ?? 'hybrid' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( in_array( $mode, array( 'legacy', 'hybrid', 'gutenberg' ), true ) ) {
			update_option( 'ath_content_mode', $mode, false );
		}
		wp_safe_redirect( add_query_arg( 'ath_msg', 'mode_saved', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate' ) {
		$overwrite = ! empty( $_POST['ath_overwrite'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$results   = ath_seed_gutenberg_from_legacy( $overwrite );
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate_home' ) {
		$results = ath_migrate_homepage_to_gutenberg( true );
		if ( empty( $results['errors'] ) ) {
			update_option( 'ath_content_mode', 'hybrid', false );
			update_option( 'ath_homepage_shortcodes_version', '8', false );
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'home_migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate_workshops' ) {
		$results = ath_migrate_workshops_hub_page( true );
		$child   = ath_migrate_workshop_pages( true );
		$results['updated'] = array_merge( $results['updated'], $child['updated'] );
		$results['skipped'] = array_merge( $results['skipped'], $child['skipped'] );
		$results['errors']  = array_merge( $results['errors'], $child['errors'] );
		if ( empty( $results['errors'] ) ) {
			update_option( 'ath_workshop_gutenberg_version', '5', false );
			update_option( 'ath_content_mode', 'hybrid', false );
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'workshops_migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate_courses' ) {
		$results = ath_migrate_adult_course_pages( true );
		if ( empty( $results['errors'] ) ) {
			update_option( 'ath_adult_course_gutenberg_version', '1', false );
			update_option( 'ath_content_mode', 'hybrid', false );
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'courses_migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate_kids_courses' ) {
		$results = ath_migrate_kids_courses_hub_page( true );
		if ( empty( $results['errors'] ) ) {
			update_option( 'ath_kids_courses_hub_gutenberg_version', ATH_KIDS_COURSES_HUB_GUTENBERG_VERSION, false );
			update_option( 'ath_content_mode', 'hybrid', false );
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'kids_courses_migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate_courses_hub' ) {
		$results = ath_migrate_courses_hub_page( true );
		if ( empty( $results['errors'] ) ) {
			update_option( 'ath_courses_hub_gutenberg_version', ATH_COURSES_HUB_GUTENBERG_VERSION, false );
			update_option( 'ath_content_mode', 'hybrid', false );
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'courses_hub_migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate_faq' ) {
		$results = ath_migrate_faq_page( true );
		if ( empty( $results['errors'] ) ) {
			update_option( 'ath_faq_gutenberg_version', '1', false );
			update_option( 'ath_content_mode', 'hybrid', false );
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'faq_migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'migrate_pricing' ) {
		$results = ath_migrate_pricing_page( true );
		if ( empty( $results['errors'] ) ) {
			update_option( 'ath_pricing_gutenberg_version', ATH_PRICING_GUTENBERG_VERSION, false );
			update_option( 'ath_content_mode', 'hybrid', false );
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'pricing_migrated', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'restore_db' ) {
		$results = ath_restore_gutenberg_backup();
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'restored', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'rollback_legacy' ) {
		ath_rollback_to_legacy_mode();
		wp_safe_redirect( add_query_arg( 'ath_msg', 'legacy', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'sync_guide' ) {
		$result = ath_ensure_guide_page( true );
		if ( $result['updated'] ) {
			set_transient( 'ath_guide_page_sync_notice', 1, 60 );
		}
		wp_safe_redirect( add_query_arg( 'ath_msg', 'guide_synced', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'sync_seo' ) {
		$result = ath_ensure_seo_page( true );
		if ( $result['updated'] ) {
			set_transient( 'ath_seo_page_sync_notice', 1, 60 );
		}
		wp_safe_redirect( add_query_arg( 'ath_msg', 'seo_synced', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'seed_rank_math' ) {
		$results = ath_seed_rank_math_meta( false );
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'rank_math_seeded', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'seed_rank_math_overwrite' ) {
		$results = ath_seed_rank_math_meta( true );
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'rank_math_overwritten', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'sync_seo_onpage' ) {
		$overwrite = ! empty( $_POST['ath_overwrite'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$batch     = ath_apply_seo_onpage_sprint( $overwrite );
		$results   = array(
			'updated' => array(),
			'skipped' => array(),
			'errors'  => array(),
			'missing' => array(),
		);
		foreach ( $batch as $section => $part ) {
			if ( ! is_array( $part ) ) {
				continue;
			}
			foreach ( array( 'updated', 'skipped', 'errors', 'missing' ) as $key ) {
				if ( ! empty( $part[ $key ] ) && is_array( $part[ $key ] ) ) {
					foreach ( $part[ $key ] as $item ) {
						$results[ $key ][] = $section . ': ' . $item;
					}
				}
			}
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'seo_onpage_synced', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'seed_rank_math_detail' ) {
		$results = ath_seed_rank_math_detail_meta( false );
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'rank_math_detail_seeded', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'sync_seo_monitor' ) {
		$results = ath_apply_seo_monitor_sprint();
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'seo_monitor_synced', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}

	if ( $action === 'sync_seo_content' ) {
		$overwrite = ! empty( $_POST['ath_overwrite'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$batch     = ath_apply_seo_content_sprint( $overwrite );
		$results   = array(
			'updated' => array(),
			'skipped' => array(),
			'errors'  => array(),
			'missing' => array(),
		);
		foreach ( $batch as $section => $part ) {
			if ( ! is_array( $part ) ) {
				continue;
			}
			foreach ( array( 'updated', 'skipped', 'errors', 'missing' ) as $key ) {
				if ( ! empty( $part[ $key ] ) && is_array( $part[ $key ] ) ) {
					foreach ( $part[ $key ] as $item ) {
						$results[ $key ][] = $section . ': ' . $item;
					}
				}
			}
		}
		set_transient( 'ath_migration_results', $results, 60 );
		wp_safe_redirect( add_query_arg( 'ath_msg', 'seo_content_synced', admin_url( 'tools.php?page=ath-content-migration' ) ) );
		exit;
	}
}

add_action( 'admin_init', 'ath_handle_gutenberg_migration_actions' );

/**
 * One-time homepage sync — all homepage shortcodes with full attributes.
 */
function ath_maybe_sync_homepage_shortcode_blocks() {
	if ( get_option( 'ath_homepage_shortcodes_version' ) === '8' ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$results = ath_migrate_homepage_to_gutenberg( true );

	if ( ! empty( $results['errors'] ) ) {
		set_transient( 'ath_homepage_sync_notice', $results, 120 );
		return;
	}

	update_option( 'ath_content_mode', 'hybrid', false );
	update_option( 'ath_homepage_shortcodes_version', '8', false );
	set_transient( 'ath_homepage_sync_notice', $results, 120 );
}

add_action( 'admin_init', 'ath_maybe_sync_homepage_shortcode_blocks', 99 );

/**
 * Admin notice after automatic homepage shortcode sync.
 */
function ath_homepage_sync_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$results = get_transient( 'ath_homepage_sync_notice' );
	if ( ! is_array( $results ) ) {
		return;
	}

	delete_transient( 'ath_homepage_sync_notice' );

	$is_error = ! empty( $results['errors'] );
	$class    = $is_error ? 'notice-error' : 'notice-success';
	?>
	<div class="notice <?php echo esc_attr( $class ); ?> is-dismissible">
		<p><strong>ATH homepage sync</strong></p>
		<ul style="list-style:disc;margin-left:1.5em;">
			<?php foreach ( $results as $key => $items ) : ?>
				<?php if ( ! empty( $items ) && is_array( $items ) ) : ?>
					<li><?php echo esc_html( ucfirst( (string) $key ) ); ?>: <?php echo esc_html( implode( ', ', $items ) ); ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<?php if ( ! $is_error ) : ?>
			<p>Homepage now uses <code>[ath_home_hero]</code> and <code>[ath_home_gallery]</code> with full parameters. Edit under Pages → your homepage.</p>
		<?php endif; ?>
	</div>
	<?php
}

add_action( 'admin_notices', 'ath_homepage_sync_admin_notice' );

/**
 * Render migration admin page.
 */
function ath_render_gutenberg_migration_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$mode    = ath_content_mode();
	$results = get_transient( 'ath_migration_results' );
	if ( $results ) {
		delete_transient( 'ath_migration_results' );
	}

	$migrated = array_merge(
		ath_gutenberg_migratable_page_slugs(),
		array_map(
			static function ( $slug ) {
				return 'workshops/' . $slug;
			},
			ath_workshop_slugs()
		),
		array_map(
			static function ( $slug ) {
				return 'courses/' . $slug;
			},
			ath_adult_course_slugs()
		),
		array( 'workshops' )
	);
	$legacy   = ath_legacy_only_page_slugs();
	$backup   = ATH_THEME_DIR . '/backup/pre-gutenberg-snapshot';
	?>
	<div class="wrap">
		<h1>ATH Content Migration</h1>

		<?php if ( isset( $_GET['ath_msg'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p>
				<?php
				switch ( sanitize_key( wp_unslash( $_GET['ath_msg'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					case 'mode_saved':
						echo 'Content mode saved.';
						break;
					case 'migrated':
						echo 'Migration completed. Pages now use hybrid mode.';
						break;
					case 'restored':
						echo 'Database content restored from backup.';
						break;
					case 'legacy':
						echo 'Rolled back to legacy PHP rendering.';
						break;
					case 'home_migrated':
						echo 'Homepage migration finished.';
						break;
					case 'guide_synced':
						echo 'Guide page synced from theme.';
						break;
					case 'workshops_migrated':
						echo 'Workshop pages converted to native Gutenberg blocks.';
						break;
					case 'courses_migrated':
						echo 'Adult course pages converted to native Gutenberg blocks.';
						break;
					case 'kids_courses_migrated':
						echo 'Kids courses hub reset: intro + pathways accordion ([ath_kids_courses_hub]) + editable schedule/pricing blocks.';
						break;
					case 'courses_hub_migrated':
						echo 'Courses hub reset: intro + pathways accordion ([ath_courses_hub]) + benefits/CTA.';
						break;
					case 'faq_migrated':
						echo 'FAQ page updated from theme seed (native Gutenberg blocks).';
						break;
					case 'pricing_migrated':
						echo 'Pricing page reset: intro + accordion ([ath_pricing]).';
						break;
					case 'seo_synced':
						echo 'SEO playbook synced from theme.';
						break;
					case 'rank_math_seeded':
						echo 'Rank Math meta seeded (skipped pages that already had a custom title).';
						break;
					case 'rank_math_overwritten':
						echo 'Rank Math meta overwritten from theme defaults.';
						break;
					case 'seo_onpage_synced':
						echo 'Sprint 2 on-page SEO applied (home intro, hubs, detail meta).';
						break;
					case 'rank_math_detail_seeded':
						echo 'Rank Math meta seeded for workshop/course detail pages.';
						break;
					case 'seo_content_synced':
						echo 'Sprint 3 content SEO applied (FAQ, redirects map, blog cross-links).';
						break;
					case 'seo_monitor_synced':
						echo 'Sprint 4 monitor applied (defer JS, baseline in playbook).';
						break;
				}
				?>
			</p></div>
		<?php endif; ?>

		<?php if ( is_array( $results ) ) : ?>
			<div class="notice notice-info"><p><strong>Results:</strong></p>
				<ul style="list-style:disc;margin-left:1.5em;">
					<?php foreach ( $results as $key => $items ) : ?>
						<?php if ( ! empty( $items ) && is_array( $items ) ) : ?>
							<li><?php echo esc_html( ucfirst( (string) $key ) ); ?>: <?php echo esc_html( implode( ', ', $items ) ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<p>File snapshot: <code><?php echo esc_html( $backup ); ?></code></p>
		<p>Current mode: <strong><?php echo esc_html( $mode ); ?></strong></p>
		<?php
		$guide_page = get_page_by_path( 'guide' );
		if ( $guide_page ) :
			$guide_view = ath_guide_page_url();
			$guide_edit = get_edit_post_link( $guide_page->ID, 'raw' );
			?>
		<p>
			<?php if ( $guide_view ) : ?>
				<a href="<?php echo esc_url( $guide_view ); ?>" class="button button-secondary">View Guide</a>
			<?php endif; ?>
			<?php if ( $guide_edit ) : ?>
				<a href="<?php echo esc_url( $guide_edit ); ?>" class="button button-link">Edit Guide</a>
			<?php endif; ?>
			<span class="description"> Public — <code>/guide/</code> — auto-sync khi admin login</span>
		</p>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Ghi đè trang Guide bằng nội dung mới nhất từ theme?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="sync_guide">
			<?php submit_button( 'Update Guide now', 'secondary', 'submit', false ); ?>
		</form>
		<?php
		$guide_hash = get_post_meta( $guide_page->ID, '_ath_guide_content_hash', true );
		if ( $guide_hash ) :
			?>
		<p class="description">Guide hash: <code><?php echo esc_html( substr( (string) $guide_hash, 0, 8 ) ); ?></code>
			<?php if ( $guide_hash !== ath_guide_page_content_hash() ) : ?>
				— <strong>có bản mới trong theme, sẽ sync khi reload admin</strong>
			<?php endif; ?>
		</p>
		<?php endif; ?>
		<?php endif; ?>

		<?php
		$seo_page = get_page_by_path( 'seo' );
		if ( $seo_page ) :
			$seo_view = ath_seo_page_url();
			$seo_edit = get_edit_post_link( $seo_page->ID, 'raw' );
			?>
		<p>
			<?php if ( $seo_view ) : ?>
				<a href="<?php echo esc_url( $seo_view ); ?>" class="button button-secondary">View SEO playbook</a>
			<?php endif; ?>
			<?php if ( $seo_edit ) : ?>
				<a href="<?php echo esc_url( $seo_edit ); ?>" class="button button-link">Edit SEO page</a>
			<?php endif; ?>
			<span class="description"> Internal — <code>/seo/</code> (noindex) — auto-sync khi admin login</span>
		</p>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Ghi đè trang /seo/ bằng playbook mới nhất từ theme?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="sync_seo">
			<?php submit_button( 'Update SEO playbook now', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;display:inline-block;" onsubmit="return confirm('Ghi Rank Math title/description cho trang chưa có custom title?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="seed_rank_math">
			<?php submit_button( 'Seed Rank Math meta', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;display:inline-block;" onsubmit="return confirm('GHI ĐÈ tất cả Rank Math meta từ theme defaults?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="seed_rank_math_overwrite">
			<?php submit_button( 'Overwrite Rank Math meta', 'delete', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:12px;" onsubmit="return confirm('Sprint 2: cập nhật home intro, /workshops/, /courses/, Rank Math detail pages?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="sync_seo_onpage">
			<label><input type="checkbox" name="ath_overwrite" value="1"> Ghi đè nội dung hub đã có</label>
			<?php submit_button( 'Apply Sprint 2 on-page SEO', 'primary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;display:inline-block;" onsubmit="return confirm('Seed Rank Math cho trang workshop/course detail?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="seed_rank_math_detail">
			<?php submit_button( 'Seed detail page meta only', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:12px;" onsubmit="return confirm('Sprint 3: FAQ migrate, blog redirects, FAQ schema?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="sync_seo_content">
			<label><input type="checkbox" name="ath_overwrite" value="1"> Ghi đè FAQ + Rank Math FAQ meta</label>
			<?php submit_button( 'Apply Sprint 3 content SEO', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Sprint 4: defer JS + cập nhật monitor playbook?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="sync_seo_monitor">
			<?php submit_button( 'Apply Sprint 4 monitor', 'primary', 'submit', false ); ?>
		</form>
		<?php
		$seo_hash     = get_post_meta( $seo_page->ID, '_ath_seo_content_hash', true );
		$seo_version  = get_option( 'ath_seo_page_version', '' );
		$theme_version = ath_seo_page_sync_version();
		$seo_outdated = $seo_hash !== ath_seo_page_content_hash() || $seo_version !== $theme_version;
		if ( $seo_hash || $seo_version ) :
			?>
		<p class="description">
			Playbook v: theme <code><?php echo esc_html( $theme_version ); ?></code>
			<?php if ( $seo_version !== '' ) : ?>
				· DB <code><?php echo esc_html( (string) $seo_version ); ?></code>
			<?php endif; ?>
			<?php if ( $seo_hash ) : ?>
				· hash <code><?php echo esc_html( substr( (string) $seo_hash, 0, 8 ) ); ?></code>
			<?php endif; ?>
			<?php if ( $seo_outdated ) : ?>
				— <strong style="color:#b32d2e;">chưa sync — bấm “Update SEO playbook now” hoặc mở /seo/ khi đã login admin</strong>
			<?php endif; ?>
		</p>
		<?php endif; ?>
		<?php endif; ?>

		<h2>Content mode</h2>
		<form method="post">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="set_mode">
			<select name="ath_content_mode">
				<option value="legacy" <?php selected( $mode, 'legacy' ); ?>>legacy — PHP partials only (full rollback)</option>
				<option value="hybrid" <?php selected( $mode, 'hybrid' ); ?>>hybrid — Gutenberg when migrated, else PHP</option>
				<option value="gutenberg" <?php selected( $mode, 'gutenberg' ); ?>>gutenberg — prefer editor everywhere possible</option>
			</select>
			<?php submit_button( 'Save mode', 'secondary', 'submit', false ); ?>
		</form>

		<h2>Migrate to Gutenberg</h2>
		<p>Exports current PHP partial HTML into page editor (Custom HTML block). Migrates:
			<code><?php echo esc_html( implode( ', ', $migrated ) ); ?></code>
			and the front page (if set). Homepage gallery stays dynamic PHP via <code>[ath_home_gallery]</code> (shuffle, lazy-load, lightbox).
		</p>
		<?php
		$workshop_links = array();
		foreach ( ath_workshop_slugs() as $workshop_slug ) {
			$workshop_page = ath_workshop_page_by_slug( $workshop_slug );
			if ( ! $workshop_page ) {
				continue;
			}
			$edit = get_edit_post_link( $workshop_page->ID, 'raw' );
			if ( $edit ) {
				$workshop_links[] = '<a href="' . esc_url( $edit ) . '">' . esc_html( $workshop_slug ) . '</a>';
			}
		}
		if ( $workshop_links ) :
			?>
		<p>Workshop pages: <?php echo implode( ' · ', $workshop_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Convert workshop pages to native Gutenberg blocks (heading, paragraph, list)? Overwrites Custom HTML if present.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate_workshops">
			<?php submit_button( 'Convert workshops to blocks', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Convert adult course pages under /courses/ to native Gutenberg blocks? Overwrites existing editor content.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate_courses">
			<?php submit_button( 'Convert adult courses to blocks', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Reset /courses/: intro + pathways accordion shortcode? Overwrites existing editor content.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate_courses_hub">
			<?php submit_button( 'Reset courses hub (accordion)', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Reset /kids-courses/: intro + pathways shortcode + default schedule/pricing blocks? Overwrites existing editor content.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate_kids_courses">
			<?php submit_button( 'Reset kids courses hub (accordion)', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Reset /pricing/: intro + accordion shortcode? Overwrites existing editor content.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate_pricing">
			<?php submit_button( 'Reset pricing page (accordion)', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="margin-top:8px;" onsubmit="return confirm('Update FAQ page from theme seed? Overwrites existing editor content.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate_faq">
			<?php submit_button( 'Update FAQ page', 'secondary', 'submit', false ); ?>
		</form>
		<?php endif; ?>
		<p>Stays legacy (dynamic): <code><?php echo esc_html( implode( ', ', $legacy ) ); ?></code></p>
		<form method="post" onsubmit="return confirm('Backup DB content and migrate pages to Gutenberg?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate">
			<label><input type="checkbox" name="ath_overwrite" value="1"> Overwrite existing editor content</label>
			<?php submit_button( 'Run migration', 'primary', 'submit', false ); ?>
		</form>

		<?php
		$front_id = (int) get_option( 'page_on_front' );
		$front    = $front_id ? get_post( $front_id ) : null;
		if ( $front ) :
			$edit_url = get_edit_post_link( $front->ID, 'raw' );
			?>
		<h2>Homepage only</h2>
		<p>Static homepage: <strong><?php echo esc_html( $front->post_title ); ?></strong>
			(<code><?php echo esc_html( $front->post_name ); ?></code>, ID <?php echo (int) $front->ID; ?>)
			<?php if ( $edit_url ) : ?>
				— <a href="<?php echo esc_url( $edit_url ); ?>">Edit in Pages</a>
			<?php endif; ?>
		</p>
		<p>Template: <code><?php echo esc_html( get_page_template_slug( $front->ID ) ?: 'default' ); ?></code>.
			Migrated: <?php echo get_post_meta( $front->ID, '_ath_gutenberg_migrated', true ) ? 'yes' : 'no'; ?>.
			Shortcodes v: <code><?php echo esc_html( (string) get_option( 'ath_homepage_shortcodes_version', '0' ) ); ?></code></p>
		<p>Hero: <code><?php echo esc_html( ath_home_hero_shortcode_string() ); ?></code></p>
		<p>Cards: <code><?php echo esc_html( ath_home_cards_shortcode_string() ); ?></code></p>
		<p>Gallery: <code><?php echo esc_html( ath_home_gallery_shortcode_string() ); ?></code></p>
		<p>Programs: <code><?php echo esc_html( ath_home_programs_shortcode_string() ); ?></code></p>
		<p>Community: <code><?php echo esc_html( ath_home_community_shortcode_string() ); ?></code></p>
		<p>Testimonials: <code><?php echo esc_html( ath_home_testimonials_shortcode_string() ); ?></code></p>
		<p>Studio news: <code><?php echo esc_html( ath_home_news_shortcode_string() ); ?></code></p>
		<p><strong>Site button:</strong> <code><?php echo esc_html( ath_btn_shortcode_string( array( 'label' => 'View Courses', 'link' => 'courses', 'link_type' => 'page' ) ) ); ?></code></p>
		<form method="post" onsubmit="return confirm('Rebuild homepage with all shortcode blocks? Overwrites editor content on this page.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="migrate_home">
			<?php submit_button( 'Rebuild homepage (shortcodes)', 'secondary', 'submit', false ); ?>
		</form>
		<?php endif; ?>

		<h2>Rollback</h2>
		<form method="post" style="display:inline-block;margin-right:12px;" onsubmit="return confirm('Switch to legacy PHP rendering? Editor content is kept in database.');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="rollback_legacy">
			<?php submit_button( 'Use legacy PHP only', 'secondary', 'submit', false ); ?>
		</form>
		<form method="post" style="display:inline-block;" onsubmit="return confirm('Restore page editor content from last backup?');">
			<?php wp_nonce_field( 'ath_content_migration' ); ?>
			<input type="hidden" name="ath_content_action" value="restore_db">
			<?php submit_button( 'Restore DB content backup', 'secondary', 'submit', false ); ?>
		</form>

		<h2>Not migrated (by design)</h2>
		<ul style="list-style:disc;margin-left:1.5em;">
			<li><code>/kids-courses/{slug}/</code> — virtual URLs from <code>data/*.php</code></li>
			<li>Book, calendar, thank-you, links, students-artworks — dynamic PHP</li>
		</ul>
	</div>
	<?php
}
