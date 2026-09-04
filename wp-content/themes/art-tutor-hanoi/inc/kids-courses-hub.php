<?php
/**
 * Kids courses hub — native Gutenberg block builders.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Kids courses hub page (/kids-courses/).
 *
 * @return WP_Post|null
 */
function ath_kids_courses_hub_page() {
	$page = get_page_by_path( 'kids-courses' );

	return ( $page && $page->post_type === 'page' ) ? $page : null;
}

/**
 * Intro blocks (subtitle + tagline + paragraphs).
 *
 * @return string[]
 */
function ath_kids_courses_hub_intro_blocks() {
	return array(
		ath_gutenberg_paragraph( 'Choose what they\'d like to learn.', 'courses-hero__subtitle' ),
		ath_gutenberg_group(
			implode(
				"\n\n",
				array(
					ath_gutenberg_paragraph( 'Art class for Kids that teaches in English', 'courses-intro__tag' ),
					ath_gutenberg_paragraph_html(
						'<strong>Worried that your child spends too much time on screens or feels stressed with too many activities?</strong> '
						. 'Give them a calm and creative space to relax, draw, and express themselves through art.'
					),
					ath_gutenberg_paragraph(
						'A creative program for children to explore drawing, painting, storytelling, and contemporary art through hands-on studio practice in Tay Ho, Hanoi.'
					),
				)
			),
			'courses-intro__inner'
		),
	);
}

/**
 * One kids pathway as editable group blocks.
 *
 * @param array<string, mixed> $pathway Row from data/kids-pathways.php.
 */
function ath_kids_pathway_to_gutenberg_block( array $pathway ) {
	require_once ATH_THEME_DIR . '/config/images.php';
	require_once ATH_THEME_DIR . '/config/kids-courses.php';

	$bits = array();

	if ( ! empty( $pathway['num'] ) ) {
		$bits[] = ath_gutenberg_paragraph( (string) $pathway['num'], 'pathway__num' );
	}

	$bits[] = ath_gutenberg_heading( (string) $pathway['title'], 2, 'pathway__title' );
	$bits[] = ath_gutenberg_paragraph( (string) $pathway['description'], 'pathway__desc' );

	if ( ! empty( $pathway['image']['url'] ) ) {
		$img_src  = v2_img_url( (string) $pathway['image']['url'], 'pathway' );
		$img_size = v2_img_display_size( 'pathway' );
		$bits[]   = ath_gutenberg_image(
			$img_src,
			(string) ( $pathway['image']['alt'] ?? $pathway['title'] ),
			(int) $img_size['width'],
			(int) ( $img_size['height'] ?? 0 )
		);
	}

	foreach ( (array) ( $pathway['detail'] ?? array() ) as $paragraph ) {
		$bits[] = ath_gutenberg_paragraph( (string) $paragraph );
	}

	foreach ( (array) ( $pathway['courses'] ?? array() ) as $course ) {
		if ( empty( $course['slug'] ) || empty( $course['title'] ) ) {
			continue;
		}
		$bits[] = ath_gutenberg_paragraph_html(
			'<a href="' . esc_url( v2_kids_course_url( (string) $course['slug'] ) ) . '">' . esc_html( (string) $course['title'] ) . '</a>'
		);
	}

	return ath_gutenberg_group( implode( "\n\n", $bits ), 'pathway' );
}

/**
 * Schedule section — native Table blocks (editable in Gutenberg).
 */
function ath_kids_courses_schedule_block() {
	$multi_slot = '10:00 AM – 11:30 AM<br>2:00 PM – 3:30 PM<br>5:00 PM – 6:30 PM';

	$schedule_table = ath_gutenberg_table(
		array(
			array(
				array( 'content' => 'Monday', 'tag' => 'th' ),
				array( 'content' => 'Tuesday', 'tag' => 'th' ),
				array( 'content' => 'Wednesday', 'tag' => 'th' ),
				array( 'content' => 'Friday', 'tag' => 'th' ),
				array( 'content' => 'Saturday', 'tag' => 'th' ),
				array( 'content' => 'Sunday', 'tag' => 'th' ),
			),
		),
		array(
			array(
				array( 'content' => '2:00 PM – 3:30 PM' ),
				array( 'content' => $multi_slot ),
				array( 'content' => '2:00 PM – 3:30 PM' ),
				array( 'content' => $multi_slot ),
				array( 'content' => '5:00 PM – 6:30 PM' ),
				array( 'content' => $multi_slot ),
			),
		),
		'courses-schedule__table courses-schedule__table--schedule',
		'12-session course · 1.5 hours per session · Ages 5–11'
	);

	$pricing_table = ath_gutenberg_table(
		array(
			array(
				array( 'content' => 'Student', 'tag' => 'th' ),
				array( 'content' => 'USD', 'tag' => 'th' ),
				array( 'content' => 'VND', 'tag' => 'th' ),
			),
		),
		array(
			array(
				array( 'content' => 'First student' ),
				array( 'content' => '183' ),
				array( 'content' => '4,800,000' ),
			),
			array(
				array( 'content' => 'Additional student (20% off)' ),
				array( 'content' => '+ 146' ),
				array( 'content' => '+ 3,840,000' ),
			),
		),
		'courses-schedule__table courses-schedule__table--pricing'
	);

	$inner = implode(
		"\n\n",
		array(
			ath_gutenberg_heading( 'Class Schedule & Price', 2, 'courses-schedule__title' ),
			ath_gutenberg_group( $schedule_table, 'courses-schedule__table-wrap' ),
			ath_gutenberg_paragraph_html(
				'<strong>Tuition — 4,800,000 VND / 12 sessions</strong>',
				'courses-schedule__tuition'
			),
			ath_gutenberg_heading( 'Summer 2026 Offer', 3, 'courses-schedule__offer-title' ),
			ath_gutenberg_group( $pricing_table, 'courses-schedule__table-wrap' ),
		)
	);

	return ath_gutenberg_group(
		ath_gutenberg_group( $inner, 'courses-schedule__inner' ),
		'courses-schedule'
	);
}

/**
 * Benefits section — native List block (editable in Gutenberg).
 */
function ath_kids_courses_benefits_block() {
	$inner = implode(
		"\n\n",
		array(
			ath_gutenberg_heading( 'What\'s included', 2, 'courses-benefits__title' ),
			ath_gutenberg_list(
				array(
					'Small group classes with English-speaking teachers',
					'All materials included',
					'Guidance based on each child\'s level',
					'Certificate after course completion',
					'Artwork portfolio to bring home',
					'Optional guided visit to an art museum or exhibition',
					'Support submitting work to selected international kids art exhibitions',
				),
				'courses-benefits__list'
			),
		)
	);

	return ath_gutenberg_group(
		ath_gutenberg_group( $inner, 'courses-benefits__inner' ),
		'courses-benefits'
	);
}

/**
 * Teachers blurb — editable paragraphs.
 */
function ath_kids_courses_teachers_block() {
	$about_artists = ath_page_url( 'about-artists' );
	$inner         = implode(
		"\n\n",
		array(
			ath_gutenberg_heading( 'Who will teach', 2, 'courses-teachers__title' ),
			ath_gutenberg_paragraph_html(
				'Classes are taught by graduates from the <a href="https://mythuatvietnam.edu.vn/" target="_blank" rel="noopener noreferrer">Vietnam University of Fine Arts</a>, '
				. 'with expertise in both academic drawing and contemporary art practices.'
			),
			ath_gutenberg_paragraph_html(
				'<a href="' . esc_url( $about_artists ) . '">Meet our artists</a>'
			),
		)
	);

	return ath_gutenberg_group(
		ath_gutenberg_group( $inner, 'courses-teachers__inner' ),
		'courses-teachers'
	);
}

/**
 * Editable hub blocks after pathways shortcode (schedule, benefits, gallery, CTA).
 *
 * @return string[]
 */
function ath_kids_courses_hub_body_blocks() {
	return array(
		ath_kids_courses_schedule_block(),
		ath_kids_courses_benefits_block(),
		ath_kids_courses_teachers_block(),
		ath_kids_courses_gallery_blocks(),
	);
}

/**
 * Gallery section blocks.
 */
function ath_kids_courses_gallery_blocks() {
	$images = array(
		array(
			'url' => 'https://res.cloudinary.com/dftadlujq/images/w_600,h_400,c_fill,f_auto,q_auto/v1759744433/Arttutor_Pics/IMG_0434-1/IMG_0434-1.jpg',
			'alt' => 'Child sketching at Art Tutor Hanoi',
		),
		array(
			'url' => 'https://res.cloudinary.com/dftadlujq/images/w_600,h_400,c_fill,f_auto,q_auto/v1759373017/Arttutor_Pics/298/298.jpg',
			'alt' => 'Kids art class in the studio',
		),
		array(
			'url' => 'https://res.cloudinary.com/dftadlujq/images/w_600,h_400,c_fill,f_auto,q_auto/v1759373031/Arttutor_Pics/IMG_0426-EDIT/IMG_0426-EDIT.jpg',
			'alt' => 'Child creating artwork at Art Tutor Hanoi',
		),
	);

	$figures = array();
	foreach ( $images as $image ) {
		$figures[] = ath_gutenberg_group(
			ath_gutenberg_image( $image['url'], $image['alt'], 600, 400 ),
			'kids-gallery__item'
		);
	}

	return ath_gutenberg_group(
		ath_gutenberg_group( implode( "\n\n", $figures ), 'kids-gallery__inner' ),
		'kids-gallery'
	);
}

/**
 * CTA + crosslinks blocks.
 *
 * @return string[]
 */
function ath_kids_courses_cta_blocks() {
	$blocks = array(
		ath_gutenberg_group(
			ath_gutenberg_group(
				implode(
					"\n\n",
					array(
						ath_gutenberg_paragraph(
							'Try a trial class — teachers will observe your child\'s level and recommend a suitable group.',
							'courses-cta__text'
						),
						ath_gutenberg_paragraph_html(
							'Trial class: <s>400,000 VND</s> <strong>300,000 VND</strong> (Summer offer, 25% off)',
							'courses-cta__note'
						),
						ath_gutenberg_btn( 'Book a Kids Class', ath_book_url( 'kids' ), 'cta', 'courses-cta__btn' ),
						ath_gutenberg_paragraph_html(
							'<a href="' . esc_url( ath_page_url( 'kids-international-exhibition' ) ) . '">Kids international exhibition support</a> '
							. '<span aria-hidden="true">·</span> '
							. '<a href="' . esc_url( ath_page_url( 'pricing' ) ) . '">View pricing</a> '
							. '<span aria-hidden="true">·</span> '
							. '<a href="' . esc_url( ath_page_url( 'calendar' ) ) . '">Weekly calendar</a>',
							'courses-cta__links'
						),
					)
				),
				'courses-cta__inner'
			),
			'courses-cta'
		),
	);

	$links = array(
		'home'      => array( 'label' => 'Home', 'url' => ath_page_url( 'home' ) ),
		'courses'   => array( 'label' => 'Art classes', 'url' => ath_page_url( 'courses' ) ),
		'workshops' => array( 'label' => 'Workshops', 'url' => ath_page_url( 'workshops' ) ),
		'pricing'   => array( 'label' => 'Pricing', 'url' => ath_page_url( 'pricing' ) ),
		'book'      => array( 'label' => 'Book a trial', 'url' => ath_book_url( 'adult' ) ),
		'calendar'  => array( 'label' => 'Calendar', 'url' => ath_page_url( 'calendar' ) ),
	);

	$items = array();
	foreach ( $links as $key => $link ) {
		$items[] = '<a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . '</a>';
	}

	$blocks[] = ath_gutenberg_group(
		implode(
			"\n\n",
			array(
				ath_gutenberg_paragraph( 'Explore', 'commercial-crosslinks__label' ),
				ath_gutenberg_paragraph_html(
					implode( ' <span aria-hidden="true">·</span> ', $items ),
					'commercial-crosslinks__links'
				),
			)
		),
		'commercial-crosslinks'
	);

	return $blocks;
}

/**
 * Gutenberg markup for /kids-courses/ — intro + pathways shortcode + editable body blocks.
 */
function ath_kids_courses_hub_to_gutenberg_blocks() {
	$blocks   = ath_kids_courses_hub_intro_blocks();
	$blocks[] = "<!-- wp:shortcode -->\n[ath_kids_courses_hub]\n<!-- /wp:shortcode -->";
	$blocks   = array_merge( $blocks, ath_kids_courses_hub_body_blocks(), ath_kids_courses_cta_blocks() );

	return trim( implode( "\n\n", $blocks ) );
}

/**
 * Migrate /kids-courses/ hub to native Gutenberg blocks.
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_kids_courses_hub_page( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	$page = ath_kids_courses_hub_page();
	if ( ! $page ) {
		$results['errors'][] = 'kids-courses hub page not found';
		return $results;
	}

	if ( ! $overwrite && ath_page_has_editor_content( $page ) && get_post_meta( $page->ID, '_ath_gutenberg_migrated', true ) ) {
		$results['skipped'][] = 'kids-courses';
		return $results;
	}

	$block_content = ath_kids_courses_hub_to_gutenberg_blocks();
	if ( $block_content === '' ) {
		$results['errors'][] = 'No blocks built for kids-courses hub';
		return $results;
	}

	ath_prepare_gutenberg_page( $page->ID, $block_content );
	$results['updated'][] = 'kids-courses';

	return $results;
}

define( 'ATH_KIDS_COURSES_HUB_GUTENBERG_VERSION', '5' );

/**
 * Whether post content has flat pathway blocks (no accordion markup / shortcode).
 *
 * @param string $content Raw post content.
 */
function ath_kids_courses_has_flat_pathway_blocks( $content ) {
	if ( strpos( $content, '[ath_kids_courses_hub]' ) !== false ) {
		return false;
	}

	if ( strpos( $content, 'pathway__header' ) !== false ) {
		return false;
	}

	return strpos( $content, 'pathways-section' ) !== false
		|| strpos( $content, 'pathway__title' ) !== false
		|| strpos( $content, 'pathway__num' ) !== false
		|| preg_match( '/<!-- wp:group[^\n]*"className":"pathway"/', $content );
}

/**
 * @deprecated Use ath_kids_courses_has_flat_pathway_blocks().
 *
 * @param string $content Raw post content.
 */
function ath_kids_courses_has_flat_native_hub_blocks( $content ) {
	return ath_kids_courses_has_flat_pathway_blocks( $content )
		|| strpos( $content, 'courses-schedule' ) !== false;
}

/**
 * Pattern matching the first editable tail block after pathways.
 */
function ath_kids_courses_tail_start_pattern() {
	return '/\n\n(?=<!-- wp:group[^\n]*courses-schedule|<!-- wp:heading[^\n]*courses-schedule__title|<!-- wp:group[^\n]*courses-benefits|<!-- wp:group[^\n]*courses-teachers|<!-- wp:group[^\n]*kids-gallery|<!-- wp:group[^\n]*courses-cta|<!-- wp:group[^\n]*commercial-crosslinks)/s';
}

/**
 * Split intro / pathways / tail for hybrid frontend rendering.
 *
 * @param string $content Raw post content.
 * @return array{intro: string, tail: string}|null
 */
function ath_kids_courses_split_intro_pathways_tail( $content ) {
	$intro = ath_kids_courses_hybrid_extract_intro( $content );
	if ( $intro === '' || $intro === trim( $content ) ) {
		return null;
	}

	$rest = trim( substr( $content, strlen( $intro ) ) );
	if ( $rest === '' ) {
		return array(
			'intro' => $intro,
			'tail'  => '',
		);
	}

	if ( preg_match( '/^<!-- wp:group[^\n]*courses-schedule/s', $rest )
		|| preg_match( '/^<!-- wp:heading[^\n]*courses-schedule__title/s', $rest ) ) {
		return null;
	}

	if ( ! ath_kids_courses_has_flat_pathway_blocks( $content ) ) {
		return null;
	}

	if ( ! preg_match( ath_kids_courses_tail_start_pattern(), $rest, $match, PREG_OFFSET_CAPTURE ) ) {
		return array(
			'intro' => $intro,
			'tail'  => '',
		);
	}

	return array(
		'intro' => $intro,
		'tail'  => trim( substr( $rest, (int) $match[0][1] ) ),
	);
}

/**
 * Append default schedule/benefits blocks after hub shortcode (v4 → v5 upgrade).
 *
 * @param WP_Post $page Kids courses page.
 */
function ath_kids_courses_upgrade_v5_editor_tail( $page ) {
	$content = (string) $page->post_content;
	if ( strpos( $content, '[ath_kids_courses_hub]' ) === false || strpos( $content, 'courses-schedule' ) !== false ) {
		return false;
	}

	$tail_blocks = array_merge( ath_kids_courses_hub_body_blocks(), ath_kids_courses_cta_blocks() );
	$tail        = trim( implode( "\n\n", $tail_blocks ) );
	$replaced    = preg_replace(
		'/(<!-- wp:shortcode -->\s*\[ath_kids_courses_hub\]\s*<!-- \/wp:shortcode -->)/s',
		'$1' . "\n\n" . $tail,
		$content,
		1
	);

	if ( ! is_string( $replaced ) || $replaced === $content ) {
		return false;
	}

	wp_update_post(
		array(
			'ID'           => (int) $page->ID,
			'post_content' => $replaced,
		),
		true
	);

	return true;
}

/**
 * Intro portion of /kids-courses/ content (before pathways / schedule body).
 *
 * @param string $content Raw post content.
 */
function ath_kids_courses_hybrid_extract_intro( $content ) {
	$patterns = array(
		'/\n\n(?=<!-- wp:group[^\n]*pathways-section)/s',
		'/\n\n(?=<!-- wp:group[^\n]*courses-schedule)/s',
		'/\n\n(?=<!-- wp:heading[^\n]*courses-schedule__title)/s',
		'/\n\n(?=<!-- wp:group[^\n]*"className":"pathway")/s',
		'/\n\n(?=<!-- wp:paragraph[^\n]*pathway__num)/s',
	);

	foreach ( $patterns as $pattern ) {
		if ( preg_match( $pattern, $content, $match, PREG_OFFSET_CAPTURE ) ) {
			return trim( substr( $content, 0, $match[0][1] ) );
		}
	}

	return '';
}

/**
 * Auto-upgrade kids courses hub when theme version bumps (non-destructive).
 */
function ath_maybe_migrate_kids_courses_hub_page() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'ath_kids_courses_hub_gutenberg_version', '' ) === ATH_KIDS_COURSES_HUB_GUTENBERG_VERSION ) {
		return;
	}

	$page = ath_kids_courses_hub_page();
	if ( $page ) {
		ath_kids_courses_upgrade_v5_editor_tail( $page );
	}

	update_option( 'ath_kids_courses_hub_gutenberg_version', ATH_KIDS_COURSES_HUB_GUTENBERG_VERSION, false );
}

add_action( 'admin_init', 'ath_maybe_migrate_kids_courses_hub_page', 102 );

/**
 * Ensure migrated meta when a hub page is saved from the block editor.
 *
 * @param int $post_id Post ID.
 */
function ath_sync_kids_courses_hub_meta( $post_id ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'page' || $post->post_name !== 'kids-courses' ) {
		return;
	}

	if ( ! ath_page_has_editor_content( $post ) ) {
		return;
	}

	ath_mark_page_gutenberg( $post_id );
}

add_action( 'save_post_page', 'ath_sync_kids_courses_hub_meta', 20 );

/**
 * Swap flat pathway blocks for accordion shortcode; keep editor tail (schedule, pricing, …).
 *
 * @param string $content Post content.
 */
function ath_kids_courses_editor_pathways_content( $content ) {
	if ( ! is_page( 'kids-courses' ) || strpos( $content, '[ath_kids_courses_hub]' ) !== false ) {
		return $content;
	}

	if ( ! ath_kids_courses_has_flat_pathway_blocks( $content ) ) {
		return $content;
	}

	$split = ath_kids_courses_split_intro_pathways_tail( $content );
	if ( ! $split ) {
		return $content;
	}

	$shortcode = "<!-- wp:shortcode -->\n[ath_kids_courses_hub]\n<!-- /wp:shortcode -->";
	$output    = $split['intro'] . "\n\n" . $shortcode;

	if ( $split['tail'] !== '' ) {
		$output .= "\n\n" . $split['tail'];
	}

	return $output;
}

add_filter( 'the_content', 'ath_kids_courses_editor_pathways_content', 7 );
