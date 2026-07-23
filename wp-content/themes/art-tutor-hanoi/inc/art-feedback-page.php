<?php
/**
 * Free art feedback page — video banner + hero helpers.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cloudinary video URL for the free-art-feedback top banner.
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
 * Render full-bleed video banner for free-art-feedback (top of main).
 */
function ath_render_art_feedback_video_banner() {
	echo ath_render_home_hero_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode HTML.
		array(
			'aria_label'        => 'Free art feedback',
			'video_url'         => ath_art_feedback_banner_video_url(),
			'video_id'          => '',
			'video_version'     => '',
			'cloudinary_folder' => 'video',
			'brand_h1'          => '',
			'poster'            => '',
		)
	);
}

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
