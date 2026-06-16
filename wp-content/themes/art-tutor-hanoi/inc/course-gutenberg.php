<?php
/**
 * Build native Gutenberg blocks from adult course data.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

require_once ATH_THEME_DIR . '/inc/gutenberg-blocks.php';

/**
 * Convert course data to native Gutenberg block markup.
 *
 * @param array<string, mixed> $course Course row from data file.
 */
function ath_course_to_gutenberg_blocks( array $course ) {
	require_once ATH_THEME_DIR . '/config/images.php';

	$hub_url      = ! empty( $course['hub_url'] ) ? ath_resolve_url( $course['hub_url'] ) : ath_page_url( 'courses' );
	$hub_label    = ! empty( $course['hub_label'] ) ? (string) $course['hub_label'] : 'Courses';
	$pathway_href = $hub_url . '#' . rawurlencode( (string) $course['pathway_id'] );
	$book_url     = ath_book_url( ! empty( $course['book_tab'] ) ? (string) $course['book_tab'] : 'adult' );

	$blocks = array();

	$hero_src  = ath_course_hero_img_url( $course['image']['url'] );
	$hero_alt  = isset( $course['image']['alt'] ) ? (string) $course['image']['alt'] : (string) $course['title'];

	$hero_inner  = ath_gutenberg_group(
		ath_gutenberg_image( $hero_src, $hero_alt, 0, 0 ),
		'course-hero__media'
	);
	$hero_inner .= "\n\n";

	$content_bits = array();
	if ( ! empty( $course['pathway_title'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph_html(
			'<a href="' . esc_url( $pathway_href ) . '">' . esc_html( (string) $course['pathway_title'] ) . '</a>',
			'course-hero__pathway'
		);
	}
	$content_bits[] = ath_gutenberg_heading( (string) $course['title'], 1, 'course-hero__title' );
	if ( ! empty( $course['sessions'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph( (string) $course['sessions'], 'course-hero__sessions' );
	}
	if ( ! empty( $course['intro'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph( (string) $course['intro'], 'course-hero__intro' );
	}

	$hero_inner .= ath_gutenberg_group( implode( "\n\n", $content_bits ), 'course-hero__content' );
	$blocks[]     = ath_gutenberg_group( $hero_inner, 'course-hero' );

	if ( ! empty( $course['gallery'] ) && is_array( $course['gallery'] ) ) {
		$gallery_bits = array();
		foreach ( $course['gallery'] as $item ) {
			if ( empty( $item['url'] ) ) {
				continue;
			}
			$gallery_src = ath_course_gallery_img_url( $item['url'] );
			$alt         = ! empty( $item['caption'] )
				? (string) $item['caption'] . ' — ' . (string) $course['title']
				: 'Student work — ' . (string) $course['title'];
			$item_class  = 'course-gallery__item';
			if ( ! empty( $item['wide'] ) ) {
				$item_class .= ' course-gallery__item--wide';
			}
			$gallery_bits[] = ath_gutenberg_group(
				ath_gutenberg_image( $gallery_src, $alt, 0, 0 ),
				$item_class
			);
		}
		if ( $gallery_bits ) {
			$blocks[] = ath_gutenberg_group(
				ath_gutenberg_group( implode( "\n\n", $gallery_bits ), 'course-gallery__grid' ),
				'course-gallery'
			);
		}
	}

	$body_bits = array();
	if ( ! empty( $course['blocks'] ) && is_array( $course['blocks'] ) ) {
		$body_bits = array_merge( $body_bits, ath_gutenberg_course_block_sections( $course['blocks'] ) );
	}

	if ( ! empty( $course['sessions_breakdown'] ) && is_array( $course['sessions_breakdown'] ) ) {
		$session_bits = array(
			ath_gutenberg_heading( 'Session breakdown', 2, 'course-sessions__title' ),
		);
		foreach ( $course['sessions_breakdown'] as $session ) {
			if ( empty( $session['title'] ) && empty( $session['text'] ) ) {
				continue;
			}
			$session_inner = array();
			if ( ! empty( $session['title'] ) ) {
				$session_inner[] = ath_gutenberg_heading( (string) $session['title'], 3, 'course-session__title' );
			}
			if ( ! empty( $session['text'] ) ) {
				$session_inner[] = ath_gutenberg_paragraph( (string) $session['text'], 'course-session__text' );
			}
			$session_bits[] = ath_gutenberg_group( implode( "\n\n", $session_inner ), 'course-session' );
		}
		$body_bits[] = ath_gutenberg_group( implode( "\n\n", $session_bits ), 'course-sessions' );
	}

	if ( $body_bits ) {
		$blocks[] = ath_gutenberg_group( implode( "\n\n", $body_bits ), 'course-body' );
	}

	$cta_bits   = array();
	$cta_bits[] = ath_gutenberg_paragraph( 'Ready to start? Book an adult class or view pricing.', 'courses-cta__text' );
	$cta_bits[] = ath_gutenberg_btn( 'Book a Class', $book_url, 'cta', 'courses-cta__btn' );
	$cta_bits[] = ath_gutenberg_paragraph_html(
		'<a href="' . esc_url( $pathway_href ) . '">Back to ' . esc_html( (string) $course['pathway_title'] ) . '</a> '
		. '<span aria-hidden="true">·</span> <a href="' . esc_url( $hub_url ) . '">All ' . esc_html( strtolower( $hub_label ) ) . '</a>',
		'courses-cta__links'
	);

	$blocks[] = ath_gutenberg_group(
		ath_gutenberg_group( implode( "\n\n", $cta_bits ), 'courses-cta__inner' ),
		'courses-cta'
	);

	return trim( implode( "\n\n", $blocks ) );
}
