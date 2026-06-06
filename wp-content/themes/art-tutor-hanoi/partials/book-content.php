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

        <?php if ( $active_tab === 'workshops' && ! empty( $active_data['workshops'] ) ) : ?>
          <ul class="book-workshops">
            <?php foreach ( $active_data['workshops'] as $workshop ) : ?>
              <li class="book-workshops__item">
                <h2 class="book-workshops__title"><?php echo esc_html( $workshop['title'] ); ?></h2>
                <p class="book-workshops__desc"><?php echo esc_html( $workshop['desc'] ); ?></p>
                <a href="<?php echo esc_url( $workshop['url'] ); ?>" class="book-workshops__link">Learn more</a>
              </li>
            <?php endforeach; ?>
          </ul>
          <p class="book-panel__note">
            <a href="<?php echo esc_url( ath_page_url( 'calendar' ) ); ?>">View weekly calendar</a>
            for session dates, or
            <a href="<?php echo esc_url( ath_book_url( 'trial' ) ); ?>">book a trial class</a>
            to visit the studio first.
          </p>
        <?php endif; ?>

        <?php if ( ! empty( $active_data['form_id'] ) ) : ?>
          <div class="book-form-section__inner calendar-wp-content">
            <?php ath_render_fluent_form( (int) $active_data['form_id'] ); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
