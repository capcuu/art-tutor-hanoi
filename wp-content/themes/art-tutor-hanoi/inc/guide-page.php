<?php
/**
 * Editor guide page — /guide/ (public, auto-synced from theme).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gutenberg code block from plain text.
 *
 * @param string $code Example code or shortcode.
 */
function ath_guide_code_block( $code ) {
	// &#91; displays as "[" but stops do_shortcode from firing inside Code blocks.
	$safe = esc_html( (string) $code );
	$safe = str_replace( '[ath_', '&#91;ath_', $safe );

	return "<!-- wp:code -->\n<pre class=\"wp-block-code\"><code>" . $safe . "</code></pre>\n<!-- /wp:code -->\n\n";
}

/**
 * Build guide page block content (source of truth in theme code).
 */
function ath_guide_page_block_content() {
	$hero_example    = ath_home_hero_shortcode_string(
		array(
			'video_url'     => 'https://res.cloudinary.com/dwy4wtjtc/video/upload/q_auto,w_1920,c_limit/f_auto/v1780352977/Banner_tmdhl6.mp4',
			'video_id'      => '',
			'video_version' => '',
		)
	);
	$cards_example    = ath_home_cards_shortcode_string();
	$gallery_example  = ath_home_gallery_shortcode_string();
	$programs_example  = ath_home_programs_shortcode_string();
	$community_example    = ath_home_community_shortcode_string();
	$testimonials_example = ath_home_testimonials_shortcode_string();
	$news_example         = ath_home_news_shortcode_string();
	$btn_example          = ath_btn_shortcode_string(
		array(
			'label'     => 'View Courses',
			'link'      => 'courses',
			'link_type' => 'page',
		)
	);
	$ath_tools_url   = admin_url( 'tools.php?page=ath-content-migration' );

	$blocks  = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Site Editor Guide — Art Tutor Hanoi</h2>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Hướng dẫn chỉnh sửa website tại <code>/guide/</code>. Nội dung <strong>tự động đồng bộ từ theme</strong> khi admin đăng nhập WP hoặc upload code mới — không cần sửa tay page này (chỉnh trong <code>inc/guide-page.php</code> nếu là dev).</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">1. Công cụ ATH Content</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>WP Admin → Tools → ATH Content</strong> (<a href=\"" . esc_url( $ath_tools_url ) . "\">mở</a>)</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>legacy</strong> — luôn dùng file PHP trong theme (rollback nhanh).</li><li><strong>hybrid</strong> — trang đã migrate dùng Gutenberg; trang động vẫn PHP.</li><li><strong>gutenberg</strong> — ưu tiên editor khi có nội dung.</li><li><strong>Run migration</strong> — export partial PHP → editor (about, pricing, courses, kids-courses, workshops).</li><li><strong>Rebuild homepage</strong> — toàn bộ section trang chủ thành shortcode (hero → testimonials → community).</li><li><strong>Update Guide now</strong> — đồng bộ lại trang hướng dẫn từ theme.</li><li><strong>Restore DB content backup</strong> — khôi phục nội dung editor trước migration.</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">2. Trang chủ (Homepage)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Trang chủ = page tại <strong>Settings → Reading → Homepage</strong>. Sửa trong <strong>Pages → [homepage] → Edit</strong>. Block động = block <strong>Shortcode</strong> (dán cả chuỗi kèm tham số vào một dòng).</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">2a. [ath_home_hero] — Video banner</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Ảnh <code>poster</code> hiện trước khi video chạy. Video: <code>video_url</code> (khuyên dùng) hoặc <code>video_id</code> + <code>video_version</code> (Cloudinary).</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>aria_label</td><td>Nhãn accessibility</td><td>Art studio</td></tr><tr><td>poster</td><td>Ảnh chờ video — assets/images/ hoặc URL</td><td>hero2.jpg</td></tr><tr><td>video_url</td><td>URL .mp4 đầy đủ</td><td>(trống)</td></tr><tr><td>video_id</td><td>Public ID Cloudinary</td><td>Banner_tmdhl6</td></tr><tr><td>video_version</td><td>Version Cloudinary</td><td>v1780352977</td></tr><tr><td>video_width</td><td>Max width (px)</td><td>1920</td></tr><tr><td>cloudinary_folder</td><td>Ghi chú folder</td><td>video</td></tr><tr><td>autoplay, muted, loop, playsinline</td><td>1 hoặc 0</td><td>1</td></tr><tr><td>preload</td><td>auto | metadata | none</td><td>auto</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>video_id / video_version:</strong> từ URL Cloudinary <code>.../v1780352977/Banner_tmdhl6.mp4</code>.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= ath_guide_code_block( $hero_example );

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">2b. [ath_home_cards] — 4 thẻ dưới hero</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Thẻ từ <code>data/{source}.php</code> — mỗi thẻ: <code>title</code>, <code>desc</code>, <code>link</code>, <code>link_type</code> (experience | page | url).</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>source</td><td>File data</td><td>home-cards</td></tr><tr><td>button</td><td>Chữ nút</td><td>View Courses</td></tr><tr><td>aria_label</td><td>Nhãn section</td><td>Featured programs</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= ath_guide_code_block( $cards_example );

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">2c. [ath_home_gallery] — Gallery collage</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>PHP động (shuffle, lazy-load). Ảnh từ <code>data/{source}.php</code>.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>intro</td><td>Text trên gallery</td><td>(text studio)</td></tr><tr><td>source</td><td>File data</td><td>frontpage-gallery</td></tr><tr><td>cloudinary_folder</td><td>Ghi chú folder</td><td>website/image/frontpage</td></tr><tr><td>visible</td><td>Ảnh hiện ngay</td><td>5</td></tr><tr><td>load_more</td><td>Ảnh khi Load more; 0 = còn lại</td><td>0</td></tr><tr><td>shuffle</td><td>1 = xáo mỗi lần load</td><td>1</td></tr><tr><td>button</td><td>Chữ nút Load more</td><td>Load more photos</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= ath_guide_code_block( $gallery_example );

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">2d. [ath_home_programs] — Structured programs (Our courses)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Danh sách khóa học trên trang chủ — mỗi dòng link tới <code>/courses/#anchor</code>. Dữ liệu từ <code>data/{source}.php</code> — mỗi item: <code>num</code>, <code>title</code>, <code>anchor</code>.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>source</td><td>File data</td><td>home-programs</td></tr><tr><td>label</td><td>Nhãn nhỏ (Our courses)</td><td>(từ data)</td></tr><tr><td>heading</td><td>Tiêu đề section</td><td>(từ data)</td></tr><tr><td>courses_page</td><td>Page key cho link</td><td>courses</td></tr><tr><td>footer_label</td><td>Link dưới cùng</td><td>Explore all courses</td></tr><tr><td>aria_label</td><td>Nhãn accessibility</td><td>Structured programs</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= ath_guide_code_block( $programs_example );

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">2e. [ath_home_community] — Artist community</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Carousel học viên (Posts category <strong>Learner's artworks</strong>) — PHP động, sắp xếp theo comment mới nhất. Dòng thống kê từ <code>data/home-community.php</code> hoặc tham số <code>stat</code> (hai dòng cách nhau bằng <code>|</code>).</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>source</td><td>File data</td><td>home-community</td></tr><tr><td>stat</td><td>Text thống kê (dòng1|dòng2)</td><td>(từ data)</td></tr><tr><td>limit</td><td>Số thẻ carousel</td><td>6</td></tr><tr><td>always_visible</td><td>1 = luôn mở (homepage)</td><td>1</td></tr><tr><td>footer_label</td><td>Link dưới carousel</td><td>View all</td></tr><tr><td>view_all_page</td><td>Page key hub artworks</td><td>students-artworks</td></tr><tr><td>aria_label</td><td>Nhãn accessibility</td><td>Artist community</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Thêm học viên vào carousel:</strong> Posts → Add New → category Learner's artworks → Featured image + publish. Không sửa file data.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= ath_guide_code_block( $community_example );

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">2f. [ath_home_testimonials] — Testimonials slider</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Slider đánh giá + ảnh bên phải. Nội dung từ <code>data/{source}.php</code> — mỗi slide: <code>text</code>, <code>author</code>. JS slider dùng <code>track_id</code> / <code>dots_id</code> (mặc định <code>testimonials-track</code>).</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>source</td><td>File data</td><td>home-testimonials</td></tr><tr><td>label</td><td>Nhãn nhỏ</td><td>Testimonials</td></tr><tr><td>heading</td><td>Tiêu đề</td><td>What our students say</td></tr><tr><td>photo</td><td>Ảnh — assets/images/ hoặc URL</td><td>gallery/HTT_1707.jpg</td></tr><tr><td>photo_alt</td><td>Alt ảnh</td><td>(từ data)</td></tr><tr><td>aria_label</td><td>Nhãn accessibility</td><td>Testimonials</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= ath_guide_code_block( $testimonials_example );

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">2g. [ath_home_news] — From Our Studio (Studio News)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Carousel tin tức studio (Posts category <strong>Studio News</strong>) — PHP động, mới nhất trước. Hiện 3 thẻ desktop; kéo ngang hoặc nút mũi tên để xem thêm (mặc định tối đa 6 bài). Section chỉ hiện khi category có bài publish.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>source</td><td>File data</td><td>home-news</td></tr><tr><td>label</td><td>Nhãn nhỏ</td><td>Art Tutor Hanoi</td></tr><tr><td>heading</td><td>Tiêu đề section</td><td>From Our Studio</td></tr><tr><td>limit</td><td>Số thẻ carousel</td><td>6</td></tr><tr><td>btn_label</td><td>Chữ nút trên thẻ</td><td>Read More</td></tr><tr><td>footer_label</td><td>Link dưới carousel</td><td>View all news</td></tr><tr><td>aria_label</td><td>Nhãn accessibility</td><td>Studio news</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Thêm tin:</strong> Posts → Add New → category <strong>Studio News</strong> → Featured image + publish. Bài và archive category dùng <code>single.php</code> + <code>partials/post-content.php</code> — header/footer Art Tutor Hanoi.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= ath_guide_code_block( $news_example );

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">3. [ath_btn] — Nút chuẩn (dùng mọi trang)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Nút teal giống thẻ trang chủ (<code>.btn-view</code>). Dùng block <strong>Shortcode</strong> trong bất kỳ page Gutenberg nào — thay cho nút mặc định của editor.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Tham số</th><th>Ý nghĩa</th><th>Mặc định</th></tr></thead><tbody><tr><td>label</td><td>Chữ trên nút (bắt buộc)</td><td>—</td></tr><tr><td>link</td><td>Slug / page key / URL (bắt buộc)</td><td>—</td></tr><tr><td>link_type</td><td>experience | page | book | url</td><td>url</td></tr><tr><td>style</td><td>view | contact | cta</td><td>view</td></tr><tr><td>align</td><td>center | right (trống = trái)</td><td>(trống)</td></tr><tr><td>newtab</td><td>1 = mở tab mới</td><td>0</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Ví dụ link_type:</strong> <code>page</code> + <code>courses</code> → /courses/; <code>experience</code> + <code>life-drawing</code> → workshop; <code>book</code> + <code>adults</code> → tab Book.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= ath_guide_code_block( $btn_example );
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Xem trước:</strong></p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:shortcode -->\n" . $btn_example . "\n<!-- /wp:shortcode -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">4. Thêm hoặc sửa Page</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Page thường</strong> — Pages → Add New; giữ template <strong>Default</strong> (page.php). Theme tự xóa template custom khi lưu (trừ Book và Students' Artworks).</li><li><strong>Post / category / tag</strong> — <code>single.php</code> (trừ Learner's artworks → template riêng).</li><li><strong>Trang migrate</strong> (about, pricing, workshops, courses, …) — Pages → Edit; reset bằng migration + Overwrite.</li><li><strong>Homepage</strong> — Rebuild homepage trong ATH Content.</li><li><strong>Template</strong> — Page: Default; Post: single.php; chỉ Book và Students' Artworks dùng template riêng (PHP động).</li><li><strong>Permalinks</strong> — Save sau khi thêm slug mới.</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">5. Hồ sơ học viên mới</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\"><li>Posts → Add New</li><li>Category: <strong>Learner's artworks</strong></li><li>Paragraph đầu = mô tả hero profile</li><li>Featured image = thumbnail trên <a href=\"" . esc_url( ath_page_url( 'students-artworks' ) ) . "\">Students' Artworks</a></li><li>Publish</li></ol>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">6. Trang không sửa Gutenberg</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Book, Weekly calendar, Thank you, Links, Students' Artworks hub — template riêng hoặc PHP động</li><li><code>/courses/{slug}/</code>, <code>/workshops/{slug}/</code> — Pages con, template Default + Gutenberg (breadcrumb do theme)</li><li><code>/kids-courses/{slug}/</code> — file <code>data/*.php</code></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">7. Fluent Form #40 — Adult Booking (Date &amp; time slots)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Form đặt chỗ người lớn nhúng tại <strong>/book/?tab=adult</strong>. Quy tắc <strong>Date</strong> và <strong>Preferred time slots</strong> nằm trong <strong>Custom Javascript</strong> của form — không sửa trong theme trừ VietQR (mục 7d).</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Mở editor:</strong> WP Admin → <strong>Fluent Forms → All Forms → Adult Booking</strong> (ID <strong>40</strong>) → tab <strong>Settings &amp; Integrations</strong> → <strong>Custom CSS/JS</strong> → ô <strong>Custom Javascript</strong> → Save Form.</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">7a. Tên field (phải khớp trong JS)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Field trên form</th><th>Name attribute</th><th>Loại</th></tr></thead><tbody><tr><td>Program</td><td><code>dropdown</code></td><td>Dropdown</td></tr><tr><td>Workshop</td><td><code>dropdown_1</code></td><td>Dropdown (conditional)</td></tr><tr><td>Date</td><td><code>datetime</code></td><td>Date/time (flatpickr)</td></tr><tr><td>Preferred time slot</td><td><code>time_slots</code></td><td><strong>Radio</strong> — một lựa chọn; Value từng option phải khớp JS</td></tr><tr><td>Number of participants</td><td><code>item-quantity</code></td><td>Item Quantity</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">7b. Quy tắc Date (calendar)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Điều kiện</th><th>Date picker</th></tr></thead><tbody><tr><td>Program = <strong>Art Residency</strong></td><td>Mọi ngày đều chọn được</td></tr><tr><td>Program khác Art Residency</td><td><strong>Thứ 5</strong> bị mờ — không chọn</td></tr><tr><td>Workshop = <strong>Nude model drawing</strong></td><td>Chỉ <strong>thứ 7</strong> (Saturday)</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Trong JS: <code>getDay() === 2</code> = thứ 3, <code>getDay() === 4</code> = thứ 5 (Date), <code>getDay() === 6</code> = thứ 7 (0 = Chủ nhật).</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">7c. Quy tắc time slots</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr><th>Slot (Value trên form)</th><th>Chỉ chọn khi</th></tr></thead><tbody><tr><td><code>13;30 – 16:00</code></td><td>Workshop = Nude model drawing (lưu ý dấu <code>;</code> trong form hiện tại)</td></tr><tr><td><code>18:30 – 20:30</code></td><td>Date đã chọn là <strong>thứ 3</strong></td></tr><tr><td>Các slot khác</td><td>Bình thường (trừ khi bị rule Nude model khóa)</td></tr></tbody></table></figure>\n<!-- /wp:table -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Đổi Date sang ngày không phải thứ 3 → slot 18:30 tự bỏ chọn và mờ. Đổi Workshop khỏi Nude model → các slot khác mở lại.</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">7d. Sửa rule — chỉnh biến đầu Custom Javascript</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Sau khi đổi <strong>label/value</strong> option trong form editor, cập nhật các hằng số ở đầu file JS (phải trùng <strong>Value</strong> của option, không chỉ label hiển thị):</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= ath_guide_code_block(
		"var RESIDENCY = 'Art Residency';\n" .
		"var NUDE_MODEL = 'Nude model drawing';\n" .
		"var NUDE_SLOT = '13;30 \\u2013 16:00';\n" .
		"var TUESDAY_SLOT = '18:30 \\u2013 20:30';"
	);
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Thêm rule Date mới</strong> — sửa <code>getDisableRules()</code> và <code>isDateAllowed()</code>.</li><li><strong>Thêm rule slot mới</strong> — thêm biến <code>var MY_SLOT = '...';</code> và nhánh trong <code>isSlotAllowed()</code>.</li><li><strong>Đổi tên field</strong> — tìm <code>select[name=\"dropdown\"]</code>, <code>dropdown_1</code>, <code>datetime</code>, <code>input[name=\"time_slots\"]</code> trong JS và đổi cho khớp Name Attribute.</li><li><strong>Sửa label slot trong form</strong> — mở Radio Time slots → từng option → copy cột <strong>Value</strong> → dán vào JS (ký tự en-dash <code>–</code> phải giống hệt; có thể dùng <code>\\u2013</code> trong chuỗi JS).</li><li><strong>Test</strong> — Preview form: thử từng Program/Workshop, chọn Date, chọn một slot; mở DevTools Console nếu có lỗi JS.</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">7e. Thanh toán &amp; VietQR (tham khảo)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Giá</strong> — Calc Value trên Program / Courses / Duration; Custom Payment Amount công thức <code>(Program + Courses + Duration)</code>; Item Quantity map <code>payment_input</code>.</li><li><strong>VietQR</strong> — theme <code>config/fluent-qr.php</code> (form 40, tỉ giá USD→VND 26 500). Custom HTML cần <code>#qr_amount</code> và <code>#qr_img</code>.</li><li><strong>Form ID tab Adult</strong> — mặc định <code>40</code> (<code>ATH_ADULT_BOOKING_FORM_ID</code> trong <code>config/book-tabs.php</code>).</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">8. Rollback</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Backup file: <code>backup/pre-gutenberg-snapshot/</code></li><li>ATH Content → legacy / Restore DB backup</li></ul>\n<!-- /wp:list -->\n\n";

	return $blocks;
}

/**
 * Hash of guide body — changes when theme docs / shortcode defaults change.
 */
function ath_guide_page_content_hash() {
	return md5( ath_guide_page_block_content() );
}

/**
 * Create or update the public guide page when content hash differs.
 *
 * @param bool $force Overwrite even if hash matches.
 * @return array{id: int, updated: bool}
 */
function ath_ensure_guide_page( $force = false ) {
	$slug    = 'guide';
	$title   = 'Site Editor Guide';
	$hash    = ath_guide_page_content_hash();
	$content = ath_guide_page_block_content();
	$result  = array(
		'id'      => 0,
		'updated' => false,
	);

	$existing = get_page_by_path( $slug );

	if ( $existing ) {
		$stored_hash  = (string) get_post_meta( $existing->ID, '_ath_guide_content_hash', true );
		$needs_update = $force || $stored_hash !== $hash || $existing->post_status !== 'publish';

		$result['id'] = (int) $existing->ID;

		if ( ! $needs_update ) {
			return $result;
		}

		$update = array(
			'ID'           => $existing->ID,
			'post_status'  => 'publish',
			'post_content' => $content,
		);

		wp_update_post( $update );
		update_post_meta( $existing->ID, '_ath_guide_content_hash', $hash );
		delete_post_meta( $existing->ID, '_wp_page_template' );
		delete_post_meta( $existing->ID, '_ath_guide_content_version' );

		$result['updated'] = true;

		return $result;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => $content,
		),
		true
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return $result;
	}

	update_post_meta( $page_id, '_ath_guide_content_hash', $hash );
	$result['id']      = (int) $page_id;
	$result['updated'] = true;

	return $result;
}

/**
 * Auto-sync guide when an admin visits WP Admin.
 */
function ath_maybe_sync_guide_page() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$result = ath_ensure_guide_page( false );

	if ( $result['updated'] ) {
		set_transient( 'ath_guide_page_sync_notice', 1, 60 );
	}
}

/**
 * Permalink for the guide page (empty if missing).
 */
function ath_guide_page_url() {
	$page = get_page_by_path( 'guide' );
	if ( ! $page || $page->post_status !== 'publish' ) {
		return '';
	}

	return get_permalink( $page );
}

/**
 * Admin notice after guide sync.
 */
function ath_guide_page_sync_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_transient( 'ath_guide_page_sync_notice' ) ) {
		return;
	}

	delete_transient( 'ath_guide_page_sync_notice' );
	?>
	<div class="notice notice-success is-dismissible">
		<p><strong>Guide page updated</strong> — <a href="<?php echo esc_url( ath_guide_page_url() ); ?>">View /guide/</a></p>
	</div>
	<?php
}

add_action( 'after_switch_theme', 'ath_ensure_guide_page' );
add_action( 'admin_init', 'ath_maybe_sync_guide_page', 100 );
add_action( 'admin_notices', 'ath_guide_page_sync_admin_notice' );

add_action(
	'init',
	function () {
		if ( get_page_by_path( 'guide' ) ) {
			return;
		}
		ath_ensure_guide_page( true );
	},
	6
);
