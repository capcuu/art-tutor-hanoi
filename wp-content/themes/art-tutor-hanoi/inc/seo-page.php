<?php
/**
 * SEO playbook page — /seo/ (internal, noindex, auto-synced from theme).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bump when playbook content changes — triggers DB sync on next admin load.
 */
function ath_seo_page_sync_version() {
	return '3';
}

/**
 * Build SEO guide block content (source of truth in theme code).
 */
function ath_seo_page_block_content() {
	$site     = home_url( '/' );
	$rm       = admin_url( 'admin.php?page=rank-math' );
	$rm_sitemap = admin_url( 'options-general.php?page=rank-math-options-sitemap' );
	$rm_titles  = admin_url( 'options-general.php?page=rank-math-options-titles' );
	$rm_local   = admin_url( 'options-general.php?page=rank-math-options-local' );
	$rm_redir   = admin_url( 'admin.php?page=rank-math-redirections' );
	$migration  = admin_url( 'tools.php?page=ath-content-migration' );
	$plugins    = admin_url( 'plugins.php' );
	$snippets   = admin_url( 'admin.php?page=snippets' );
	$gsc        = 'https://search.google.com/search-console';
	$guide      = ath_guide_page_url();
	$seo_url    = ath_seo_page_url();

	$blocks = '';

	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Trang nội bộ SEO</strong> — cập nhật từ theme khi deploy. Không index (noindex). Làm lần lượt từ <strong>Sprint 1</strong> trở đi; tick trong Rank Math / GSC khi hoàn thành.</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:paragraph -->\n<p>Dữ liệu GSC export: <code>doc/seo/*.csv</code> trong repo. Site Editor Guide: ";
	if ( $guide ) {
		$blocks .= '<a href="' . esc_url( $guide ) . '">/guide/</a>';
	} else {
		$blocks .= '/guide/';
	}
	$blocks .= ".</p>\n<!-- /wp:paragraph -->\n\n";

	// --- GSC baseline report & expectations ---
	$blocks .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Báo cáo GSC baseline (export <code>doc/seo/</code>)</h2>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:paragraph -->\n<p>Nguồn: Google Search Console export khoảng <strong>09/03/2026 – 08/06/2026</strong> (~3 tháng). Dùng làm mốc so sánh sau khi deploy SEO sprint — export file mới mỗi 2 tuần, ghi ngày trong tên file.</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Tổng quan</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>~277.000 impressions</strong> · <strong>~1.100 clicks</strong> (3 tháng)</li><li>CTR tổng site thấp (~0,4%) — nhiều người <em>thấy</em> site trên Google nhưng <em>chưa bấm</em></li><li>Position trung bình ~5 — đã vào trang kết quả, chưa top 3 cho query commercial rộng</li><li>~<strong>10–15 clicks/ngày</strong> organic (từ chart GSC) ≈ <strong>300–450 clicks/tháng</strong></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Theo thiết bị (<code>Devices.csv</code>)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Desktop</strong> — ~245k imp · 360 clicks · CTR <strong>0,15%</strong> · pos ~5,6 → ưu tiên sửa <em>title + meta</em> (SERP desktop)</li><li><strong>Mobile</strong> — ~21k imp · 714 clicks · CTR <strong>3,36%</strong> · pos ~7,3 → đã ổn hơn desktop</li><li><strong>Tablet</strong> — ~984 imp · CTR ~3,15%</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Top queries (<code>Queries.csv</code>)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>art tutor hanoi</strong> — 123 clicks · 262 imp · CTR <strong>47%</strong> · pos ~2,7 → brand mạnh, giữ nguyên</li><li><strong>art supplies hanoi</strong> / <strong>art shop hanoi</strong> — CTR tốt → trang supply map</li><li><strong>free art feedback</strong> / <strong>rate my art</strong> → trang feedback</li><li><strong>painting classes near me</strong> — ~11k imp · CTR ~0,02% → intent “near me” không khớp Hanoi; <em>không kỳ vọng</em> nhiều click/booking từ query này</li><li>Tutorial (posterizing, multi point perspective…) — traffic phụ, giữ index + link nội bộ về courses</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Trang có vấn đề (<code>Pages.csv</code>) → đã map sprint</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Homepage</strong> — ~194k imp · CTR <strong>0,18%</strong> → Sprint 2: H1, intro, meta; Sprint 4: A/B title</li><li><strong>/workshops/</strong> — ~22k imp · CTR <strong>0,07%</strong> → Sprint 2: H1 + intro SEO</li><li><strong>/art-classes-in-hanoi/</strong> — ~28k imp · <strong>0 click</strong> → 301 → <code>/courses/</code> pillar</li><li><strong>best art classes… 2025/2026</strong> — imp cao, CTR ~0,03% → 301 → <code>/courses/</code></li><li><strong>free-art-feedback</strong> — ~5k imp · CTR ~3,5% → giữ, meta seed</li><li><strong>hanoi-art-supply-map</strong> — ~6,5k imp · CTR ~2,6% → giữ, meta seed</li><li>Bài nude / life drawing trùng URL → 301 → workshop life drawing</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Kỳ vọng sau tối ưu SEO</h2>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Có hy vọng tăng click</strong> — chủ yếu từ <em>cùng lượng hiển thị</em> (cải CTR), không cần xếp hạng mới ngay. SEO đưa người vào site; <strong>booking</strong> còn phụ thuộc landing, form, WhatsApp.</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Dễ đạt (CTR — 1–2 tháng sau deploy + index)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>+30–50% clicks</strong> organic so với baseline (~1.100/3 tháng) → khoảng <strong>1.400–1.650 clicks / 3 tháng</strong> nếu SERP cập nhật tốt</li><li><strong>Homepage</strong> — imp ~194k · CTR 0,18% → mục tiêu <strong>0,5–1%</strong> → ước lượng <strong>+600–1.500 clicks / 3 tháng</strong> (cùng imp)</li><li><strong>Workshops hub</strong> — imp ~22k · CTR 0,07% → mục tiêu <strong>0,3–0,5%</strong> → <strong>+50–100 clicks / 3 tháng</strong></li><li><strong>Courses pillar</strong> — sau redirect từ art-classes (~28k imp) → mục tiêu CTR <strong>0,2–0,5%</strong> → <strong>+50–140 clicks / 3 tháng</strong></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Khó hơn / dài hạn (2–6 tháng)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Position <strong>5 → 3</strong> cho <em>art classes hanoi</em>, <em>art workshops hanoi</em></li><li>FAQ + schema → long-tail booking questions</li><li><strong>Không kỳ vọng</strong> nhiều click từ <em>painting classes near me</em> hoặc x10 traffic overnight</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Không tính được từ GSC</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Số trial class / form book thực tế (theo dõi Fluent Forms + WhatsApp)</li><li>Traffic direct, WhatsApp, GBP (Google Business Profile)</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Thời hạn kiểm tra kết quả</h2>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>3–14 ngày</strong> sau deploy — Google crawl meta, redirect, FAQ; test redirect tay; URL Inspection → Request indexing (home, workshops, courses, faq)</li><li><strong>2–4 tuần</strong> — GSC Performance bắt đầu phản ánh CTR mới; export Pages + Queries so với baseline</li><li><strong>4–8 tuần</strong> — đánh giá chính: watchlist bên dưới (Sprint 4); so CTR desktop homepage</li><li><strong>4 tuần / variant</strong> — nếu home CTR desktop &lt; <strong>0,5%</strong>: đổi title B hoặc C trong Rank Math (mục A/B)</li><li><strong>2–6 tháng</strong> — position queries commercial; so export 3 tháng mới vs file baseline</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Lịch kiểm tra (ghi ngày khi làm)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>☐ <strong>Ngày deploy SEO</strong> — ghi ngày: ___/___/2026</li><li>☐ <strong>+14 ngày</strong> — test redirect, view-source robots FAQ/category, URL Inspection</li><li>☐ <strong>+28 ngày</strong> — GSC export #1 → <code>doc/seo/</code> · so CTR home / workshops / courses</li><li>☐ <strong>+56 ngày</strong> — GSC export #2 · quyết định A/B title homepage</li><li>☐ <strong>+90 ngày</strong> — GSC export #3 · đánh giá +30–50% clicks vs baseline</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Điều kiện:</strong> Sprint 1–4 phải <strong>deploy theme</strong> + chạy migration tool trên server. Chỉ code trong repo mà chưa deploy → GSC <em>chưa</em> phản ánh thay đổi.</p>\n<!-- /wp:paragraph -->\n\n";

	// --- Sprint 1 ---
	$blocks .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Sprint 1 — Rank Math + kỹ thuật (làm trước)</h2>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.1 — Rank Math General Settings</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Vào <strong>Rank Math → Dashboard</strong> (<a href=\"" . esc_url( $rm ) . "\">WP Admin</a>) — chạy <strong>Setup Wizard</strong> nếu chưa xong.</li><li><strong>Settings → General → Links</strong>: bật <em>Remove Category Base</em> (tùy chọn, gọn URL blog).</li><li><strong>Settings → General → Breadcrumbs</strong>: bật breadcrumbs (theme hiển thị nếu Rank Math hook có).</li><li><strong>Settings → General → robots.txt</strong>: kiểm tra không chặn <code>/wp-content/uploads/</code>.</li><li>Đảm bảo site URL là <code>" . esc_html( $site ) . "</code> (Settings → General).</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.2 — Titles &amp; Meta (template)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Mở <a href=\"" . esc_url( $rm_titles ) . "\">Rank Math → Titles &amp; Meta</a> và đặt template mặc định:</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Homepage</strong> — Title: <code>%sitename% %sep% %sitedesc%</code> hoặc để theme seed (bước 1.4). Description: mô tả studio 1–2 câu, có <em>Art Tutor Hanoi</em>, <em>Tay Ho</em>, <em>English art classes</em>.</li><li><strong>Pages</strong> — Title: <code>%title% %sep% %sitename%</code>. Description: <code>%excerpt%</code> hoặc custom trong từng page.</li><li><strong>Posts (blog)</strong> — Title: <code>%title% %sep% %sitename%</code>. Description: <code>%excerpt%</code>.</li><li><strong>Media / Author archives</strong> — đặt <strong>noindex</strong> (tránh duplicate thin content).</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.3 — Sitemap XML (fix lỗi 500 nếu có)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Mở <a href=\"" . esc_url( $rm_sitemap ) . "\">Rank Math → Sitemap Settings</a>.</li><li>Bật <strong>Sitemap</strong>. URL index: <code>" . esc_html( home_url( '/sitemap_index.xml' ) ) . "</code></li><li><strong>Pages</strong>: Include. <strong>Posts</strong>: Include.</li><li><strong>Exclude</strong> (noindex): slug <code>thank-you</code>, <code>seo</code> (trang này).</li><li>Nếu <code>sitemap_index.xml</code> trả <strong>500</strong>:<ol><li>Tắt plugin khác tạm thời (MEC, Modula…) → test lại sitemap.</li><li>Rank Math → Status &amp; Tools → <strong>Flush SEO cache</strong>.</li><li>Settings → Permalinks → <strong>Save</strong> (flush rewrite).</li><li>Xem error log server (PHP fatal trong sitemap).</li></ol></li><li>Sau khi OK: GSC → Sitemaps → submit <code>sitemap_index.xml</code>.</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.4 — Seed meta title/description (theme)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Theme có sẵn title/description cho trang commercial (dựa trên GSC). Chạy một lần:</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Tools → ATH Content Migration</strong> → <a href=\"" . esc_url( $migration ) . "\">mở trang</a></li><li>Bấm <strong>Seed Rank Math meta</strong> (chỉ ghi trang chưa có custom title).</li><li>Hoặc bấm <strong>Overwrite Rank Math meta</strong> để ghi lại từ theme.</li><li>Kiểm tra: mở từng page trong editor → sidebar <strong>Rank Math</strong> → Title / Description.</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Meta mặc định (theme seed)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( ath_rank_math_page_defaults() as $slug => $meta ) {
		$blocks .= '<li><code>' . esc_html( $slug ) . '</code> — <strong>' . esc_html( $meta['title'] ) . '</strong><br>' . esc_html( $meta['description'] ) . '</li>';
	}
	$home = ath_rank_math_home_defaults();
	$blocks .= '<li><code>homepage</code> — <strong>' . esc_html( $home['title'] ) . '</strong><br>' . esc_html( $home['description'] ) . '</li>';
	$blocks .= "</ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.5 — Local SEO + Schema</h3>\n<!-- /wp:heading -->\n\n";
	$facts = ath_local_business_facts();
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Mở <a href=\"" . esc_url( $rm_local ) . "\">Rank Math → Local SEO</a>.</li><li><strong>Organization / Local Business</strong>:<ul><li>Name: <code>" . esc_html( $facts['name'] ) . "</code></li><li>URL: <code>" . esc_html( $facts['url'] ) . "</code></li><li>Email: <code>" . esc_html( $facts['email'] ) . "</code></li><li>Phone: <code>" . esc_html( $facts['telephone'] ) . "</code></li><li>Address: <code>" . esc_html( $facts['address'] ) . "</code></li></ul></li><li>Theme bổ sung <code>LocalBusiness</code> JSON-LD trên homepage (filter <code>rank_math/json_ld</code>).</li><li>Theme bổ sung <code>Course</code> / <code>Event</code> / <code>ItemList</code> trên hub + detail (<code>inc/seo-schema.php</code>): <code>/courses/</code>, <code>/workshops/</code>, <code>/kids-courses/</code>, từng slug con.</li><li>Đồng bộ với <strong>Google Business Profile</strong> (cùng NAP, giờ mở cửa, ảnh, link book).</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.6 — Redirect 301 (theme + Rank Math)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Theme tự redirect slug cũ → URL mới (<code>inc/legacy-redirects.php</code>). Không chain redirect.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( ath_legacy_redirect_paths() as $from => $to ) {
		$blocks .= '<li><code>/' . esc_html( $from ) . '/</code> → <a href="' . esc_url( $to ) . '">' . esc_html( $to ) . '</a></li>';
	}
	$blocks .= "<li>Redirect thêm (nếu cần): <a href=\"" . esc_url( $rm_redir ) . "\">Rank Math → Redirections</a></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.7 — Noindex trang utility</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Theme noindex: <code>thank-you</code>, <code>seo</code> (filter <code>rank_math/frontend/robots</code>).</li><li>Rank Math per-page: đặt <strong>noindex</strong> cho thank-you nếu sidebar chưa đồng bộ.</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.8 — Google Search Console</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><a href=\"" . esc_url( $gsc ) . "\">Search Console</a> → property <code>arttutorhanoi.com</code></li><li>Sitemaps: submit <code>sitemap_index.xml</code></li><li>Pages: kiểm tra URL có impression cao + CTR thấp (home, workshops, art-classes-in-hanoi) → Request indexing sau khi sửa meta</li><li>Theo dõi: Coverage, Core Web Vitals, Mobile usability</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 1.9 — Dọn plugin &amp; Code Snippets</h3>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Mục tiêu:</strong> giảm plugin thừa (đặc biệt MEC/Modula gây lỗi sitemap 500), tăng tốc admin + front-end. <strong>Luôn làm trên staging trước</strong>; mỗi phase tắt <em>một nhóm</em>, test xong mới phase tiếp. Đối chiếu danh sách thực tế: <a href=\"" . esc_url( $plugins ) . "\">Plugins</a> · <a href=\"" . esc_url( $snippets ) . "\">Snippets</a> (nếu cài Code Snippets).</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Giữ — không deactivate</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><strong>Fluent Forms</strong> — <code>/book/</code>, certificate, thank-you / VietQR</li><li><strong>Rank Math SEO</strong> — meta, sitemap, schema (theme hook)</li><li><strong>TablePress</strong> — lịch tuần (<code>[table id=2 /]</code> trên <a href=\"" . esc_url( ath_page_url( 'calendar' ) ) . "\">/weekly-calendar/</a>)</li><li><strong>FluentSMTP</strong> (hoặc SMTP tương đương) — email booking / invoice</li><li><strong>Masu</strong> (<code>masu-wpcom</code>) — parent theme</li><li><strong>Custom:</strong> GalleryFeedBack (<a href=\"" . esc_url( ath_page_url( 'art-feedback' ) ) . "\">/free-art-feedback/</a>), gsheet-image-gallery + artist-country-summary (<a href=\"" . esc_url( ath_page_url( 'exhibition' ) ) . "\">exhibition</a>), ARTH Send Invoice (<code>/quotation-table/</code> admin)</li><li><strong>Code Snippets</strong> (hoặc chuyển code vào theme trước khi gỡ plugin): <code>[art_calendar]</code>, <code>[generate_trial_discount]</code>, <code>[generate_more_discounts]</code></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Phase A — Tắt an toàn (legacy, theme đã thay)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>☐ Backup site + export danh sách plugin/snippet đang active</li><li>☐ Deactivate <strong>BookingPress</strong> — booking → Fluent Forms</li><li>☐ Deactivate <strong>Yoast SEO</strong> — đã dùng Rank Math</li><li>☐ Deactivate <strong>latest-by-comment</strong> — theme <code>ath_learner_artwork_posts()</code> sort theo comment</li><li>☐ Deactivate <strong>artist-countries-shortcode</strong> — trùng artist-country-summary (<code>[artist_summary]</code>)</li><li>☐ Deactivate <strong>galleryFeedBackPrivate</strong> — không thấy shortcode trên trang</li><li>☐ Deactivate <strong>portfolio-form-fill</strong> (nếu còn trên server)</li><li>☐ Deactivate <strong>TrustIndex</strong> — widget review cũ; theme có testimonials riêng</li><li>☐ Trong Snippets: gỡ <code>[ath_cta_cards]</code> và snippet homepage HTML cũ (đã thay <code>[ath_home_*]</code>)</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Phase B — Tắt sau khi test nội dung cũ</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Các plugin này có thể còn embed trong trang chưa migrate. Tắt từng cái, test URL bên dưới, bật lại nếu layout hỏng.</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>☐ Deactivate <strong>Modern Events Calendar (MEC)</strong><ol><li>Mở <code>sitemap_index.xml</code> — phải <strong>200</strong>, không 500</li><li>Test: <a href=\"" . esc_url( ath_page_url( 'calendar' ) ) . "\">calendar</a>, <code>/art-tutor-for-kids-eng/</code>, <code>/art-tutor-fine-art-courses-hanoi/</code></li></ol></li><li>☐ Deactivate <strong>Modula</strong><ol><li>Test homepage, about, các trang gallery cũ</li><li>Gallery mới: Cloudinary + <code>[ath_home_gallery]</code></li></ol></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Phase C — Chỉ tắt nếu workflow comment student đã dừng</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>☐ Xác nhận giáo viên <strong>không còn</strong> feedback qua WP comment trên bài Learner's Artworks</li><li>☐ Nếu đã dừng → deactivate <strong>google-photo-comment-fixer-alt-final</strong></li><li>☐ Nếu đã dừng → deactivate <strong>comment-email-notifier-acfV3</strong> (post meta <code>comment_notify_email</code>)</li><li>☐ Nếu vẫn dùng comment → <strong>giữ cả hai</strong></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Phase D — Snippet: chuyển vào theme rồi gỡ Code Snippets (tùy chọn)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>☐ Copy nội dung snippet <code>[art_calendar]</code> → <code>inc/</code> hoặc mu-plugin</li><li>☐ Copy snippet <code>[generate_trial_discount]</code> + <code>[generate_more_discounts]</code> (trang <code>/discount-code/</code> admin)</li><li>☐ Test calendar (admin thấy panel), discount-code, quotation invoice</li><li>☐ Deactivate plugin <strong>Code Snippets</strong> khi mọi shortcode đã có nguồn thay thế</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">URL test sau mỗi phase (bắt buộc)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><code>sitemap_index.xml</code> — HTTP 200</li><li><a href=\"" . esc_url( home_url( '/' ) ) . "\">Homepage</a> — hero, gallery, intro, book links</li><li><a href=\"" . esc_url( ath_page_url( 'book' ) ) . "\">/book/</a> — tab Adult + Kids, Fluent Form submit</li><li><a href=\"" . esc_url( ath_page_url( 'calendar' ) ) . "\">/weekly-calendar/</a> — bảng TablePress</li><li><a href=\"" . esc_url( ath_page_url( 'students-artworks' ) ) . "\">/students-artworks/</a> — grid sort theo comment</li><li><a href=\"" . esc_url( ath_page_url( 'art-feedback' ) ) . "\">/free-art-feedback/</a> — gallery Google Sheet</li><li><a href=\"" . esc_url( ath_page_url( 'exhibition' ) ) . "\">Exhibition</a> — <code>[sheet_gallery]</code> + <code>[artist_summary]</code></li><li><code>/discount-code/</code>, <code>/quotation-table/</code> — admin only (More links)</li><li>Thank-you sau form book offline bank — VietQR hiển thị</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Production — sau khi staging OK</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>☐ Lặp Phase A → B → C trên production (giờ traffic thấp)</li><li>☐ Rank Math → Flush SEO cache</li><li>☐ Settings → Permalinks → Save</li><li>☐ GSC → resubmit sitemap</li><li>☐ Xóa hẳn plugin (Delete) chỉ sau 1–2 tuần không lỗi</li><li>☐ Ghi ngày hoàn thành: ___/___/2026</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Sprint 1 — Checklist</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>✅ Rank Math wizard + templates</li><li>✅ Sitemap OK (không 500)</li><li>✅ Seed Rank Math meta (migration tool)</li><li>✅ Local SEO + GBP đồng bộ</li><li>✅ Redirect cũ hoạt động</li><li>✅ GSC sitemap submitted</li><li>✅ Request indexing (commercial URLs)</li><li>☐ Plugin cleanup Phase A (staging)</li><li>☐ Plugin cleanup Phase B — MEC + Modula (staging)</li><li>☐ Plugin cleanup Phase C — comment plugins (nếu workflow dừng)</li><li>☐ Plugin cleanup production + ghi ngày</li></ul>\n<!-- /wp:list -->\n\n";

	// --- Sprint 2 ---
	$blocks .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Sprint 2 — On-page commercial</h2>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 2.1 — Chạy tool theme (một lần sau deploy)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><a href=\"" . esc_url( $migration ) . "\">Tools → ATH Content Migration</a></li><li>Bấm <strong>Apply Sprint 2 on-page SEO</strong> — thêm <code>[ath_home_intro]</code> trên homepage, cập nhật <code>/workshops/</code> + <code>/courses/</code>, seed meta detail pages</li><li>Tick <em>Ghi đè nội dung hub đã có</em> nếu hub đã migrate trước đó</li><li>Bấm <strong>Update SEO playbook now</strong> để sync trang này</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 2.2 — Homepage</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Theme thêm <code>[ath_home_intro]</code> — H1 <strong>Art Tutor Hanoi</strong>, subtitle, đoạn intro + links (classes, workshops, book, pricing)</li><li>Rank Math homepage: title đã seed — <strong>không</strong> trùng H1 (title cho SERP, H1 cho on-page)</li><li>Kiểm tra mobile + desktop: H1 hiển thị dưới video hero</li><li>Cards / programs vẫn link tới commercial pages</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 2.3 — /workshops/ hub</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>H1 shell: <strong>Art Workshops in Hanoi</strong> (<code>ath_page_seo_h1</code>)</li><li>Intro có keyword <em>art workshops hanoi</em>, link tới courses + trial</li><li>Footer links: pricing, calendar, art classes + Explore nav</li><li>GSC: ~22k impressions, CTR ~0.07% — theo dõi sau 2–4 tuần</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 2.4 — /courses/ pillar</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>H1: <strong>Art Classes in Hanoi</strong></li><li>Body: intro SEO + <code>[ath_courses_hub]</code> (pathways, benefits, CTA)</li><li>Internal links tới workshops, kids, pricing, book</li><li>GSC query <em>art classes in hanoi</em> ~28k imp — trang này là canonical pillar</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 2.5 — Detail pages (workshop / course)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Tool <strong>Seed detail page meta only</strong> hoặc Sprint 2 batch — title/description cho <code>/workshops/{slug}/</code> và <code>/courses/{slug}/</code></li><li>Mỗi page: Rank Math sidebar → kiểm tra Focus Keyword + preview SERP</li><li>Theme thêm <em>Explore</em> cross-links cuối workshop/course detail</li><li>Ảnh trong content: alt mô tả (vd. <em>Pencil drawing class at Art Tutor Hanoi</em>)</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 2.6 — GSC sau Sprint 2</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Request indexing: homepage, <code>/workshops/</code>, <code>/courses/</code></li><li>URL Inspection: confirm H1 + meta khác nhau</li><li>So sánh CTR desktop vs mobile sau 14 ngày</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Sprint 2 — Checklist</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>✅ Apply Sprint 2 on-page SEO (migration tool)</li><li>✅ Homepage H1 + intro links</li><li>✅ /workshops/ intro + cross-links</li><li>✅ /courses/ pillar + pathways</li><li>✅ Detail pages meta seeded</li><li>✅ GSC re-index commercial URLs</li></ul>\n<!-- /wp:list -->\n\n";

	$h1_map = ath_page_seo_h1_map();
	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">H1 map (theme)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $h1_map as $slug => $h1 ) {
		$blocks .= '<li><code>' . esc_html( $slug ) . '</code> → ' . esc_html( $h1 ) . '</li>';
	}
	$blocks .= "</ul>\n<!-- /wp:list -->\n\n";

	// --- Sprint 3 ---
	$blocks .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Sprint 3 — Content cleanup &amp; FAQ schema</h2>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 3.1 — Chạy tool theme</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><a href=\"" . esc_url( $migration ) . "\">Tools → ATH Content Migration</a> → <strong>Apply Sprint 3 content SEO</strong></li><li>Tick <em>Ghi đè FAQ</em> nếu đã chỉnh FAQ trong editor</li><li>Cập nhật <strong>Update SEO playbook</strong></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 3.2 — 301 blog trùng commercial</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Theme redirect (<code>data/seo-blog-redirects.php</code> + <code>legacy-redirects.php</code>) — bài tutorial giữ nguyên, chỉ gộp bài <em>trùng landing commercial</em>:</p>\n<!-- /wp:paragraph -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( ath_seo_blog_redirect_urls() as $slug => $url ) {
		$blocks .= '<li><code>/' . esc_html( $slug ) . '/</code> → <a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a></li>';
	}
	$blocks .= "<li>+ các slug commercial đã có từ Sprint 1 (art-classes-in-hanoi, best-art-classes…, nude-drawing…)</li></ul>\n<!-- /wp:list -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p><strong>Thủ công (WP Admin):</strong> bài blog còn trùng intent — đổi title bỏ năm cũ (2025→2026), thêm link nội bộ tới <code>/courses/</code> hoặc <code>/workshops/</code>, hoặc thêm slug vào <code>data/seo-blog-redirects.php</code>.</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 3.3 — FAQ + FAQPage schema</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Trang <a href=\"" . esc_url( ath_page_url( 'faq' ) ) . "\">/faq/</a> — migrate từ <code>data/faq-page.php</code> (internal links tới book, courses, workshops)</li><li>Theme inject <code>FAQPage</code> JSON-LD trên /faq/ (filter <code>rank_math/json_ld</code>)</li><li>Rank Math → <a href=\"" . esc_url( $rm_titles ) . "\">Titles &amp; Meta</a> → FAQ: bật schema nếu trùng — theme schema là nguồn chính</li><li>GSC → URL Inspection <code>/faq/</code> → rich results test (FAQ)</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 3.4 — Blog archives noindex</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Theme: <code>noindex, follow</code> cho category, tag, author, date archives (tránh thin duplicate)</li><li>Rank Math → Titles &amp; Meta → Categories/Tags: confirm <strong>noindex</strong> (đồng bộ với theme)</li><li>Bài post đơn lẻ vẫn <strong>index</strong></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 3.5 — Blog posts: internal links</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Theme tự thêm block <em>Explore</em> cuối mỗi blog post (không áp learner artwork posts)</li><li>Tutorial posts (posterizing, perspective…): giữ index, thêm 1–2 link tay trong editor về courses/workshops</li><li>Ảnh alt: mô tả nội dung, không spam keyword</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 3.6 — Rank Math (không bắt buộc GSC OAuth)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Verify GSC bằng file HTML = đủ cho GSC</li><li>Kết nối Rank Math Analytics = tiện, không bắt buộc</li><li>Instant Indexing API: chỉ khi cần auto ping Google khi publish</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 3.7 — hreflang (tùy chọn)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:paragraph -->\n<p>Site có bài JP/KR riêng. Chỉ cần hreflang nếu giữ nhiều URL ngôn ngữ song song. Đơn giản hơn: 1 URL EN + section ngôn ngữ trong bài.</p>\n<!-- /wp:paragraph -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Sprint 3 — Checklist</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>✅ Apply Sprint 3 content SEO</li><li>✅ Blog redirect map</li><li>✅ FAQ + FAQPage schema</li><li>✅ Archive noindex</li><li>✅ Blog cross-links</li><li>☐ Review blog titles 2025→2026 (ongoing)</li></ul>\n<!-- /wp:list -->\n\n";

	// --- Sprint 4 ---
	$blocks .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Sprint 4 — Monitor &amp; optimize</h2>\n<!-- /wp:heading -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 4.1 — Chạy tool (perf)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li><a href=\"" . esc_url( $migration ) . "\">Tools → ATH Content Migration</a> → <strong>Apply Sprint 4 monitor</strong> (defer JS)</li><li><strong>Update SEO playbook</strong></li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 4.2 — GSC routine (mỗi 2 tuần)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Export Pages + Queries → <code>doc/seo/</code> (ghi ngày trong tên file)</li><li><a href=\"" . esc_url( $gsc ) . "\">Search Console</a> → Desktop vs Mobile CTR</li><li>So sánh watchlist baseline bên dưới</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":4} -->\n<h4 class=\"wp-block-heading\">Watchlist (baseline GSC)</h4>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( ath_seo_gsc_watchlist() as $row ) {
		$path   = (string) ( $row['path'] ?? '' );
		$label  = (string) ( $row['label'] ?? $path );
		$imp    = (int) ( $row['baseline_impressions'] ?? 0 );
		$ctr    = (float) ( $row['baseline_ctr'] ?? 0 );
		$pos    = (float) ( $row['baseline_position'] ?? 0 );
		$target = (float) ( $row['target_ctr'] ?? 0 );
		$note   = (string) ( $row['note'] ?? '' );
		$url    = home_url( $path );
		$blocks .= '<li><a href="' . esc_url( $url ) . '"><code>' . esc_html( $path ) . '</code></a> — ' . esc_html( $label );
		if ( $imp > 0 ) {
			$blocks .= ' · ~' . number_format_i18n( $imp ) . ' imp · CTR ' . esc_html( (string) $ctr ) . '% · pos ' . esc_html( (string) $pos );
		}
		if ( $target > 0 ) {
			$blocks .= ' · target CTR ≥ ' . esc_html( (string) $target ) . '%';
		}
		if ( $note !== '' ) {
			$blocks .= ' — ' . esc_html( $note );
		}
		$blocks .= '</li>';
	}
	$blocks .= "</ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 4.3 — A/B homepage title (Rank Math)</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( ath_seo_home_title_variants() as $variant_label => $title ) {
		$blocks .= '<li><strong>' . esc_html( $variant_label ) . '</strong>: <code>' . esc_html( $title ) . '</code></li>';
	}
	$blocks .= "</ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 4.4 — Core Web Vitals + perf</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>GSC → Core Web Vitals</li><li>PageSpeed Insights: homepage + /courses/ (mobile)</li><li>Theme: defer <code>main.js</code>, preconnect Cloudinary</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Bước 4.5 — Redirect maintenance</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>Thêm slug trong <code>data/seo-blog-redirects.php</code> khi GSC báo duplicate</li><li>Không redirect tutorial posts — chỉ internal links</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">Sprint 4 — Checklist</h3>\n<!-- /wp:heading -->\n\n";
	$blocks .= "<!-- wp:list -->\n<ul class=\"wp-block-list\"><li>☐ Apply Sprint 4 monitor</li><li>☐ GSC export → doc/seo</li><li>☐ So sánh watchlist</li><li>☐ PageSpeed mobile</li><li>☐ A/B title nếu home CTR &lt; 0.5%</li><li>☐ Lặp sau 14 ngày</li></ul>\n<!-- /wp:list -->\n\n";

	$blocks .= "<!-- wp:paragraph -->\n<p><em>Playbook theme v" . esc_html( ath_seo_page_sync_version() ) . " — GSC baseline + Sprint 1–4 + plugin cleanup (Bước 1.9). Sync URL: ";
	if ( $seo_url ) {
		$blocks .= '<a href="' . esc_url( $seo_url ) . '">' . esc_html( $seo_url ) . '</a>';
	} else {
		$blocks .= '/seo/';
	}
	$blocks .= ".</em></p>\n<!-- /wp:paragraph -->\n\n";

	return $blocks;
}

/**
 * Hash of SEO page body.
 */
function ath_seo_page_content_hash() {
	return md5( ath_seo_page_block_content() );
}

/**
 * Create or update /seo/ when content hash differs.
 *
 * @param bool $force Overwrite even if hash matches.
 * @return array{id: int, updated: bool}
 */
function ath_ensure_seo_page( $force = false ) {
	$slug    = 'seo';
	$title   = 'SEO Playbook';
	$version = ath_seo_page_sync_version();
	$hash    = ath_seo_page_content_hash();
	$content = ath_seo_page_block_content();
	$result  = array(
		'id'      => 0,
		'updated' => false,
	);

	if ( ! $force && get_option( 'ath_seo_page_version', '' ) !== $version ) {
		$force = true;
	}

	$existing = get_page_by_path( $slug );

	if ( $existing ) {
		$stored_hash  = (string) get_post_meta( $existing->ID, '_ath_seo_content_hash', true );
		$needs_update = $force || $stored_hash !== $hash || $existing->post_status !== 'publish';

		$result['id'] = (int) $existing->ID;

		if ( ! $needs_update ) {
			return $result;
		}

		wp_update_post(
			array(
				'ID'           => $existing->ID,
				'post_status'  => 'publish',
				'post_content' => $content,
			)
		);
		update_post_meta( $existing->ID, '_ath_seo_content_hash', $hash );
		delete_post_meta( $existing->ID, '_wp_page_template' );
		update_option( 'ath_seo_page_version', $version, false );

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

	update_post_meta( $page_id, '_ath_seo_content_hash', $hash );
	update_option( 'ath_seo_page_version', $version, false );
	$result['id']      = (int) $page_id;
	$result['updated'] = true;

	return $result;
}

/**
 * Run SEO page sync for admins (admin or front-end /seo/ view).
 */
function ath_run_seo_page_sync() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return array(
			'id'      => 0,
			'updated' => false,
		);
	}

	$result = ath_ensure_seo_page( false );

	if ( $result['updated'] ) {
		set_transient( 'ath_seo_page_sync_notice', 1, 60 );
	}

	return $result;
}

/**
 * Auto-sync SEO page when an admin visits WP Admin.
 */
function ath_maybe_sync_seo_page() {
	if ( ! is_admin() ) {
		return;
	}

	ath_run_seo_page_sync();
}

/**
 * Auto-sync when an admin opens /seo/ on the front end (DB page can lag behind theme).
 */
function ath_maybe_sync_seo_page_on_view() {
	if ( ! is_page( 'seo' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$result = ath_run_seo_page_sync();

	if ( $result['updated'] && ! isset( $_GET['ath_seo_synced'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( add_query_arg( 'ath_seo_synced', '1', get_permalink() ) );
		exit;
	}
}

/**
 * Permalink for the SEO page (empty if missing).
 */
function ath_seo_page_url() {
	$page = get_page_by_path( 'seo' );
	if ( ! $page || $page->post_status !== 'publish' ) {
		return '';
	}

	return get_permalink( $page );
}

/**
 * Admin notice after SEO page sync.
 */
function ath_seo_page_sync_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_transient( 'ath_seo_page_sync_notice' ) ) {
		return;
	}

	delete_transient( 'ath_seo_page_sync_notice' );
	$url = ath_seo_page_url();
	?>
	<div class="notice notice-success is-dismissible">
		<p><strong>SEO playbook updated</strong> —
			<?php if ( $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>">View /seo/</a>
			<?php else : ?>
				/seo/
			<?php endif; ?>
		</p>
	</div>
	<?php
}

add_action( 'after_switch_theme', 'ath_ensure_seo_page' );
add_action( 'admin_init', 'ath_maybe_sync_seo_page', 100 );
add_action( 'template_redirect', 'ath_maybe_sync_seo_page_on_view', 1 );
add_action( 'admin_notices', 'ath_seo_page_sync_admin_notice' );

add_action(
	'init',
	function () {
		if ( get_page_by_path( 'seo' ) ) {
			return;
		}
		ath_ensure_seo_page( true );
	},
	6
);
