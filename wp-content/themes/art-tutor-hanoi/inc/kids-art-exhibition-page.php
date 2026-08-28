<?php
/**
 * Kids international exhibition landing — hide paid packages (sell after studio visit).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Content transform version for /kids-art-exhibition/ pages.
 */
function ath_kids_exhibition_funnel_version() {
	return 1;
}

/**
 * VN + EN landing slugs.
 *
 * @return array<string, string> slug => lang
 */
function ath_kids_exhibition_page_langs() {
	return array(
		'kids-art-exhibition'    => 'vi',
		'kids-art-exhibition-en' => 'en',
	);
}

/**
 * Start offset of a heading block (wp:heading or <h2>) that contains $needle.
 *
 * @param string $content Page post_content.
 * @param string $needle  Visible heading text.
 * @return int Offset or -1.
 */
function ath_kids_exhibition_heading_start( $content, $needle ) {
	$pos = strpos( $content, $needle );
	if ( $pos === false ) {
		return -1;
	}

	$prefix = substr( $content, 0, $pos );
	$block  = strrpos( $prefix, '<!-- wp:heading' );
	if ( $block !== false ) {
		return (int) $block;
	}

	$h2 = strrpos( $prefix, '<h2' );
	if ( $h2 !== false ) {
		return (int) $h2;
	}

	$h3 = strrpos( $prefix, '<h3' );
	if ( $h3 !== false ) {
		return (int) $h3;
	}

	return $pos;
}

/**
 * Ordered Gutenberg list (allows <strong>).
 *
 * @param string[] $items Inner HTML per item.
 */
function ath_kids_exhibition_ordered_list_block( array $items ) {
	$inner = '';
	foreach ( $items as $item ) {
		$safe   = wp_kses(
			(string) $item,
			array(
				'strong' => array(),
			)
		);
		$inner .= "<!-- wp:list-item -->\n<li>" . $safe . "</li>\n<!-- /wp:list-item -->\n";
	}

	return '<!-- wp:list' . ath_gutenberg_block_attrs( array( 'ordered' => true ) ) . " -->\n<ol class=\"wp-block-list\">\n" . $inner . "</ol>\n<!-- /wp:list -->";
}

/**
 * Replace the first ordered list after a heading needle.
 *
 * @param string   $content      Post content.
 * @param string   $after_needle Heading text.
 * @param string[] $items        New list items (HTML).
 */
function ath_kids_exhibition_replace_steps_list( $content, $after_needle, array $items ) {
	$anchor = strpos( $content, $after_needle );
	if ( $anchor === false ) {
		return $content;
	}

	$window_end = min( strlen( $content ), $anchor + 4000 );
	$slice      = substr( $content, $anchor, $window_end - $anchor );
	$rel_block  = strpos( $slice, '<!-- wp:list' );
	$rel_ol     = strpos( $slice, '<ol' );

	if ( $rel_block !== false && ( $rel_ol === false || $rel_block < $rel_ol ) ) {
		$abs  = $anchor + $rel_block;
		$end  = strpos( $content, '<!-- /wp:list -->', $abs );
		if ( $end === false ) {
			return $content;
		}
		$end += strlen( '<!-- /wp:list -->' );

		return substr( $content, 0, $abs ) . ath_kids_exhibition_ordered_list_block( $items ) . substr( $content, $end );
	}

	if ( $rel_ol !== false ) {
		$abs = $anchor + $rel_ol;
		$end = strpos( $content, '</ol>', $abs );
		if ( $end === false ) {
			return $content;
		}
		$end += strlen( '</ol>' );

		return substr( $content, 0, $abs ) . ath_kids_exhibition_ordered_list_block( $items ) . substr( $content, $end );
	}

	return $content;
}

/**
 * Cut content from one heading through the start of another heading.
 *
 * @param string $content Post content.
 * @param string $start   Start heading needle (removed).
 * @param string $end     End heading needle (kept).
 */
function ath_kids_exhibition_cut_heading_section( $content, $start, $end ) {
	$from = ath_kids_exhibition_heading_start( $content, $start );
	$to   = ath_kids_exhibition_heading_start( $content, $end );
	if ( $from < 0 || $to < 0 || $to <= $from ) {
		return $content;
	}

	return substr( $content, 0, $from ) . substr( $content, $to );
}

/**
 * Replace FAQ heading + everything after it.
 *
 * @param string $content Post content.
 * @param string $faq     New FAQ blocks.
 */
function ath_kids_exhibition_replace_faq( $content, $faq ) {
	$from = ath_kids_exhibition_heading_start( $content, 'FAQ' );
	if ( $from < 0 ) {
		return $content . "\n\n" . $faq;
	}

	return rtrim( substr( $content, 0, $from ) ) . "\n\n" . $faq . "\n";
}

/**
 * How-it-works list items.
 *
 * @param string $lang vi|en.
 * @return string[]
 */
function ath_kids_exhibition_step_items( $lang ) {
	if ( $lang === 'en' ) {
		return array(
			'<strong>Step 1:</strong> Send 1–2 photos of your child’s artwork (form below or message us).',
			'<strong>Step 2:</strong> An artist reviews the work and invites you to a free 20–30 minute studio consult in Tay Ho — please bring your child.',
			'<strong>Step 3:</strong> After the visit, we explain how to submit if you want to continue. No obligation.',
		);
	}

	return array(
		'<strong>Bước 1:</strong> Gửi 1–2 ảnh tranh con (form bên dưới hoặc nhắn tin).',
		'<strong>Bước 2:</strong> Họa sĩ xem tranh và mời bé tới studio Tây Hồ 1 buổi tư vấn 20–30 phút (không mất phí) — bé đi cùng.',
		'<strong>Bước 3:</strong> Sau buổi tư vấn, studio hướng dẫn cách nộp bài nếu ba mẹ muốn tiếp tục. Không bắt buộc mua gói.',
	);
}

/**
 * FAQ blocks (no paid package prices).
 *
 * @param string $lang vi|en.
 */
function ath_kids_exhibition_faq_blocks( $lang ) {
	if ( $lang === 'en' ) {
		$qa = array(
			'Is sending a photo free?' =>
				'Yes. Send 1–2 photos of the most recent artwork. No fee for this step.',
			'Does a contest suggestion mean the work is accepted?' =>
				'No. Acceptance and prizes belong to the organizer — the studio does not guarantee results.',
			'Is the studio consult free?' =>
				'Yes. About 20–30 minutes at our Tay Ho studio, with your child. We look at the work on paper and talk through the next step.',
			'How long until you look at the photos?' =>
				'Usually within 3 working days after we have clear photos. Slots may be limited near exhibition deadlines.',
			'How do we start?' =>
				'Send the form (photos) or message us. After we review, we invite you to the studio consult.',
		);
	} else {
		$qa = array(
			'Gửi ảnh tranh có mất phí không?' =>
				'Không. Gửi 1–2 ảnh tranh gần nhất — bước này không mất phí.',
			'Studio xem ảnh xong có nghĩa là bé được nhận vào cuộc thi?' =>
				'Không. Việc BTC chấp nhận hay trúng giải thuộc ban tổ chức — studio không cam kết kết quả.',
			'Buổi tư vấn tại studio có mất phí không?' =>
				'Không. Khoảng 20–30 phút tại studio Tây Hồ, bé đi cùng. Họa sĩ xem tranh trên giấy và nói bước tiếp theo.',
			'Mất bao lâu thì họa sĩ xem ảnh?' =>
				'Thường trong 03 ngày làm việc sau khi đủ ảnh rõ. Có thể giới hạn suất gần deadline cuộc thi.',
			'Bắt đầu thế nào?' =>
				'Gửi form (ảnh tranh) hoặc nhắn tin. Studio xem xong sẽ mời bé tới 1 buổi tư vấn tại studio.',
		);
	}

	$blocks   = array();
	$blocks[] = ath_gutenberg_heading( 'FAQ', 2 );
	foreach ( $qa as $question => $answer ) {
		$blocks[] = ath_gutenberg_heading( $question, 3 );
		$blocks[] = ath_gutenberg_paragraph( $answer );
	}

	return trim( implode( "\n\n", $blocks ) );
}

/**
 * Transform one landing page’s Gutenberg content.
 *
 * @param string $content Post content.
 * @param string $lang    vi|en.
 */
function ath_kids_exhibition_transform_content( $content, $lang ) {
	$lang = $lang === 'en' ? 'en' : 'vi';

	$how_needle = $lang === 'en' ? 'How it works' : 'Cách tham gia';
	$content    = ath_kids_exhibition_replace_steps_list(
		$content,
		$how_needle,
		ath_kids_exhibition_step_items( $lang )
	);

	$pkg_start = $lang === 'en' ? 'Service packages' : 'Gói dịch vụ';
	$pkg_end   = $lang === 'en' ? 'Artists who review' : 'Họa sĩ xem bài';
	$content   = ath_kids_exhibition_cut_heading_section( $content, $pkg_start, $pkg_end );

	$content = ath_kids_exhibition_replace_faq( $content, ath_kids_exhibition_faq_blocks( $lang ) );

	return $content;
}

/**
 * True if paid package prices are still visible in content.
 *
 * @param string $content Post content.
 */
function ath_kids_exhibition_content_has_paid_packages( $content ) {
	$needles = array(
		'1.200.000',
		'1,200,000',
		'2.500.000',
		'2,500,000',
		'5.500.000',
		'5,500,000',
		'Gói 1',
		'Gói 2',
		'Gói 3',
		'Package 1',
		'Package 2',
		'Package 3',
		'Service packages',
		'Gói dịch vụ',
	);

	foreach ( $needles as $needle ) {
		if ( strpos( $content, $needle ) !== false ) {
			return true;
		}
	}

	return false;
}

/**
 * Patch VN + EN exhibition landings.
 *
 * @param bool $force Re-run even if version option is current.
 * @return array{updated: string[], skipped: string[], errors: string[]}
 */
function ath_migrate_kids_exhibition_funnel( $force = false ) {
	$results = array(
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	foreach ( ath_kids_exhibition_page_langs() as $slug => $lang ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$results['errors'][] = sprintf( 'Page "%s" not found.', $slug );
			continue;
		}

		$original = (string) $page->post_content;
		$updated  = ath_kids_exhibition_transform_content( $original, $lang );

		if ( $updated === $original && ! ath_kids_exhibition_content_has_paid_packages( $original ) ) {
			$results['skipped'][] = $slug;
			continue;
		}

		if ( $updated === $original && ath_kids_exhibition_content_has_paid_packages( $original ) ) {
			$results['errors'][] = sprintf( '%s: paid packages still present; could not match page structure.', $slug );
			continue;
		}

		$saved = wp_update_post(
			array(
				'ID'           => (int) $page->ID,
				'post_content' => $updated,
			),
			true
		);

		if ( is_wp_error( $saved ) ) {
			$results['errors'][] = sprintf( '%s: %s', $slug, $saved->get_error_message() );
			continue;
		}

		ath_mark_page_gutenberg( (int) $page->ID );
		$results['updated'][] = $slug;
	}

	unset( $force );

	return $results;
}

/**
 * One-time patch after theme deploy.
 */
function ath_maybe_migrate_kids_exhibition_funnel() {
	if ( (int) get_option( 'ath_kids_exhibition_funnel_version', 0 ) >= ath_kids_exhibition_funnel_version() ) {
		return;
	}

	$results = ath_migrate_kids_exhibition_funnel( false );
	if ( ! empty( $results['errors'] ) ) {
		return;
	}

	update_option( 'ath_kids_exhibition_funnel_version', ath_kids_exhibition_funnel_version(), false );
}

add_action( 'init', 'ath_maybe_migrate_kids_exhibition_funnel', 30 );
