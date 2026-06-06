<?php
/**
 * Art Tutor Hanoi child theme bootstrap.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

define( 'ATH_THEME_VERSION', '1.0.0' );
define( 'ATH_THEME_DIR', get_stylesheet_directory() );
define( 'ATH_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Homepage block "From Our Studio" (.news-section) — carousel + placeholder cards.
 *
 * Tạm ẩn: nội dung hiện là demo (ảnh gallery, link #, tiêu đề mẫu).
 * Khi có bài viết / ảnh / link chính thức trên site, đổi thành true trong file này
 * (hoặc define ATH_SHOW_HOME_NEWS_SECTION true trong wp-config.php trước khi load theme).
 */
if ( ! defined( 'ATH_SHOW_HOME_NEWS_SECTION' ) ) {
	define( 'ATH_SHOW_HOME_NEWS_SECTION', false );
}

require_once ATH_THEME_DIR . '/inc/book-page.php';
require_once ATH_THEME_DIR . '/inc/fluent-qr.php';
require_once ATH_THEME_DIR . '/inc/thank-you-page.php';
require_once ATH_THEME_DIR . '/inc/links-page.php';
require_once ATH_THEME_DIR . '/inc/generic-page.php';
require_once ATH_THEME_DIR . '/inc/posts.php';
require_once ATH_THEME_DIR . '/inc/urls.php';
require_once ATH_THEME_DIR . '/inc/assets.php';
require_once ATH_THEME_DIR . '/inc/layout.php';
require_once ATH_THEME_DIR . '/inc/rewrites.php';
require_once ATH_THEME_DIR . '/inc/setup.php';

require_once ATH_THEME_DIR . '/config/cloudinary.php';
require_once ATH_THEME_DIR . '/config/images.php';
require_once ATH_THEME_DIR . '/config/courses.php';
require_once ATH_THEME_DIR . '/config/kids-courses.php';
require_once ATH_THEME_DIR . '/config/experiences.php';
require_once ATH_THEME_DIR . '/config/wordpress.php';
require_once ATH_THEME_DIR . '/config/book-tabs.php';
