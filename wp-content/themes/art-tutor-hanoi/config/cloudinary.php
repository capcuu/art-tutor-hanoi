<?php
/**
 * Cloudinary image URLs for v2.
 */
define( 'CLD_CLOUD', 'dwy4wtjtc' );

/**
 * Square-cropped delivery URL for a public ID.
 *
 * @param string      $public_id Cloudinary public ID (no file extension).
 * @param int         $size      Width and height in px.
 * @param string|null $version   Optional version segment, e.g. v1780101102.
 */
function cld_teacher_photo( $public_id, $size = 400, $version = null ) {
	$version_path = $version ? $version . '/' : '';
	return sprintf(
		'https://res.cloudinary.com/%s/image/upload/c_fill,g_face,w_%d,h_%d,f_auto,q_auto/%s%s',
		CLD_CLOUD,
		$size,
		$size,
		$version_path,
		$public_id
	);
}

define( 'CLD_STUDIO_FOLDER', 'website/image/studio' );

/**
 * Delivery URL for assets in website/image/studio (or full public_id / override url).
 *
 * @param array  $asset       Keys: public_id, version (optional), url (optional full override).
 * @param string $transform   Cloudinary transformation segment.
 */
function cld_studio_image( $asset, $transform = 'f_auto,q_auto,w_1200' ) {
	if ( ! empty( $asset['url'] ) ) {
		return $asset['url'];
	}

	$public_id = $asset['public_id'] ?? '';
	if ( $public_id === '' ) {
		return '';
	}

	$in_folder = ! isset( $asset['in_folder'] ) || $asset['in_folder'];
	if ( $in_folder && strpos( $public_id, '/' ) === false ) {
		$public_id = CLD_STUDIO_FOLDER . '/' . $public_id;
	}

	$version = $asset['version'] ?? ( $asset['cloudinary_version'] ?? null );
	$version_path = $version ? $version . '/' : '';

	return sprintf(
		'https://res.cloudinary.com/%s/image/upload/%s/%s%s',
		CLD_CLOUD,
		$transform,
		$version_path,
		$public_id
	);
}

/**
 * Home gallery — limit width, keep aspect ratio, medium delivery quality.
 *
 * @param array $asset        Keys: public_id, version (optional).
 * @param int   $max_width    Display max width in px (~2× used for Cloudinary w_).
 */
function cld_frontpage_image( $asset, $max_width = 480 ) {
	$public_id = $asset['public_id'] ?? '';
	if ( $public_id === '' ) {
		return '';
	}

	$max_width   = max( 200, min( (int) $max_width, 1400 ) );
	$deliver_w   = (int) min( $max_width * 2, 1600 );
	$transform   = 'c_limit,w_' . $deliver_w . ',f_auto,q_auto:eco';
	$version     = $asset['version'] ?? ( $asset['cloudinary_version'] ?? null );
	$version_path = $version ? $version . '/' : '';

	return sprintf(
		'https://res.cloudinary.com/%s/image/upload/%s/%s%s',
		CLD_CLOUD,
		$transform,
		$version_path,
		$public_id
	);
}

/**
 * @return array{width: int, height: int}
 */
function cld_frontpage_display_size( $asset, $max_width = 480 ) {
	$orig_w = max( 1, (int) ( $asset['width'] ?? 4 ) );
	$orig_h = max( 1, (int) ( $asset['height'] ?? 3 ) );
	$max_w  = max( 120, (int) $max_width );
	$w      = min( $max_w, $orig_w );
	$h      = (int) round( $w * $orig_h / $orig_w );

	return array(
		'width'  => $w,
		'height' => $h,
	);
}

/**
 * Home page hero banner video (Cloudinary).
 */
function cld_home_hero_video_url() {
	return sprintf(
		'https://res.cloudinary.com/%s/video/upload/q_auto,w_1920,c_limit/f_auto/v1780352977/Banner_tmdhl6.mp4',
		CLD_CLOUD
	);
}
