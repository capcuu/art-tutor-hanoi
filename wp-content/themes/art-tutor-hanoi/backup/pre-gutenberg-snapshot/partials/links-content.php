<?php
/**
 * More links hub — partner logos and resource links.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$links_data = require __DIR__ . '/../data/links.php';
$is_admin   = is_user_logged_in();
?>
<main class="links-page">
  <section class="courses-hero" aria-labelledby="links-heading">
    <div class="courses-hero__inner">
      <h1 id="links-heading" class="courses-hero__title">Links</h1>
    </div>
  </section>

  <section class="links-page__body" aria-label="Partner links and resources">
    <div class="links-page__inner">
      <ul class="links-page__partners">
        <?php foreach ( $links_data['partners'] as $partner ) : ?>
          <?php
          $partner_url = ( strpos( $partner['url'], 'http' ) === 0 )
            ? $partner['url']
            : home_url( '/' . trim( $partner['url'], '/' ) . '/' );
          $image_url   = ( strpos( $partner['image'], 'http' ) === 0 )
            ? $partner['image']
            : home_url( $partner['image'] );
          ?>
        <li class="links-page__partner">
          <a href="<?php echo esc_url( $partner_url ); ?>" target="_blank" rel="noopener noreferrer">
            <img
              src="<?php echo esc_url( $image_url ); ?>"
              alt="<?php echo esc_attr( $partner['label'] ); ?>"
              width="<?php echo esc_attr( (string) $partner['width'] ); ?>"
              loading="lazy"
              decoding="async">
          </a>
        </li>
        <?php endforeach; ?>
      </ul>

      <hr class="links-page__divider">

      <ul class="links-page__list">
        <?php foreach ( $links_data['links'] as $item ) : ?>
          <?php
          if ( ! empty( $item['admin_only'] ) && ! $is_admin ) {
            continue;
          }

          $href = ! empty( $item['external'] )
            ? $item['url']
            : ath_resolve_url( $item['url'] );
          ?>
        <li class="links-page__item<?php echo ! empty( $item['admin_only'] ) ? ' links-page__item--admin' : ''; ?>">
          <?php if ( ! empty( $item['prefix'] ) ) : ?>
            <span class="links-page__prefix"><?php echo esc_html( $item['prefix'] ); ?></span>
          <?php endif; ?>
          <a
            href="<?php echo esc_url( $href ); ?>"
            class="links-page__link<?php echo ! empty( $item['em'] ) ? ' links-page__link--em' : ''; ?>"
            <?php echo ! empty( $item['external'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
          ><?php echo esc_html( $item['label'] ); ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
</main>
