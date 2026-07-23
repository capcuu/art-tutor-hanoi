<?php
/**
 * Homepage hero markup — rendered by [ath_home_hero].
 *
 * Expects: $aria_label, $poster_url, $video_url, $cloudinary_folder,
 *          $autoplay, $muted, $loop, $playsinline, $preload, $brand_h1
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$brand_h1 = isset( $brand_h1 ) ? (string) $brand_h1 : 'Art Tutor Hanoi';
?>
  <!-- Hero -->
  <section class="hero" aria-label="<?php echo esc_attr( $aria_label ); ?>" data-cloudinary-folder="<?php echo esc_attr( $cloudinary_folder ); ?>">
    <?php if ( $brand_h1 !== '' ) : ?>
    <h1 class="screen-reader-text"><?php echo esc_html( $brand_h1 ); ?></h1>
    <?php endif; ?>
    <video
      class="hero__video"
      <?php echo $autoplay ? 'autoplay' : ''; ?>
      <?php echo $muted ? 'muted' : ''; ?>
      <?php echo $loop ? 'loop' : ''; ?>
      <?php echo $playsinline ? 'playsinline' : ''; ?>
      preload="<?php echo esc_attr( $preload ); ?>"
      <?php if ( $poster_url !== '' ) : ?>
      poster="<?php echo esc_url( $poster_url ); ?>"
      <?php endif; ?>
    >
      <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
    </video>
  </section>
