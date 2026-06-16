<?php
/**
 * Default page body — Gutenberg, routed partial, or generic editor layout.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

while ( have_posts() ) {
	the_post();

	$slug = get_post()->post_name;

	if ( ath_is_chromeless_page() ) {
		?>
<main class="<?php echo esc_attr( ath_chromeless_page_main_class() ); ?>">
		<?php the_content(); ?>
</main>
		<?php
		break;
	}

	if ( ath_is_workshop_page() && ath_content_mode() !== 'legacy' ) {
		require ATH_THEME_DIR . '/partials/workshop-page-content.php';
		break;
	}

	if ( ath_is_adult_course_detail_page() && ath_content_mode() !== 'legacy' ) {
		require ATH_THEME_DIR . '/partials/course-page-content.php';
		break;
	}

	if ( ath_should_use_page_gutenberg() ) {
		if ( ath_page_uses_full_html_layout() && ! ath_page_uses_pricing_layout() && $slug !== 'about' ) {
			the_content();
			break;
		}

		$pricing_layout = ath_page_uses_pricing_layout();
		$about_layout   = ( $slug === 'about' );
		$hub_layout     = in_array( $slug, array( 'courses', 'kids-courses' ), true );

		if ( $about_layout ) {
			?>
<main class="about-page">
			<?php the_content(); ?>
</main>
			<?php
			break;
		}

		if ( $pricing_layout ) {
			?>
<main class="pricing-page">
  <section class="pricing-hero" aria-labelledby="pricing-heading">
    <div class="pricing-hero__inner">
      <h1 id="pricing-heading" class="pricing-hero__title"><?php echo esc_html( ath_page_seo_h1() ); ?></h1>
			<?php
			echo ath_render_pricing_hero_intro(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_blocks output.
			?>
    </div>
  </section>

  <section class="pricing-programs" aria-label="Pricing options">
    <div class="pricing-programs__inner">
			<?php the_content(); ?>
    </div>
  </section>
</main>
			<?php
			break;
		}

		$main_class = 'generic-page';
		if ( $slug === 'courses' ) {
			$main_class = 'courses-page';
		} elseif ( $slug === 'kids-courses' ) {
			$main_class = 'courses-page courses-page--kids';
		} elseif ( $slug === 'hanoi-art-supply-map' ) {
			$main_class = 'generic-page generic-page--art-supplies';
		}
		?>
<main class="<?php echo esc_attr( $main_class ); ?>">
  <section class="courses-hero" aria-labelledby="generic-page-heading">
    <div class="courses-hero__inner">
      <h1 id="generic-page-heading" class="courses-hero__title"><?php echo esc_html( ath_page_seo_h1() ); ?></h1>
			<?php
			if ( $hub_layout ) {
				echo ath_render_hub_hero_subtitle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_blocks output.
			}
			?>
    </div>
  </section>

		<?php if ( $hub_layout ) : ?>
  <?php the_content(); ?>
		<?php else : ?>
  <section class="generic-page__body" aria-label="Page content">
    <div class="generic-page__inner generic-page__content">
      <?php the_content(); ?>
    </div>
  </section>
		<?php endif; ?>
</main>
		<?php
		break;
	}

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
      <h1 id="generic-page-heading" class="courses-hero__title"><?php echo esc_html( ath_page_seo_h1() ); ?></h1>
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
