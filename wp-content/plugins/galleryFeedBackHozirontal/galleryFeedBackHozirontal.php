<?php
/*
Plugin Name: GalleryFeedBack
Kế thừa gallery exhibition
Bản này chạy đổi link WP sang Cloudinary tương ứng, phải kèm plugin Cloudinary hỗ trợ
Có lightbox và phân trang!
Version: 2.0
Author: Hung Nguyen + ChatGPT
*/

if ( ! function_exists('convert_sheet_to_csv') ) {
  /**
   * Lấy dữ liệu CSV từ Google Sheet URL
   *
   * @param string $sheet_url URL full của Google Sheets (có thể chứa ?gid=…)
   * @return string CSV raw, hoặc chuỗi rỗng nếu lỗi
   */
  function convert_sheet_to_csv( $sheet_url ) {
    // 1) Parse ID của spreadsheet
    if ( ! preg_match( '/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $sheet_url, $m ) ) {
      return '';
    }
    $sheet_id = $m[1];

    // 2) Lấy gid (tab) nếu có, mặc định 0
    $gid = '0';
    if ( preg_match( '/[?&]gid=([0-9]+)/', $sheet_url, $g ) ) {
      $gid = $g[1];
    }

    // 3) Xây export URL
    $export_url = sprintf(
      'https://docs.google.com/spreadsheets/d/%s/export?format=csv&gid=%s',
      $sheet_id,
      $gid
    );

    // 4) Gọi HTTP API của WP để lấy CSV
    $resp = wp_remote_get( $export_url );
    if ( is_wp_error($resp) ) {
      return '';
    }
    return wp_remote_retrieve_body( $resp );
  }
}


add_shortcode('galleryFeedBack', 'galleryFeedBack_shortcode');

function galleryFeedBack_shortcode($atts) {
    // 1) Thiết lập shortcode và mặc định
    $atts = shortcode_atts([
        'sheet_url'              => '',
        'date_column'            => 'Date',
        'image_column'           => 'Image Upload',
        'columns'                => 3,
        'author_column'          => 'Name',
        'description_column'     => 'Description',
        'displayName_column'     => 'Display Your Name on Gallery?',
        'teacherComment_column'  => 'Teacher comment',
        'teacherImage_column'   => 'Teacher Image Upload',
        'start_row'              => 2,
        'items_per_page'         => 9,
    ], $atts, 'galleryFeedBack');

    if (!$atts['sheet_url']) {
        return '<p style="color:red;">Vui lòng cung cấp sheet_url.</p>';
    }

// 2) Lấy thẳng CSV từ Google Sheets
$csv = convert_sheet_to_csv($atts['sheet_url']);
if (empty($csv)) {
    return '<p style="color:red;">Lỗi khi đọc dữ liệu từ Google Sheets. Vui lòng kiểm tra lại sheet_url.</p>';
}
   
    // 3) Parse CSV thành mảng rows
    $rows = [];
    $stream = fopen('php://memory','r+');
    fwrite($stream, $csv);
    rewind($stream);
    while (($r = fgetcsv($stream)) !== false) {
        $rows[] = $r;
    }
    fclose($stream);




    // 4) Lấy header & tìm index các cột
    $headers = array_shift($rows);
    
    //*** Đọc ngược từ dưới, ưu tiên cái mới
    $rows = array_reverse($rows);
    
    $image_index        = array_search($atts['image_column'], $headers);
    $teacherComment_idx = array_search($atts['teacherComment_column'], $headers);
    $teacherImage_idx = array_search($atts['teacherImage_column'], $headers);
    $date_idx           = array_search($atts['date_column'], $headers);
    $author_idx         = array_search($atts['author_column'], $headers);
    $description_idx    = array_search($atts['description_column'], $headers);
    $displayName_idx    = array_search($atts['displayName_column'], $headers);

    if ($image_index === false) {
        return '<p style="color:red;">Không tìm thấy cột ảnh.</p>';
    }

    // 5) Phân trang đơn giản
    $page           = isset($_GET['gallery_page']) ? max(1, intval($_GET['gallery_page'])) : 1;
    $items_per_page = max(1, intval($atts['items_per_page']));

    // 6) Lọc rows hợp lệ (cột A, có ảnh, có comment)
    $filtered = [];
    foreach ($rows as $row) {
        
        if (!isset($row[0]) || trim($row[0]) === '') break;
        if (empty($row[$image_index]) || empty($row[$teacherComment_idx])) continue;
        $filtered[] = $row;
    }
    
    
    $total_items = count($filtered);
    $total_pages = max(1, ceil($total_items / $items_per_page));
    if ($page > $total_pages) $page = $total_pages;
    $offset     = ($page - 1) * $items_per_page;
    $paged_rows = array_slice($filtered, $offset, $items_per_page);

    // 7) Build gallery HTML, Luu lai item per page de khong bi loi nho cache
$html = '<div id="gallery-container" class="gsheet-gallery"'
      . ' data-items-per-page="' . $items_per_page . '"'
      . ' data-total-pages="'    . $total_pages    . '"'
      //Xếp ngang, thay vì xếp dọc       . ' style="position:relative; column-count:' . intval($atts['columns']) . '">';
      . ' style="display:grid; '
      . 'grid-template-columns:repeat(' . intval($atts['columns']) . ',1fr); '
      . 'gap:20px;">';
    
   /////////////////////
// loader overlay (ẩn sẵn)
//Hieu ung load cho gallery
$html .= '
<style>
  #gallery-loader {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255,255,255,0.75);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10;
  }
  /* simple CSS spinner */
  #gallery-loader .spinner {
    border: 4px solid #eee;
    border-top:   4px solid #333;
    border-radius: 50%;
    width: 30px; height: 30px;
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
</style>

<div id="gallery-loader"><div class="spinner"></div></div>
';
    ///////////////////// 
    
foreach ($paged_rows as $row) {
    $raw_url     = trim($row[$image_index]);
    $opt_url     = str_replace(
                      '/upload/',
                      '/upload/w_300,h_300,c_fill,q_auto,f_auto/', // Đảm bảo kích thước ảnh đều nhau.
                      $raw_url 
                   );
    $large_url = str_replace(
                    '/upload/',
                     '/upload/w_800,q_auto,f_auto/',
                     $raw_url 
                    );
    
    //Ảnh do teacher comment
    $raw_url2     = trim($row[$teacherImage_idx]);
    $large_url2 = str_replace(
                    '/upload/',
                     '/upload/w_800,q_auto,f_auto/',
                     $raw_url2
                    );
        // Lấy author + description
    $author      = isset($row[$author_idx])      ? esc_html(trim($row[$author_idx]))      : '';
    $date = isset($row[$date_idx]) ? esc_html(trim($row[$date_idx])) : '';
    $date  = substr($date, 0, 10);  // Do nạp lên nhiều thông tin, chỉ cắt lấy ngày tháng năm
//Kiểm tra có cho hiện không

    $author_value = isset($row[$author_idx]) ? $row[$author_idx] : '';
    $display_name_value = isset($row[$displayName_idx]) ? $row[$displayName_idx] : '';
    if ($display_name_value === 'No, keep me anonymous') {
    $author_value = 'Anonymous';
    }


    $html .= '<div style="break-inside: avoid; margin-bottom:20px; text-align:center;">'
          .  '<img src="' . esc_url($opt_url) . '" '
          .  'data-large="' . esc_url($large_url) . '" '
          .  'data-large2="' . esc_url($large_url2) . '" '
          //Nạp các thông tin 
          .  'data-date="' . esc_attr($date) . '" '
          .  'data-displayName="' . esc_attr(isset($row[$displayName_idx]) ? $row[$displayName_idx] : '') . '" '    
          .  'data-author="' . esc_attr($author_value) . '" '
          .  'data-comment="' . esc_attr(isset($row[$teacherComment_idx]) ? $row[$teacherComment_idx] : '') . '" '
          .  'data-description="' . esc_attr(isset($row[$description_idx]) ? $row[$description_idx] : '') . '" '
          .  'onclick="galleryFeedBack_openLightbox(this)" '
          .  'style="width:100%; height:auto; border-radius:8px; cursor:pointer;" />'

          // caption bên dưới ảnh
          .  '<div class="caption" style="margin-top:8px; font-size:14px; color:#333;">'
          .     '<div class="caption-author"><strong>' . $author_value . '</strong></div>'
          .     '<div class="caption-date">' . $date . '</div>'
          .  '</div>'
          .  '</div>';
}

    $html .= '</div>'; // đóng gallery-container
    


    // 8) Build pagination HTML
    $html .= '<div class="gallery-pagination" style="text-align:center; margin-top:20px; font-size:16px;">';
    if ($total_pages > 1) {
        
        //Cả 3 đều thêm items_per_page để làm fallback
        // Prev
        if ($page > 1) {
            $prev = $page - 1;
            $html .= '<a href="?gallery_page=' . $prev . '&items_per_page=' . $items_per_page . '" class="page-link" data-page="' . $prev . '" style="margin:0 8px;">Prev</a>';

        }
        // Page numbers
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                $html .= '<strong style="margin:0 5px;">' . $i . '</strong>';
            } else {
                $html .= '<a href="?gallery_page=' . $i . '&items_per_page=' . $items_per_page . '" class="page-link" data-page="' . $i . '" style="margin:0 5px;">' . $i . '</a>';

            }
        }
        // Next
        if ($page < $total_pages) {
            $next = $page + 1;
            $html .= '<a href="?gallery_page=' . $next . '&items_per_page=' . $items_per_page . '" class="page-link" data-page="' . $next . '" style="margin:0 8px;">Next</a>';

        }
    }
    $html .= '</div>';

    // 9) Lightbox overlay + script (giữ nguyên của bạn)
    $html .= '

<style>

  #lightbox-overlay img {
    max-width: 100vw;
    max-height: 100vh;
    border-radius: 8px;
  }
</style>

<div id="lightbox" style="width:100%; height:100%;">
  <div id="lightbox-overlay" onclick="galleryFeedBack_closeLightbox()" style="
         display: none;
         position: fixed;
         top: 0 !important;
         left: 0 !important;
         width: 100vw !important;
         height: 100vh !important;
         background: rgba(0,0,0,0.9) !important;
         justify-content: center;
         align-items: flex-start;   /* cho phép nội dung bắt đầu từ trên */
         overflow: auto;            /* bật cuộn */
         padding: 40px 80px;           /* thêm khoảng trên/dưới khi scroll */
         -webkit-overflow-scrolling: touch;    /* ① enable touch scrolling */
         overscroll-behavior: contain;         /* ② stop scroll chaining */
         box-sizing: border-box;
         z-index: 10000;
        ">
    <div style="display:flex; flex-direction:column; border-radius:0px; max-width:90vw;">
       <div style="flex:1;">
        <img id="lightbox-image" src="" style="max-width:100%; max-height:80vh; border-radius:0px;display:block; margin: 0 auto;" />
        <img id="lightbox-image2" src="" style="max-width:100%; max-height:80vh; border-radius:0px;display:block; margin: 0 auto;" />
      </div>
      
    <div style="color:white; width:100%; font-size:16px;">
        <div id="lightbox-author" style="text-align: center;"></div>
        <div id="lightbox-date" style="text-align: center;"></div>
        <div id="lightbox-description"></div>
        <div id="lightbox-comment"></div>
      </div>
      
    </div>

  </div>
</div>


<script>
function galleryFeedBack_openLightbox(img) {
  var largeSrc = img.dataset.large || img.src;
  var largeSrc2 = img.dataset.large2;

  // Tham chiếu tới các phần tử
  var overlay      = document.getElementById("lightbox-overlay");
  var lightImg     = document.getElementById("lightbox-image");
  var lightImg2 = document.getElementById("lightbox-image2");
  var authorEl     = document.getElementById("lightbox-author");
  var dateEl       = document.getElementById("lightbox-date");
  var descEl       = document.getElementById("lightbox-description");
  var commentEl    = document.getElementById("lightbox-comment");

  // 1) Ẩn text info trước, Đảm bảo khi ảnh load chậm thì text không bị nhẩy vị trí !
  authorEl.style.display      = "none";
  dateEl.style.display        = "none";
  descEl.style.display        = "none";
  commentEl.style.display     = "none";

  // 2) Show overlay & khóa cuộn
  overlay.style.display       = "flex";
  document.body.style.overflow = "hidden";

  // 3) Khi ảnh load xong, cập nhật nội dung và hiện info
  lightImg.onload = function() {
    authorEl.innerHTML   = "<strong>"+ (img.dataset.author || "N/A")+"</strong>";
    authorEl.style.fontSize = "x-large";
    dateEl.innerText     = img.dataset.date || "N/A";
    dateEl.style.fontStyle = "italic";
    descEl.innerHTML     = "&#9658; Description: <br>" + (img.dataset.description || "N/A");
    descEl.style.marginBottom = "10px";  
    commentEl.innerHTML  = "&#9658; Comment: <br>" + (img.dataset.comment || "N/A").replace(/\n/g, "<br>"); //Nạp cả phần xuống dòng ***

    // Hiện các thẻ info
    authorEl.style.display   = "";
    dateEl.style.display     = "";
    descEl.style.display     = "";
    commentEl.style.display  = "";
    commentEl.style.marginBottom  = "60px"; //Thêm margin đáy để trên ipad hiện hết dòng ( ko chừa bounce )
    // Ngắt handler để tránh gọi lại
    lightImg.onload = null;
  };

  // 4) Gán src để bắt đầu load ảnh
  lightImg.src = largeSrc;
  lightImg2.src = largeSrc2;
  lightImg2.style.marginTop = "10px";
}

 
 
  function galleryFeedBack_closeLightbox() {
    document.getElementById("lightbox-overlay").style.display = "none";
    document.body.style.overflow = "";
  }
</script>
';


$html .= <<<JS
<script>
document.addEventListener("click", function(e) {
  if (!e.target.matches(".page-link")) return;
  e.preventDefault();
  const container = document.getElementById("gallery-container");
  const loader = document.getElementById("gallery-loader");
 const perPage   = parseInt(container.dataset.itemsPerPage, 10);
  
  // Khóa chiều cao tạm thời, tránh layout nhẩy khi load
  var h = container.getBoundingClientRect().height + 'px';
  container.style.minHeight = h;

  // 1) Show spinner overlay
  loader.style.display = "flex";

  // 2) Fetch mới
  var url = new URL(window.location.href);
  url.searchParams.set("gallery_page", e.target.dataset.page);
 //Kết quả là, sau khi gọi xong, url.href sẽ chứa luôn ...?items_per_page=9 (hoặc giá trị tương ứng), đảm bảo mỗi lần fetch bạn đều gửi đúng số item cần hiển thị, bất kể URL gốc có gì.
 url.searchParams.set("items_per_page", perPage);

  fetch(url)
    .then(r => r.text())
    .then(html => {
      const doc = new DOMParser().parseFromString(html, 'text/html');
      container.innerHTML = doc.getElementById("gallery-container").innerHTML;
      
    // —— Thêm dòng này để remove inline height thả khóa chiều cao, không bị lỗi khi vừa load vừa resize browser
    container.style.height = container.scrollHeight + 'px';
    setTimeout(()=>{
    //Mở khóa cả 2 giới hạn height
    container.style.removeProperty('min-height');
    container.style.removeProperty('height');
    }, 300);  // sau khi transition xong
      
      document.querySelector(".gallery-pagination").innerHTML =
        doc.querySelector(".gallery-pagination").innerHTML;
    })
    .catch(console.error)
    .finally(() => {
      // 3) Ẩn spinner
      loader.style.display = "none";

      
    });
});

</script>
JS;


    return $html;
}