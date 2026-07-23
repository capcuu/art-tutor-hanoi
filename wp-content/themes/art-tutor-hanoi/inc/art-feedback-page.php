<?php
/**
 * Free art feedback page — replace image banner with video + hero helpers.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cloudinary video URL for the art-feedback-banner block.
 */
function ath_art_feedback_banner_video_url() {
	return 'https://res.cloudinary.com/dwy4wtjtc/video/upload/v1784799094/fb1_1_hezmgx.mp4';
}

/**
 * Whether this page is free-art-feedback.
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_art_feedback_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	return $post->post_name === 'free-art-feedback';
}

/**
 * Markup for the video that replaces figure.art-feedback-banner > img.
 *
 * @param string $alt Accessible label (from the original image alt when available).
 */
function ath_art_feedback_banner_video_html( $alt = 'Hanoi Art Tour' ) {
	$alt = trim( (string) $alt );
	if ( $alt === '' ) {
		$alt = 'Hanoi Art Tour';
	}

	$video_url = ath_art_feedback_banner_video_url();

	return sprintf(
		'<video class="art-feedback-banner__video" autoplay muted loop playsinline preload="metadata" aria-label="%s">' .
		'<source src="%s" type="video/mp4">' .
		'</video>',
		esc_attr( $alt ),
		esc_url( $video_url )
	);
}

/**
 * Replace the Feedback_banner image with the Cloudinary MP4 inside art-feedback-banner.
 *
 * @param string $content Post content HTML.
 */
function ath_art_feedback_replace_banner_image_with_video( $content ) {
	if ( ! is_string( $content ) || $content === '' || ! ath_is_art_feedback_page() ) {
		return $content;
	}

	$replaced = preg_replace_callback(
		'/<figure\b([^>]*\bart-feedback-banner\b[^>]*)>(.*?)<\/figure>/is',
		static function ( $match ) {
			$attrs = $match[1];
			$inner = $match[2];
			$alt   = 'Hanoi Art Tour';

			if ( preg_match( '/\balt=(["\'])(.*?)\1/i', $inner, $alt_match ) ) {
				$alt = html_entity_decode( $alt_match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			}

			// Already a video — leave alone.
			if ( stripos( $inner, '<video' ) !== false ) {
				return $match[0];
			}

			$class_attr = '';
			if ( preg_match( '/\bclass=(["\'])(.*?)\1/i', $attrs, $class_match ) ) {
				$classes = trim( $class_match[2] );
				if ( ! preg_match( '/\bart-feedback-banner--video\b/', $classes ) ) {
					$classes .= ' art-feedback-banner--video';
				}
				$class_attr = ' class="' . esc_attr( $classes ) . '"';
				$attrs      = preg_replace( '/\s*class=(["\'])(.*?)\1/i', '', $attrs );
			} else {
				$class_attr = ' class="wp-block-image size-large art-feedback-banner art-feedback-banner--video"';
			}

			return '<figure' . $class_attr . $attrs . '>' . ath_art_feedback_banner_video_html( $alt ) . '</figure>';
		},
		$content,
		1
	);

	return is_string( $replaced ) ? $replaced : $content;
}

add_filter( 'the_content', 'ath_art_feedback_replace_banner_image_with_video', 20 );

/**
 * Hero subtitle + CTA under the H1 (matches live free-art-feedback shell).
 */
function ath_render_art_feedback_hero_meta() {
	?>
      <p class="courses-hero__subtitle">Real advice from trained artists — not AI.</p>
      <p class="art-feedback-hero__actions">
        <a class="courses-cta__btn art-feedback-hero__btn" href="#art-feedback-submit">Submit your art</a>
      </p>
	<?php
}
