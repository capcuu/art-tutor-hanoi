<?php
/**
 * Homepage hero markup — rendered by [ath_home_hero].
 *
 * Expects: $aria_label, $poster_url, $video_url, $cloudinary_folder,
 *          $autoplay, $muted, $loop, $playsinline, $preload
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;
?>
  <!-- Hero -->
  <section class="hero" aria-label="<?php echo esc_attr( $aria_label ); ?>" data-cloudinary-folder="<?php echo esc_attr( $cloudinary_folder ); ?>">
    <h1 class="screen-reader-text">Art Tutor Hanoi</h1>
    <video
      class="hero__video"
      <?php echo $autoplay ? 'autoplay' : ''; ?>
      <?php echo $muted ? 'muted' : ''; ?>
      <?php echo $loop ? 'loop' : ''; ?>
      <?php echo $playsinline ? 'playsinline' : ''; ?>
      preload="<?php echo esc_attr( $preload ); ?>"
      poster="<?php echo esc_url( $poster_url ); ?>"
    >
      <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
    </video>
  </section>
