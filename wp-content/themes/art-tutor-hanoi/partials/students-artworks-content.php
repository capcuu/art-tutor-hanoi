<?php
/**
 * Students' Artworks hub — all learner profiles in a grid.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$student_posts = ath_learner_artwork_posts( 0 );
?>
<main class="students-artworks-page">
  <section class="courses-hero" aria-labelledby="students-artworks-heading">
    <div class="courses-hero__inner">
      <h1 id="students-artworks-heading" class="courses-hero__title">Students&rsquo; Artworks</h1>
      <p class="courses-hero__subtitle">150+ artists from 40+ countries have studied at Art Tutor Hanoi.</p>
    </div>
  </section>

  <section class="students-artworks-grid-section" aria-label="Student artwork profiles">
    <div class="students-artworks-grid-section__inner">
      <?php if ( empty( $student_posts ) ) : ?>
        <p class="students-artworks-grid-section__empty">No student profiles yet. Check back soon.</p>
      <?php else : ?>
        <div class="students-artworks-grid">
          <?php foreach ( $student_posts as $student_post ) : ?>
            <?php
            $label     = ath_community_student_label( $student_post );
            $permalink = get_permalink( $student_post );
            $image_url = ath_community_student_image_url( $student_post );
            ?>
          <article class="news-card">
            <a href="<?php echo esc_url( $permalink ); ?>" class="news-card__image">
              <?php if ( $image_url !== '' ) : ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $label ); ?>" loading="lazy" decoding="async">
              <?php else : ?>
                <span class="news-card__image-placeholder" aria-hidden="true"></span>
              <?php endif; ?>
            </a>
            <div class="news-card__body">
              <h2 class="news-card__title"><?php echo esc_html( $label ); ?></h2>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>
