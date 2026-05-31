<?php
/**
 * @var array $experience
 */
require_once __DIR__ . '/../config/images.php';

$hero_src  = v2_img_url( $experience['image']['url'], 'hero' );
$hero_size = v2_img_display_size( 'hero' );
?>
<main class="experience-page">
  <nav class="course-breadcrumb" aria-label="Breadcrumb">
    <ol class="course-breadcrumb__list">
      <li><a href="pricing.php">Programs</a></li>
      <li aria-current="page"><?php echo htmlspecialchars( $experience['title'], ENT_QUOTES, 'UTF-8' ); ?></li>
    </ol>
  </nav>

  <article class="experience-article">
    <header class="experience-hero">
      <div class="experience-hero__media">
        <img
          src="<?php echo htmlspecialchars( $hero_src, ENT_QUOTES, 'UTF-8' ); ?>"
          alt="<?php echo htmlspecialchars( $experience['image']['alt'], ENT_QUOTES, 'UTF-8' ); ?>"
          width="<?php echo (int) $hero_size['width']; ?>"
          <?php if ( $hero_size['height'] ) : ?>height="<?php echo (int) $hero_size['height']; ?>"<?php endif; ?>
          decoding="async"
          fetchpriority="high"
        >
      </div>
      <div class="experience-hero__content">
        <p class="experience-hero__category"><?php echo htmlspecialchars( $experience['category'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <h1 class="experience-hero__title"><?php echo htmlspecialchars( $experience['title'], ENT_QUOTES, 'UTF-8' ); ?></h1>
        <?php if ( ! empty( $experience['subtitle'] ) ) : ?>
          <p class="experience-hero__subtitle"><?php echo htmlspecialchars( $experience['subtitle'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <?php endif; ?>
        <?php if ( ! empty( $experience['price'] ) ) : ?>
          <p class="experience-hero__price"><?php echo htmlspecialchars( $experience['price'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <?php endif; ?>
        <?php if ( ! empty( $experience['price_note'] ) ) : ?>
          <p class="experience-hero__price-note"><?php echo htmlspecialchars( $experience['price_note'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <?php endif; ?>
        <?php if ( ! empty( $experience['intro'] ) ) : ?>
          <p class="experience-hero__intro"><?php echo htmlspecialchars( $experience['intro'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <?php endif; ?>
        <?php if ( ! empty( $experience['book'] ) ) : ?>
          <a href="<?php echo htmlspecialchars( $experience['book']['url'], ENT_QUOTES, 'UTF-8' ); ?>" class="experience-hero__btn"><?php echo htmlspecialchars( $experience['book']['label'], ENT_QUOTES, 'UTF-8' ); ?></a>
        <?php endif; ?>
      </div>
    </header>

    <?php if ( ! empty( $experience['meta'] ) ) : ?>
      <section class="experience-meta" aria-label="Session details">
        <dl class="experience-meta__list">
          <?php foreach ( $experience['meta'] as $item ) : ?>
            <div class="experience-meta__item">
              <dt><?php echo htmlspecialchars( $item['label'], ENT_QUOTES, 'UTF-8' ); ?></dt>
              <dd><?php echo htmlspecialchars( $item['value'], ENT_QUOTES, 'UTF-8' ); ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </section>
    <?php endif; ?>

    <div class="experience-body">
      <?php foreach ( $experience['blocks'] as $block ) : ?>
        <section class="course-block">
          <?php if ( ! empty( $block['heading'] ) ) : ?>
            <h2 class="course-block__heading"><?php echo htmlspecialchars( $block['heading'], ENT_QUOTES, 'UTF-8' ); ?></h2>
          <?php endif; ?>
          <?php if ( ! empty( $block['text'] ) ) : ?>
            <p class="course-block__text"><?php echo htmlspecialchars( $block['text'], ENT_QUOTES, 'UTF-8' ); ?></p>
          <?php endif; ?>
          <?php if ( ! empty( $block['items'] ) ) : ?>
            <ul class="course-block__list">
              <?php foreach ( $block['items'] as $item ) : ?>
                <li><?php echo htmlspecialchars( $item, ENT_QUOTES, 'UTF-8' ); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>
    </div>

    <?php if ( ! empty( $experience['gallery'] ) ) : ?>
      <section class="course-gallery" aria-label="Photos">
        <div class="course-gallery__grid">
          <?php foreach ( $experience['gallery'] as $item ) : ?>
            <?php
            $gallery_src  = v2_img_url( $item['url'], 'gallery' );
            $gallery_size = v2_img_display_size( 'gallery' );
            ?>
            <figure class="course-gallery__item">
              <img
                src="<?php echo htmlspecialchars( $gallery_src, ENT_QUOTES, 'UTF-8' ); ?>"
                alt="<?php echo htmlspecialchars( isset( $item['caption'] ) ? $item['caption'] : $experience['title'], ENT_QUOTES, 'UTF-8' ); ?>"
                width="<?php echo (int) $gallery_size['width']; ?>"
                loading="lazy"
                decoding="async"
              >
              <?php if ( ! empty( $item['caption'] ) ) : ?>
                <figcaption class="course-gallery__caption"><?php echo htmlspecialchars( $item['caption'], ENT_QUOTES, 'UTF-8' ); ?></figcaption>
              <?php endif; ?>
            </figure>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </article>

  <section class="courses-cta">
    <div class="courses-cta__inner">
      <?php if ( ! empty( $experience['book'] ) ) : ?>
        <p class="courses-cta__text">Ready to join? Complete your booking online.</p>
        <a href="<?php echo htmlspecialchars( $experience['book']['url'], ENT_QUOTES, 'UTF-8' ); ?>" class="courses-cta__btn"><?php echo htmlspecialchars( $experience['book']['label'], ENT_QUOTES, 'UTF-8' ); ?></a>
      <?php endif; ?>
      <?php if ( ! empty( $experience['policy'] ) ) : ?>
        <p class="courses-cta__note"><?php echo htmlspecialchars( $experience['policy'], ENT_QUOTES, 'UTF-8' ); ?></p>
      <?php endif; ?>
      <p class="courses-cta__links">
        <a href="pricing.php">All programs</a>
        <span aria-hidden="true">&middot;</span>
        <a href="calendar.php">Weekly calendar</a>
      </p>
    </div>
  </section>
</main>
