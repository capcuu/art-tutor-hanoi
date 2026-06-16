<?php
/**
 * Homepage gallery markup — rendered by [ath_home_gallery] / ath_render_home_gallery_block().
 *
 * Expects: $intro, $cloudinary_folder, $gallery_batches, $gallery_has_more, $button_label
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $gallery_batches ) || ! is_array( $gallery_batches ) ) {
	return;
}
?>
  <!-- Gallery -->
  <section class="gallery-section" data-cloudinary-folder="<?php echo esc_attr( $cloudinary_folder ?? '' ); ?>">
    <?php if ( ! empty( $intro ) ) : ?>
      <p class="gallery-section__intro"><?php echo esc_html( $intro ); ?></p>
    <?php endif; ?>

    <div class="gallery-wrap">
      <?php foreach ( $gallery_batches as $batch_num => $batch_items ) : ?>
        <?php if ( empty( $batch_items ) ) { continue; } ?>
        <div class="gallery-batch gallery-collage<?php echo $batch_num === 1 ? ' is-visible' : ''; ?>">
          <?php foreach ( $batch_items as $item ) : ?>
            <?php
            $display_w = (int) ( $item['display_width'] ?? 400 );
            $img_src   = cld_frontpage_image( $item, $display_w );
            $img_full  = cld_frontpage_image( $item, 1400 );
            $img_size  = cld_frontpage_display_size( $item, $display_w );
            $style     = sprintf(
              '--gallery-w:%s%%;--gallery-rotate:%sdeg;--gallery-mt:%spx;',
              (float) ( $item['width_pct'] ?? 42 ),
              (float) ( $item['rotate'] ?? 0 ),
              (int) ( $item['margin_top'] ?? 0 )
            );
            ?>
            <figure
              class="gallery-item"
              style="<?php echo esc_attr( $style ); ?>"
              role="button"
              tabindex="0"
              aria-label="<?php echo esc_attr( sprintf( __( 'View larger: %s', 'art-tutor-hanoi' ), $item['alt'] ?? 'Art Tutor Hanoi studio' ) ); ?>"
            >
              <img
                class="js-lazy-img"
                data-src="<?php echo esc_url( $img_src ); ?>"
                data-full="<?php echo esc_url( $img_full ); ?>"
                alt="<?php echo esc_attr( $item['alt'] ?? 'Art Tutor Hanoi studio' ); ?>"
                width="<?php echo (int) $img_size['width']; ?>"
                height="<?php echo (int) $img_size['height']; ?>"
                decoding="async"
              >
            </figure>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <button type="button" class="gallery-more<?php echo $gallery_has_more ? '' : ' is-hidden'; ?>"><?php echo esc_html( $button_label ?? 'Load more photos' ); ?></button>
    </div>

    <div
      class="gallery-lightbox"
      id="gallery-lightbox"
      hidden
      aria-hidden="true"
      role="dialog"
      aria-modal="true"
      aria-label="<?php esc_attr_e( 'Enlarged photo', 'art-tutor-hanoi' ); ?>"
    >
      <button type="button" class="gallery-lightbox__close" aria-label="<?php esc_attr_e( 'Close', 'art-tutor-hanoi' ); ?>">&times;</button>
      <img class="gallery-lightbox__img" src="" alt="">
    </div>
  </section>
