<?php
/**
 * Homepage structured programs — [ath_home_programs].
 *
 * Expects: $label, $heading, $items, $courses_url, $footer_label, $aria_label
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;
?>
  <!-- Courses -->
  <section class="courses-section" aria-label="<?php echo esc_attr( $aria_label ); ?>">
    <div class="courses-section__inner">
      <div class="courses-section__header">
        <?php if ( $label !== '' ) : ?>
          <p class="courses-section__label"><?php echo esc_html( $label ); ?></p>
        <?php endif; ?>
        <?php if ( $heading !== '' ) : ?>
          <h2 class="courses-section__heading"><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>
      </div>

      <?php if ( ! empty( $items ) ) : ?>
      <div class="courses-list">
        <?php foreach ( $items as $item ) : ?>
          <?php
          $row_url = ath_home_programs_row_url( $courses_url, $item );
          $num     = (string) ( $item['num'] ?? '' );
          $title   = (string) ( $item['title'] ?? '' );
          ?>
        <a href="<?php echo esc_url( $row_url ); ?>" class="course-row">
          <?php if ( $num !== '' ) : ?>
            <span class="course-row__num"><?php echo esc_html( $num ); ?></span>
          <?php endif; ?>
          <span class="course-row__title"><?php echo esc_html( $title ); ?></span>
          <span class="course-row__arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ( $footer_label !== '' ) : ?>
      <div class="courses-section__footer">
        <a href="<?php echo esc_url( $courses_url ); ?>" class="courses-section__link"><?php echo esc_html( $footer_label ); ?></a>
      </div>
      <?php endif; ?>
    </div>
  </section>
