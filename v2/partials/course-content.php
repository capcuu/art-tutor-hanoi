<?php
/**
 * @var array $course
 */
require_once __DIR__ . '/../config/images.php';

$hub_url   = ! empty( $course['hub_url'] ) ? $course['hub_url'] : 'courses.php';
$hub_label = ! empty( $course['hub_label'] ) ? $course['hub_label'] : 'Courses';
$pathway_href = $hub_url . '#' . rawurlencode( $course['pathway_id'] );
$hero_src  = v2_img_url( $course['image']['url'], 'course_hero' );
$hero_size = v2_img_display_size( 'course_hero' );
?>
<main class="course-page">
  <nav class="course-breadcrumb" aria-label="Breadcrumb">
    <ol class="course-breadcrumb__list">
      <li><a href="<?php echo htmlspecialchars( $hub_url, ENT_QUOTES, 'UTF-8' ); ?>"><?php echo htmlspecialchars( $hub_label, ENT_QUOTES, 'UTF-8' ); ?></a></li>
      <li><a href="<?php echo htmlspecialchars( $pathway_href, ENT_QUOTES, 'UTF-8' ); ?>"><?php echo htmlspecialchars( $course['pathway_title'], ENT_QUOTES, 'UTF-8' ); ?></a></li>
      <li aria-current="page"><?php echo htmlspecialchars( $course['title'], ENT_QUOTES, 'UTF-8' ); ?></li>
    </ol>
  </nav>

  <article class="course-article">
    <header class="course-hero">
      <div class="course-hero__media">
        <img
          src="<?php echo htmlspecialchars( $hero_src, ENT_QUOTES, 'UTF-8' ); ?>"
          alt="<?php echo htmlspecialchars( $course['image']['alt'], ENT_QUOTES, 'UTF-8' ); ?>"
          width="<?php echo (int) $hero_size['width']; ?>"
          height="450"
          decoding="async"
          fetchpriority="high"
        >
      </div>
      <div class="course-hero__content">
        <p class="course-hero__pathway">
          <a href="<?php echo htmlspecialchars( $pathway_href, ENT_QUOTES, 'UTF-8' ); ?>"><?php echo htmlspecialchars( $course['pathway_title'], ENT_QUOTES, 'UTF-8' ); ?></a>
        </p>
        <h1 class="course-hero__title"><?php echo htmlspecialchars( $course['title'], ENT_QUOTES, 'UTF-8' ); ?></h1>
        <?php if ( ! empty( $course['sessions'] ) ) : ?>
          <p class="course-hero__sessions"><?php echo htmlspecialchars( $course['sessions'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <?php endif; ?>
        <?php if ( ! empty( $course['intro'] ) ) : ?>
          <p class="course-hero__intro"><?php echo htmlspecialchars( $course['intro'], ENT_QUOTES, 'UTF-8' ); ?></p>
        <?php endif; ?>
      </div>
    </header>

    <?php if ( ! empty( $course['gallery'] ) ) : ?>
      <section class="course-gallery" aria-label="Student work examples">
        <div class="course-gallery__grid">
          <?php foreach ( $course['gallery'] as $item ) : ?>
            <?php
            $item_class = 'course-gallery__item';
            if ( ! empty( $item['wide'] ) ) {
              $item_class .= ' course-gallery__item--wide';
            }
            $alt = ! empty( $item['caption'] )
              ? $item['caption'] . ' — ' . $course['title']
              : 'Student work — ' . $course['title'];
            $gallery_src  = v2_img_url( $item['url'], 'gallery' );
            $gallery_size = v2_img_display_size( 'gallery' );
            ?>
            <figure class="<?php echo htmlspecialchars( $item_class, ENT_QUOTES, 'UTF-8' ); ?>">
              <img
                src="<?php echo htmlspecialchars( $gallery_src, ENT_QUOTES, 'UTF-8' ); ?>"
                alt="<?php echo htmlspecialchars( $alt, ENT_QUOTES, 'UTF-8' ); ?>"
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

    <div class="course-body">
      <?php foreach ( $course['blocks'] as $block ) : ?>
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

      <?php if ( ! empty( $course['sessions_breakdown'] ) ) : ?>
        <section class="course-sessions" aria-labelledby="course-sessions-heading">
          <h2 id="course-sessions-heading" class="course-sessions__title">Session breakdown</h2>
          <ol class="course-sessions__list">
            <?php foreach ( $course['sessions_breakdown'] as $session ) : ?>
              <li class="course-session">
                <h3 class="course-session__title"><?php echo htmlspecialchars( $session['title'], ENT_QUOTES, 'UTF-8' ); ?></h3>
                <p class="course-session__text"><?php echo htmlspecialchars( $session['text'], ENT_QUOTES, 'UTF-8' ); ?></p>
              </li>
            <?php endforeach; ?>
          </ol>
        </section>
      <?php endif; ?>
    </div>
  </article>

  <section class="courses-cta">
    <div class="courses-cta__inner">
      <p class="courses-cta__text">Ready to start? Book a sample class or view pricing.</p>
      <a href="experience.php?slug=trial-art-class" class="courses-cta__btn">Book a Sample Class</a>
      <p class="courses-cta__links">
        <a href="<?php echo htmlspecialchars( $pathway_href, ENT_QUOTES, 'UTF-8' ); ?>">Back to <?php echo htmlspecialchars( $course['pathway_title'], ENT_QUOTES, 'UTF-8' ); ?></a>
        <span aria-hidden="true">&middot;</span>
        <a href="<?php echo htmlspecialchars( $hub_url, ENT_QUOTES, 'UTF-8' ); ?>">All <?php echo htmlspecialchars( strtolower( $hub_label ), ENT_QUOTES, 'UTF-8' ); ?></a>
      </p>
    </div>
  </section>
</main>
