<?php
/**
 * Post views — single article or category/tag archive.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$home_url = ath_page_url( 'home' );

if ( is_singular( 'post' ) ) :
	$primary_cat = ath_blog_post_primary_category();
	?>
<main class="blog-post-page generic-page">
  <nav class="course-breadcrumb" aria-label="Breadcrumb">
    <ol class="course-breadcrumb__list">
      <li><a href="<?php echo esc_url( $home_url ); ?>">Home</a></li>
      <?php if ( $primary_cat ) : ?>
        <li><a href="<?php echo esc_url( $primary_cat['url'] ); ?>"><?php echo esc_html( $primary_cat['name'] ); ?></a></li>
      <?php endif; ?>
      <li aria-current="page"><?php echo esc_html( get_the_title() ); ?></li>
    </ol>
  </nav>

  <article class="blog-post-page__article">
    <header class="blog-post-page__hero courses-hero" aria-labelledby="blog-post-heading">
      <div class="courses-hero__inner">
        <h1 id="blog-post-heading" class="courses-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>
        <p class="blog-post-page__meta">
          <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
          <?php if ( $primary_cat ) : ?>
            <span class="blog-post-page__meta-sep" aria-hidden="true">·</span>
            <a href="<?php echo esc_url( $primary_cat['url'] ); ?>"><?php echo esc_html( $primary_cat['name'] ); ?></a>
          <?php endif; ?>
        </p>
      </div>
    </header>

    <?php if ( has_post_thumbnail() ) : ?>
      <figure class="blog-post-page__featured">
        <?php
        the_post_thumbnail(
          'large',
          array(
            'class'    => 'blog-post-page__featured-img',
            'decoding' => 'async',
            'alt'      => get_the_title(),
          )
        );
        ?>
      </figure>
    <?php endif; ?>

    <section class="generic-page__body blog-post-page__body" aria-label="Article content">
      <div class="generic-page__inner generic-page__content entry-content">
        <?php the_content(); ?>
      </div>
    </section>

    <?php
    $prev = get_previous_post();
    $next = get_next_post();
    if ( $prev || $next ) :
      ?>
      <nav class="blog-post-page__nav student-artwork-page__nav" aria-label="Other articles">
        <?php if ( $prev ) : ?>
          <a class="student-artwork-page__nav-link student-artwork-page__nav-link--prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
            <span class="student-artwork-page__nav-label">Previous</span>
            <span class="student-artwork-page__nav-name"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
          </a>
        <?php endif; ?>
        <?php if ( $next ) : ?>
          <a class="student-artwork-page__nav-link student-artwork-page__nav-link--next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
            <span class="student-artwork-page__nav-label">Next</span>
            <span class="student-artwork-page__nav-name"><?php echo esc_html( get_the_title( $next ) ); ?></span>
          </a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

    <?php if ( comments_open() || get_comments_number() ) : ?>
      <section class="blog-post-page__comments generic-page__body" aria-label="Comments">
        <div class="generic-page__inner">
          <?php comments_template(); ?>
        </div>
      </section>
    <?php endif; ?>
  </article>
</main>
	<?php
	return;
endif;

$term        = get_queried_object();
$heading     = ath_post_archive_heading();
$description = '';
$is_learner  = false;

if ( ( is_category() || is_tag() ) && $term instanceof WP_Term ) {
	$description = term_description( $term );
	$is_learner  = ath_is_learner_artwork_category( $term );
}
$updated_map = array();

if ( $is_learner && have_posts() ) {
	global $wp_query;
	$post_ids    = wp_list_pluck( $wp_query->posts, 'ID' );
	$updated_map = ath_student_post_last_comment_timestamps_map( $post_ids );
}
?>
<main class="category-archive-page generic-page">
  <nav class="course-breadcrumb" aria-label="Breadcrumb">
    <ol class="course-breadcrumb__list">
      <li><a href="<?php echo esc_url( $home_url ); ?>">Home</a></li>
      <li aria-current="page"><?php echo esc_html( $heading ); ?></li>
    </ol>
  </nav>

  <section class="courses-hero category-archive-page__hero" aria-labelledby="category-archive-heading">
    <div class="courses-hero__inner">
      <h1 id="category-archive-heading" class="courses-hero__title"><?php echo esc_html( $heading ); ?></h1>
      <?php if ( $description !== '' ) : ?>
        <div class="category-archive-page__description entry-content">
          <?php echo wp_kses_post( $description ); ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="category-archive-page__grid-section" aria-label="<?php echo esc_attr( $heading ); ?>">
    <div class="category-archive-page__grid-inner">
      <?php if ( have_posts() ) : ?>
        <div class="category-archive-grid">
          <?php
          while ( have_posts() ) :
            the_post();
            $permalink = get_permalink();
            ?>
            <?php if ( $is_learner ) : ?>
              <?php
              $label        = ath_community_student_label();
              $image_html   = ath_community_student_image_html();
              $updated_ts   = $updated_map[ get_the_ID() ] ?? 0;
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
                <h2 class="news-card__title">
                  <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $label ); ?></a>
                </h2>
                <?php if ( $last_updated !== '' ) : ?>
                  <p class="news-card__updated">
                    <time datetime="<?php echo esc_attr( $updated_iso ); ?>"><?php echo esc_html( $last_updated ); ?></time>
                  </p>
                <?php endif; ?>
              </div>
            </article>
            <?php else : ?>
              <?php
              $title     = get_the_title();
              $image_url = ath_studio_news_post_image_url();
              $excerpt   = get_the_excerpt();
              ?>
            <article class="news-card">
              <a href="<?php echo esc_url( $permalink ); ?>" class="news-card__image">
                <?php if ( $image_url !== '' ) : ?>
                  <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async">
                <?php else : ?>
                  <span class="news-card__image-placeholder" aria-hidden="true"></span>
                <?php endif; ?>
              </a>
              <div class="news-card__body">
                <h2 class="news-card__title">
                  <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                </h2>
                <p class="news-card__updated">
                  <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                </p>
                <?php if ( $excerpt !== '' ) : ?>
                  <p class="category-archive-page__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url( $permalink ); ?>" class="news-card__btn"><?php esc_html_e( 'Read more', 'art-tutor-hanoi' ); ?></a>
              </div>
            </article>
            <?php endif; ?>
          <?php endwhile; ?>
        </div>

        <nav class="category-archive-page__pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'art-tutor-hanoi' ); ?>">
          <?php
          the_posts_pagination(
            array(
              'mid_size'  => 2,
              'prev_text' => __( 'Previous', 'art-tutor-hanoi' ),
              'next_text' => __( 'Next', 'art-tutor-hanoi' ),
            )
          );
          ?>
        </nav>
      <?php else : ?>
        <p class="category-archive-page__empty"><?php esc_html_e( 'No posts found.', 'art-tutor-hanoi' ); ?></p>
      <?php endif; ?>
    </div>
  </section>
</main>
