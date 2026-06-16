<?php
/**
 * Homepage testimonials — [ath_home_testimonials].
 *
 * Expects: $label, $heading, $items, $photo_url, $photo_alt, $track_id, $dots_id,
 *          $prev_label, $next_label, $aria_label
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;
?>
  <!-- Testimonials -->
  <section class="testimonials-section" aria-label="<?php echo esc_attr( $aria_label ); ?>">
    <div class="testimonials-panel">
      <?php if ( $label !== '' ) : ?>
        <p class="testimonials-panel__label"><?php echo esc_html( $label ); ?></p>
      <?php endif; ?>
      <?php if ( $heading !== '' ) : ?>
        <h2 class="testimonials-panel__heading"><?php echo esc_html( $heading ); ?></h2>
      <?php endif; ?>

      <?php if ( ! empty( $items ) ) : ?>
      <div class="testimonials-slider">
        <button type="button" class="testimonials-nav testimonials-nav--prev" aria-label="<?php echo esc_attr( $prev_label ); ?>">
          <svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg>
        </button>

        <div class="testimonials-track-wrap">
          <div class="testimonials-track" id="<?php echo esc_attr( $track_id ); ?>">
            <?php foreach ( $items as $item ) : ?>
              <?php
              $text   = (string) ( $item['text'] ?? '' );
              $author = (string) ( $item['author'] ?? '' );
              ?>
            <div class="testimonial-slide">
              <?php if ( $text !== '' ) : ?>
                <p class="testimonial-slide__text"><?php echo esc_html( $text ); ?></p>
              <?php endif; ?>
              <?php if ( $author !== '' ) : ?>
                <p class="testimonial-slide__author"><?php echo esc_html( $author ); ?></p>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <button type="button" class="testimonials-nav testimonials-nav--next" aria-label="<?php echo esc_attr( $next_label ); ?>">
          <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
        </button>
      </div>

      <div class="testimonials-dots" id="<?php echo esc_attr( $dots_id ); ?>"></div>
      <?php endif; ?>
    </div>

    <?php if ( $photo_url !== '' ) : ?>
    <div class="testimonials-photo">
      <img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $photo_alt ); ?>" loading="lazy">
    </div>
    <?php endif; ?>
  </section>
