<?php
/**
 * Default page template — v2 layout for editor-driven pages.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

ath_render_page(
	static function () {
		require ATH_THEME_DIR . '/partials/page-content.php';
	}
);
