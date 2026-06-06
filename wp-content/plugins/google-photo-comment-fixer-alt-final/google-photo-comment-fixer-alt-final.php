<?php
/**
 * Plugin Name: Google Photo Comment Fixer Alt Final v10.5 (Cloudinary w_300 + Responsive Lightbox)
 * Description: Dán link ảnh vào comment -> tự chuyển thành <img> + alt text. Cloudinary tự chèn w_300 cho ảnh hiển thị. Click mở lightbox, ảnh lớn tự fit theo màn hình (mobile/desktop) với Cloudinary c_limit,w_,q_auto,f_auto.
 * Version: 10.5
 * Author: Capcuu
 */

if (!defined('ABSPATH')) exit;

// Ghi tạm alt_text từ form vào biến toàn cục sớm hơn
add_action('pre_comment_on_post', function () {
    if (!empty($_POST['alt_text'])) {
        $GLOBALS['gpcf_user_alt'] = sanitize_text_field($_POST['alt_text']);
    }
});

// Xử lý khi bình luận được gửi
add_action('comment_post', 'gpcf_alt_final_handle', 10, 2);

function gpcf_alt_final_handle($comment_ID, $comment_approved) {
    $comment = get_comment($comment_ID);
    if (!$comment) return;

    $content = $comment->comment_content;

    // Lấy alt text đã lưu sớm
    $alt = isset($GLOBALS['gpcf_user_alt']) ? $GLOBALS['gpcf_user_alt'] : '';
    $alt_attr = $alt ? ' alt="' . esc_attr($alt) . '"' : ' alt=""';

    // Regex nhận mọi link ảnh kể cả có query string
    $pattern = '/https?:\/\/[^\s"]+\.(jpg|jpeg|png|gif|webp)(\?[^\s"]*)?/i';

    $new_content = preg_replace_callback($pattern, function ($matches) use ($alt_attr) {
        $src_raw = $matches[0];

        // thumb: ảnh nhỏ hiển thị trong comment (Cloudinary -> w_300)
        $thumb_raw = gpcf_maybe_add_cloudinary_w300($src_raw);

        // data-src giữ link gốc (để JS tự tạo link lightbox theo màn hình)
        $thumb = esc_url($thumb_raw);
        $orig  = esc_url($src_raw);

        return '<figure class="wp-block-image gpcf-figure">' .
               '<a href="' . $orig . '" class="gpcf-lightbox-link" data-src="' . $orig . '">' .
               '<img src="' . $thumb . '"' . $alt_attr . ' />' .
               '</a>' .
               '</figure>';
    }, $content);

    if ($new_content !== $content) {
        wp_update_comment([
            'comment_ID'      => $comment_ID,
            'comment_content' => $new_content,
        ]);
    }
}

/**
 * Nếu URL là Cloudinary dạng:
 *   https://res.cloudinary.com/<cloud>/image/upload/v123/filename.jpg
 * thì đổi thành:
 *   https://res.cloudinary.com/<cloud>/image/upload/w_300/v123/filename.jpg
 *
 * Không đụng vào nếu:
 * - không phải Cloudinary
 * - đã có transformation (w_, h_, c_, q_, f_, g_, ar_, dpr_...) ngay sau /upload/
 */
function gpcf_maybe_add_cloudinary_w300($url) {
    // Chỉ xử Cloudinary res.cloudinary.com
    if (stripos($url, 'https://res.cloudinary.com/') !== 0 && stripos($url, 'http://res.cloudinary.com/') !== 0) {
        return $url;
    }

    // Tách query string ra để xử path cho sạch
    $parts = explode('?', $url, 2);
    $base  = $parts[0];
    $query = isset($parts[1]) ? ('?' . $parts[1]) : '';

    $needle = '/image/upload/';
    $pos = stripos($base, $needle);
    if ($pos === false) return $url;

    $after = substr($base, $pos + strlen($needle)); // phần sau /image/upload/

    // Nếu ngay sau upload đã có transformations thì thôi (tránh double)
    if (preg_match('/^(?:w_|h_|c_|q_|f_|g_|ar_|dpr_)/i', $after)) {
        return $base . $query;
    }

    // Chèn w_300/
    $new_base = substr($base, 0, $pos + strlen($needle)) . 'w_300/' . $after;
    return $new_base . $query;
}

/**
 * Tạo URL ảnh lightbox cho Cloudinary theo max width mong muốn:
 * chèn: c_limit,w_{max_w},q_auto,f_auto/
 *
 * Chỉ chèn nếu:
 * - là Cloudinary
 * - ngay sau /image/upload/ CHƯA có transformation
 */
function gpcf_cloudinary_lightbox_url($url, $max_w) {
    if (stripos($url, 'https://res.cloudinary.com/') !== 0 && stripos($url, 'http://res.cloudinary.com/') !== 0) {
        return $url; // không phải cloudinary -> dùng nguyên link
    }

    $parts = explode('?', $url, 2);
    $base  = $parts[0];
    $query = isset($parts[1]) ? ('?' . $parts[1]) : '';

    $needle = '/image/upload/';
    $pos = stripos($base, $needle);
    if ($pos === false) return $url;

    $after = substr($base, $pos + strlen($needle));

    // nếu đã có transformation ngay sau upload thì không chèn thêm
    if (preg_match('/^(?:w_|h_|c_|q_|f_|g_|ar_|dpr_)/i', $after)) {
        return $base . $query;
    }

    $tx = 'c_limit,w_' . intval($max_w) . ',q_auto,f_auto/';
    $new_base = substr($base, 0, $pos + strlen($needle)) . $tx . $after;

    return $new_base . $query;
}

/**
 * Thêm trường nhập alt text vào form bình luận
 */
add_action('comment_form_logged_in_after', 'gpcf_alt_final_add_field');
add_action('comment_form_after_fields', 'gpcf_alt_final_add_field');

function gpcf_alt_final_add_field() {
    echo '<p class="comment-form-alt-text">
        <label for="alt_text">Alt text for images</label><br />
        <input id="alt_text" name="alt_text" type="text" style="width: 100%;" />
    </p>';
}

/**
 * Inject Lightbox HTML + CSS + JS
 */
add_action('wp_footer', function () {

    // cấu hình kích thước theo thiết bị
    $mobile_w  = 1200;
    $desktop_w = 1600;

    // expose 2 size này sang JS
    ?>
    <style>
    #gpcf-lightbox-overlay{
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 18px;
    }
    #gpcf-lightbox-overlay img{
        max-width: min(95vw, 1200px);
        max-height: 90vh;
        object-fit: contain;
        border-radius: 6px;
    }
    #gpcf-lightbox-close{
        position: absolute;
        top: 14px;
        right: 16px;
        font-size: 28px;
        line-height: 1;
        color: #fff;
        cursor: pointer;
        user-select: none;
        padding: 8px 10px;
    }
    </style>

    <div id="gpcf-lightbox-overlay" aria-hidden="true">
        <span id="gpcf-lightbox-close" aria-label="Close">✕</span>
        <img id="gpcf-lightbox-img" src="" alt="">
    </div>

    <script>
    (function(){
        const overlay = document.getElementById('gpcf-lightbox-overlay');
        const overlayImg = document.getElementById('gpcf-lightbox-img');
        const closeBtn = document.getElementById('gpcf-lightbox-close');

        const MOBILE_W = <?php echo (int)$mobile_w; ?>;
        const DESKTOP_W = <?php echo (int)$desktop_w; ?>;

        function isCloudinary(url){
            return /^https?:\/\/res\.cloudinary\.com\//i.test(url);
        }

        function hasTransformAfterUpload(url){
            // kiểm tra ngay sau /image/upload/ có w_/h_/c_/... không
            const m = url.match(/\/image\/upload\/([^?]+)/i);
            if(!m) return false;
            const after = m[1] || '';
            return /^(w_|h_|c_|q_|f_|g_|ar_|dpr_)/i.test(after);
        }

        function buildCloudinaryLightboxUrl(url, maxW){
            // nếu đã có transform thì dùng nguyên url (tránh chồng)
            if(!isCloudinary(url) || hasTransformAfterUpload(url)) return url;

            const parts = url.split('?', 2);
            const base = parts[0];
            const query = parts.length > 1 ? '?' + parts[1] : '';

            const needle = '/image/upload/';
            const idx = base.toLowerCase().indexOf(needle);
            if(idx === -1) return url;

            const before = base.slice(0, idx + needle.length);
            const after = base.slice(idx + needle.length);

            const tx = 'c_limit,w_' + maxW + ',q_auto,f_auto/';
            return before + tx + after + query;
        }

        function getBestUrl(origUrl){
            const w = (window.innerWidth || document.documentElement.clientWidth || 1200);
            const maxW = (w <= 768) ? MOBILE_W : DESKTOP_W;
            return buildCloudinaryLightboxUrl(origUrl, maxW);
        }

        function openLightbox(url){
            overlayImg.src = url;
            overlay.style.display = 'flex';
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(){
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
            overlayImg.src = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e){
            const link = e.target.closest('.gpcf-lightbox-link');
            if(!link) return;

            e.preventDefault();
            const orig = link.getAttribute('data-src') || link.getAttribute('href');
            if(!orig) return;

            const best = getBestUrl(orig);
            openLightbox(best);
        });

        closeBtn.addEventListener('click', closeLightbox);
        overlay.addEventListener('click', function(e){
            if(e.target === overlay) closeLightbox();
        });

        // ESC to close
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape' && overlay.style.display === 'flex'){
                closeLightbox();
            }
        });
    })();
    </script>
    <?php
});
