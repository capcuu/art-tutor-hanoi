<?php
/**
 * Enqueue v2 styles and scripts.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block editor — preview styles matching front-end (generic, workshop, course).
 */
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'editor-styles' );
		add_theme_support( 'wp-block-styles' );

		add_editor_style(
			array(
				'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap',
				'assets/css/editor.css',
			)
		);
	},
	11
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_singular( 'page' ) && ath_is_chromeless_page() ) {
			return;
		}

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
		if ( is_singular( 'page' ) && ath_is_chromeless_page() ) {
			return $classes;
		}

		if ( ath_is_v2_layout() ) {
			$classes[] = 'ath-v2';
		}
		return $classes;
	}
);

add_action(
	'wp_head',
	function () {
		if ( is_singular( 'page' ) && ath_is_chromeless_page() ) {
			return;
		}

		if ( ! ath_is_v2_layout() ) {
			return;
		}
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
		echo '<link rel="preconnect" href="https://res.cloudinary.com">' . "\n";
	},
	1
);

/**
 * Workshop / course hero & gallery — bypass Cloudinary lazy (wrong portrait dimensions).
 */
add_action(
	'wp_head',
	function () {
		if ( ! is_singular( 'page' ) ) {
			return;
		}

		$post = get_queried_object();
		if ( ! ( $post instanceof WP_Post ) ) {
			return;
		}

		$fix_page = ath_is_workshop_page( $post )
			|| ( function_exists( 'ath_is_adult_course_detail_page' ) && ath_is_adult_course_detail_page( $post ) );

		if ( ! $fix_page ) {
			return;
		}
		?>
<script id="ath-fix-cld-img">
(function () {
	function fixAthContentImage(img) {
		var id = img.getAttribute('data-public-id');
		var ver = (img.getAttribute('data-version') || '').replace(/\D/g, '');
		var src = img.getAttribute('src') || '';

		if (!id || !ver) {
			return;
		}

		if (
			img.classList.contains('ath-content-img')
			&& src.indexOf('f_webp,q_40') !== -1
			&& src.indexOf('c_scale') === -1
			&& !img.getAttribute('data-cloudinary')
		) {
			return;
		}

		img.onload = null;
		img.removeAttribute('data-cloudinary');
		['data-public-id', 'data-version', 'data-size', 'width', 'height'].forEach(function (attr) {
			img.removeAttribute(attr);
		});
		img.src = 'https://res.cloudinary.com/dftadlujq/images/f_webp,q_40/v' + ver + '/' + id;
		img.classList.add('ath-content-img');
	}

	function scanAthContentImages() {
		document.querySelectorAll(
			'.experience-hero__media img, .course-gallery__item img, .course-hero__media img'
		).forEach(fixAthContentImage);
	}

	if (typeof MutationObserver !== 'undefined') {
		new MutationObserver(scanAthContentImages).observe(document.documentElement, {
			childList: true,
			subtree: true
		});
	}

	document.addEventListener('DOMContentLoaded', scanAthContentImages);
})();
</script>
		<?php
	},
	1
);
