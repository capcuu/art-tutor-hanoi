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
