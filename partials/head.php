<?php
/**
 * Shared <head> for v2 templates.
 *
 * @var string $page_title Page title (optional).
 */
$page_title = isset( $page_title ) ? $page_title : 'Art Tutor Hanoi';
$v2_uri       = rtrim( str_replace( '\\', '/', dirname( $_SERVER['SCRIPT_NAME'] ) ), '/' );
$asset_base   = ( $v2_uri === '' ? '' : $v2_uri ) . '/';
$main_css_path = __DIR__ . '/../assets/css/main.css';
$main_css_ver  = is_readable( $main_css_path ) ? (string) filemtime( $main_css_path ) : '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars( $page_title, ENT_QUOTES, 'UTF-8' ); ?></title>
  <base href="<?php echo htmlspecialchars( $asset_base, ENT_QUOTES, 'UTF-8' ); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://res.cloudinary.com">
  <link rel="dns-prefetch" href="https://arttutorhanoi.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/main.css?v=<?php echo htmlspecialchars( $main_css_ver, ENT_QUOTES, 'UTF-8' ); ?>">
</head>
<body>
