<?php
/**
 * On-page SEO — H1 overrides, commercial cross-links, hub migrations.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * SEO H1 overrides (visible heading; Rank Math title stays separate).
 *
 * @return array<string, string> slug => H1
 */
function ath_page_seo_h1_map() {
	return array(
		'workshops'                         => 'Art Workshops in Hanoi',
		'courses'                           => 'Art Classes in Hanoi',
		'kids-courses'                      => 'Kids Art Classes in Hanoi',
		'pricing'                           => 'Art Class Pricing in Hanoi',
		'book'                              => 'Book a Trial Art Class',
		'kids-international-art-exhibition' => 'Kids International Art Exhibition Support',
	);
}

/**
 * H1 for page.php shell (falls back to post title).
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_page_seo_h1( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$map = ath_page_seo_h1_map();

	if ( isset( $map[ $post->post_name ] ) ) {
		return $map[ $post->post_name ];
	}

	return ath_page_display_title( $post );
}

/**
 * Intro Gutenberg blocks for /courses/ hub (above pathways shortcode).
 */
function ath_courses_hub_intro_blocks() {
	$workshops = ath_page_url( 'workshops' );
	$book      = ath_book_url( 'adult' );

	return array(
		ath_gutenberg_paragraph(
			'Weekly English-speaking art classes in Tay Ho, Hanoi — structured programs for adults from drawing foundations to colour, painting, life drawing, and portfolio preparation.',
			'courses-hero__subtitle'
		),
		ath_gutenberg_paragraph_html(
			'New to the studio? <a href="' . esc_url( $book ) . '">Book a trial class</a> or browse our '
			. '<a href="' . esc_url( $workshops ) . '">one-day art workshops</a> for travelers.',
			'courses-hero__lede'
		),
	);
}

/**
 * Gutenberg block markup for /courses/ hub page.
 */
function ath_courses_hub_to_gutenberg_blocks() {
	$blocks   = ath_courses_hub_intro_blocks();
	$blocks[] = "<!-- wp:shortcode -->\n[ath_courses_hub]\n<!-- /wp:shortcode -->";

	return trim( implode( "\n\n", $blocks ) );
}

/**
 * Migrate /courses/ hub to Gutenberg (intro + pathways shortcode).
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_courses_hub_page( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	$page = ath_courses_hub_page();
	if ( ! $page ) {
		$results['errors'][] = 'courses hub page not found';
		return $results;
	}

	if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
		$results['skipped'][] = 'courses';
		return $results;
	}

	$block_content = ath_courses_hub_to_gutenberg_blocks();
	if ( $block_content === '' ) {
		$results['errors'][] = 'No blocks built for courses hub';
		return $results;
	}

	ath_prepare_gutenberg_page( $page->ID, $block_content );
	$results['updated'][] = 'courses';

	return $results;
}

/**
 * Rank Math defaults for workshop and course detail pages.
 *
 * @return array<string, array{title: string, description: string, focus_keyword?: string}>
 */
function ath_rank_math_detail_page_defaults() {
	$defaults = array();

	foreach ( ath_workshop_slugs() as $slug ) {
		$experience = v2_experience_by_slug( $slug );
		if ( ! $experience ) {
			continue;
		}

		$title = (string) ( $experience['title'] ?? $slug );
		$intro = (string) ( $experience['intro'] ?? $experience['desc'] ?? '' );
		if ( strlen( $intro ) > 155 ) {
			$intro = wp_trim_words( $intro, 22, '…' );
		}

		$defaults[ 'workshops/' . $slug ] = array(
			'title'          => $title . ' Workshop in Hanoi | Art Tutor Hanoi',
			'description'    => $intro !== '' ? $intro : $title . ' at Art Tutor Hanoi studio in Tay Ho. Book online.',
			'focus_keyword'  => strtolower( $title ) . ' hanoi',
		);
	}

	foreach ( ath_adult_course_slugs() as $slug ) {
		$course = v2_course_by_slug( $slug );
		if ( ! $course ) {
			continue;
		}

		$title = (string) ( $course['title'] ?? $slug );
		$intro = (string) ( $course['intro'] ?? '' );
		if ( strlen( $intro ) > 155 ) {
			$intro = wp_trim_words( $intro, 22, '…' );
		}

		$defaults[ 'courses/' . $slug ] = array(
			'title'          => $title . ' Course | Adult Art Classes Hanoi',
			'description'    => $intro !== '' ? $intro : 'Adult ' . $title . ' course at Art Tutor Hanoi — English-friendly instruction in Tay Ho.',
			'focus_keyword'  => strtolower( $title ) . ' art class hanoi',
		);
	}

	return $defaults;
}

/**
 * Seed Rank Math meta on workshop + course detail pages.
 *
 * @param bool $overwrite Replace existing meta.
 * @return array{updated: string[], skipped: string[], missing: string[]}
 */
function ath_seed_rank_math_detail_meta( $overwrite = false ) {
	$results = array(
		'updated'  => array(),
		'skipped'  => array(),
		'missing'  => array(),
	);

	foreach ( ath_rank_math_detail_page_defaults() as $path => $meta ) {
		$page = get_page_by_path( $path );
		if ( ! $page ) {
			$results['missing'][] = $path;
			continue;
		}

		if ( ath_apply_rank_math_meta( (int) $page->ID, $meta, $overwrite ) ) {
			$results['updated'][] = $path;
		} else {
			$results['skipped'][] = $path;
		}
	}

	return $results;
}

/**
 * Sprint 2 batch — refresh hubs, homepage shortcodes, detail meta.
 *
 * @param bool $overwrite Hubs + Rank Math detail pages.
 * @return array<string, array<string, string[]>>
 */
function ath_apply_seo_onpage_sprint( $overwrite = false ) {
	$out = array(
		'workshops_hub'     => ath_migrate_workshops_hub_page( $overwrite ),
		'courses_hub'       => ath_migrate_courses_hub_page( $overwrite ),
		'kids_courses_hub'  => ath_migrate_kids_courses_hub_page( $overwrite ),
		'home'              => ath_migrate_homepage_to_gutenberg( true ),
		'detail_meta'       => ath_seed_rank_math_detail_meta( $overwrite ),
	);

	if ( empty( $out['workshops_hub']['errors'] ) ) {
		update_option( 'ath_workshop_gutenberg_version', '5', false );
	}

	if ( empty( $out['courses_hub']['errors'] ) ) {
		update_option( 'ath_courses_hub_gutenberg_version', ATH_COURSES_HUB_GUTENBERG_VERSION, false );
	}

	if ( empty( $out['kids_courses_hub']['errors'] ) ) {
		update_option( 'ath_kids_courses_hub_gutenberg_version', ATH_KIDS_COURSES_HUB_GUTENBERG_VERSION, false );
	}

	if ( empty( $out['home']['errors'] ) ) {
		update_option( 'ath_homepage_shortcodes_version', '8', false );
	}

	return $out;
}

/**
 * Internal link nav for commercial pages (skips current section key).
 *
 * @param string $current home|courses|workshops|pricing|book|kids-courses|calendar
 */
function ath_render_commercial_crosslinks( $current = '' ) {
	$current = sanitize_key( (string) $current );

	$links = array(
		'home'         => array( 'label' => 'Home', 'url' => ath_page_url( 'home' ) ),
		'courses'      => array( 'label' => 'Art classes', 'url' => ath_page_url( 'courses' ) ),
		'workshops'    => array( 'label' => 'Workshops', 'url' => ath_page_url( 'workshops' ) ),
		'kids-courses' => array( 'label' => 'Kids classes', 'url' => ath_page_url( 'kids-courses' ) ),
		'kids-international-exhibition' => array(
			'label' => 'Kids exhibition support',
			'url'   => ath_page_url( 'kids-international-exhibition' ),
		),
		'pricing'      => array( 'label' => 'Pricing', 'url' => ath_page_url( 'pricing' ) ),
		'book'         => array( 'label' => 'Book a trial', 'url' => ath_book_url( 'adult' ) ),
		'calendar'     => array( 'label' => 'Calendar', 'url' => ath_page_url( 'calendar' ) ),
	);

	$items = array();
	foreach ( $links as $key => $link ) {
		if ( $key === $current ) {
			continue;
		}
		$items[] = '<a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . '</a>';
	}

	if ( empty( $items ) ) {
		return;
	}

	echo '<nav class="commercial-crosslinks" aria-label="Explore more">';
	echo '<p class="commercial-crosslinks__label">Explore</p>';
	echo '<p class="commercial-crosslinks__links">' . implode( ' <span aria-hidden="true">·</span> ', $items ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</nav>';
}
