<?php
/**
 * Enqueue v2 styles and scripts.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! ath_is_v2_layout() ) {
			return;
		}

		wp_dequeue_style( 'masu-style' );
		wp_deregister_style( 'masu-style' );

		$css_path = ATH_THEME_DIR . '/assets/css/main.css';
		$css_ver  = is_readable( $css_path ) ? (string) filemtime( $css_path ) : ATH_THEME_VERSION;

		wp_enqueue_style(
			'ath-main',
			ATH_THEME_URI . '/assets/css/main.css',
			array(),
			$css_ver
		);

		wp_enqueue_style(
			'ath-fonts',
			'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap',
			array(),
			null
		);

		$js_path = ATH_THEME_DIR . '/assets/js/main.js';
		$js_ver  = is_readable( $js_path ) ? (string) filemtime( $js_path ) : ATH_THEME_VERSION;

		wp_enqueue_script(
			'ath-main',
			ATH_THEME_URI . '/assets/js/main.js',
			array(),
			$js_ver,
			true
		);
	},
	100
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( ath_is_v2_layout() ) {
			$classes[] = 'ath-v2';
		}
		return $classes;
	}
);

add_action(
	'wp_head',
	function () {
		if ( ! ath_is_v2_layout() ) {
			return;
		}
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
		echo '<link rel="preconnect" href="https://res.cloudinary.com">' . "\n";
	},
	1
);
