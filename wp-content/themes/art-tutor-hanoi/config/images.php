<?php
/**
 * Responsive image delivery — sized for display, ~2× for retina where useful.
 */
function v2_img_presets() {
	return array(
		'hero'        => array(
			'width'         => 1920,
			'display_width' => 960,
			'height'        => null,
			'crop'          => 'limit',
			'quality'       => 'best',
		),
		'gallery'     => array(
			'width'   => 960,
			'height'  => null,
			'crop'    => 'limit',
			'quality' => '40',
		),
		'pathway'     => array( 'width' => 440, 'height' => 275, 'crop' => 'fill' ),
		'course_hero' => array( 'width' => 720, 'height' => null, 'crop' => 'limit' ),
	);
}

/**
 * @return array{width: int, height: int|null, crop: string, quality: string}
 */
function v2_img_preset( $preset = 'course_hero' ) {
	$presets = v2_img_presets();

	return isset( $presets[ $preset ] ) ? $presets[ $preset ] : $presets['course_hero'];
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

	$url = preg_replace( '#\?.*$#', '', trim( $url ) );
	$p   = v2_img_preset( $preset );
	$width   = (int) $p['width'];
	$height  = isset( $p['height'] ) ? (int) $p['height'] : null;
	$crop    = $p['crop'];
	$quality = isset( $p['quality'] ) ? $p['quality'] : 'good';

	if ( preg_match( '#^https://res\.cloudinary\.com/dftadlujq/images/(?:[^/]+/)*(v\d+/.+)$#', $url, $matches ) ) {
		return v2_cld_arttutor_url( $matches[1], $width, $height, $crop, $quality );
	}

	if ( preg_match( '#^https://res\.cloudinary\.com/[^/]+/image/upload/([^/]+/)*(.+)$#', $url, $matches ) ) {
		return v2_cld_upload_url( $url, $width, $height, $crop, $quality );
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
	$p = v2_img_preset( $preset );

	return array(
		'width'  => (int) ( isset( $p['display_width'] ) ? $p['display_width'] : $p['width'] ),
		'height' => isset( $p['height'] ) ? (int) $p['height'] : null,
	);
}

/**
 * Max delivery width for course / experience gallery (height follows aspect ratio).
 */
function ath_course_gallery_max_width() {
	return 960;
}

/**
 * Original-aspect Cloudinary URL — f_webp,q_40 only (no w/h crop).
 *
 * @param string $url Source URL.
 */
function ath_cld_natural_img_url( $url ) {
	if ( $url === '' || $url === null ) {
		return '';
	}

	$url       = preg_replace( '#\?.*$#', '', trim( $url ) );
	$transform = 'f_webp,q_40';

	if ( preg_match( '#^https://res\.cloudinary\.com/dftadlujq/images/(?:[^/]+/)*(v\d+/.+)$#', $url, $matches ) ) {
		return 'https://res.cloudinary.com/dftadlujq/images/' . $transform . '/' . ltrim( $matches[1], '/' );
	}

	if ( preg_match( '#^https://res\.cloudinary\.com/[^/]+/image/upload/#', $url ) ) {
		$path = v2_cld_upload_asset_path( $url );
		if ( $path !== '' ) {
			return 'https://res.cloudinary.com/dftadlujq/images/' . $transform . '/' . ltrim( $path, '/' );
		}
	}

	return $url;
}

/**
 * Course / experience gallery — original aspect ratio.
 *
 * @param string $url Source URL (Cloudinary or WordPress uploads).
 */
function ath_course_gallery_img_url( $url ) {
	if ( $url === '' || $url === null ) {
		return '';
	}

	$natural = ath_cld_natural_img_url( $url );
	if ( $natural !== '' && $natural !== $url ) {
		return $natural;
	}

	return v2_img_url( $url, 'gallery' );
}

/**
 * Experience / workshop hero image URL.
 *
 * @param string $url Source URL.
 */
function ath_experience_hero_img_url( $url ) {
	$natural = ath_cld_natural_img_url( $url );
	if ( $natural !== '' && $natural !== $url ) {
		return $natural;
	}

	return v2_img_url( $url, 'hero' );
}

/**
 * Course detail hero image URL.
 *
 * @param string $url Source URL.
 */
function ath_course_hero_img_url( $url ) {
	$natural = ath_cld_natural_img_url( $url );
	if ( $natural !== '' && $natural !== $url ) {
		return $natural;
	}

	return v2_img_url( $url, 'course_hero' );
}

function v2_cld_arttutor_url( $version_and_path, $width, $height, $crop, $quality = 'good' ) {
	$transform = v2_cld_transform( $width, $height, $crop, $quality );

	return 'https://res.cloudinary.com/dftadlujq/images/' . $transform . '/' . ltrim( $version_and_path, '/' );
}

function v2_cld_upload_asset_path( $url ) {
	$path = preg_replace( '#^https://res\.cloudinary\.com/[^/]+/image/upload/#', '', $url );

	while ( $path !== '' && ! preg_match( '#^v\d+/#', $path ) ) {
		$slash = strpos( $path, '/' );
		if ( $slash === false ) {
			break;
		}

		$segment = substr( $path, 0, $slash );
		if ( strpos( $segment, ',' ) === false ) {
			break;
		}

		$path = substr( $path, $slash + 1 );
	}

	return $path;
}

function v2_cld_upload_url( $url, $width, $height, $crop, $quality = 'good' ) {
	if ( ! preg_match( '#^https://res\.cloudinary\.com/([^/]+)/image/upload/#', $url, $matches ) ) {
		return $url;
	}

	$asset_path = v2_cld_upload_asset_path( $url );
	if ( $asset_path === '' ) {
		return $url;
	}

	$transform = v2_cld_transform( $width, $height, $crop, $quality );

	return 'https://res.cloudinary.com/' . $matches[1] . '/image/upload/' . $transform . '/' . $asset_path;
}

function v2_cld_transform( $width, $height, $crop, $quality = 'good' ) {
	$parts = array( 'w_' . $width );

	if ( $height ) {
		$parts[] = 'h_' . $height;
		$parts[] = 'c_' . $crop;
	} else {
		$parts[] = 'c_limit';
	}

	$parts[] = 'f_auto';
	if ( is_numeric( $quality ) ) {
		$parts[] = 'q_' . (int) $quality;
	} else {
		$parts[] = 'q_auto:' . $quality;
	}

	return implode( ',', $parts );
}

/**
 * WordPress media library URLs — upscale via Jetpack Photon when a thumbnail is too small.
 *
 * @param string   $url    Full uploads URL.
 * @param int      $width  Target delivery width from preset.
 * @param int|null $height Unused; kept for call-site compatibility.
 */
function v2_wp_upload_url( $url, $width, $height = null ) {
	$delivery_w = max( 400, (int) $width );

	if ( preg_match( '#^(https://arttutorhanoi\.com/wp-content/uploads/.+)-(\d+)x(\d+)\.(jpe?g|png|webp)$#i', $url, $matches ) ) {
		$existing_w = (int) $matches[2];
		if ( $existing_w < (int) ceil( $delivery_w * 0.9 ) ) {
			$url = $matches[1] . '.' . $matches[4];
		}
	}

	if ( preg_match( '#^https://arttutorhanoi\.com/wp-content/uploads/(.+)$#i', $url, $matches ) ) {
		return sprintf(
			'https://i0.wp.com/arttutorhanoi.com/wp-content/uploads/%s?w=%d&quality=85&ssl=1',
			$matches[1],
			min( $delivery_w, 2048 )
		);
	}

	return $url;
}
