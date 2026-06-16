<?php
/**
 * FAQ page — Gutenberg seed and migration.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * FAQ page data from theme file.
 *
 * @return array<string, mixed>
 */
function ath_faq_page_data() {
	static $data = null;

	if ( $data === null ) {
		$path = ATH_THEME_DIR . '/data/faq-page.php';
		$data = is_readable( $path ) ? require $path : array();
	}

	return is_array( $data ) ? $data : array();
}

/**
 * Resolve inline link placeholders in FAQ copy.
 *
 * Syntax: {key:Label} or {key}text{/key}
 *
 * @param string $text Raw text with placeholders.
 */
function ath_faq_inline_links( $text ) {
	$urls = array(
		'book'         => ath_book_url( 'adult' ),
		'book-kids'    => ath_book_url( 'kids' ),
		'pricing'      => ath_page_url( 'pricing' ),
		'courses'      => ath_page_url( 'courses' ),
		'kids-courses' => ath_page_url( 'kids-courses' ),
		'workshops'    => ath_page_url( 'workshops' ),
		'calendar'     => ath_page_url( 'calendar' ),
		'life-drawing' => ath_experience_url( 'life-drawing' ),
		'silk'         => ath_experience_url( 'silk-painting' ),
		'residency'    => ath_experience_url( 'artist-residency' ),
		'maps'         => 'https://maps.app.goo.gl/iNPAGGuyeTB5r3Qi8',
		'vnfam'        => 'https://vnfam.vn/en/',
		'vme'          => 'https://www.vme.org.vn/en',
	);

	$replace_pair = static function ( $matches ) use ( $urls ) {
		$key = $matches[1];
		if ( ! isset( $urls[ $key ] ) ) {
			return $matches[0];
		}

		$label = esc_html( $matches[2] );
		$url   = esc_url( $urls[ $key ] );

		return '<a href="' . $url . '">' . $label . '</a>';
	};

	$text = preg_replace_callback( '/\{(\w+):([^}]+)\}/', $replace_pair, $text );

	$text = preg_replace_callback(
		'/\{(\w+)\}(.+?)\{\/\1\}/s',
		$replace_pair,
		$text
	);

	return $text;
}

/**
 * Build footer link row (middle dot separated).
 *
 * @param string $footer Footer placeholder string.
 */
function ath_faq_footer_links_block( $footer ) {
	$footer = trim( (string) $footer );
	if ( $footer === '' ) {
		return '';
	}

	$parts = preg_split( '/\s·\s/', $footer );
	if ( ! is_array( $parts ) ) {
		return '';
	}

	$links = array();
	foreach ( $parts as $part ) {
		$part = trim( $part );
		if ( $part === '' ) {
			continue;
		}
		$html = ath_faq_inline_links( $part );
		if ( preg_match( '/<a\s/', $html ) ) {
			$links[] = $html;
		}
	}

	if ( ! $links ) {
		return '';
	}

	return ath_gutenberg_paragraph_html(
		implode( ' <span aria-hidden="true">·</span> ', $links ),
		'faq-section__footer'
	);
}

/**
 * One FAQ Q&A as native Gutenberg blocks.
 *
 * @param array<string, mixed> $row Question row from data file.
 * @return string[]
 */
function ath_faq_qa_blocks( array $row ) {
	$blocks = array();
	$skip_q = ! empty( $row['skip_q'] );

	if ( ! $skip_q && ! empty( $row['q'] ) ) {
		$blocks[] = ath_gutenberg_heading( (string) $row['q'], 3, 'faq-qa__question' );
	}

	if ( ! empty( $row['a_list'] ) && is_array( $row['a_list'] ) ) {
		if ( ! empty( $row['linkify_list'] ) ) {
			$list_inner = '';
			foreach ( $row['a_list'] as $item ) {
				$list_inner .= "<!-- wp:list-item -->\n<li>" . ath_faq_inline_links( (string) $item ) . "</li>\n<!-- /wp:list-item -->\n";
			}
			$blocks[] = "<!-- wp:list -->\n<ul class=\"wp-block-list faq-qa__list\">{$list_inner}</ul>\n<!-- /wp:list -->";
		} else {
			$blocks[] = ath_gutenberg_list( $row['a_list'], 'faq-qa__list' );
		}
	} elseif ( ! empty( $row['a'] ) ) {
		$blocks[] = ath_gutenberg_paragraph_html(
			ath_faq_inline_links( (string) $row['a'] ),
			'faq-qa__answer'
		);
	}

	return $blocks;
}

/**
 * Build native Gutenberg blocks for the /faq/ page.
 */
function ath_faq_to_gutenberg_blocks() {
	$data   = ath_faq_page_data();
	$blocks = array();

	foreach ( (array) ( $data['intro'] ?? array() ) as $paragraph ) {
		$blocks[] = ath_gutenberg_paragraph_html(
			ath_faq_inline_links( (string) $paragraph ),
			'faq-intro'
		);
	}

	foreach ( (array) ( $data['sections'] ?? array() ) as $section ) {
		$title = (string) ( $section['title'] ?? '' );

		if ( $title !== '' ) {
			$blocks[] = ath_gutenberg_heading( $title, 2, 'faq-section__title' );
		}

		if ( ! empty( $section['intro'] ) ) {
			$blocks[] = ath_gutenberg_paragraph_html(
				'<strong>' . esc_html( (string) $section['intro'] ) . '</strong>',
				'faq-section__intro'
			);
		}

		foreach ( (array) ( $section['qa'] ?? array() ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$blocks = array_merge( $blocks, ath_faq_qa_blocks( $row ) );
		}

		if ( ! empty( $section['footer'] ) ) {
			$footer = ath_faq_footer_links_block( (string) $section['footer'] );
			if ( $footer !== '' ) {
				$blocks[] = $footer;
			}
		}
	}

	return trim( implode( "\n\n", array_filter( $blocks ) ) );
}

/**
 * Migrate the FAQ page to native Gutenberg blocks.
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_faq_page( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	$page = get_page_by_path( 'faq' );
	if ( ! $page ) {
		$results['errors'][] = 'FAQ page not found.';
		return $results;
	}

	if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
		$results['skipped'][] = 'faq';
		return $results;
	}

	$block_content = ath_faq_to_gutenberg_blocks();
	if ( $block_content === '' ) {
		$results['errors'][] = 'No blocks built for FAQ page.';
		return $results;
	}

	ath_prepare_gutenberg_page( $page->ID, $block_content );
	$results['updated'][] = 'faq';

	return $results;
}
