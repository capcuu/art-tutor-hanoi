<?php
/**
 * v2 page layout wrapper.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a page without site header, footer, or chat widgets.
 *
 * @param callable $content_callback Outputs main content.
 * @param string   $title            Document title (optional).
 */
function ath_render_chromeless_page( $content_callback, $title = '' ) {
	if ( $title !== '' ) {
		add_filter(
			'pre_get_document_title',
			static function () use ( $title ) {
				return $title;
			},
			99
		);
	}

	status_header( 200 );
	nocache_headers();
	?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class( 'ath-chromeless' ); ?>>
<?php wp_body_open(); ?>
	<?php
	if ( is_callable( $content_callback ) ) {
		call_user_func( $content_callback );
	}
	?>
<?php wp_footer(); ?>
</body>
</html>
	<?php
	exit;
}

/**
 * Render a full v2 page (head, header, content, footer).
 *
 * @param callable $content_callback Outputs main content.
 * @param string   $title            Document title (optional).
 */
function ath_render_page( $content_callback, $title = '' ) {
	if ( is_singular( 'page' ) && ath_is_chromeless_page() ) {
		ath_render_chromeless_page( $content_callback, $title );
	}

	if ( $title !== '' ) {
		add_filter(
			'pre_get_document_title',
			static function () use ( $title ) {
				return $title;
			},
			99
		);
	}

	status_header( 200 );
	nocache_headers();

	require ATH_THEME_DIR . '/partials/header.php';

	if ( is_callable( $content_callback ) ) {
		call_user_func( $content_callback );
	}

	require ATH_THEME_DIR . '/partials/footer.php';
	exit;
}

/**
 * Render 404 within v2 layout.
 *
 * @param string $heading Heading text.
 * @param string $message Body message.
 * @param string $cta_url CTA link.
 * @param string $cta_label CTA label.
 */
function ath_render_not_found( $heading, $message, $cta_url, $cta_label ) {
	ath_render_page(
		static function () use ( $heading, $message, $cta_url, $cta_label ) {
			?>
<main class="course-page course-page--not-found">
  <div class="course-page__inner">
    <h1 class="course-page__title"><?php echo esc_html( $heading ); ?></h1>
    <p class="course-page__intro"><?php echo esc_html( $message ); ?></p>
    <p><a href="<?php echo esc_url( $cta_url ); ?>" class="courses-cta__btn"><?php echo esc_html( $cta_label ); ?></a></p>
  </div>
</main>
			<?php
		},
		$heading . ' — Art Tutor Hanoi'
	);
	exit;
}
