<?php
/**
 * Course page body shell — used from page-content.php (default template).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$slug   = get_post()->post_name;
$course = v2_course_by_slug( $slug );

if ( ath_should_use_page_gutenberg() ) {
	$hub_url       = $course ? ( ! empty( $course['hub_url'] ) ? ath_resolve_url( $course['hub_url'] ) : ath_page_url( 'courses' ) ) : ath_page_url( 'courses' );
	$hub_label     = $course && ! empty( $course['hub_label'] ) ? (string) $course['hub_label'] : 'Courses';
	$pathway_href  = ( $course && ! empty( $course['pathway_id'] ) )
		? $hub_url . '#' . rawurlencode( (string) $course['pathway_id'] )
		: $hub_url;
	$pathway_title = $course && ! empty( $course['pathway_title'] ) ? (string) $course['pathway_title'] : '';
	?>
<main class="course-page">
  <nav class="course-breadcrumb" aria-label="Breadcrumb">
    <ol class="course-breadcrumb__list">
      <li><a href="<?php echo esc_url( $hub_url ); ?>"><?php echo esc_html( $hub_label ); ?></a></li>
      <?php if ( $pathway_title !== '' ) : ?>
        <li><a href="<?php echo esc_url( $pathway_href ); ?>"><?php echo esc_html( $pathway_title ); ?></a></li>
      <?php endif; ?>
      <li aria-current="page"><?php the_title(); ?></li>
    </ol>
  </nav>

  <article class="course-article">
    <?php the_content(); ?>
  </article>
  <?php ath_render_commercial_crosslinks( 'courses' ); ?>
</main>
	<?php
	return;
}

if ( ! $course ) {
	?>
<main class="course-page">
  <p>Course not found.</p>
  <p><a href="<?php echo esc_url( ath_page_url( 'courses' ) ); ?>">View all courses</a></p>
</main>
	<?php
	return;
}

require ATH_THEME_DIR . '/partials/course-content.php';
