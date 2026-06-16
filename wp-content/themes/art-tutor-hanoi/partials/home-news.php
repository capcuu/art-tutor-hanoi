<?php
/**
 * Homepage "From Our Studio" — Studio News carousel.
 *
 * @var string     $news_label
 * @var string     $news_heading
 * @var WP_Post[]  $news_posts
 * @var string     $news_btn_label
 * @var string     $news_footer_label
 * @var string     $news_view_all_url
 * @var string     $news_aria_label
 */
defined( 'ABSPATH' ) || exit;
?>
  <section class="news-section" aria-label="<?php echo esc_attr( $news_aria_label ); ?>">
    <div class="news-section__inner">
      <?php if ( $news_label !== '' ) : ?>
        <p class="news-section__label"><?php echo esc_html( $news_label ); ?></p>
      <?php endif; ?>
      <?php if ( $news_heading !== '' ) : ?>
        <h2 class="news-section__heading"><?php echo esc_html( $news_heading ); ?></h2>
      <?php endif; ?>

      <div class="news-carousel">
        <button type="button" class="news-nav news-nav--prev" aria-label="<?php esc_attr_e( 'Previous news', 'art-tutor-hanoi' ); ?>">
          <svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg>
        </button>

        <div class="news-track-wrap" id="news-track-wrap">
          <div class="news-track" id="news-track">
            <?php foreach ( $news_posts as $news_post ) : ?>
              <?php
              $permalink = get_permalink( $news_post );
              $title     = get_the_title( $news_post );
              $image_url = ath_studio_news_post_image_url( $news_post );
              $image_alt = $title !== '' ? $title : __( 'Studio news', 'art-tutor-hanoi' );
              ?>
            <article class="news-card">
              <a href="<?php echo esc_url( $permalink ); ?>" class="news-card__image">
                <?php if ( $image_url !== '' ) : ?>
                  <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" decoding="async">
                <?php else : ?>
                  <span class="news-card__image-placeholder" aria-hidden="true"></span>
                <?php endif; ?>
              </a>
              <div class="news-card__body">
                <h3 class="news-card__title"><?php echo esc_html( $title ); ?></h3>
                <a href="<?php echo esc_url( $permalink ); ?>" class="news-card__btn"><?php echo esc_html( $news_btn_label ); ?></a>
              </div>
            </article>
            <?php endforeach; ?>
          </div>
        </div>

        <button type="button" class="news-nav news-nav--next" aria-label="<?php esc_attr_e( 'Next news', 'art-tutor-hanoi' ); ?>">
          <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
        </button>
      </div>

      <?php if ( $news_view_all_url !== '' && $news_footer_label !== '' ) : ?>
        <p class="news-section__footer">
          <a href="<?php echo esc_url( $news_view_all_url ); ?>" class="news-section__view-all"><?php echo esc_html( $news_footer_label ); ?></a>
        </p>
      <?php endif; ?>
    </div>
  </section>
