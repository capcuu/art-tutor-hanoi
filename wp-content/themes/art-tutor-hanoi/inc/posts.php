<?php
/**
 * Blog post helpers — learner artwork profiles.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Category slugs treated as student artwork profiles.
 *
 * @return string[]
 */
function ath_learner_artwork_category_slugs() {
	return apply_filters(
		'ath_learner_artwork_category_slugs',
		array(
			'learners-artworks',
			'learner-s-artworks',
			'learners-artwork',
		)
	);
}

/**
 * Whether a post belongs to the Learner's artworks category.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_learner_artwork_post( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'post' ) {
		return false;
	}

	foreach ( ath_learner_artwork_category_slugs() as $slug ) {
		if ( has_category( $slug, $post ) ) {
			return true;
		}
	}

	$categories = get_the_category( $post->ID );
	foreach ( $categories as $category ) {
		$name = strtolower( $category->name );
		if ( false !== strpos( $name, 'learner' ) && false !== strpos( $name, 'artwork' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Category term IDs for Learner's artworks.
 *
 * @return int[]
 */
function ath_learner_artwork_category_ids() {
	$ids = array();

	foreach ( ath_learner_artwork_category_slugs() as $slug ) {
		$term = get_category_by_slug( $slug );
		if ( $term ) {
			$ids[] = (int) $term->term_id;
		}
	}

	if ( empty( $ids ) ) {
		$categories = get_categories(
			array(
				'hide_empty' => false,
			)
		);

		foreach ( $categories as $category ) {
			$name = strtolower( $category->name );
			if ( false !== strpos( $name, 'learner' ) && false !== strpos( $name, 'artwork' ) ) {
				$ids[] = (int) $category->term_id;
			}
		}
	}

	return array_values( array_unique( array_map( 'intval', $ids ) ) );
}

/**
 * Learner artwork posts — latest comment first.
 *
 * @param int $limit Max posts; 0 = all.
 * @return WP_Post[]
 */
function ath_learner_artwork_posts( $limit = 0 ) {
	$limit   = max( 0, (int) apply_filters( 'ath_learner_artwork_posts_limit', $limit ) );
	$cat_ids = ath_learner_artwork_category_ids();

	if ( empty( $cat_ids ) ) {
		return array();
	}

	$cache_key = 'ath_learner_artworks_v2_' . ( $limit > 0 ? (string) $limit : 'all' );
	$cached    = get_transient( $cache_key );

	if ( is_array( $cached ) && ! empty( $cached ) ) {
		$posts = array_values( array_filter( array_map( 'get_post', $cached ) ) );
		if ( count( $posts ) === count( $cached ) ) {
			return $posts;
		}
		delete_transient( $cache_key );
	} elseif ( is_array( $cached ) && empty( $cached ) ) {
		return array();
	}

	global $wpdb;

	$placeholders = implode( ',', array_fill( 0, count( $cat_ids ), '%d' ) );
	$sql          = "
		SELECT p.ID
		FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
		INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			AND tt.taxonomy = 'category'
			AND tt.term_id IN ($placeholders)
		LEFT JOIN (
			SELECT comment_post_ID, MAX(comment_date_gmt) AS last_comment_at
			FROM {$wpdb->comments}
			WHERE comment_approved = '1'
				AND comment_type NOT IN ('pingback', 'trackback')
			GROUP BY comment_post_ID
		) lc ON lc.comment_post_ID = p.ID
		WHERE p.post_type = 'post'
			AND p.post_status = 'publish'
		ORDER BY (lc.last_comment_at IS NULL) ASC,
			lc.last_comment_at DESC,
			p.post_date_gmt DESC
	";

	if ( $limit > 0 ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- placeholders built from intval IDs.
		$post_ids = $wpdb->get_col( $wpdb->prepare( $sql . ' LIMIT %d', array_merge( $cat_ids, array( $limit ) ) ) );
	} else {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- placeholders built from intval IDs.
		$post_ids = $wpdb->get_col( $wpdb->prepare( $sql, $cat_ids ) );
	}

	if ( empty( $post_ids ) ) {
		set_transient( $cache_key, array(), MINUTE_IN_SECONDS );
		return array();
	}

	$post_ids = array_map( 'intval', $post_ids );
	set_transient( $cache_key, $post_ids, 5 * MINUTE_IN_SECONDS );

	return array_values( array_filter( array_map( 'get_post', $post_ids ) ) );
}

/**
 * Learner artwork posts for the community carousel — latest comment first.
 *
 * @param int $limit Max posts.
 * @return WP_Post[]
 */
function ath_community_student_posts( $limit = 12 ) {
	return ath_learner_artwork_posts( max( 1, (int) $limit ) );
}

/**
 * Carousel label, e.g. "Camila – South Africa".
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_community_student_label( $post = null ) {
	$name = ath_student_post_title( $post );
	$lead = ath_student_post_lead_text( $post );

	if ( $lead !== '' ) {
		return $name . ' – ' . $lead;
	}

	return $name;
}

/**
 * Featured image URL for a community card.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_community_student_image_url( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$thumb = get_the_post_thumbnail_url( $post, 'medium_large' );
	if ( $thumb ) {
		return $thumb;
	}

	$thumb = get_the_post_thumbnail_url( $post, 'medium' );
	if ( $thumb ) {
		return $thumb;
	}

	return '';
}

/**
 * Clear community carousel cache when comments or learner posts change.
 */
function ath_flush_community_student_cache() {
	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s",
			$wpdb->esc_like( '_transient_ath_community_students_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_ath_community_students_' ) . '%',
			$wpdb->esc_like( '_transient_ath_learner_artworks_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_ath_learner_artworks_' ) . '%'
		)
	);
}

add_action( 'comment_post', 'ath_flush_community_student_cache', 10, 0 );
add_action( 'wp_set_comment_status', 'ath_flush_community_student_cache', 10, 0 );
add_action( 'edit_comment', 'ath_flush_community_student_cache', 10, 0 );
add_action( 'delete_comment', 'ath_flush_community_student_cache', 10, 0 );
add_action( 'trashed_comment', 'ath_flush_community_student_cache', 10, 0 );
add_action( 'spammed_comment', 'ath_flush_community_student_cache', 10, 0 );
add_action( 'unspammed_comment', 'ath_flush_community_student_cache', 10, 0 );
add_action( 'save_post_post', 'ath_flush_community_student_cache', 10, 0 );

add_action(
	'init',
	function () {
		if ( get_option( 'ath_community_cache_v' ) === '2' ) {
			return;
		}
		ath_flush_community_student_cache();
		update_option( 'ath_community_cache_v', '2' );
	},
	1
);

/**
 * Students' artworks hub page URL.
 */
function ath_students_artworks_url() {
	$page = get_page_by_path( 'students-artworks' );
	if ( $page ) {
		return get_permalink( $page );
	}

	return home_url( '/students-artworks/' );
}

/**
 * Is this the students' artworks hub page?
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_students_artworks_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	if ( $post->post_name === 'students-artworks' ) {
		return true;
	}

	return get_page_template_slug( $post->ID ) === 'page-templates/students-artworks.php';
}

/**
 * Create or update the Students' Artworks hub page.
 */
function ath_ensure_students_artworks_page() {
	$template = 'page-templates/students-artworks.php';
	$page     = get_page_by_path( 'students-artworks' );

	if ( ! $page ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => 'Students\' Artworks',
				'post_name'    => 'students-artworks',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
			$page = get_post( $page_id );
		}
	}

	if ( $page ) {
		update_post_meta( $page->ID, '_wp_page_template', $template );
	}
}

add_action( 'after_switch_theme', 'ath_ensure_students_artworks_page' );

add_action(
	'init',
	function () {
		if ( get_option( 'ath_students_artworks_page_setup' ) === '1' ) {
			return;
		}
		ath_ensure_students_artworks_page();
		update_option( 'ath_students_artworks_page_setup', '1' );
	},
	5
);

/**
 * Display title for a student profile post.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_student_post_title( $post = null ) {
	$post  = get_post( $post );
	$title = $post ? get_the_title( $post ) : get_the_title();
	$title = preg_replace( '/\s*[-–|]\s*art tutor.*$/i', '', $title );

	return trim( $title );
}

/**
 * Learner artwork category link (first matching category).
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_learner_artwork_category_link( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}

	$categories = get_the_category( $post->ID );
	foreach ( $categories as $category ) {
		$name = strtolower( $category->name );
		if ( false !== strpos( $name, 'learner' ) && false !== strpos( $name, 'artwork' ) ) {
			return array(
				'name' => $category->name,
				'url'  => get_category_link( $category->term_id ),
			);
		}
	}

	foreach ( $categories as $category ) {
		if ( in_array( $category->slug, ath_learner_artwork_category_slugs(), true ) ) {
			return array(
				'name' => $category->name,
				'url'  => get_category_link( $category->term_id ),
			);
		}
	}

	return null;
}

/**
 * Short lead line (country / bio) — excerpt or first paragraph.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_student_post_lead_text( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	if ( has_excerpt( $post ) ) {
		return trim( wp_strip_all_tags( get_the_excerpt( $post ) ) );
	}

	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( 'core/paragraph' !== ( $block['blockName'] ?? '' ) ) {
			continue;
		}

		$text = trim( wp_strip_all_tags( $block['innerHTML'] ?? '' ) );
		if ( $text !== '' ) {
			return $text;
		}
	}

	return '';
}

/**
 * Post body with the lead paragraph removed (shown in hero instead).
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_student_post_body_html( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_content === '' ) {
		return '';
	}

	if ( has_excerpt( $post ) ) {
		return apply_filters( 'the_content', $post->post_content );
	}

	$blocks     = parse_blocks( $post->post_content );
	$skip_lead  = false;
	$filtered   = array();

	foreach ( $blocks as $block ) {
		if ( ! $skip_lead && 'core/paragraph' === ( $block['blockName'] ?? '' ) ) {
			$text = trim( wp_strip_all_tags( $block['innerHTML'] ?? '' ) );
			if ( $text !== '' ) {
				$skip_lead = true;
				continue;
			}
		}

		$filtered[] = $block;
	}

	if ( ! $skip_lead ) {
		return apply_filters( 'the_content', $post->post_content );
	}

	$html = '';
	foreach ( $filtered as $block ) {
		$html .= render_block( $block );
	}

	return apply_filters( 'the_content', $html );
}

/**
 * Split comment HTML — all figures left, text right (supports multiple images).
 *
 * @param string $html Comment content HTML.
 */
function ath_format_student_comment_html( $html ) {
	if ( ! preg_match( '/<figure[\s>]/i', $html ) ) {
		return $html;
	}

	preg_match_all( '#<figure\b[^>]*>.*?</figure>#is', $html, $matches );
	$figures = $matches[0] ?? array();

	if ( empty( $figures ) ) {
		return $html;
	}

	$text = trim( preg_replace( '#<figure\b[^>]*>.*?</figure>#is', '', $html ) );

	$out  = '<div class="student-comment-layout">';
	$out .= '<div class="student-comment-layout__media">' . implode( "\n", $figures ) . '</div>';
	$out .= '<div class="student-comment-layout__text">' . $text . '</div>';
	$out .= '</div>';

	return $out;
}

add_filter(
	'comment_text',
	function ( $text, $comment ) {
		unset( $comment );
		if ( ! is_singular( 'post' ) || ! ath_is_learner_artwork_post() ) {
			return $text;
		}

		return ath_format_student_comment_html( $text );
	},
	20,
	2
);

add_filter(
	'template_include',
	function ( $template ) {
		if ( is_singular( 'post' ) && ath_is_learner_artwork_post() ) {
			$learner_template = ATH_THEME_DIR . '/templates/single-learner-artwork.php';
			if ( is_readable( $learner_template ) ) {
				return $learner_template;
			}
		}

		return $template;
	},
	99
);
