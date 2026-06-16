<?php
/**
 * Sprint 3 — blog redirects, FAQ schema, archive robots, blog cross-links.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Blog slug → commercial URL targets (from data/seo-blog-redirects.php).
 *
 * @return array<string, string> slug => absolute URL
 */
function ath_seo_blog_redirect_urls() {
	static $urls = null;

	if ( $urls !== null ) {
		return $urls;
	}

	$path = ATH_THEME_DIR . '/data/seo-blog-redirects.php';
	$rows = is_readable( $path ) ? require $path : array();
	$urls = array();

	foreach ( $rows as $slug => $target ) {
		if ( ! is_array( $target ) ) {
			continue;
		}

		$type = (string) ( $target['type'] ?? '' );
		$key  = (string) ( $target['key'] ?? '' );

		if ( $type === 'experience' && $key !== '' ) {
			$urls[ $slug ] = ath_experience_url( $key );
		} elseif ( $type === 'page' && $key !== '' ) {
			$urls[ $slug ] = ath_page_url( $key );
		}
	}

	return apply_filters( 'ath_seo_blog_redirect_urls', $urls );
}

add_filter(
	'ath_legacy_redirect_paths',
	function ( $map ) {
		return array_merge( $map, ath_seo_blog_redirect_urls() );
	}
);

/**
 * FAQ Q&A rows for JSON-LD (plain text answers).
 *
 * @return array<int, array<string, mixed>>
 */
function ath_faq_schema_entities() {
	$data     = ath_faq_page_data();
	$entities = array();

	foreach ( (array) ( $data['sections'] ?? array() ) as $section ) {
		foreach ( (array) ( $section['qa'] ?? array() ) as $row ) {
			if ( ! is_array( $row ) || empty( $row['q'] ) ) {
				continue;
			}

			$answer = '';
			if ( ! empty( $row['a'] ) ) {
				$answer = (string) $row['a'];
			} elseif ( ! empty( $row['a_list'] ) && is_array( $row['a_list'] ) ) {
				$answer = implode( ' ', $row['a_list'] );
			}

			if ( $answer === '' ) {
				continue;
			}

			$answer = wp_strip_all_tags( ath_faq_inline_links( $answer ) );
			$answer = preg_replace( '/\s+/', ' ', trim( $answer ) );

			$entities[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( (string) $row['q'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}
	}

	return $entities;
}

/**
 * FAQPage schema on /faq/.
 *
 * @param array<string, mixed> $data   Schema graph.
 * @param object               $jsonld Rank Math JSON-LD instance.
 * @return array<string, mixed>
 */
function ath_rank_math_faq_schema( $data, $jsonld ) {
	if ( ! is_page( 'faq' ) ) {
		return $data;
	}

	$entities = ath_faq_schema_entities();
	if ( empty( $entities ) ) {
		return $data;
	}

	$data['FAQPage'] = array(
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);

	return $data;
}

add_filter( 'rank_math/json_ld', 'ath_rank_math_faq_schema', 98, 2 );

/**
 * Noindex thin archives (posts remain indexable; follow links).
 */
add_filter(
	'rank_math/frontend/robots',
	function ( $robots ) {
		if ( is_category() || is_tag() || is_author() || is_date() ) {
			$robots['index']  = 'noindex';
			$robots['follow'] = 'follow';
		}

		return $robots;
	},
	20
);

/**
 * Commercial cross-links at the end of blog posts (not tutorials hub / learner posts).
 *
 * @param string $content Post content.
 */
function ath_append_blog_commercial_links( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( ath_is_learner_artwork_post() ) {
		return $content;
	}

	ob_start();
	echo '<div class="blog-commercial-footer">';
	ath_render_commercial_crosslinks( 'blog' );
	echo '</div>';

	return $content . (string) ob_get_clean();
}

add_filter( 'the_content', 'ath_append_blog_commercial_links', 25 );

/**
 * Sprint 3 batch — FAQ migrate, FAQ meta, blog redirect map active on deploy.
 *
 * @param bool $overwrite FAQ page content overwrite.
 * @return array<string, array<string, string[]>>
 */
function ath_apply_seo_content_sprint( $overwrite = false ) {
	$out = array(
		'faq'     => ath_migrate_faq_page( $overwrite ),
		'faq_meta' => array( 'updated' => array(), 'skipped' => array(), 'missing' => array() ),
		'redirects' => array( 'updated' => array_keys( ath_seo_blog_redirect_urls() ), 'skipped' => array(), 'errors' => array() ),
	);

	$page = get_page_by_path( 'faq' );
	if ( $page ) {
		if ( ath_apply_rank_math_meta( (int) $page->ID, ath_rank_math_page_defaults()['faq'], $overwrite ) ) {
			$out['faq_meta']['updated'][] = 'faq';
		} else {
			$out['faq_meta']['skipped'][] = 'faq';
		}
	} else {
		$out['faq_meta']['missing'][] = 'faq';
	}

	if ( empty( $out['faq']['errors'] ) ) {
		update_option( 'ath_faq_gutenberg_version', '2', false );
	}

	update_option( 'ath_seo_content_sprint_version', '1', false );

	return $out;
}
