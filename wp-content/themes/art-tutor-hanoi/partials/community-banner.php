<?php
/**
 * Community section — shared by homepage and About page.
 *
 * @var string|null $community_section_title   Optional h2 above the stat line (About page).
 * @var bool        $community_panel_open      Open carousel on load (About; ignored if always visible).
 * @var bool        $community_always_visible  Homepage: show carousel without toggle (default false).
 */
$community_section_title  = isset( $community_section_title ) ? $community_section_title : null;
$community_always_visible = ! empty( $community_always_visible );
$community_panel_open     = $community_always_visible || ! empty( $community_panel_open );
$panel_id                 = isset( $community_panel_id ) ? $community_panel_id : 'community-panel';
$toggle_id                = isset( $community_toggle_id ) ? $community_toggle_id : 'community-toggle';
$community_limit          = isset( $community_limit ) ? max( 1, (int) $community_limit ) : 6;
$community_posts          = ath_community_student_posts( $community_limit );
$community_view_all_url   = isset( $community_view_all_url ) ? (string) $community_view_all_url : ath_students_artworks_url();
$community_stat_text      = isset( $community_stat_text ) ? (string) $community_stat_text : '150+ artists from 40+ countries<br>have studied at Art Tutor Hanoi.';
$community_footer_label   = isset( $community_footer_label ) ? (string) $community_footer_label : 'View all';
$community_toggle_show    = isset( $community_toggle_show ) ? (string) $community_toggle_show : 'View Community';
$community_toggle_hide    = isset( $community_toggle_hide ) ? (string) $community_toggle_hide : 'Hide Community';
$section_class            = 'community-banner';
if ( $community_always_visible ) {
	$section_class .= ' community-banner--always-open';
}
?>
  <section class="<?php echo esc_attr( $section_class ); ?>" aria-labelledby="<?php echo $community_section_title ? 'community-section-title' : 'community-banner-text'; ?>">
    <div class="community-banner__inner">
      <?php if ( $community_section_title ) : ?>
        <h2 id="community-section-title" class="community-banner__section-title"><?php echo esc_html( $community_section_title ); ?></h2>
      <?php endif; ?>
      <?php if ( $community_stat_text !== '' ) : ?>
      <p id="community-banner-text" class="community-banner__text"><?php echo wp_kses( $community_stat_text, array( 'br' => array() ) ); ?></p>
      <?php endif; ?>

      <?php if ( ! $community_always_visible ) : ?>
      <button type="button" class="community-banner__btn<?php echo $community_panel_open ? ' is-open' : ''; ?>" id="<?php echo esc_attr( $toggle_id ); ?>" aria-expanded="<?php echo $community_panel_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
        <?php echo $community_panel_open ? esc_html( $community_toggle_hide ) : esc_html( $community_toggle_show ); ?>
      </button>
      <?php endif; ?>

      <?php if ( ! empty( $community_posts ) ) : ?>
      <div class="community-panel<?php echo $community_panel_open ? ' is-open' : ''; ?>" id="<?php echo esc_attr( $panel_id ); ?>"<?php echo ( $community_panel_open || $community_always_visible ) ? '' : ' hidden'; ?>>
        <div class="news-carousel community-carousel">
          <button type="button" class="news-nav community-nav community-nav--prev" aria-label="Previous students">
            <svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg>
          </button>

          <div class="news-track-wrap" id="community-track-wrap">
            <div class="news-track" id="community-track">
              <?php foreach ( $community_posts as $student_post ) : ?>
                <?php
                $label       = ath_community_student_label( $student_post );
                $permalink   = get_permalink( $student_post );
                $image_html  = ath_community_student_image_html( $student_post );
                $updated_ts  = ath_student_post_last_comment_timestamp( $student_post );
                $last_updated = $updated_ts ? wp_date( get_option( 'date_format' ), $updated_ts ) : '';
                $updated_iso  = $updated_ts ? wp_date( 'c', $updated_ts ) : '';
                ?>
              <article class="news-card">
                <a href="<?php echo esc_url( $permalink ); ?>" class="news-card__image">
                  <?php if ( $image_html !== '' ) : ?>
                    <?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
                  <?php else : ?>
                    <span class="news-card__image-placeholder" aria-hidden="true"></span>
                  <?php endif; ?>
                </a>
                <div class="news-card__body">
                  <h3 class="news-card__title"><?php echo esc_html( $label ); ?></h3>
                  <?php if ( $last_updated !== '' ) : ?>
                    <p class="news-card__updated">
                      <time datetime="<?php echo esc_attr( $updated_iso ); ?>"><?php echo esc_html( $last_updated ); ?></time>
                    </p>
                  <?php endif; ?>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
          </div>

          <button type="button" class="news-nav community-nav community-nav--next" aria-label="Next students">
            <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
          </button>
        </div>

        <?php if ( $community_view_all_url !== '' ) : ?>
          <p class="community-banner__footer">
            <a href="<?php echo esc_url( $community_view_all_url ); ?>" class="community-banner__view-all"><?php echo esc_html( $community_footer_label ); ?></a>
          </p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
