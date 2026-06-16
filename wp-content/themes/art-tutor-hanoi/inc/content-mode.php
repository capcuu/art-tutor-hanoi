<?php
/**
 * Content source mode — legacy PHP partials vs Gutenberg editor.
 *
 * Modes (option ath_content_mode, overridable via ATH_CONTENT_MODE constant):
 * - legacy   : always use theme partials / data files (pre-migration behaviour).
 * - hybrid   : use Gutenberg when the page has editor content; otherwise legacy.
 * - gutenberg: prefer editor content; empty pages still fall back to legacy.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Active content mode.
 *
 * @return string legacy|hybrid|gutenberg
 */
function ath_content_mode() {
	if ( defined( 'ATH_CONTENT_MODE' ) ) {
		return ATH_CONTENT_MODE;
	}

	$mode = get_option( 'ath_content_mode', 'hybrid' );

	if ( ! in_array( $mode, array( 'legacy', 'hybrid', 'gutenberg' ), true ) ) {
		return 'hybrid';
	}

	return $mode;
}

/**
 * Page slugs that must always use legacy PHP (dynamic logic).
 *
 * @return string[]
 */
function ath_legacy_only_page_slugs() {
	$slugs = array(
		'thank-you',        // ?from= query variants.
		'book',             // Tabbed Fluent forms.
		'weekly-calendar',  // Live week range + TablePress.
		'students-artworks', // WP post query grid.
		'link',             // admin_only link filtering.
	);

	return apply_filters( 'ath_legacy_only_page_slugs', $slugs );
}

/**
 * Routed pages that can be seeded / edited via Gutenberg.
 *
 * @return string[]
 */
function ath_gutenberg_migratable_page_slugs() {
	$slugs = array(
		'about',
		'pricing',
		'courses',
		'kids-courses',
	);

	return apply_filters( 'ath_gutenberg_migratable_page_slugs', $slugs );
}

/**
 * Whether a page slug is locked to legacy rendering.
 *
 * @param string $slug Page slug.
 */
function ath_is_legacy_only_page( $slug ) {
	return in_array( $slug, ath_legacy_only_page_slugs(), true );
}

/**
 * Whether a post has non-empty block editor content.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_page_has_editor_content( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	$content = trim( (string) $post->post_content );

	return $content !== '';
}

/**
 * Whether post content contains native Gutenberg block comments.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_page_has_block_editor_content( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	return strpos( (string) $post->post_content, '<!-- wp:' ) !== false;
}

/**
 * Whether the front page should render from the block editor.
 *
 * @param WP_Post|int|null $post Front page post.
 */
function ath_should_use_home_gutenberg( $post = null ) {
	if ( ath_content_mode() === 'legacy' ) {
		return false;
	}

	$post = get_post( $post );
	if ( ! $post || ! ath_page_has_editor_content( $post ) ) {
		return false;
	}

	if ( ath_content_mode() === 'gutenberg' ) {
		return true;
	}

	return (bool) get_post_meta( $post->ID, '_ath_gutenberg_migrated', true );
}

/**
 * Whether a page should render editor content instead of a theme partial.
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_should_use_page_gutenberg( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	if ( ath_content_mode() === 'legacy' ) {
		return false;
	}

	if ( ath_is_legacy_only_page( $post->post_name ) ) {
		return false;
	}

	if ( ! ath_page_has_editor_content( $post ) ) {
		return false;
	}

	if ( ath_content_mode() === 'gutenberg' ) {
		return true;
	}

	// Hybrid: migrated meta, or block editor content on a migratable routed page.
	if ( get_post_meta( $post->ID, '_ath_gutenberg_migrated', true ) ) {
		return true;
	}

	if ( ath_page_has_block_editor_content( $post ) && in_array( $post->post_name, ath_gutenberg_migratable_page_slugs(), true ) ) {
		return true;
	}

	// Workshop detail pages — editor is source of truth when content exists.
	if ( ath_is_workshop_page( $post ) && ath_page_has_editor_content( $post ) && ath_content_mode() !== 'legacy' ) {
		return true;
	}

	$partial = ath_routed_page_partial_path( $post->post_name );
	if ( $partial ) {
		return false;
	}

	return true;
}

/**
 * Migrated pages store full <main> HTML in a Custom HTML block.
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_page_uses_full_html_layout( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	return (bool) get_post_meta( $post->ID, '_ath_gutenberg_full_layout', true );
}

/**
 * When true, home export outputs a shortcode instead of baking gallery HTML.
 *
 * @param bool|null $set Toggle export mode.
 */
function ath_home_gallery_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode placeholder used during home migration export.
 */
function ath_home_gallery_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_gallery_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Render homepage gallery (shuffle, lazy-load, lightbox).
 */
function ath_render_home_gallery() {
	if ( ath_home_gallery_export_mode() ) {
		echo ath_home_gallery_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_gallery_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Capture gallery HTML as a string (for replacing baked-in static galleries).
 */
function ath_get_home_gallery_html( array $args = array() ) {
	return ath_render_home_gallery_block( $args );
}

/**
 * Replace static gallery HTML in migrated front-page content with live PHP output.
 *
 * @param string $content Post content.
 */
function ath_inject_dynamic_home_gallery( $content ) {
	if ( ! is_front_page() || ath_content_mode() === 'legacy' ) {
		return $content;
	}

	if ( ! ath_should_use_home_gutenberg() ) {
		return $content;
	}

	$gallery = ath_get_home_gallery_html();

	if ( preg_match( '/\[ath_home_gallery\b/', $content ) ) {
		$replaced = preg_replace(
			'/<!-- wp:shortcode -->\s*\[ath_home_gallery\b[^\]]*\]\s*<!-- \/wp:shortcode -->/s',
			$gallery,
			$content,
			1
		);
		if ( is_string( $replaced ) && $replaced !== $content ) {
			return $replaced;
		}

		$replaced = preg_replace(
			'/\[ath_home_gallery\b[^\]]*\]/',
			$gallery,
			$content,
			1
		);
		if ( is_string( $replaced ) && $replaced !== $content ) {
			return $replaced;
		}
	}

	if ( strpos( $content, 'gallery-section' ) === false ) {
		return $content;
	}

	$replaced = preg_replace(
		'/<section\s+class="gallery-section"[^>]*>.*?<\/section>\s*/s',
		$gallery,
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_inject_dynamic_home_gallery', 9 );

/**
 * Remove legacy home-intro block from front page (Sprint 2 SEO artefact).
 *
 * @param string $content Post content.
 */
function ath_strip_home_intro_from_front_page( $content ) {
	if ( ! is_front_page() ) {
		return $content;
	}

	$replaced = preg_replace(
		'/<!-- wp:shortcode -->\s*\[ath_home_intro\b[^\]]*\]\s*<!-- \/wp:shortcode -->\s*/s',
		'',
		$content
	);
	if ( is_string( $replaced ) ) {
		$content = $replaced;
	}

	$replaced = preg_replace(
		'/<section\s+class="home-intro"[^>]*>.*?<\/section>\s*/s',
		'',
		$content
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_strip_home_intro_from_front_page', 8 );

/**
 * Up to two intro paragraphs for /courses/ hero (first blocks in post content).
 *
 * @param string $content Raw post content.
 */
function ath_courses_hub_hero_blocks_from_content( $content ) {
	$content = (string) $content;

	if ( ! preg_match_all(
		'/<!-- wp:paragraph(?:\s[^\n]*)? -->.*?<!-- \/wp:paragraph -->/s',
		$content,
		$matches
	) ) {
		return '';
	}

	$blocks = array_slice( array_map( 'trim', $matches[0] ), 0, 2 );

	return implode( "\n\n", $blocks );
}

/**
 * Up to two intro paragraphs for /courses/ hero (before pathways / shortcode).
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_courses_hub_hero_blocks_markup( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_name !== 'courses' ) {
		return '';
	}

	return ath_courses_hub_hero_blocks_from_content( (string) $post->post_content );
}

/**
 * Apply hero CSS classes to rendered /courses/ intro paragraphs.
 *
 * @param string $html Rendered block HTML.
 */
function ath_courses_hub_style_hero_html( $html ) {
	$classes = array( 'courses-hero__subtitle', 'courses-hero__lede' );
	$index   = 0;

	return (string) preg_replace_callback(
		'/<p(\s[^>]*)?>/',
		function ( $match ) use ( &$index, $classes ) {
			if ( $index >= count( $classes ) ) {
				return $match[0];
			}

			$hero_class = $classes[ $index++ ];
			$attrs      = $match[1] ?? '';

			if ( preg_match( '/\bcourses-hero__/', $attrs ) ) {
				return $match[0];
			}

			if ( preg_match( '/class="([^"]*)"/', $attrs, $class_match ) ) {
				$new_class = $hero_class . ' ' . $class_match[1];

				return '<p' . preg_replace( '/class="[^"]*"/', 'class="' . $new_class . '"', $attrs, 1 ) . '>';
			}

			return '<p class="' . $hero_class . '"' . $attrs . '>';
		},
		$html,
		count( $classes )
	);
}

function ath_hub_hero_subtitle_block_markup( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	if ( $post->post_name === 'courses' ) {
		return ath_courses_hub_hero_blocks_markup( $post );
	}

	$content = (string) $post->post_content;

	if ( preg_match(
		'/<!-- wp:paragraph[^\n]*"className"\s*:\s*"courses-hero__subtitle"[^\n]*-->.*?<!-- \/wp:paragraph -->/s',
		$content,
		$match
	) ) {
		return trim( $match[0] );
	}

	if ( preg_match(
		'/<!-- wp:paragraph[^\n]*-->\\s*<p[^>]*\\bcourses-hero__subtitle\\b[^>]*>.*?<\\/p>\\s*<!-- \\/wp:paragraph -->/s',
		$content,
		$match
	) ) {
		return trim( $match[0] );
	}

	return '';
}

/**
 * Render hub hero subtitle from editor content (inside courses-hero__inner).
 *
 * @param WP_Post|int|null $post Page post.
 */
function ath_render_hub_hero_subtitle( $post = null ) {
	$post  = get_post( $post );
	$block = ath_hub_hero_subtitle_block_markup( $post );
	if ( $block === '' ) {
		return '';
	}

	$html = do_blocks( $block );

	if ( $post && $post->post_name === 'courses' ) {
		$html = ath_courses_hub_style_hero_html( $html );
	}

	return is_string( $html ) ? $html : '';
}

/**
 * Whether the current page uses courses hub layout (hero + bare the_content).
 */
function ath_page_uses_courses_hub_layout( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	return in_array( $post->post_name, array( 'courses', 'kids-courses' ), true );
}

/**
 * Keep hero intro out of main hub body (rendered in page-content.php hero).
 *
 * @param string $content Post content.
 */
function ath_strip_courses_hub_hero_from_content( $content, $post ) {
	$hero_blocks = ath_courses_hub_hero_blocks_markup( $post );
	$hero_count  = $hero_blocks === '' ? 0 : substr_count( $hero_blocks, '<!-- wp:paragraph' );

	foreach ( array( 'courses-hero__subtitle', 'courses-hero__lede' ) as $class ) {
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
			'/<!-- wp:paragraph[^\n]*"className"\s*:\s*"' . preg_quote( $class, '/' ) . '"[^\n]*-->.*?<!-- \/wp:paragraph -->\s*/s',
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

	return $content;
}

/**
 * Keep hero subtitle out of main hub body (rendered in page-content.php hero).
 *
 * @param string $content Post content.
 */
function ath_strip_hub_hero_subtitle_from_content( $content ) {
	if ( ! is_singular( 'page' ) || ! ath_should_use_page_gutenberg() ) {
		return $content;
	}

	if ( ! ath_page_uses_courses_hub_layout() ) {
		return $content;
	}

	$post = get_post();

	if ( $post && $post->post_name === 'courses' ) {
		return ath_strip_courses_hub_hero_from_content( $content, $post );
	}

	$replaced = preg_replace(
		'/^\s*<p[^>]*\bcourses-hero__subtitle\b[^>]*>.*?<\/p>\s*/is',
		'',
		$content,
		1
	);
	if ( is_string( $replaced ) ) {
		$content = $replaced;
	}

	$replaced = preg_replace(
		'/<!-- wp:paragraph[^\n]*"className"\s*:\s*"courses-hero__subtitle"[^\n]*-->.*?<!-- \/wp:paragraph -->\s*/s',
		'',
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_strip_hub_hero_subtitle_from_content', 12 );

/**
 * Capture homepage cards HTML as a string.
 *
 * @param array<string, mixed> $args Optional shortcode overrides.
 */
function ath_get_home_cards_html( array $args = array() ) {
	return ath_render_home_cards_block( $args );
}

/**
 * Replace static cards HTML in migrated front-page content with live PHP output.
 *
 * @param string $content Post content.
 */
function ath_inject_dynamic_home_cards( $content ) {
	if ( ! is_front_page() || ath_content_mode() === 'legacy' ) {
		return $content;
	}

	if ( ! ath_should_use_home_gutenberg() ) {
		return $content;
	}

	if ( preg_match( '/\[ath_home_cards\b/', $content ) ) {
		return $content;
	}

	if ( strpos( $content, 'cards-section' ) === false ) {
		return $content;
	}

	$cards    = ath_get_home_cards_html();
	$replaced = preg_replace(
		'/<section\s+class="cards-section"[^>]*>.*?<\/section>\s*/s',
		$cards,
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_inject_dynamic_home_cards', 9 );

/**
 * Capture homepage testimonials HTML as a string.
 *
 * @param array<string, mixed> $args Optional shortcode overrides.
 */
function ath_get_home_testimonials_html( array $args = array() ) {
	return ath_render_home_testimonials_block( $args );
}

/**
 * Replace static testimonials HTML in migrated front-page content with live PHP output.
 *
 * @param string $content Post content.
 */
function ath_inject_dynamic_home_testimonials( $content ) {
	if ( ! is_front_page() || ath_content_mode() === 'legacy' ) {
		return $content;
	}

	if ( ! ath_should_use_home_gutenberg() ) {
		return $content;
	}

	$testimonials = ath_get_home_testimonials_html();

	if ( preg_match( '/\[ath_home_testimonials\b/', $content ) ) {
		$replaced = preg_replace(
			'/<!-- wp:shortcode -->\s*\[ath_home_testimonials\b[^\]]*\]\s*<!-- \/wp:shortcode -->/s',
			$testimonials,
			$content,
			1
		);
		if ( is_string( $replaced ) && $replaced !== $content ) {
			return $replaced;
		}

		$replaced = preg_replace(
			'/\[ath_home_testimonials\b[^\]]*\]/',
			$testimonials,
			$content,
			1
		);
		if ( is_string( $replaced ) && $replaced !== $content ) {
			return $replaced;
		}
	}

	if ( strpos( $content, 'testimonials-section' ) === false ) {
		return $content;
	}

	$replaced = preg_replace(
		'/<section\s+class="testimonials-section"[^>]*>.*?<\/section>\s*/s',
		$testimonials,
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_inject_dynamic_home_testimonials', 9 );

/**
 * Capture homepage studio news HTML as a string.
 *
 * @param array<string, mixed> $args Optional shortcode overrides.
 */
function ath_get_home_news_html( array $args = array() ) {
	return ath_render_home_news_block( $args );
}

/**
 * Replace static or shortcode studio news on migrated front-page with live PHP output.
 *
 * @param string $content Post content.
 */
function ath_inject_dynamic_home_news( $content ) {
	if ( ! is_front_page() || ath_content_mode() === 'legacy' ) {
		return $content;
	}

	if ( ! ath_should_use_home_gutenberg() ) {
		return $content;
	}

	$news = ath_get_home_news_html();

	if ( preg_match( '/\[ath_home_news\b/', $content ) ) {
		$replaced = preg_replace(
			'/<!-- wp:shortcode -->\s*\[ath_home_news\b[^\]]*\]\s*<!-- \/wp:shortcode -->/s',
			$news,
			$content,
			1
		);
		if ( is_string( $replaced ) && $replaced !== $content ) {
			return $replaced;
		}

		$replaced = preg_replace(
			'/\[ath_home_news\b[^\]]*\]/',
			$news,
			$content,
			1
		);
		if ( is_string( $replaced ) && $replaced !== $content ) {
			return $replaced;
		}
	}

	if ( strpos( $content, 'news-section' ) === false ) {
		return $content;
	}

	$replaced = preg_replace(
		'/<section\s+class="news-section"[^>]*>.*?<\/section>\s*/s',
		$news,
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_inject_dynamic_home_news', 9 );
