<?php
/**
 * Content images — original Cloudinary URL (f_webp,q_40), natural aspect via CSS.
 *
 * Runs after the Cloudinary WP plugin (render_block + the_content at priority 999).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return string[]
 */
function ath_content_image_context_stack() {
	if ( ! isset( $GLOBALS['ath_content_image_context_stack'] ) ) {
		$GLOBALS['ath_content_image_context_stack'] = array();
	}

	return $GLOBALS['ath_content_image_context_stack'];
}

/**
 * Map group block className to image context.
 *
 * @param string $class_name Group className attribute.
 */
function ath_content_image_context_from_group_class( $class_name ) {
	$class_name = (string) $class_name;

	if ( strpos( $class_name, 'experience-hero__media' ) !== false ) {
		return 'experience_hero';
	}

	if ( strpos( $class_name, 'course-gallery__item' ) !== false ) {
		return 'gallery';
	}

	if ( strpos( $class_name, 'course-hero__media' ) !== false ) {
		return 'course_hero';
	}

	return '';
}

/**
 * Detect image context from preceding HTML.
 *
 * @param string $before_html HTML before the img tag.
 */
function ath_detect_content_image_context( $before_html ) {
	if ( preg_match( '/<body\b[^>]*>(.*)\z/is', $before_html, $body_match ) ) {
		$before_html = $body_match[1];
	} else {
		$head_end = stripos( $before_html, '</head>' );
		if ( $head_end !== false ) {
			$before_html = substr( $before_html, $head_end + 7 );
		}
	}

	$markers = array(
		'course-gallery__item'   => 'gallery',
		'experience-hero__media' => 'experience_hero',
		'course-hero__media'     => 'course_hero',
	);

	$last_pos = -1;
	$context  = '';

	foreach ( $markers as $needle => $ctx ) {
		$pos = strrpos( $before_html, $needle );
		if ( $pos !== false && $pos > $last_pos ) {
			$last_pos = $pos;
			$context  = $ctx;
		}
	}

	return $context;
}

/**
 * Expected Cloudinary transform marker for a context.
 *
 * @param string $context gallery|experience_hero|course_hero
 */
function ath_content_image_url_marker( $context ) {
	unset( $context );

	return 'f_webp,q_40';
}

/**
 * Delivery URL for a content image context.
 *
 * @param string $url     Source URL.
 * @param string $context gallery|experience_hero|course_hero
 */
function ath_content_image_url( $url, $context ) {
	switch ( $context ) {
		case 'gallery':
			return ath_course_gallery_img_url( $url );
		case 'experience_hero':
			return ath_experience_hero_img_url( $url );
		case 'course_hero':
			return ath_course_hero_img_url( $url );
		default:
			return $url;
	}
}

/**
 * Should this content img be rebuilt?
 *
 * @param string $attrs   Raw img attribute string.
 * @param string $context gallery|experience_hero|course_hero
 */
function ath_should_rebuild_content_image( $attrs, $context ) {
	unset( $context );

	if ( preg_match( '/\bclass=(["\'])[^"\']*\bath-content-img\b[^"\']*\1/i', $attrs ) ) {
		if (
			preg_match( '/\bsrc=(["\'])[^"\']*f_webp,q_40[^"\']*\1/i', $attrs )
			&& ! preg_match( '/\bsrc=(["\'])[^"\']*(?:c_scale|,w_|,h_|c_limit)[^"\']*\1/i', $attrs )
			&& ! preg_match( '/\b(?:width|height)=/i', $attrs )
		) {
			return false;
		}
	}

	if ( preg_match( '/\bdata-cloudinary=(["\'])lazy\1/i', $attrs ) ) {
		return true;
	}

	if ( preg_match( '/\bdata-public-id=/i', $attrs ) ) {
		return true;
	}

	if ( preg_match( '/\bonload=[^>]*CLDBind/i', $attrs ) ) {
		return true;
	}

	if ( preg_match( '/\bsrc=(["\'])[^"\']*c_scale[^"\']*\1/i', $attrs ) ) {
		return true;
	}

	if ( preg_match( '/\bsrc=(["\'])data:/i', $attrs ) && preg_match( '/\bdata-public-id=/i', $attrs ) ) {
		return true;
	}

	if (
		preg_match( '/\bwidth=(["\'])(\d+)\1/i', $attrs, $width_match )
		&& preg_match( '/\bheight=(["\'])(\d+)\1/i', $attrs, $height_match )
		&& (int) $height_match[2] > (int) $width_match[2]
	) {
		return true;
	}

	$marker = preg_quote( ath_content_image_url_marker( '' ), '/' );

	if (
		preg_match( '/\bsrc=(["\'])[^"\']*res\.cloudinary\.com\/dftadlujq[^"\']*\1/i', $attrs )
		&& (
			! preg_match( '/\bsrc=(["\'])[^"\']*' . $marker . '[^"\']*\1/i', $attrs )
			|| preg_match( '/\bsrc=(["\'])[^"\']*(?:c_scale|,w_|,h_|c_limit)[^"\']*\1/i', $attrs )
		)
	) {
		return true;
	}

	return false;
}

/**
 * Resolve original asset URL from img attributes.
 *
 * @param string $attrs Raw img attribute string.
 */
function ath_cld_img_source_url( $attrs ) {
	if ( preg_match( '/\bdata-public-id=(["\'])(.*?)\1/i', $attrs, $public_id ) ) {
		$version = '';
		if ( preg_match( '/\bdata-version=(["\'])(.*?)\1/i', $attrs, $ver ) ) {
			$version = 'v' . preg_replace( '/\D/', '', $ver[2] ) . '/';
		}

		return 'https://res.cloudinary.com/dftadlujq/images/' . $version . $public_id[2];
	}

	if ( preg_match( '/\bsrc=(["\'])(.*?)\1/i', $attrs, $src ) ) {
		$url = html_entity_decode( $src[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		if ( $url !== '' && strpos( $url, 'data:' ) !== 0 ) {
			return preg_replace( '/\?.*$/', '', $url );
		}
	}

	return '';
}

/**
 * Rebuild img tag — plain src, natural aspect ratio via CSS.
 *
 * @param string $attrs   Raw img attributes.
 * @param string $context gallery|experience_hero|course_hero
 */
function ath_rebuild_content_image_tag( $attrs, $context ) {
	if ( ! function_exists( 'ath_cld_natural_img_url' ) ) {
		require_once ATH_THEME_DIR . '/config/images.php';
	}

	$alt = '';

	if ( preg_match( '/\balt=(["\'])(.*?)\1/i', $attrs, $alt_match ) ) {
		$alt = html_entity_decode( $alt_match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}

	$source_url = ath_cld_img_source_url( $attrs );
	if ( $source_url === '' ) {
		return null;
	}

	$delivery_url = ath_content_image_url( $source_url, $context );
	if ( $delivery_url === '' ) {
		return null;
	}

	$loading = ( $context === 'experience_hero' ) ? 'eager' : 'lazy';
	$fetch   = ( $context === 'experience_hero' ) ? 'high' : 'auto';

	return sprintf(
		'<img src="%s" alt="%s" loading="%s" decoding="async" fetchpriority="%s" class="ath-content-img" />',
		esc_attr( $delivery_url ),
		esc_attr( $alt ),
		esc_attr( $loading ),
		esc_attr( $fetch )
	);
}

/**
 * Replace Cloudinary lazy / scaled imgs in an HTML fragment.
 *
 * @param string $html            HTML fragment.
 * @param string $forced_context  Optional fixed context (gallery|experience_hero|course_hero).
 */
function ath_fix_images_in_html( $html, $forced_context = '' ) {
	if ( $html === '' || strpos( $html, '<img' ) === false ) {
		return $html;
	}

	$source = $html;

	$result = preg_replace_callback(
		'/<img\b([^>]*)\/?>/i',
		function ( $match ) use ( $source, $forced_context ) {
			$full  = $match[0];
			$attrs = $match[1];
			$pos   = strpos( $source, $full );

			if ( $pos === false ) {
				return $full;
			}

			$context = $forced_context;
			if ( $context === '' ) {
				$context = ath_detect_content_image_context( substr( $source, 0, $pos ) );
			}

			if ( $context === '' || ! ath_should_rebuild_content_image( $attrs, $context ) ) {
				return $full;
			}

			$replacement = ath_rebuild_content_image_tag( $attrs, $context );

			return $replacement ?? $full;
		},
		$html
	);

	return is_string( $result ) ? $result : $html;
}

/**
 * Push image context while rendering nested group blocks.
 *
 * @param string|null $pre_render Short-circuit return value.
 * @param array       $block      Block data.
 */
function ath_pre_render_content_image_context( $pre_render, $block ) {
	if ( ( $block['blockName'] ?? '' ) !== 'core/group' ) {
		return $pre_render;
	}

	$context = ath_content_image_context_from_group_class( $block['attrs']['className'] ?? '' );
	if ( $context !== '' ) {
		ath_content_image_context_stack()[] = $context;
	}

	return $pre_render;
}

/**
 * Fix core/image output and pop group context stack.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 */
function ath_render_block_content_images( $block_content, $block ) {
	$block_name = $block['blockName'] ?? '';

	if ( $block_name === 'core/image' ) {
		$stack = ath_content_image_context_stack();
		$context = $stack !== array() ? $stack[ count( $stack ) - 1 ] : '';
		if ( $context !== '' ) {
			return ath_fix_images_in_html( $block_content, $context );
		}

		return $block_content;
	}

	if ( $block_name !== 'core/group' ) {
		return $block_content;
	}

	$context = ath_content_image_context_from_group_class( $block['attrs']['className'] ?? '' );
	if ( $context === '' ) {
		return $block_content;
	}

	$stack = ath_content_image_context_stack();
	if ( $stack !== array() && end( $stack ) === $context ) {
		array_pop( $stack );
	}

	return ath_fix_images_in_html( $block_content, $context );
}

/**
 * Normalize gallery & hero images on render (after Cloudinary plugin).
 *
 * @param string $content Post content HTML.
 */
function ath_fix_content_cloudinary_images( $content ) {
	if (
		strpos( $content, 'course-gallery' ) === false
		&& strpos( $content, 'experience-hero__media' ) === false
		&& strpos( $content, 'course-hero__media' ) === false
	) {
		return $content;
	}

	return ath_fix_images_in_html( $content );
}

add_filter( 'pre_render_block', 'ath_pre_render_content_image_context', 10, 2 );
add_filter( 'render_block', 'ath_render_block_content_images', PHP_INT_MAX, 2 );
add_filter( 'the_content', 'ath_fix_content_cloudinary_images', PHP_INT_MAX );

/**
 * Final HTML pass — beats page cache fragments and Cloudinary lazy markup.
 */
function ath_should_buffer_fix_cloudinary_images() {
	if ( is_admin() || wp_doing_ajax() || wp_is_json_request() ) {
		return false;
	}

	if ( ! is_singular( 'page' ) ) {
		return false;
	}

	$post = get_queried_object();
	if ( ! ( $post instanceof WP_Post ) ) {
		return false;
	}

	if ( ath_is_workshop_page( $post ) ) {
		return true;
	}

	if ( function_exists( 'ath_is_adult_course_detail_page' ) && ath_is_adult_course_detail_page( $post ) ) {
		return true;
	}

	return false;
}

/**
 * @param string $html Full page HTML.
 */
function ath_buffer_fix_cloudinary_images( $html ) {
	if (
		! is_string( $html )
		|| $html === ''
		|| (
			strpos( $html, 'course-gallery' ) === false
			&& strpos( $html, 'experience-hero__media' ) === false
			&& strpos( $html, 'course-hero__media' ) === false
		)
	) {
		return $html;
	}

	$html = ath_fix_images_in_html( $html );

	// Target Cloudinary lazy placeholders explicitly (plugin adds long data: src attrs).
	while ( preg_match( '/<img\b([^>]*data-cloudinary=(["\'])lazy\2[^>]*)\/?>/i', $html, $match, PREG_OFFSET_CAPTURE ) ) {
		$full  = $match[0][0];
		$pos   = (int) $match[0][1];
		$attrs = $match[1][0];

		$context = ath_detect_content_image_context( substr( $html, 0, $pos ) );
		if ( $context === '' ) {
			break;
		}

		$replacement = ath_rebuild_content_image_tag( $attrs, $context );
		if ( $replacement === null ) {
			break;
		}

		$html = substr( $html, 0, $pos ) . $replacement . substr( $html, $pos + strlen( $full ) );
	}

	return $html;
}

/**
 * Start output buffering on workshop / course detail pages.
 */
function ath_maybe_buffer_fix_cloudinary_images() {
	if ( ! ath_should_buffer_fix_cloudinary_images() ) {
		return;
	}

	ob_start( 'ath_buffer_fix_cloudinary_images' );
}

add_action( 'template_redirect', 'ath_maybe_buffer_fix_cloudinary_images', 0 );

/** @deprecated Use ath_content_image_url_marker( 'gallery' ). */
function ath_course_gallery_url_marker() {
	return ath_content_image_url_marker( 'gallery' );
}

/** @deprecated Use ath_cld_img_source_url(). */
function ath_course_gallery_img_source_url( $attrs ) {
	return ath_cld_img_source_url( $attrs );
}
