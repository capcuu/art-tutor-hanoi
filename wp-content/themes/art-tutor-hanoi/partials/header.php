<?php
/**
 * Site header — v2 navigation.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class( 'ath-v2' ); ?>>
<?php wp_body_open(); ?>
  <header class="header">
    <div class="header__inner">
      <div class="header__left">
        <button class="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="main-nav">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <a href="<?php echo esc_url( ath_page_url( 'home' ) ); ?>" aria-label="Art Tutor Hanoi home">
          <img src="<?php echo esc_url( ath_asset_url( 'logo.png' ) ); ?>" alt="Art Tutor Hanoi" class="logo" width="252" height="168" decoding="async">
        </a>
      </div>

      <div class="nav-overlay" aria-hidden="true"></div>
      <nav class="nav-wrapper" id="main-nav">
        <ul class="nav">
          <li><a href="<?php echo esc_url( ath_page_url( 'about' ) ); ?>">About</a></li>
          <li class="nav-item">
            <a class="nav-item__trigger" href="<?php echo esc_url( ath_page_url( 'pricing' ) ); ?>">Workshops</a>
            <ul class="nav-submenu">
              <li><a href="<?php echo esc_url( ath_experience_url( 'trial-art-class' ) ); ?>">Trial Art Class</a></li>
              <li><a href="<?php echo esc_url( ath_experience_url( 'life-drawing' ) ); ?>">Nude Model Drawing</a></li>
              <li><a href="<?php echo esc_url( ath_experience_url( 'silk-painting' ) ); ?>">Silk Painting</a></li>
              <li><a href="<?php echo esc_url( ath_experience_url( 'artist-residency' ) ); ?>">Artist Residency</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-item__trigger" href="<?php echo esc_url( ath_page_url( 'courses' ) ); ?>">Adults</a>
            <ul class="nav-submenu">
              <li><a href="<?php echo esc_url( ath_page_url( 'courses' ) ); ?>">Courses</a></li>
              <li><a href="<?php echo esc_url( ath_page_url( 'adults-portfolio' ) ); ?>">Portfolio Preparation</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-item__trigger" href="<?php echo esc_url( ath_page_url( 'kids-courses' ) ); ?>">Kids</a>
            <ul class="nav-submenu">
              <li><a href="<?php echo esc_url( ath_page_url( 'kids-courses' ) ); ?>">Courses</a></li>
              <li><a href="<?php echo esc_url( ath_page_url( 'kids-portfolio' ) ); ?>">Portfolio Building</a></li>
            </ul>
          </li>
          <li><a href="<?php echo esc_url( ath_page_url( 'pricing' ) ); ?>">Pricing</a></li>
          <li><a href="<?php echo esc_url( ath_page_url( 'calendar' ) ); ?>">Calendar</a></li>
          <li class="nav-item nav-item--book"><a href="<?php echo esc_url( ath_book_url( 'trial' ) ); ?>">Book a Class</a></li>
        </ul>
      </nav>

      <div class="header__actions">
        <a href="<?php echo esc_url( ath_book_url( 'trial' ) ); ?>" class="btn-contact">Book a Class</a>
      </div>
    </div>
  </header>
