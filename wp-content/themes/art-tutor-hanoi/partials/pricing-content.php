<?php
$pricing = require __DIR__ . '/../data/pricing-programs.php';
?>
<main class="pricing-page">
  <section class="pricing-hero" aria-labelledby="pricing-heading">
    <div class="pricing-hero__inner">
      <h1 id="pricing-heading" class="pricing-hero__title">Programs</h1>
      <?php foreach ( $pricing['intro'] as $index => $paragraph ) : ?>
        <p class="pricing-hero__text<?php echo $index === 0 ? ' pricing-hero__text--lead' : ''; ?>"><?php echo htmlspecialchars( $paragraph, ENT_QUOTES, 'UTF-8' ); ?></p>
      <?php endforeach; ?>
      <p class="pricing-hero__faq">
        Find details about validity of long-term courses:
        <a href="<?php echo esc_url( ath_resolve_url( $pricing['faq_url'] ) ); ?>"><?php echo htmlspecialchars( $pricing['faq_label'], ENT_QUOTES, 'UTF-8' ); ?></a>
      </p>
    </div>
  </section>

  <section class="pricing-programs" aria-label="Program options">
    <div class="pricing-programs__inner">
      <div class="pricing-grid">
        <?php foreach ( $pricing['programs'] as $program ) : ?>
          <?php
          $card_class = 'pricing-card';
          if ( ! empty( $program['featured'] ) ) {
            $card_class .= ' pricing-card--featured';
          }
          ?>
          <article class="<?php echo htmlspecialchars( $card_class, ENT_QUOTES, 'UTF-8' ); ?>" id="<?php echo htmlspecialchars( $program['id'], ENT_QUOTES, 'UTF-8' ); ?>">
            <?php if ( ! empty( $program['badge'] ) ) : ?>
              <p class="pricing-card__badge"><?php echo htmlspecialchars( $program['badge'], ENT_QUOTES, 'UTF-8' ); ?></p>
            <?php endif; ?>
            <h2 class="pricing-card__title"><?php echo htmlspecialchars( $program['title'], ENT_QUOTES, 'UTF-8' ); ?></h2>
            <p class="pricing-card__meta">
              <span class="pricing-card__tag"><?php echo htmlspecialchars( $program['tag'], ENT_QUOTES, 'UTF-8' ); ?></span>
              <span class="pricing-card__price"><?php echo htmlspecialchars( $program['price'], ENT_QUOTES, 'UTF-8' ); ?></span>
            </p>
            <p class="pricing-card__desc"><?php echo htmlspecialchars( $program['description'], ENT_QUOTES, 'UTF-8' ); ?></p>
            <?php if ( ! empty( $program['features'] ) ) : ?>
              <ul class="pricing-card__features">
                <?php foreach ( $program['features'] as $feature ) : ?>
                  <li><?php echo htmlspecialchars( $feature, ENT_QUOTES, 'UTF-8' ); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <a href="<?php echo esc_url( ath_resolve_url( $program['cta']['url'] ) ); ?>" class="pricing-card__btn"><?php echo htmlspecialchars( $program['cta']['label'], ENT_QUOTES, 'UTF-8' ); ?></a>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="pricing-solo">
        <p class="pricing-solo__heading"><?php echo htmlspecialchars( $pricing['solo_note']['heading'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <p class="pricing-solo__text"><?php echo htmlspecialchars( $pricing['solo_note']['text'], ENT_QUOTES, 'UTF-8' ); ?></p>
      </div>

      <?php $residency = $pricing['residency']; ?>
      <article class="pricing-card pricing-card--residency" id="artist-residency">
        <h2 class="pricing-card__title"><?php echo htmlspecialchars( $residency['title'], ENT_QUOTES, 'UTF-8' ); ?></h2>
        <p class="pricing-card__meta">
          <span class="pricing-card__tag"><?php echo htmlspecialchars( $residency['tag'], ENT_QUOTES, 'UTF-8' ); ?></span>
          <span class="pricing-card__price"><?php echo htmlspecialchars( $residency['price'], ENT_QUOTES, 'UTF-8' ); ?></span>
        </p>
        <p class="pricing-card__desc"><?php echo htmlspecialchars( $residency['description'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <a href="<?php echo esc_url( ath_resolve_url( $residency['cta']['url'] ) ); ?>" class="pricing-card__btn"><?php echo htmlspecialchars( $residency['cta']['label'], ENT_QUOTES, 'UTF-8' ); ?></a>
      </article>
    </div>
  </section>
</main>
