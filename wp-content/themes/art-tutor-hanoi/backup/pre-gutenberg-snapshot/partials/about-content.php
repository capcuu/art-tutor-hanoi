<?php
require_once __DIR__ . '/../config/cloudinary.php';
$teachers      = require __DIR__ . '/../data/teachers.php';
$studio_images = require __DIR__ . '/../data/studio-images.php';

// Delivery sizes ~2× display width — keeps About page fast.
$about_img_hero = 'f_auto,q_auto:good,w_1280';
$about_img_life = 'c_fill,w_560,h_420,f_auto,q_auto:good';
$about_teacher_size = 280;

$hero_img = cld_studio_image( $studio_images['hero'], $about_img_hero );
$life_img = cld_studio_image( $studio_images['studio_life'], $about_img_life );
?>
<main class="about-page">
  <!-- Studio banner -->
  <?php if ( $hero_img ) : ?>
    <section class="about-banner" aria-label="<?php echo htmlspecialchars( $studio_images['hero']['alt'], ENT_QUOTES, 'UTF-8' ); ?>">
      <img class="about-banner__image" src="<?php echo htmlspecialchars( $hero_img, ENT_QUOTES, 'UTF-8' ); ?>" alt="<?php echo htmlspecialchars( $studio_images['hero']['alt'], ENT_QUOTES, 'UTF-8' ); ?>" width="1280" height="480" decoding="async" fetchpriority="high">
    </section>
  <?php endif; ?>

  <!-- Meet the Team -->
  <section class="about-section about-team" aria-labelledby="about-team-heading">
    <div class="about-section__inner">
      <h2 id="about-team-heading" class="about-section__title">Meet the Team</h2>
      <ul class="about-team__grid">
        <?php foreach ( $teachers as $teacher ) : ?>
          <?php
          $photo = ! empty( $teacher['cloudinary_id'] )
            ? cld_teacher_photo(
              $teacher['cloudinary_id'],
              $about_teacher_size,
              isset( $teacher['cloudinary_version'] ) ? $teacher['cloudinary_version'] : null
            )
            : $teacher['image_url'];
          $alt   = 'Portrait of ' . $teacher['name'];
          ?>
          <li class="teacher-card">
            <div class="teacher-card__photo">
              <img src="<?php echo htmlspecialchars( $photo, ENT_QUOTES, 'UTF-8' ); ?>" alt="<?php echo htmlspecialchars( $alt, ENT_QUOTES, 'UTF-8' ); ?>" width="280" height="280" loading="lazy" decoding="async">
            </div>
            <div class="teacher-card__body">
              <h3 class="teacher-card__name"><?php echo htmlspecialchars( $teacher['name'], ENT_QUOTES, 'UTF-8' ); ?></h3>
              <p class="teacher-card__role"><?php echo htmlspecialchars( $teacher['role'], ENT_QUOTES, 'UTF-8' ); ?></p>
              <p class="teacher-card__links">
                <a href="<?php echo htmlspecialchars( $teacher['cv'], ENT_QUOTES, 'UTF-8' ); ?>" target="_blank" rel="noopener noreferrer">Download CV</a>
                <?php if ( ! empty( $teacher['website'] ) ) : ?>
                  <span class="teacher-card__sep" aria-hidden="true">·</span>
                  <a href="<?php echo htmlspecialchars( $teacher['website'], ENT_QUOTES, 'UTF-8' ); ?>" target="_blank" rel="noopener noreferrer">Website</a>
                <?php endif; ?>
              </p>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="about-team__faq">More information can be found on our <a href="<?php echo esc_url( ath_page_url( 'faq' ) ); ?>">FAQ</a> page.</p>
    </div>
  </section>

  <!-- Studio Life -->
  <section class="about-section about-studio-life" aria-labelledby="about-studio-heading">
    <div class="about-section__inner about-split">
      <div class="about-split__media">
        <?php if ( $life_img ) : ?>
          <img src="<?php echo htmlspecialchars( $life_img, ENT_QUOTES, 'UTF-8' ); ?>" alt="<?php echo htmlspecialchars( $studio_images['studio_life']['alt'], ENT_QUOTES, 'UTF-8' ); ?>" width="560" height="420" loading="lazy" decoding="async">
        <?php endif; ?>
      </div>
      <div class="about-split__content">
        <h2 id="about-studio-heading" class="about-section__title">Studio Life</h2>
        <div class="about-prose">
          <p>At <a href="<?php echo esc_url( ath_page_url( 'home' ) ); ?>">Art Tutor Hanoi</a>, we are a team of academically trained artists — including watercolor artists, oil painters, and experienced academic drawing teachers — committed to sharing our passion in an open, international environment.</p>
          <p>Whether you&rsquo;re in Hanoi for a short visit or a long stay, our workshops offer more than just a fun afternoon. They are a way to connect with real artists, build skills, and enjoy one of the most creative things to do in Hanoi.</p>
        </div>
      </div>
    </div>
  </section>

  <?php require __DIR__ . '/community-banner.php'; ?>

  <section class="about-section about-story-inline">
    <div class="about-section__inner">
      <div class="about-prose about-prose--center">
        <p>Our course materials are inspired by official training from the <a href="https://en.wikipedia.org/wiki/Vietnam_University_of_Fine_Arts" target="_blank" rel="noopener noreferrer">Vietnam University of Fine Arts</a>, as well as global programs like the <a href="https://www.washingtonstudioschool.org/" target="_blank" rel="noopener noreferrer">Washington Studio School</a> and <a href="https://www.virtualartacademy.com/" target="_blank" rel="noopener noreferrer">Virtual Art Academy</a>. You&rsquo;ll not only meet the artist — you&rsquo;ll learn from them in a hands-on, culturally rich setting.</p>
        <p>Join our growing international art community, and make art part of your journey.</p>
      </div>
    </div>
  </section>

  <!-- Visit Our Studio -->
  <section class="about-section about-visit" aria-labelledby="about-visit-heading">
    <div class="about-section__inner about-split about-split--reverse">
      <div class="about-split__content">
        <h2 id="about-visit-heading" class="about-section__title">Visit Our Studio</h2>
        <ul class="about-visit__list">
          <li>82 Ngách 264/15, Ngõ 374 Đường Âu Cơ, Tây Hồ, Hanoi, Vietnam</li>
          <li><a href="tel:+84988288302">(+84) 98 828 8302</a> · Zalo / WhatsApp</li>
          <li><a href="mailto:contact@arttutorhanoi.com">contact@arttutorhanoi.com</a></li>
        </ul>
        <p class="about-visit__partners">Also available on GetYourGuide, Airbnb &amp; Tripadvisor</p>
      </div>
      <div class="about-split__media about-split__media--map">
        <div class="about-visit__map">
          <iframe
            src="https://www.google.com/maps?q=Art+tutor+Hanoi,21.077096,105.8237665&hl=en&z=17&output=embed"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Art Tutor Hanoi on Google Maps">
          </iframe>
        </div>
      </div>
    </div>
  </section>
</main>
