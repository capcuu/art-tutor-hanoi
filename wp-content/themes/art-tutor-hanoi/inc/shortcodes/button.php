<?php
/**
 * Site-wide button shortcode — [ath_btn]
 *
 * Default style matches homepage card buttons (.btn-view).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default shortcode attributes.
 *
 * @return array<string, string>
 */
function ath_btn_default_atts() {
	return array(
		'label'     => '',
		'link'      => '',
		'link_type' => 'url',
		'style'     => 'view',
		'align'     => '',
		'class'     => '',
		'target'    => '',
		'rel'       => '',
		'newtab'    => '',
	);
}

/**
 * CSS class for button style variant.
 *
 * @param string $style view | contact | cta
 */
function ath_btn_style_class( $style ) {
	$map = array(
		'view'    => 'btn-view',
		'contact' => 'btn-contact',
		'cta'     => 'courses-cta__btn',
	);

	$style = strtolower( trim( (string) $style ) );

	return $map[ $style ] ?? 'btn-view';
}

/**
 * Build button anchor HTML (reusable in PHP partials).
 *
 * @param string               $label Button text.
 * @param string               $url   Destination URL.
 * @param array<string, mixed> $args  style, class, target, rel.
 */
function ath_btn_html( $label, $url, array $args = array() ) {
	$label = trim( (string) $label, " \t\n\r\0\x0B\"'" );
	$url   = trim( (string) $url );

	if ( $label === '' || $url === '' ) {
		return '';
	}

	$style = (string) ( $args['style'] ?? 'view' );
	$class = ath_btn_style_class( $style );
	$extra = trim( (string) ( $args['class'] ?? '' ) );

	if ( $extra !== '' ) {
		$class .= ' ' . $extra;
	}

	$attrs = array(
		'href'  => $url,
		'class' => trim( $class ),
	);

	$target = (string) ( $args['target'] ?? '' );
	if ( $target !== '' ) {
		$attrs['target'] = $target;
	}

	$rel = (string) ( $args['rel'] ?? '' );
	if ( $rel !== '' ) {
		$attrs['rel'] = $rel;
	}

	$attr_html = '';
	foreach ( $attrs as $key => $value ) {
		$attr_html .= sprintf( ' %s="%s"', $key, esc_attr( $value ) );
	}

	return '<a' . $attr_html . '>' . esc_html( $label ) . '</a>';
}

/**
 * Wrap button HTML for alignment.
 *
 * @param string $html  Button markup.
 * @param string $align left | center | right
 */
function ath_btn_wrap_html( $html, $align = '' ) {
	$html = trim( (string) $html );
	if ( $html === '' ) {
		return '';
	}

	$align = strtolower( trim( (string) $align ) );
	if ( ! in_array( $align, array( 'center', 'right' ), true ) ) {
		return $html;
	}

	return '<div class="ath-btn-wrap ath-btn-wrap--' . esc_attr( $align ) . '">' . $html . '</div>';
}

/**
 * Render button from shortcode attributes.
 *
 * @param array<string, string> $atts Shortcode attributes.
 */
function ath_render_btn_block( array $atts ) {
	$atts = wp_parse_args( $atts, ath_btn_default_atts() );

	$label = trim( (string) $atts['label'], " \t\n\r\0\x0B\"'" );
	$link  = trim( (string) $atts['link'], " \t\n\r\0\x0B\"'" );

	if ( $label === '' || $link === '' ) {
		return '';
	}

	$url = ath_resolve_theme_link( $link, (string) $atts['link_type'] );

	$btn_args = array(
		'style' => (string) $atts['style'],
		'class' => trim( (string) $atts['class'] ),
	);

	if ( in_array( strtolower( (string) $atts['newtab'] ), array( '1', 'true', 'yes' ), true ) ) {
		$btn_args['target'] = '_blank';
		$btn_args['rel']    = trim( (string) $atts['rel'] ) !== '' ? (string) $atts['rel'] : 'noopener noreferrer';
	} else {
		if ( trim( (string) $atts['target'] ) !== '' ) {
			$btn_args['target'] = (string) $atts['target'];
		}
		if ( trim( (string) $atts['rel'] ) !== '' ) {
			$btn_args['rel'] = (string) $atts['rel'];
		}
	}

	$html = ath_btn_html( $label, $url, $btn_args );

	return ath_btn_wrap_html( $html, (string) $atts['align'] );
}

/**
 * Build shortcode string for docs / copy-paste.
 *
 * @param array<string, mixed> $overrides Optional overrides.
 */
function ath_btn_shortcode_string( array $overrides = array() ) {
	$atts  = array_merge( ath_btn_default_atts(), $overrides );
	$parts = array();

	foreach ( $atts as $key => $value ) {
		$value = (string) $value;
		if ( $value === '' ) {
			continue;
		}
		$parts[] = sprintf( "%s='%s'", $key, esc_attr( $value ) );
	}

	return '[ath_btn ' . implode( ' ', $parts ) . ']';
}

/**
 * Shortcode callback.
 *
 * @param array<string, string>|string $atts Attributes.
 */
function ath_btn_shortcode( $atts ) {
	$atts = shortcode_atts( ath_btn_default_atts(), $atts, 'ath_btn' );

	return ath_render_btn_block( $atts );
}

add_shortcode( 'ath_btn', 'ath_btn_shortcode' );
