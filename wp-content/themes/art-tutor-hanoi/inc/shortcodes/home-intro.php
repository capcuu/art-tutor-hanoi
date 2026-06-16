<?php
/**
 * Homepage SEO intro — H1 + keyword paragraph below hero.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default shortcode attributes.
 *
 * @return array<string, string>
 */
function ath_home_intro_default_atts() {
	return array(
		'heading'    => 'Art Tutor Hanoi',
		'subheading' => 'English art classes & workshops in Tay Ho, Hanoi',
		'aria_label' => 'Art Tutor Hanoi introduction',
	);
}

/**
 * Export mode toggle.
 *
 * @param bool|null $set Toggle.
 */
function ath_home_intro_export_mode( $set = null ) {
	static $exporting = false;

	if ( $set !== null ) {
		$exporting = (bool) $set;
	}

	return $exporting;
}

/**
 * Gutenberg shortcode block for migration export.
 */
function ath_home_intro_shortcode_block() {
	return "<!-- wp:shortcode -->\n" . ath_home_intro_shortcode_string() . "\n<!-- /wp:shortcode -->";
}

/**
 * Build shortcode string.
 *
 * @param array<string, mixed> $overrides Optional overrides.
 */
function ath_home_intro_shortcode_string( array $overrides = array() ) {
	$atts  = array_merge( ath_home_intro_default_atts(), $overrides );
	$parts = array();

	foreach ( $atts as $key => $value ) {
		$value = (string) $value;
		if ( $value === '' ) {
			continue;
		}
		$parts[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
	}

	return '[ath_home_intro ' . implode( ' ', $parts ) . ']';
}

/**
 * Render homepage intro section.
 *
 * @param array<string, mixed> $args Shortcode attributes.
 */
function ath_render_home_intro_block( array $args = array() ) {
	$args = wp_parse_args( $args, ath_home_intro_default_atts() );

	$courses   = ath_page_url( 'courses' );
	$workshops = ath_page_url( 'workshops' );
	$book      = ath_book_url( 'adult' );
	$pricing   = ath_page_url( 'pricing' );

	ob_start();
	?>
<section class="home-intro" aria-label="<?php echo esc_attr( (string) $args['aria_label'] ); ?>">
  <div class="home-intro__inner">
    <h1 class="home-intro__title"><?php echo esc_html( (string) $args['heading'] ); ?></h1>
    <p class="home-intro__subtitle"><?php echo esc_html( (string) $args['subheading'] ); ?></p>
    <p class="home-intro__text">Structured courses for adults and kids, creative workshops for travelers, and life drawing sessions — all with English-friendly instruction near West Lake.</p>
    <p class="home-intro__links">
      <a href="<?php echo esc_url( $courses ); ?>">Art classes</a>
      <span aria-hidden="true">·</span>
      <a href="<?php echo esc_url( $workshops ); ?>">Workshops</a>
      <span aria-hidden="true">·</span>
      <a href="<?php echo esc_url( $book ); ?>">Book a trial</a>
      <span aria-hidden="true">·</span>
      <a href="<?php echo esc_url( $pricing ); ?>">Pricing</a>
    </p>
  </div>
</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render in partial or export shortcode.
 */
function ath_render_home_intro() {
	if ( ath_home_intro_export_mode() ) {
		echo ath_home_intro_shortcode_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo ath_render_home_intro_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Shortcode callback.
 *
 * @param array<string, string>|string $atts Attributes.
 */
function ath_home_intro_shortcode( $atts ) {
	// H1 is in the hero (.screen-reader-text). Intro block is not shown on the live homepage.
	return '';
}

add_shortcode( 'ath_home_intro', 'ath_home_intro_shortcode' );
