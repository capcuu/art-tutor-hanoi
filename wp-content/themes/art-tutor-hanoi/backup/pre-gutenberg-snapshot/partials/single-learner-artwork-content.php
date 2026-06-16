<?php
/**
 * Learner artwork — single post content.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$student_title = ath_student_post_title();
$hub_url       = ath_students_artworks_url();
$category      = ath_learner_artwork_category_link();
$lead          = ath_student_post_lead_text();
$body_html     = ath_student_post_body_html();
?>
<main class="student-artwork-page">
  <nav class="course-breadcrumb" aria-label="Breadcrumb">
    <ol class="course-breadcrumb__list">
      <li><a href="<?php echo esc_url( $hub_url ); ?>">Students&rsquo; Artworks</a></li>
      <?php if ( $category ) : ?>
        <li><a href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['name'] ); ?></a></li>
      <?php endif; ?>
      <li aria-current="page"><?php echo esc_html( $student_title ); ?></li>
    </ol>
  </nav>

  <article class="student-artwork-page__article">
    <header class="student-artwork-page__hero">
      <?php if ( has_post_thumbnail() ) : ?>
        <div class="student-artwork-page__portrait">
          <?php
          the_post_thumbnail(
            'large',
            array(
              'class'         => 'student-artwork-page__portrait-img',
              'decoding'      => 'async',
              'fetchpriority' => 'high',
              'alt'           => $student_title,
            )
          );
          ?>
        </div>
      <?php endif; ?>

      <div class="student-artwork-page__intro">
        <h1 class="student-artwork-page__title"><?php echo esc_html( $student_title ); ?></h1>
        <?php if ( $lead !== '' ) : ?>
          <p class="student-artwork-page__subtitle"><?php echo esc_html( $lead ); ?></p>
        <?php endif; ?>
      </div>
    </header>

    <?php if ( $body_html !== '' ) : ?>
      <div class="student-artwork-page__body entry-content">
        <?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filter. ?>
      </div>
    <?php endif; ?>

    <?php
    $prev = get_previous_post( true, '', 'category' );
    $next = get_next_post( true, '', 'category' );
    if ( $prev || $next ) :
      ?>
      <nav class="student-artwork-page__nav" aria-label="Other student profiles">
        <?php if ( $prev ) : ?>
          <a class="student-artwork-page__nav-link student-artwork-page__nav-link--prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
            <span class="student-artwork-page__nav-label">Previous</span>
            <span class="student-artwork-page__nav-name"><?php echo esc_html( ath_student_post_title( $prev ) ); ?></span>
          </a>
        <?php endif; ?>
        <?php if ( $next ) : ?>
          <a class="student-artwork-page__nav-link student-artwork-page__nav-link--next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
            <span class="student-artwork-page__nav-label">Next</span>
            <span class="student-artwork-page__nav-name"><?php echo esc_html( ath_student_post_title( $next ) ); ?></span>
          </a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

    <section class="courses-cta student-artwork-page__cta">
      <div class="courses-cta__inner">
        <p class="courses-cta__text">Explore more work from our students.</p>
        <a href="<?php echo esc_url( $hub_url ); ?>" class="courses-cta__btn">View all student artworks</a>
      </div>
    </section>
  </article>
</main>
