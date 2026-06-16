<?php
/**
 * Courses hub — pathways, benefits, CTA (no hero; H1 is page shell).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$pathways = require ATH_THEME_DIR . '/data/pathways.php';
?>
<section class="pathways-section" aria-label="Course pathways">
  <div class="pathways-section__inner">
    <?php
    $pathways_panel = 'courses';
    require ATH_THEME_DIR . '/partials/pathways-list.php';
    ?>
  </div>
</section>

<section class="courses-benefits" aria-labelledby="courses-benefits-heading">
  <div class="courses-benefits__inner">
    <h2 id="courses-benefits-heading" class="courses-benefits__title">Why study at Art Tutor Hanoi?</h2>
    <ul class="courses-benefits__list">
      <li>Learn drawing, painting, and composition from instructors trained at Vietnam University of Fine Arts</li>
      <li>Classes in English or bilingual instruction &mdash; ideal for expats and visitors in Hanoi</li>
      <li>Flexible scheduling for short stays or long-term residents in Tay Ho</li>
      <li>Also offering <a href="<?php echo esc_url( ath_page_url( 'workshops' ) ); ?>">creative art workshops</a> and <a href="<?php echo esc_url( ath_page_url( 'kids-courses' ) ); ?>">kids art classes</a></li>
    </ul>
  </div>
</section>

<section class="courses-cta">
  <div class="courses-cta__inner">
    <p class="courses-cta__text">Still undecided? Try a sample class and find your pathway.</p>
    <a href="<?php echo esc_url( ath_book_url( 'adult' ) ); ?>" class="courses-cta__btn">Book a Sample Class</a>
    <p class="courses-cta__links">
      <a href="<?php echo esc_url( ath_page_url( 'pricing' ) ); ?>">View pricing</a>
      <span aria-hidden="true">&middot;</span>
      <a href="<?php echo esc_url( ath_page_url( 'calendar' ) ); ?>">Weekly calendar</a>
      <span aria-hidden="true">&middot;</span>
      <a href="<?php echo esc_url( ath_page_url( 'workshops' ) ); ?>">Workshops</a>
    </p>
  </div>
</section>

<?php ath_render_commercial_crosslinks( 'courses' ); ?>
