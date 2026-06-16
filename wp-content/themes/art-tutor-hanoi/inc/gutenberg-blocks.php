<?php
/**
 * Shared Gutenberg block builders (native blocks, not Custom HTML).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * JSON attrs for a Gutenberg block comment.
 *
 * @param array<string, mixed> $attrs Attributes.
 */
function ath_gutenberg_block_attrs( array $attrs ) {
	if ( empty( $attrs ) ) {
		return '';
	}

	return ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
}

/**
 * Paragraph block (plain text).
 *
 * @param string $text  Plain text.
 * @param string $class Optional CSS class.
 */
function ath_gutenberg_paragraph( $text, $class = '' ) {
	$attrs      = $class !== '' ? array( 'className' => $class ) : array();
	$class_attr = $class !== '' ? ' class="' . esc_attr( $class ) . '"' : '';

	return '<!-- wp:paragraph' . ath_gutenberg_block_attrs( $attrs ) . " -->\n<p{$class_attr}>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->";
}

/**
 * Paragraph block with limited inline HTML (links, strong).
 *
 * @param string $html  Inner HTML for the paragraph.
 * @param string $class Optional CSS class.
 */
function ath_gutenberg_paragraph_html( $html, $class = '' ) {
	$attrs      = $class !== '' ? array( 'className' => $class ) : array();
	$class_attr = $class !== '' ? ' class="' . esc_attr( $class ) . '"' : '';
	$safe       = wp_kses(
		$html,
		array(
			'a'    => array(
				'href'   => array(),
				'class'  => array(),
				'target' => array(),
				'rel'    => array(),
			),
			'strong' => array(),
			's'      => array(),
			'strike' => array(),
			'del'    => array(),
			'span'   => array(
				'aria-hidden' => array(),
				'class'       => array(),
			),
		)
	);

	return '<!-- wp:paragraph' . ath_gutenberg_block_attrs( $attrs ) . " -->\n<p{$class_attr}>" . $safe . "</p>\n<!-- /wp:paragraph -->";
}

/**
 * Heading block.
 *
 * @param string $text  Plain text.
 * @param int    $level Heading level 1–6.
 * @param string $class Optional CSS class.
 */
function ath_gutenberg_heading( $text, $level, $class = '' ) {
	$level = max( 1, min( 6, (int) $level ) );
	$attrs = array( 'level' => $level );
	if ( $class !== '' ) {
		$attrs['className'] = $class;
	}

	$tag        = 'h' . $level;
	$class_attr = $class !== '' ? ' class="' . esc_attr( $class ) . '"' : '';

	return '<!-- wp:heading' . ath_gutenberg_block_attrs( $attrs ) . " -->\n<{$tag}{$class_attr}>" . esc_html( $text ) . "</{$tag}>\n<!-- /wp:heading -->";
}

/**
 * Bulleted list block.
 *
 * @param string[] $items List item strings.
 * @param string   $class Optional CSS class on ul.
 */
function ath_gutenberg_list( array $items, $class = '' ) {
	$attrs      = $class !== '' ? array( 'className' => $class ) : array();
	$list_class = 'wp-block-list' . ( $class !== '' ? ' ' . esc_attr( $class ) : '' );
	$inner      = '';

	foreach ( $items as $item ) {
		$inner .= "<!-- wp:list-item -->\n<li>" . esc_html( (string) $item ) . "</li>\n<!-- /wp:list-item -->\n";
	}

	return '<!-- wp:list' . ath_gutenberg_block_attrs( $attrs ) . " -->\n<ul class=\"{$list_class}\">{$inner}</ul>\n<!-- /wp:list -->";
}

/**
 * Render one table cell for ath_gutenberg_table().
 *
 * @param array{content?: string, tag?: string, colspan?: int} $cell Cell definition.
 */
function ath_gutenberg_table_cell_html( array $cell ) {
	$tag     = isset( $cell['tag'] ) && $cell['tag'] === 'th' ? 'th' : 'td';
	$content = (string) ( $cell['content'] ?? '' );

	if ( strpos( $content, '<br' ) !== false ) {
		$inner = wp_kses( $content, array( 'br' => array() ) );
	} elseif ( strpos( $content, "\n" ) !== false ) {
		$inner = wp_kses( nl2br( esc_html( $content ), false ), array( 'br' => array() ) );
	} else {
		$inner = esc_html( $content );
	}

	$colspan = ! empty( $cell['colspan'] ) ? ' colspan="' . (int) $cell['colspan'] . '"' : '';

	return "<{$tag}{$colspan}>{$inner}</{$tag}>";
}

/**
 * Native wp:table block.
 *
 * @param array<int, array<int, array{content?: string, tag?: string, colspan?: int}>> $head_rows Header rows.
 * @param array<int, array<int, array{content?: string, tag?: string, colspan?: int}>> $body_rows Body rows.
 * @param string                                                                      $class     Figure className.
 * @param string                                                                      $caption   Optional caption.
 */
function ath_gutenberg_table( array $head_rows, array $body_rows, $class = '', $caption = '' ) {
	$attrs = array();
	if ( $class !== '' ) {
		$attrs['className'] = $class;
	}

	$thead = '';
	if ( $head_rows ) {
		$rows = '';
		foreach ( $head_rows as $row ) {
			$cells = '';
			foreach ( $row as $cell ) {
				$cell['tag'] = $cell['tag'] ?? 'th';
				$cells      .= ath_gutenberg_table_cell_html( $cell );
			}
			$rows .= '<tr>' . $cells . '</tr>';
		}
		$thead = '<thead>' . $rows . '</thead>';
	}

	$tbody = '';
	foreach ( $body_rows as $row ) {
		$cells = '';
		foreach ( $row as $cell ) {
			$cells .= ath_gutenberg_table_cell_html( $cell );
		}
		$tbody .= '<tr>' . $cells . '</tr>';
	}

	$caption_html  = $caption !== '' ? '<caption class="courses-schedule__caption">' . esc_html( $caption ) . '</caption>' : '';
	$figure_class  = 'wp-block-table' . ( $class !== '' ? ' ' . esc_attr( $class ) : '' );
	$table_class   = 'courses-schedule__table' . ( $class !== '' ? ' ' . esc_attr( $class ) : '' );
	$table_markup  = '<figure class="' . $figure_class . '"><table class="' . esc_attr( trim( $table_class ) ) . '">'
		. $caption_html . $thead . '<tbody>' . $tbody . '</tbody></table></figure>';

	return '<!-- wp:table' . ath_gutenberg_block_attrs( $attrs ) . " -->\n" . $table_markup . "\n<!-- /wp:table -->";
}

/**
 * Image block (external URL).
 *
 * @param string $url    Image URL.
 * @param string $alt    Alt text.
 * @param int    $width  Optional width.
 * @param int    $height Optional height.
 */
function ath_gutenberg_image( $url, $alt, $width = 0, $height = 0 ) {
	$attrs = array(
		'sizeSlug'        => 'large',
		'linkDestination' => 'none',
	);

	$size = '';
	if ( $width ) {
		$size .= ' width="' . (int) $width . '"';
	}
	if ( $height ) {
		$size .= ' height="' . (int) $height . '"';
	}

	return '<!-- wp:image' . ath_gutenberg_block_attrs( $attrs ) . " -->\n<figure class=\"wp-block-image size-large\"><img src=\"" . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . "\"{$size}/></figure>\n<!-- /wp:image -->";
}

/**
 * Shortcode block.
 *
 * @param string $shortcode Shortcode string.
 */
function ath_gutenberg_shortcode( $shortcode ) {
	return "<!-- wp:shortcode -->\n" . $shortcode . "\n<!-- /wp:shortcode -->";
}

/**
 * Group block wrapper.
 *
 * @param string               $inner_html Inner blocks markup.
 * @param string               $class      Group CSS class.
 * @param array<string, mixed> $extra      Extra block attributes.
 */
function ath_gutenberg_group( $inner_html, $class = '', array $extra = array() ) {
	$attrs = $extra;
	if ( $class !== '' ) {
		$attrs['className'] = $class;
	}

	$class_attr = $class !== '' ? ' class="wp-block-group ' . esc_attr( $class ) . '"' : ' class="wp-block-group"';

	return '<!-- wp:group' . ath_gutenberg_block_attrs( $attrs ) . " -->\n<div{$class_attr}>\n{$inner_html}\n</div>\n<!-- /wp:group -->";
}

/**
 * [ath_btn] shortcode block.
 *
 * @param string $label Button label.
 * @param string $url   Destination URL.
 * @param string $style Button style (view|cta).
 * @param string $class Extra CSS class.
 */
function ath_gutenberg_btn( $label, $url, $style = 'view', $class = '' ) {
	$parts = array(
		'label="' . esc_attr( $label ) . '"',
		'link="' . esc_attr( $url ) . '"',
		'link_type="url"',
		'style="' . esc_attr( $style ) . '"',
	);

	if ( $class !== '' ) {
		$parts[] = 'class="' . esc_attr( $class ) . '"';
	}

	return ath_gutenberg_shortcode( '[ath_btn ' . implode( ' ', $parts ) . ']' );
}

/**
 * Course-style content blocks (heading + text + list sections).
 *
 * @param array<int, array<string, mixed>> $blocks Section rows.
 * @return string[]
 */
function ath_gutenberg_course_block_sections( array $blocks ) {
	$sections = array();

	foreach ( $blocks as $block ) {
		$section = array();
		if ( ! empty( $block['heading'] ) ) {
			$section[] = ath_gutenberg_heading( (string) $block['heading'], 2, 'course-block__heading' );
		}
		if ( ! empty( $block['text'] ) ) {
			$section[] = ath_gutenberg_paragraph( (string) $block['text'], 'course-block__text' );
		}
		if ( ! empty( $block['items'] ) && is_array( $block['items'] ) ) {
			$section[] = ath_gutenberg_list( $block['items'], 'course-block__list' );
		}
		if ( $section ) {
			$sections[] = ath_gutenberg_group( implode( "\n\n", $section ), 'course-block' );
		}
	}

	return $sections;
}

/**
 * Convert experience data to native Gutenberg blocks.
 *
 * @param array<string, mixed> $experience Experience row from data file.
 */
function ath_experience_to_gutenberg_blocks( array $experience ) {
	require_once ATH_THEME_DIR . '/config/images.php';

	$blocks = array();

	$hero_src  = ath_experience_hero_img_url( $experience['image']['url'] );
	$hero_alt  = isset( $experience['image']['alt'] ) ? (string) $experience['image']['alt'] : (string) $experience['title'];

	$hero_inner  = ath_gutenberg_group(
		ath_gutenberg_image( $hero_src, $hero_alt, 0, 0 ),
		'experience-hero__media'
	);
	$hero_inner .= "\n\n";

	$content_bits = array();
	if ( ! empty( $experience['category'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph( (string) $experience['category'], 'experience-hero__category' );
	}
	$content_bits[] = ath_gutenberg_heading( (string) $experience['title'], 1, 'experience-hero__title' );
	if ( ! empty( $experience['subtitle'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph( (string) $experience['subtitle'], 'experience-hero__subtitle' );
	}
	if ( ! empty( $experience['price'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph( (string) $experience['price'], 'experience-hero__price' );
	}
	if ( ! empty( $experience['price_note'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph( (string) $experience['price_note'], 'experience-hero__price-note' );
	}
	if ( ! empty( $experience['intro'] ) ) {
		$content_bits[] = ath_gutenberg_paragraph( (string) $experience['intro'], 'experience-hero__intro' );
	}
	if ( ! empty( $experience['book']['label'] ) && ! empty( $experience['book']['url'] ) ) {
		$content_bits[] = ath_gutenberg_btn(
			(string) $experience['book']['label'],
			(string) $experience['book']['url'],
			'view',
			'experience-hero__btn'
		);
	}

	$hero_inner .= ath_gutenberg_group( implode( "\n\n", $content_bits ), 'experience-hero__content' );
	$blocks[]     = ath_gutenberg_group( $hero_inner, 'experience-hero' );

	if ( ! empty( $experience['meta'] ) && is_array( $experience['meta'] ) ) {
		$meta_bits = array();
		foreach ( $experience['meta'] as $item ) {
			if ( empty( $item['label'] ) || empty( $item['value'] ) ) {
				continue;
			}
			$meta_bits[] = '<!-- wp:paragraph {"className":"experience-meta__item"} -->'
				. "\n<p class=\"experience-meta__item\"><strong>" . esc_html( (string) $item['label'] ) . ':</strong> '
				. esc_html( (string) $item['value'] ) . "</p>\n<!-- /wp:paragraph -->";
		}
		if ( $meta_bits ) {
			$blocks[] = ath_gutenberg_group( implode( "\n\n", $meta_bits ), 'experience-meta' );
		}
	}

	if ( ! empty( $experience['blocks'] ) && is_array( $experience['blocks'] ) ) {
		$body_bits = ath_gutenberg_course_block_sections( $experience['blocks'] );
		if ( $body_bits ) {
			$blocks[] = ath_gutenberg_group( implode( "\n\n", $body_bits ), 'experience-body' );
		}
	}

	if ( ! empty( $experience['gallery'] ) && is_array( $experience['gallery'] ) ) {
		$gallery_bits = array();
		foreach ( $experience['gallery'] as $item ) {
			if ( empty( $item['url'] ) ) {
				continue;
			}
			$gallery_src = ath_course_gallery_img_url( $item['url'] );
			$caption     = isset( $item['caption'] ) ? (string) $item['caption'] : (string) $experience['title'];
			$gallery_bits[] = ath_gutenberg_group(
				ath_gutenberg_image( $gallery_src, $caption, 0, 0 ),
				'course-gallery__item'
			);
		}
		if ( $gallery_bits ) {
			$blocks[] = ath_gutenberg_group(
				ath_gutenberg_group( implode( "\n\n", $gallery_bits ), 'course-gallery__grid' ),
				'course-gallery'
			);
		}
	}

	$cta_bits = array();
	if ( ! empty( $experience['book']['label'] ) && ! empty( $experience['book']['url'] ) ) {
		$cta_bits[] = ath_gutenberg_paragraph( 'Ready to join? Complete your booking online.', 'courses-cta__text' );
		$cta_bits[] = ath_gutenberg_btn(
			(string) $experience['book']['label'],
			(string) $experience['book']['url'],
			'cta',
			'courses-cta__btn'
		);
	}
	if ( ! empty( $experience['policy'] ) ) {
		$cta_bits[] = ath_gutenberg_paragraph( (string) $experience['policy'], 'courses-cta__note' );
	}

	$pricing_url  = ath_page_url( 'pricing' );
	$calendar_url = ath_page_url( 'calendar' );
	$cta_bits[]     = ath_gutenberg_paragraph_html(
		'<a href="' . esc_url( $pricing_url ) . '">All programs</a> '
		. '<span aria-hidden="true">·</span> <a href="' . esc_url( $calendar_url ) . '">Weekly calendar</a>',
		'courses-cta__links'
	);

	$blocks[] = ath_gutenberg_group(
		ath_gutenberg_group( implode( "\n\n", $cta_bits ), 'courses-cta__inner' ),
		'courses-cta'
	);

	return trim( implode( "\n\n", $blocks ) );
}
