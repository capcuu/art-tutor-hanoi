<?php
defined( 'ABSPATH' ) || exit;

$frontpage_gallery = require ATH_THEME_DIR . '/data/frontpage-gallery.php';
$gallery_batches   = ath_prepare_frontpage_gallery_batches( $frontpage_gallery, 5 );
$gallery_has_more    = ! empty( $gallery_batches[2] );
?>
<!-- Hero -->
  <section class="hero" aria-label="Art studio">
    <video
      class="hero__video"
      autoplay
      muted
      loop
      playsinline
      preload="auto"
      poster="<?php echo esc_url( ath_asset_url( 'hero2.jpg' ) ); ?>"
    >
      <source src="<?php echo esc_url( cld_home_hero_video_url() ); ?>" type="video/mp4">
    </video>
  </section>

  <!-- Cards -->
  <section class="cards-section">
    <div class="cards">
      <article class="card">
        <h2 class="card__title">Sample art class</h2>
        <p class="card__desc">Try a taster session and experience our teaching style before you commit to a full course.</p>
        <a href="<?php echo esc_url( ath_experience_url( 'trial-art-class' ) ); ?>" class="btn-view">View Courses</a>
      </article>

      <article class="card">
        <h2 class="card__title">Life drawing</h2>
        <p class="card__desc">Figure drawing from life with guided instruction in proportion, anatomy, and expressive mark-making.</p>
        <a href="<?php echo esc_url( ath_experience_url( 'life-drawing' ) ); ?>" class="btn-view">View Courses</a>
      </article>

      <article class="card">
        <h2 class="card__title">Artist residency</h2>
        <p class="card__desc">Dedicated studio time and mentorship for artists developing their practice in Hanoi.</p>
        <a href="<?php echo esc_url( ath_experience_url( 'artist-residency' ) ); ?>" class="btn-view">View Courses</a>
      </article>

      <article class="card">
        <h2 class="card__title">Kids art class</h2>
        <p class="card__desc">Creative classes for young artists — drawing, painting, and fun projects in a supportive setting.</p>
        <a href="<?php echo esc_url( ath_page_url( 'kids-courses' ) ); ?>" class="btn-view">View Courses</a>
      </article>
    </div>
  </section>

  <!-- Gallery -->
  <section class="gallery-section">
    <p class="gallery-section__intro">
      Art Tutor Hanoi are a team of academically trained artists — including watercolor artists, oil paint artists, and experienced drawing teachers.
    </p>

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
            $style    = sprintf(
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

      <button type="button" class="gallery-more<?php echo $gallery_has_more ? '' : ' is-hidden'; ?>">Load more photos</button>
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

  <!-- Courses -->
  <section class="courses-section">
    <div class="courses-section__inner">
      <div class="courses-section__header">
        <p class="courses-section__label">Our courses</p>
        <h2 class="courses-section__heading">Structured programs for every stage of your artistic journey in Hanoi.</h2>
      </div>

      <div class="courses-list">
        <a href="<?php echo esc_url( ath_page_url( 'courses' ) . '#drawing-foundations' ); ?>" class="course-row">
          <span class="course-row__num">01</span>
          <span class="course-row__title">Drawing Foundations</span>
          <span class="course-row__arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </a>

        <a href="<?php echo esc_url( ath_page_url( 'courses' ) . '#charcoal-drawing' ); ?>" class="course-row">
          <span class="course-row__num">02</span>
          <span class="course-row__title">Charcoal Drawing</span>
          <span class="course-row__arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </a>

        <a href="<?php echo esc_url( ath_page_url( 'courses' ) . '#colour-painting' ); ?>" class="course-row">
          <span class="course-row__num">03</span>
          <span class="course-row__title">Colour &amp; Painting</span>
          <span class="course-row__arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </a>

        <a href="<?php echo esc_url( ath_page_url( 'courses' ) . '#life-drawing' ); ?>" class="course-row">
          <span class="course-row__num">04</span>
          <span class="course-row__title">Life Drawing</span>
          <span class="course-row__arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </a>
      </div>

      <div class="courses-section__footer">
        <a href="<?php echo esc_url( ath_page_url( 'courses' ) ); ?>" class="courses-section__link">Explore all courses</a>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="testimonials-section">
    <div class="testimonials-panel">
      <p class="testimonials-panel__label">Testimonials</p>
      <h2 class="testimonials-panel__heading">What our students say</h2>

      <div class="testimonials-slider">
        <button type="button" class="testimonials-nav testimonials-nav--prev" aria-label="Previous review">
          <svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg>
        </button>

        <div class="testimonials-track-wrap">
          <div class="testimonials-track" id="testimonials-track">
            <div class="testimonial-slide">
              <p class="testimonial-slide__text">The teachers at Art Tutor Hanoi are incredibly supportive. My watercolor skills improved dramatically in just a few months — I finally feel confident showing my work.</p>
              <p class="testimonial-slide__author">Sarah M.</p>
            </div>
            <div class="testimonial-slide">
              <p class="testimonial-slide__text">Life drawing sessions here are the best in Hanoi. Professional, welcoming, and genuinely inspiring — exactly what I was looking for as a returning artist.</p>
              <p class="testimonial-slide__author">James L.</p>
            </div>
            <div class="testimonial-slide">
              <p class="testimonial-slide__text">My daughter loves the kids art class. She can't wait for Saturday mornings and comes home proud of everything she creates.</p>
              <p class="testimonial-slide__author">Linh T.</p>
            </div>
            <div class="testimonial-slide">
              <p class="testimonial-slide__text">The artist residency gave me the studio time and mentorship I needed to finish my portfolio. A calm, focused environment in the heart of the city.</p>
              <p class="testimonial-slide__author">Ana K.</p>
            </div>
          </div>
        </div>

        <button type="button" class="testimonials-nav testimonials-nav--next" aria-label="Next review">
          <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
        </button>
      </div>

      <div class="testimonials-dots" id="testimonials-dots"></div>
    </div>

    <div class="testimonials-photo">
      <img src="<?php echo esc_url( ath_asset_url( 'gallery/HTT_1707.jpg' ) ); ?>" alt="Students at an art exhibition" loading="lazy">
    </div>
  </section>

  <?php
  $community_always_visible = true;
  require __DIR__ . '/community-banner.php';
  ?>

  <?php
  /**
   * "From Our Studio" — controlled by ATH_SHOW_HOME_NEWS_SECTION in functions.php.
   * Hiện đang tắt; bật lại khi có nội dung studio/news chính thức (WP posts hoặc data thật).
   */
  if ( ATH_SHOW_HOME_NEWS_SECTION ) :
    ?>
  <!-- From Our Studio -->
  <section class="news-section">
    <div class="news-section__inner">
      <p class="news-section__label">Art Tutor Hanoi</p>
      <h2 class="news-section__heading">From Our Studio</h2>

      <div class="news-carousel">
        <button type="button" class="news-nav news-nav--prev" aria-label="Previous news">
          <svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg>
        </button>

        <div class="news-track-wrap" id="news-track-wrap">
          <div class="news-track" id="news-track">
            <article class="news-card">
              <a href="#" class="news-card__image">
                <img src="<?php echo esc_url( ath_asset_url( 'gallery/HTT_1587.jpg' ) ); ?>" alt="Open studio exhibition" loading="lazy">
              </a>
              <div class="news-card__body">
                <h3 class="news-card__title">Open Studio Show 2025</h3>
                <a href="#" class="news-card__btn">Read More</a>
              </div>
            </article>

            <article class="news-card">
              <a href="#" class="news-card__image">
                <img src="<?php echo esc_url( ath_asset_url( 'gallery/HTT_1721.jpg' ) ); ?>" alt="Life drawing session" loading="lazy">
              </a>
              <div class="news-card__body">
                <h3 class="news-card__title">New Life Drawing Sessions</h3>
                <a href="#" class="news-card__btn">Read More</a>
              </div>
            </article>

            <article class="news-card">
              <a href="#" class="news-card__image">
                <img src="<?php echo esc_url( ath_asset_url( 'gallery/IMG_8700.JPG' ) ); ?>" alt="Kids art class" loading="lazy">
              </a>
              <div class="news-card__body">
                <h3 class="news-card__title">Kids Summer Art Camp</h3>
                <a href="#" class="news-card__btn">Read More</a>
              </div>
            </article>

            <article class="news-card">
              <a href="#" class="news-card__image">
                <img src="<?php echo esc_url( ath_asset_url( 'gallery/HTT_1568.jpg' ) ); ?>" alt="Watercolour workshop" loading="lazy">
              </a>
              <div class="news-card__body">
                <h3 class="news-card__title">Watercolour Workshop Series</h3>
                <a href="#" class="news-card__btn">Read More</a>
              </div>
            </article>

            <article class="news-card">
              <a href="#" class="news-card__image">
                <img src="<?php echo esc_url( ath_asset_url( 'gallery/HTT_1672.jpg' ) ); ?>" alt="Student exhibition" loading="lazy">
              </a>
              <div class="news-card__body">
                <h3 class="news-card__title">Student Exhibition Highlights</h3>
                <a href="#" class="news-card__btn">Read More</a>
              </div>
            </article>

            <article class="news-card">
              <a href="#" class="news-card__image">
                <img src="<?php echo esc_url( ath_asset_url( 'gallery/IMG_8783.JPG' ) ); ?>" alt="Art tutors" loading="lazy">
              </a>
              <div class="news-card__body">
                <h3 class="news-card__title">In Focus: Meet Our Tutors</h3>
                <a href="#" class="news-card__btn">Read More</a>
              </div>
            </article>
          </div>
        </div>

        <button type="button" class="news-nav news-nav--next" aria-label="Next news">
          <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
        </button>
      </div>
    </div>
  </section>
  <?php endif; ?>