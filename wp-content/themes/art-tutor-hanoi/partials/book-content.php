<?php
/**
 * Book a Class — tabbed booking forms.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$tabs        = ath_book_tabs();
$active_tab  = ath_book_active_tab();
$active_data = $tabs[ $active_tab ];
?>
<!-- ath-book-tabs: <?php echo esc_html( implode( ',', array_keys( $tabs ) ) ); ?> -->
<main class="book-page">
  <section class="courses-hero book-page__hero" aria-labelledby="book-heading">
    <div class="courses-hero__inner">
      <h1 id="book-heading" class="courses-hero__title">Book a Class</h1>
      <p class="courses-hero__subtitle">Choose a program and complete your booking online.</p>
    </div>
  </section>

  <section class="book-page__body" aria-label="Booking options">
    <div class="book-page__inner">
      <div class="book-tabs" role="tablist" aria-label="Booking categories">
        <?php foreach ( $tabs as $slug => $tab ) : ?>
          <?php $is_active = $slug === $active_tab; ?>
          <a
            href="<?php echo esc_url( ath_book_url( $slug ) ); ?>"
            class="book-tabs__tab<?php echo $is_active ? ' is-active' : ''; ?>"
            role="tab"
            id="book-tab-<?php echo esc_attr( $slug ); ?>"
            aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
            aria-controls="book-panel-<?php echo esc_attr( $slug ); ?>"
          ><?php echo esc_html( $tab['label'] ); ?></a>
        <?php endforeach; ?>
      </div>

      <div
        class="book-panel"
        role="tabpanel"
        id="book-panel-<?php echo esc_attr( $active_tab ); ?>"
        aria-labelledby="book-tab-<?php echo esc_attr( $active_tab ); ?>"
      >
        <?php if ( ! empty( $active_data['intro'] ) ) : ?>
          <p class="book-panel__intro"><?php echo esc_html( $active_data['intro'] ); ?></p>
        <?php endif; ?>

        <div class="book-form-section__inner calendar-wp-content">
          <?php ath_render_fluent_form( (int) ( $active_data['form_id'] ?? 0 ) ); ?>
        </div>
      </div>
    </div>
  </section>
</main>
