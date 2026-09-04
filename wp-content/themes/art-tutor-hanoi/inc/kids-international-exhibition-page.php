<?php
/**
 * Kids international exhibition support — Gutenberg landing page.
 *
 * Slug: /kids-international-art-exhibition/
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Seed data for the landing page.
 *
 * @return array<string, mixed>
 */
function ath_kids_international_exhibition_data() {
	static $data = null;

	if ( $data === null ) {
		$path = ATH_THEME_DIR . '/data/kids-international-exhibition.php';
		$data = is_readable( $path ) ? require $path : array();
	}

	return is_array( $data ) ? $data : array();
}

/**
 * Canonical slug for the landing page.
 */
function ath_kids_international_exhibition_slug() {
	$data = ath_kids_international_exhibition_data();
	$slug = sanitize_title( (string) ( $data['slug'] ?? 'kids-international-art-exhibition' ) );

	return $slug !== '' ? $slug : 'kids-international-art-exhibition';
}

/**
 * FAQ rows for on-page content and JSON-LD.
 *
 * @return array<int, array{q: string, a: string}>
 */
function ath_kids_international_exhibition_faq_rows() {
	$data = ath_kids_international_exhibition_data();
	$rows = array();

	foreach ( (array) ( $data['faq'] ?? array() ) as $row ) {
		if ( ! is_array( $row ) || empty( $row['q'] ) || empty( $row['a'] ) ) {
			continue;
		}

		$rows[] = array(
			'q' => (string) $row['q'],
			'a' => (string) $row['a'],
		);
	}

	return $rows;
}

/**
 * Build native Gutenberg blocks for the landing page.
 */
function ath_kids_international_exhibition_to_gutenberg_blocks() {
	$data   = ath_kids_international_exhibition_data();
	$book   = ath_book_url( 'kids' );
	$kids   = ath_page_url( 'kids-courses' );
	$works  = ath_students_artworks_url();
	$exh    = ath_page_url( 'exhibition' );
	$blocks = array();

	foreach ( (array) ( $data['intro'] ?? array() ) as $paragraph ) {
		$blocks[] = ath_gutenberg_paragraph( (string) $paragraph );
	}

	$blocks[] = ath_gutenberg_heading( 'Who this is for', 2 );
	$blocks[] = ath_gutenberg_list( (array) ( $data['who_for'] ?? array() ) );

	$blocks[] = ath_gutenberg_heading( 'What\'s included', 2 );
	$blocks[] = ath_gutenberg_list( (array) ( $data['included'] ?? array() ) );

	$blocks[] = ath_gutenberg_heading( 'How it works', 2 );
	foreach ( (array) ( $data['steps'] ?? array() ) as $step ) {
		if ( ! is_array( $step ) ) {
			continue;
		}
		$title = (string) ( $step['title'] ?? '' );
		$text  = (string) ( $step['text'] ?? '' );
		if ( $title !== '' ) {
			$blocks[] = ath_gutenberg_heading( $title, 3 );
		}
		if ( $text !== '' ) {
			$blocks[] = ath_gutenberg_paragraph( $text );
		}
	}

	$blocks[] = ath_gutenberg_heading( 'Results', 2 );
	if ( ! empty( $data['results_note'] ) ) {
		$blocks[] = ath_gutenberg_paragraph( (string) $data['results_note'] );
	}
	$blocks[] = ath_gutenberg_paragraph_html(
		'See also our <a href="' . esc_url( $exh ) . '">studio exhibition</a> and '
		. '<a href="' . esc_url( $works ) . '">students\' artworks</a>.'
	);

	$faq_rows = ath_kids_international_exhibition_faq_rows();
	if ( $faq_rows ) {
		$blocks[] = ath_gutenberg_heading( 'Frequently asked questions', 2 );
		foreach ( $faq_rows as $row ) {
			$blocks[] = ath_gutenberg_heading( $row['q'], 3 );
			$blocks[] = ath_gutenberg_paragraph( $row['a'] );
		}
	}

	$blocks[] = ath_gutenberg_heading( 'Enquire about the next open call', 2 );
	$blocks[] = ath_gutenberg_paragraph(
		'Message us on Zalo / WhatsApp at (+84) 98 828 8302, or book a kids trial class and mention exhibition support when you enrol.'
	);
	$blocks[] = ath_gutenberg_btn( 'Book a Kids Class', $book, 'cta' );
	$blocks[] = ath_gutenberg_paragraph_html(
		'<a href="' . esc_url( $kids ) . '">Kids art classes</a> '
		. '<span aria-hidden="true">·</span> '
		. '<a href="' . esc_url( ath_page_url( 'pricing' ) ) . '">Pricing</a> '
		. '<span aria-hidden="true">·</span> '
		. '<a href="' . esc_url( ath_page_url( 'faq' ) ) . '">FAQ</a>',
		'commercial-crosslinks__links'
	);

	return trim( implode( "\n\n", array_filter( $blocks ) ) );
}

/**
 * Create the landing page if missing (does not overwrite editor content).
 *
 * @return array{id: int, created: bool, errors: string[]}
 */
function ath_ensure_kids_international_exhibition_page() {
	$slug   = ath_kids_international_exhibition_slug();
	$data   = ath_kids_international_exhibition_data();
	$title  = (string) ( $data['title'] ?? 'Kids International Art Exhibition Support' );
	$result = array(
		'id'      => 0,
		'created' => false,
		'errors'  => array(),
	);

	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		$result['id'] = (int) $existing->ID;
		ath_use_default_page_template( $existing->ID );
		return $result;
	}

	$block_content = ath_kids_international_exhibition_to_gutenberg_blocks();
	$page_id       = ath_insert_gutenberg_page(
		array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $block_content,
			'post_status'  => 'publish',
		)
	);

	if ( is_wp_error( $page_id ) ) {
		$result['errors'][] = $page_id->get_error_message();
		return $result;
	}

	$result['id']      = (int) $page_id;
	$result['created'] = true;

	ath_apply_rank_math_meta(
		(int) $page_id,
		ath_rank_math_kids_international_exhibition_defaults(),
		true
	);

	return $result;
}

/**
 * One-time ensure after deploy (without theme reactivation).
 */
function ath_maybe_ensure_kids_international_exhibition_page() {
	if ( (int) get_option( 'ath_kids_intl_exhibition_page_seeded', 0 ) >= 1 ) {
		return;
	}

	$out = ath_ensure_kids_international_exhibition_page();
	if ( empty( $out['errors'] ) ) {
		update_option( 'ath_kids_intl_exhibition_page_seeded', 1, false );
	}
}

add_action( 'init', 'ath_maybe_ensure_kids_international_exhibition_page', 20 );

/**
 * Migrate / overwrite landing page Gutenberg content from theme data.
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_kids_international_exhibition_page( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	$slug = ath_kids_international_exhibition_slug();
	$page = get_page_by_path( $slug );

	if ( ! $page ) {
		$ensured = ath_ensure_kids_international_exhibition_page();
		if ( ! empty( $ensured['errors'] ) ) {
			$results['errors'] = array_merge( $results['errors'], $ensured['errors'] );
			return $results;
		}
		if ( $ensured['id'] ) {
			$results['updated'][] = $slug;
		}
		return $results;
	}

	if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
		$results['skipped'][] = $slug;
		return $results;
	}

	$block_content = ath_kids_international_exhibition_to_gutenberg_blocks();
	if ( $block_content === '' ) {
		$results['errors'][] = 'No blocks built for kids international exhibition page.';
		return $results;
	}

	$data  = ath_kids_international_exhibition_data();
	$title = (string) ( $data['title'] ?? $page->post_title );

	wp_update_post(
		array(
			'ID'         => $page->ID,
			'post_title' => $title,
		),
		true
	);

	ath_prepare_gutenberg_page( $page->ID, $block_content );
	ath_apply_rank_math_meta(
		(int) $page->ID,
		ath_rank_math_kids_international_exhibition_defaults(),
		true
	);

	$results['updated'][] = $slug;

	return $results;
}

/**
 * Rank Math defaults for the landing page.
 *
 * @return array{title: string, description: string, focus_keyword: string}
 */
function ath_rank_math_kids_international_exhibition_defaults() {
	return array(
		'title'         => 'Kids International Art Exhibition Support | Art Tutor Hanoi',
		'description'   => 'Help your child submit artwork to international art exhibitions. Art Tutor Hanoi prepares files, artist bio, and open-call applications for kids in Tay Ho, Hanoi.',
		'focus_keyword' => 'kids international art exhibition',
	);
}
