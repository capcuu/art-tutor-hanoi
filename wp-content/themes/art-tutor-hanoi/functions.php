<?php
/**
 * Art Tutor Hanoi child theme bootstrap.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

define( 'ATH_THEME_VERSION', '1.0.2' );
define( 'ATH_THEME_DIR', get_stylesheet_directory() );
define( 'ATH_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Homepage block "From Our Studio" — [ath_home_news] carousel (category Studio News).
 * Section renders only when published posts exist in that category.
 */
require_once ATH_THEME_DIR . '/inc/book-page.php';
require_once ATH_THEME_DIR . '/inc/fluent-qr.php';
require_once ATH_THEME_DIR . '/inc/thank-you-page.php';
require_once ATH_THEME_DIR . '/inc/links-page.php';
require_once ATH_THEME_DIR . '/inc/generic-page.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-hero.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-intro.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/courses-hub.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/kids-courses-hub.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/pricing.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-cards.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-gallery.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-programs.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-community.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-testimonials.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/home-news.php';
require_once ATH_THEME_DIR . '/inc/chromeless-pages.php';
require_once ATH_THEME_DIR . '/inc/exhibition-page-slugs.php';
require_once ATH_THEME_DIR . '/inc/urls.php';
require_once ATH_THEME_DIR . '/inc/shortcodes/button.php';
require_once ATH_THEME_DIR . '/inc/content-mode.php';
require_once ATH_THEME_DIR . '/inc/gutenberg-blocks.php';
require_once ATH_THEME_DIR . '/inc/workshop-pages.php';
require_once ATH_THEME_DIR . '/inc/course-gallery-images.php';
require_once ATH_THEME_DIR . '/inc/course-detail-pages.php';
require_once ATH_THEME_DIR . '/inc/kids-courses-hub.php';
require_once ATH_THEME_DIR . '/inc/courses-hub.php';
require_once ATH_THEME_DIR . '/inc/pricing-page.php';
require_once ATH_THEME_DIR . '/inc/faq-page.php';
require_once ATH_THEME_DIR . '/inc/kids-international-exhibition-page.php';
require_once ATH_THEME_DIR . '/inc/page-defaults.php';
require_once ATH_THEME_DIR . '/inc/gutenberg-migration.php';
require_once ATH_THEME_DIR . '/inc/posts.php';
require_once ATH_THEME_DIR . '/inc/learner-comment-upload.php';
require_once ATH_THEME_DIR . '/inc/assets.php';
require_once ATH_THEME_DIR . '/inc/layout.php';
require_once ATH_THEME_DIR . '/inc/rewrites.php';
require_once ATH_THEME_DIR . '/inc/legacy-redirects.php';
require_once ATH_THEME_DIR . '/inc/seo.php';
require_once ATH_THEME_DIR . '/inc/seo-schema.php';
require_once ATH_THEME_DIR . '/inc/seo-onpage.php';
require_once ATH_THEME_DIR . '/inc/seo-content.php';
require_once ATH_THEME_DIR . '/inc/seo-monitor.php';
require_once ATH_THEME_DIR . '/inc/seo-page.php';
require_once ATH_THEME_DIR . '/inc/guide-page.php';
require_once ATH_THEME_DIR . '/inc/setup.php';

require_once ATH_THEME_DIR . '/config/cloudinary.php';
require_once ATH_THEME_DIR . '/config/images.php';
require_once ATH_THEME_DIR . '/config/courses.php';
require_once ATH_THEME_DIR . '/config/kids-courses.php';
require_once ATH_THEME_DIR . '/config/experiences.php';
require_once ATH_THEME_DIR . '/config/wordpress.php';
require_once ATH_THEME_DIR . '/config/book-tabs.php';
