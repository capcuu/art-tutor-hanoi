<?php
$pricing = require __DIR__ . '/../data/pricing-programs.php';
?>
<main class="pricing-page">
  <section class="pricing-hero" aria-labelledby="pricing-heading">
    <div class="pricing-hero__inner">
      <h1 id="pricing-heading" class="pricing-hero__title"><?php echo esc_html( ath_page_seo_h1() ); ?></h1>
      <?php foreach ( $pricing['intro'] as $index => $paragraph ) : ?>
        <p class="pricing-hero__text<?php echo $index === 0 ? ' pricing-hero__text--lead' : ''; ?>"><?php echo esc_html( $paragraph ); ?></p>
      <?php endforeach; ?>
      <p class="pricing-hero__faq">
        Questions about course validity or scheduling?
        <a href="<?php echo esc_url( ath_resolve_url( $pricing['faq_url'] ) ); ?>"><?php echo esc_html( $pricing['faq_label'] ); ?></a>
      </p>
    </div>
  </section>

  <section class="pricing-programs" aria-label="Pricing options">
    <div class="pricing-programs__inner">
      <?php require __DIR__ . '/pricing-accordion.php'; ?>
    </div>
  </section>
</main>
