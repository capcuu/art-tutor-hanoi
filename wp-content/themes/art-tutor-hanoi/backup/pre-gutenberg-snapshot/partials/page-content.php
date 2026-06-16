<?php
/**
 * Default page body — routed partial or WordPress editor content.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

while ( have_posts() ) {
	the_post();

	$slug    = get_post()->post_name;
	$partial = ath_routed_page_partial_path( $slug );

	if ( $partial ) {
		if ( $slug === ath_calendar_page_slug() ) {
			extract( ath_calendar_page_vars(), EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		require $partial;
		break;
	}
	?>
<main class="generic-page<?php echo $slug === 'hanoi-art-supply-map' ? ' generic-page--art-supplies' : ''; ?>">
  <section class="courses-hero" aria-labelledby="generic-page-heading">
    <div class="courses-hero__inner">
      <h1 id="generic-page-heading" class="courses-hero__title"><?php echo esc_html( ath_page_display_title() ); ?></h1>
    </div>
  </section>

  <section class="generic-page__body" aria-label="Page content">
    <div class="generic-page__inner generic-page__content">
      <?php the_content(); ?>
    </div>
  </section>
</main>
	<?php
}
