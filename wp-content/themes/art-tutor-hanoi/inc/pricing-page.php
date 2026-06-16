<?php
/**
 * Pricing page — Gutenberg intro + accordion shortcode.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

define( 'ATH_PRICING_GUTENBERG_VERSION', '1' );

/**
 * Pricing page (/pricing/).
 *
 * @return WP_Post|null
 */
function ath_pricing_page() {
	$page = get_page_by_path( 'pricing' );

	return ( $page && $page->post_type === 'page' ) ? $page : null;
}

/**
 * Pricing seed data.
 *
 * @return array<string, mixed>
 */
function ath_pricing_data() {
	static $data = null;

	if ( $data === null ) {
		$data = require ATH_THEME_DIR . '/data/pricing-programs.php';
	}

	return $data;
}

/**
 * Intro + FAQ blocks for /pricing/ editor seed.
 *
 * @return string[]
 */
function ath_pricing_intro_blocks() {
	$data   = ath_pricing_data();
	$blocks = array();

	foreach ( (array) ( $data['intro'] ?? array() ) as $index => $paragraph ) {
		$class    = $index === 0 ? 'pricing-hero__text pricing-hero__text--lead' : 'pricing-hero__text';
		$blocks[] = ath_gutenberg_paragraph( (string) $paragraph, $class );
	}

	$faq_url = ath_resolve_url( (string) ( $data['faq_url'] ?? 'faq' ) );
	$blocks[] = ath_gutenberg_paragraph_html(
		'Questions about course validity or scheduling? '
		. '<a href="' . esc_url( $faq_url ) . '">' . esc_html( (string) ( $data['faq_label'] ?? 'FAQ' ) ) . '</a>',
		'pricing-hero__faq'
	);

	return $blocks;
}

/**
 * Gutenberg block markup for /pricing/ page.
 */
function ath_pricing_to_gutenberg_blocks() {
	$blocks   = ath_pricing_intro_blocks();
	$blocks[] = "<!-- wp:shortcode -->\n[ath_pricing]\n<!-- /wp:shortcode -->";

	return trim( implode( "\n\n", $blocks ) );
}

/**
 * Migrate /pricing/ to intro blocks + accordion shortcode.
 *
 * @param bool $overwrite Replace existing editor content.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_pricing_page( $overwrite = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	$page = ath_pricing_page();
	if ( ! $page ) {
		$results['errors'][] = 'pricing page not found';
		return $results;
	}

	if ( ! $overwrite && ath_page_has_editor_content( $page ) ) {
		$results['skipped'][] = 'pricing';
		return $results;
	}

	$block_content = ath_pricing_to_gutenberg_blocks();
	if ( $block_content === '' ) {
		$results['errors'][] = 'No blocks built for pricing page';
		return $results;
	}

	ath_prepare_gutenberg_page( $page->ID, $block_content );
	$results['updated'][] = 'pricing';

	return $results;
}

/**
 * Whether post content is missing accordion markup / shortcode.
 *
 * @param string $content Raw post content.
 */
function ath_pricing_needs_shortcode_swap( $content ) {
	if ( strpos( $content, 'pricing-list' ) !== false || strpos( $content, '[ath_pricing]' ) !== false ) {
		return false;
	}

	return (bool) preg_match(
		'/Trial Art Class|Workshops|Adult Classes|Kids Classes|Artist Residency|pricing-row|pricing-programs/s',
		$content
	);
}

/**
 * Intro block markup from post content (paragraphs before pricing sections).
 *
 * @param string $content Raw post content.
 */
function ath_pricing_intro_blocks_from_content( $content ) {
	$content = (string) $content;

	if ( preg_match_all(
		'/<!-- wp:paragraph(?:\s[^\n]*)? -->.*?<!-- \/wp:paragraph -->/s',
		$content,
		$matches
	) ) {
		$blocks = array();
		foreach ( $matches[0] as $block ) {
			if ( preg_match( '/pricing-row|pathway--pricing|Trial Art Class|Workshops|Adult Classes|Session fee/s', $block ) ) {
				break;
			}
			if ( preg_match( '/<!-- wp:heading/s', $block ) ) {
				break;
			}
			$blocks[] = trim( $block );
			if ( count( $blocks ) >= 3 ) {
				break;
			}
		}

		if ( $blocks !== array() ) {
			return implode( "\n\n", $blocks );
		}
	}

	return implode( "\n\n", ath_pricing_intro_blocks() );
}

/**
 * Swap broken flat pricing body for accordion shortcode.
 *
 * @param string $content Post content.
 */
function ath_pricing_editor_content( $content ) {
	if ( ! is_page( 'pricing' ) || ! ath_pricing_needs_shortcode_swap( $content ) ) {
		return $content;
	}

	$intro     = ath_pricing_intro_blocks_from_content( $content );
	$shortcode = "<!-- wp:shortcode -->\n[ath_pricing]\n<!-- /wp:shortcode -->";

	return $intro . "\n\n" . $shortcode;
}

add_filter( 'the_content', 'ath_pricing_editor_content', 7 );

/**
 * Auto-upgrade pricing page when theme version bumps.
 */
function ath_maybe_migrate_pricing_page() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'ath_pricing_gutenberg_version', '' ) === ATH_PRICING_GUTENBERG_VERSION ) {
		return;
	}

	$page = ath_pricing_page();
	if ( $page && ath_pricing_needs_shortcode_swap( (string) $page->post_content ) ) {
		ath_migrate_pricing_page( true );
	}

	update_option( 'ath_pricing_gutenberg_version', ATH_PRICING_GUTENBERG_VERSION, false );
}

add_action( 'admin_init', 'ath_maybe_migrate_pricing_page', 102 );

/**
 * Ensure migrated meta when pricing page is saved from the block editor.
 *
 * @param int $post_id Post ID.
 */
function ath_sync_pricing_page_meta( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	$page = get_post( $post_id );
	if ( ! $page || $page->post_type !== 'page' || $page->post_name !== 'pricing' ) {
		return;
	}

	if ( strpos( (string) $page->post_content, '[ath_pricing]' ) === false ) {
		return;
	}

	ath_mark_page_gutenberg( $post_id );
}

add_action( 'save_post_page', 'ath_sync_pricing_page_meta', 20 );

/**
 * Whether the current page uses pricing layout (hero + accordion body).
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_page_uses_pricing_layout( $post = null ) {
	$post = get_post( $post );

	return $post && $post->post_type === 'page' && $post->post_name === 'pricing';
}

/**
 * Intro blocks for pricing hero (from editor content).
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_pricing_hero_blocks_markup( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_name !== 'pricing' ) {
		return '';
	}

	$content = (string) $post->post_content;

	if ( preg_match_all(
		'/<!-- wp:paragraph(?:\s[^\n]*)? -->.*?<!-- \/wp:paragraph -->/s',
		$content,
		$matches
	) ) {
		$blocks = array();
		foreach ( $matches[0] as $block ) {
			if ( preg_match( '/pricing-row|pathway--pricing|\[ath_pricing\]/s', $block ) ) {
				break;
			}
			$blocks[] = trim( $block );
			if ( count( $blocks ) >= 3 ) {
				break;
			}
		}

		if ( $blocks !== array() ) {
			return implode( "\n\n", $blocks );
		}
	}

	return implode( "\n\n", ath_pricing_intro_blocks() );
}

/**
 * Render pricing hero intro from editor blocks.
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_render_pricing_hero_intro( $post = null ) {
	$block = ath_pricing_hero_blocks_markup( $post );
	if ( $block === '' ) {
		return '';
	}

	$html = do_blocks( $block );

	return is_string( $html ) ? $html : '';
}

/**
 * Keep pricing hero intro out of main body (rendered in page-content.php hero).
 *
 * @param string $content Post content.
 */
function ath_strip_pricing_hero_from_content( $content ) {
	if ( ! is_singular( 'page' ) || ! ath_should_use_page_gutenberg() || ! ath_page_uses_pricing_layout() ) {
		return $content;
	}

	$hero_blocks = ath_pricing_hero_blocks_markup();
	$hero_count  = $hero_blocks === '' ? 0 : substr_count( $hero_blocks, '<!-- wp:paragraph' );

	foreach ( array( 'pricing-hero__text', 'pricing-hero__text--lead', 'pricing-hero__faq' ) as $class ) {
		$replaced = preg_replace(
			'/^\s*<p[^>]*\b' . preg_quote( $class, '/' ) . '\b[^>]*>.*?<\/p>\s*/is',
			'',
			$content,
			1
		);
		if ( is_string( $replaced ) ) {
			$content = $replaced;
		}

		$replaced = preg_replace(
			'/<!-- wp:paragraph[^\n]*"className"\s*:\s*"[^"]*\b' . preg_quote( $class, '/' ) . '\b[^"]*"[^\n]*-->.*?<!-- \/wp:paragraph -->\s*/s',
			'',
			$content,
			1
		);
		if ( is_string( $replaced ) ) {
			$content = $replaced;
		}
	}

	for ( $i = 0; $i < $hero_count; $i++ ) {
		$replaced = preg_replace(
			'/^\s*<p class="wp-block-paragraph"[^>]*>.*?<\/p>\s*/is',
			'',
			$content,
			1
		);
		if ( ! is_string( $replaced ) || $replaced === $content ) {
			break;
		}
		$content = $replaced;
	}

	// Remove duplicate page title heading if editors added one.
	$replaced = preg_replace(
		'/^\s*<h1[^>]*>.*?<\/h1>\s*/is',
		'',
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_strip_pricing_hero_from_content', 12 );
