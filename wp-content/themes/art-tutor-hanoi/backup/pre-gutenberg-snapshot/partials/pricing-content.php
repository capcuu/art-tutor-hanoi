<?php
$pricing = require __DIR__ . '/../data/pricing-programs.php';
?>
<main class="pricing-page">
  <section class="pricing-hero" aria-labelledby="pricing-heading">
    <div class="pricing-hero__inner">
      <h1 id="pricing-heading" class="pricing-hero__title">Pricing</h1>
      <?php foreach ( $pricing['intro'] as $index => $paragraph ) : ?>
        <p class="pricing-hero__text<?php echo $index === 0 ? ' pricing-hero__text--lead' : ''; ?>"><?php echo htmlspecialchars( $paragraph, ENT_QUOTES, 'UTF-8' ); ?></p>
      <?php endforeach; ?>
      <p class="pricing-hero__faq">
        Questions about course validity or scheduling?
        <a href="<?php echo esc_url( ath_resolve_url( $pricing['faq_url'] ) ); ?>"><?php echo htmlspecialchars( $pricing['faq_label'], ENT_QUOTES, 'UTF-8' ); ?></a>
      </p>
    </div>
  </section>

  <section class="pricing-programs" aria-label="Pricing options">
    <div class="pricing-programs__inner">
      <div class="pricing-list" id="pricing-list">
        <?php foreach ( $pricing['sections'] as $section ) : ?>
          <?php
          $panel_id  = 'pricing-panel-' . $section['id'];
          $header_id = 'pricing-header-' . $section['id'];
          $items     = ! empty( $section['items'] ) ? $section['items'] : array();
          $has_panel = ! empty( $section['description'] ) || ! empty( $items ) || ! empty( $section['price'] );
          ?>
          <article class="pathway pathway--pricing" id="<?php echo htmlspecialchars( $section['id'], ENT_QUOTES, 'UTF-8' ); ?>" data-pathway="<?php echo htmlspecialchars( $section['id'], ENT_QUOTES, 'UTF-8' ); ?>">
            <button
              type="button"
              class="pathway__header pricing-row__header"
              id="<?php echo htmlspecialchars( $header_id, ENT_QUOTES, 'UTF-8' ); ?>"
              aria-expanded="false"
              aria-controls="<?php echo htmlspecialchars( $panel_id, ENT_QUOTES, 'UTF-8' ); ?>"
              <?php echo $has_panel ? '' : 'disabled'; ?>
            >
              <span class="pricing-row__num"><?php echo htmlspecialchars( $section['num'], ENT_QUOTES, 'UTF-8' ); ?></span>
              <span class="pricing-row__title"><?php echo htmlspecialchars( $section['title'], ENT_QUOTES, 'UTF-8' ); ?></span>
              <?php if ( $has_panel ) : ?>
                <span class="pricing-row__chevron pathway__chevron" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
              <?php endif; ?>
            </button>

            <?php if ( $has_panel ) : ?>
              <div
                class="pathway__panel pricing-row__panel"
                id="<?php echo htmlspecialchars( $panel_id, ENT_QUOTES, 'UTF-8' ); ?>"
                role="region"
                aria-labelledby="<?php echo htmlspecialchars( $header_id, ENT_QUOTES, 'UTF-8' ); ?>"
                hidden
              >
                <?php if ( ! empty( $section['description'] ) ) : ?>
                  <p class="pricing-panel__desc"><?php echo htmlspecialchars( $section['description'], ENT_QUOTES, 'UTF-8' ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $items ) ) : ?>
                  <ul class="pricing-items">
                    <?php foreach ( $items as $item ) : ?>
                      <li class="pricing-item">
                        <span class="pricing-item__title"><?php echo htmlspecialchars( $item['title'], ENT_QUOTES, 'UTF-8' ); ?></span>
                        <span class="pricing-item__price"><?php echo htmlspecialchars( $item['price'], ENT_QUOTES, 'UTF-8' ); ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php elseif ( ! empty( $section['price'] ) ) : ?>
                  <p class="pricing-single">
                    <span class="pricing-single__label">Session fee</span>
                    <span class="pricing-single__price"><?php echo htmlspecialchars( $section['price'], ENT_QUOTES, 'UTF-8' ); ?></span>
                  </p>
                <?php endif; ?>

                <div class="pricing-panel__footer">
                  <a href="<?php echo esc_url( ath_book_url( $section['book_tab'] ) ); ?>" class="pricing-panel__btn">Book Now</a>
                </div>
              </div>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
