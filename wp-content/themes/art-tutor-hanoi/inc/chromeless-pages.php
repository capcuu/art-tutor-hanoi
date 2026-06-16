<?php
/**
 * Pages that render without site header, footer, or chat widgets.
 *
 * Used for the 2026 exhibition mini-site (self-contained Gutenberg layout).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Page slugs without site chrome.
 *
 * @return string[]
 */
function ath_chromeless_page_slugs() {
	$slugs = array(
		'2026-exhibition-goihe',
	);

	return apply_filters( 'ath_chromeless_page_slugs', $slugs );
}

/**
 * Whether a page slug is chromeless (exhibition mini-site).
 *
 * @param string $slug Page slug.
 */
function ath_is_chromeless_page_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );

	if ( $slug === '' ) {
		return false;
	}

	if ( in_array( $slug, ath_chromeless_page_slugs(), true ) ) {
		return true;
	}

	return (bool) preg_match( '/^2026exhibition/i', $slug );
}

/**
 * Whether the current page should omit header, footer, and chat widgets.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_chromeless_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	return ath_is_chromeless_page_slug( $post->post_name );
}

/**
 * Artist profile subpage (not the exhibition hub).
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_exhibition_artist_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	return ath_is_chromeless_page_slug( $post->post_name )
		&& $post->post_name !== '2026-exhibition-goihe';
}

/**
 * CSS classes for chromeless <main>.
 */
function ath_chromeless_page_main_class() {
	$classes = array( 'ath-chromeless-page' );

	if ( ath_is_exhibition_artist_page() ) {
		$classes[] = 'ath-chromeless-page--artist';
	}

	return implode( ' ', $classes );
}

/**
 * Full-bleed body for self-contained Gutenberg exhibition pages.
 */
add_action(
	'wp_head',
	function () {
		if ( ! is_singular( 'page' ) || ! ath_is_chromeless_page() ) {
			return;
		}
		?>
<style id="ath-chromeless-layout">
body.ath-chromeless {
	margin: 0;
	padding: 0;
	overflow-x: clip;
}
.ath-chromeless-page {
	margin: 0 auto;
	padding: clamp(2rem, 5vw, 3.5rem) clamp(1rem, 3vw, 2rem) clamp(1.25rem, 4vw, 3rem);
	max-width: 1100px;
	width: 100%;
	box-sizing: border-box;
}
/* Admin bar already offsets the page — light top breathing room only. */
body.admin-bar .ath-chromeless-page {
	padding-top: clamp(2.5rem, 6vw, 4rem);
}
.ath-chromeless-page > :first-child {
	margin-top: 0 !important;
}
.ath-chromeless-page > p.wp-block-paragraph:first-of-type {
	margin-bottom: 1.5rem;
	line-height: 1.1;
}
.ath-chromeless-page .wp-block-columns {
	display: flex;
	flex-wrap: wrap;
	gap: 2rem;
}
.ath-chromeless-page .wp-block-column {
	flex-grow: 1;
	min-width: 0;
}
@media (min-width: 782px) {
	.ath-chromeless-page .wp-block-columns:not(.is-not-stacked-on-mobile) > .wp-block-column {
		flex-basis: 0;
		flex-grow: 1;
	}
}
/* Artist subpages: profile title below exhibition header. */
.ath-chromeless-page--artist > p.wp-block-paragraph:nth-of-type(2) {
	font-size: clamp(1.1rem, 2.5vw, 1.35rem);
	margin-bottom: 1.25rem;
}
@media (min-width: 782px) {
	.ath-chromeless-page--artist > figure.wp-block-image:first-of-type {
		float: left;
		width: min(280px, 34%);
		margin: 0 2rem 1rem 0;
	}
	.ath-chromeless-page--artist > figure.wp-block-image:first-of-type img {
		width: 100%;
		height: auto;
		display: block;
	}
}
@media (max-width: 781px) {
	.ath-chromeless-page--artist > figure.wp-block-image:first-of-type {
		max-width: 220px;
		margin: 0 auto 1.25rem;
	}
	.ath-chromeless-page--artist > figure.wp-block-image:first-of-type img {
		width: 100%;
		height: auto;
	}
}
</style>
		<?php
	},
	20
);

/**
 * Strip floating chat widgets injected via wp_footer (Code Snippets, Crisp, etc.).
 */
add_action(
	'wp_footer',
	function () {
		if ( ! is_singular( 'page' ) || ! ath_is_chromeless_page() ) {
			return;
		}
		?>
<style id="ath-chromeless-hide-chat">
.whatsapp-button,
#crisp-chatbox,
.crisp-client,
[class*="crisp-client"] {
	display: none !important;
	visibility: hidden !important;
	pointer-events: none !important;
}
</style>
<script>
(function () {
	document.querySelectorAll('.whatsapp-button, #crisp-chatbox, .crisp-client').forEach(function (el) {
		el.remove();
	});
})();
</script>
		<?php
	},
	99999
);
