<?php
/**
 * Community section — shared by homepage and About page.
 *
 * @var string|null $community_section_title Optional h2 above the stat line (About page).
 * @var bool        $community_panel_open   Open gallery on load (default false).
 */
$community_section_title = isset( $community_section_title ) ? $community_section_title : null;
$community_panel_open    = ! empty( $community_panel_open );
$panel_id                  = isset( $community_panel_id ) ? $community_panel_id : 'community-panel';
$toggle_id                 = isset( $community_toggle_id ) ? $community_toggle_id : 'community-toggle';
?>
  <section class="community-banner" aria-labelledby="<?php echo $community_section_title ? 'community-section-title' : 'community-banner-text'; ?>">
    <div class="community-banner__inner">
      <?php if ( $community_section_title ) : ?>
        <h2 id="community-section-title" class="community-banner__section-title"><?php echo htmlspecialchars( $community_section_title, ENT_QUOTES, 'UTF-8' ); ?></h2>
      <?php endif; ?>
      <p id="community-banner-text" class="community-banner__text">111 Artists from 40 countries<br>have studied at Art Tutor Hanoi.</p>
      <button type="button" class="community-banner__btn<?php echo $community_panel_open ? ' is-open' : ''; ?>" id="<?php echo htmlspecialchars( $toggle_id, ENT_QUOTES, 'UTF-8' ); ?>" aria-expanded="<?php echo $community_panel_open ? 'true' : 'false'; ?>" aria-controls="<?php echo htmlspecialchars( $panel_id, ENT_QUOTES, 'UTF-8' ); ?>">
        <?php echo $community_panel_open ? 'Hide Community' : 'View Community'; ?>
      </button>

      <div class="community-panel<?php echo $community_panel_open ? ' is-open' : ''; ?>" id="<?php echo htmlspecialchars( $panel_id, ENT_QUOTES, 'UTF-8' ); ?>"<?php echo $community_panel_open ? '' : ' hidden'; ?>>
        <div class="news-carousel">
          <div class="news-track-wrap">
            <div class="news-track">
              <article class="news-card">
                <a href="#" class="news-card__image">
                  <img src="community/camila.jpg" alt="Camila at the studio" loading="lazy">
                </a>
                <div class="news-card__body">
                  <h3 class="news-card__title">Camila – South Africa</h3>
                  <a href="#" class="news-card__btn">Read More</a>
                </div>
              </article>

              <article class="news-card">
                <a href="#" class="news-card__image">
                  <img src="community/Nichika.jpg" alt="Nichika Shingaki at the studio" loading="lazy">
                </a>
                <div class="news-card__body">
                  <h3 class="news-card__title">Nichika Shingaki – Japan</h3>
                  <a href="#" class="news-card__btn">Read More</a>
                </div>
              </article>

              <article class="news-card">
                <a href="#" class="news-card__image">
                  <img src="community/Joshua.jpg" alt="Joshua Park at the studio" loading="lazy">
                </a>
                <div class="news-card__body">
                  <h3 class="news-card__title">Joshua Park – Australia</h3>
                  <a href="#" class="news-card__btn">Read More</a>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
