<?php
/**
 * Workshop page body shell — used from page-content.php (default template).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

if ( ath_should_use_workshop_gutenberg() ) {
	?>
<main class="experience-page">
  <nav class="course-breadcrumb" aria-label="Breadcrumb">
    <ol class="course-breadcrumb__list">
      <li><a href="<?php echo esc_url( ath_workshops_hub_url() ); ?>">Workshops</a></li>
      <li aria-current="page"><?php the_title(); ?></li>
    </ol>
  </nav>

  <article class="experience-article">
    <?php the_content(); ?>
  </article>
  <?php ath_render_commercial_crosslinks( 'workshops' ); ?>
</main>
	<?php
	return;
}

$slug       = get_post()->post_name;
$experience = v2_experience_by_slug( $slug );

if ( ! $experience ) {
	?>
<main class="experience-page">
  <p>Workshop not found.</p>
  <p><a href="<?php echo esc_url( ath_workshops_hub_url() ); ?>">View all workshops</a></p>
</main>
	<?php
	return;
}

require ATH_THEME_DIR . '/partials/experience-content.php';
