<?php
/**
 * Responsive image delivery — sized for display, ~2× for retina where useful.
 */
function v2_img_presets() {
	return array(
		'hero'        => array( 'width' => 960, 'height' => null, 'crop' => 'limit' ),
		'gallery'     => array( 'width' => 520, 'height' => null, 'crop' => 'limit' ),
		'pathway'     => array( 'width' => 440, 'height' => 275, 'crop' => 'fill' ),
		'course_hero' => array( 'width' => 720, 'height' => null, 'crop' => 'limit' ),
	);
}

/**
 * Build a delivery URL sized for the given preset.
 *
 * @param string $url     Source URL (Cloudinary or WordPress uploads).
 * @param string $preset  hero|gallery|pathway|course_hero
 */
function v2_img_url( $url, $preset = 'course_hero' ) {
	if ( $url === '' || $url === null ) {
		return '';
	}

	$url     = preg_replace( '#\?.*$#', '', trim( $url ) );
	$presets = v2_img_presets();
	$p       = isset( $presets[ $preset ] ) ? $presets[ $preset ] : $presets['course_hero'];
	$width   = (int) $p['width'];
	$height  = isset( $p['height'] ) ? (int) $p['height'] : null;
	$crop    = $p['crop'];

	if ( preg_match( '#^https://res\.cloudinary\.com/dftadlujq/images/(?:[^/]+/)*(v\d+/.+)$#', $url, $matches ) ) {
		return v2_cld_arttutor_url( $matches[1], $width, $height, $crop );
	}

	if ( preg_match( '#^https://res\.cloudinary\.com/[^/]+/image/upload/([^/]+/)*(.+)$#', $url, $matches ) ) {
		return v2_cld_upload_url( $url, $width, $height, $crop );
	}

	if ( strpos( $url, 'arttutorhanoi.com/wp-content/uploads/' ) !== false ) {
		return v2_wp_upload_url( $url, $width, $height );
	}

	return $url;
}

/**
 * @return array{width: int, height: int|null}
 */
function v2_img_display_size( $preset = 'course_hero' ) {
	$presets = v2_img_presets();
	$p       = isset( $presets[ $preset ] ) ? $presets[ $preset ] : $presets['course_hero'];

	return array(
		'width'  => (int) $p['width'],
		'height' => isset( $p['height'] ) ? (int) $p['height'] : null,
	);
}

function v2_cld_arttutor_url( $version_and_path, $width, $height, $crop ) {
	$transform = v2_cld_transform( $width, $height, $crop );

	return 'https://res.cloudinary.com/dftadlujq/images/' . $transform . '/' . ltrim( $version_and_path, '/' );
}

function v2_cld_upload_url( $url, $width, $height, $crop ) {
	if ( preg_match( '#^(https://res\.cloudinary\.com/[^/]+/image/upload/)(?:[^/]+/)*(v\d+/)?(.+)$#', $url, $matches ) ) {
		$transform = v2_cld_transform( $width, $height, $crop );

		return $matches[1] . $transform . '/' . ( ! empty( $matches[2] ) ? $matches[2] : '' ) . $matches[3];
	}

	return $url;
}

function v2_cld_transform( $width, $height, $crop ) {
	$parts = array( 'w_' . $width );

	if ( $height ) {
		$parts[] = 'h_' . $height;
		$parts[] = 'c_' . $crop;
	} else {
		$parts[] = 'c_limit';
	}

	$parts[] = 'f_auto';
	$parts[] = 'q_auto:good';

	return implode( ',', $parts );
}

/**
 * WordPress media library URLs — served directly (same as production programs pages).
 *
 * @param string   $url    Full uploads URL.
 * @param int      $width  Target display width from preset (used to keep existing sizes).
 * @param int|null $height Unused; kept for call-site compatibility.
 */
function v2_wp_upload_url( $url, $width, $height = null ) {
	if ( preg_match( '#-(\d+)x(\d+)\.(jpe?g|png|webp)$#i', $url, $matches ) ) {
		$existing_w = (int) $matches[1];
		if ( $existing_w <= (int) ceil( $width * 1.15 ) ) {
			return $url;
		}
	}

	return $url;
}
