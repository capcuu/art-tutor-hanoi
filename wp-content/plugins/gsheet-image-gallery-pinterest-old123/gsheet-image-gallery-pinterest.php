<?php
/*
Plugin Name: Google Sheet Image Gallery (Pinterest Style)
Description: Hiển thị gallery ảnh từ Google Sheets theo kiểu Pinterest layout, dùng shortcode.
Ban nay la hoan thien gallery, ligtbox co đầy đủ thông tin phụ thuộc vào cột select, nếu không select 1 2 3 coi như loại và không hiên
CHÚ Ý: Sau này mời giám khảo thì sẽ truyền kết quả vào cột select này !!!!!!
Ban nay co nang cap toi uu anh khi load o gallery, vao lightbox moi dung anh to !!!
Cập nhật sửa tình trạng lightbox khi chưa load ảnh và bắt đầu load thì layout xộc xệch -> tạm ẩn text cho đến khi load xong ảnh !!!!
Đã thêm hiệu ứng Fade
Đã thêm preload đồng thời với Fade !!!! ko cảm giác độ trễ
Fix lỗi trùng tên hàm với plugin khác
Version: 1.1
Author: Hung Nguyen + ChatGPT
*/

add_shortcode('sheet_gallery', 'gsheet_image_gallery_shortcode');

//Không nên tìm cách đọc tên chính xác thứ tự cột 1 2 3 hoặc  A B C, tuy có thể tránh nhầm khi gõ tên cột nhưng khi cột thay đổi thứ tự phải thay lại hết!!!

function gsheet_image_gallery_shortcode($atts) {
    $atts = shortcode_atts([

        'sheet_url' => '',
        'image_column' => 'AE',
        'columns' => 3,
        'author_column' => 'Full name',
        'country_column' => 'Country',
        
        'title_column1' => 'Title of the artwork 1:',
        'medium_column1' => 'Medium (1):',
        'size_column1' => 'Size (1)',
        'year_column1' => 'Year created (1):',
        'title_column2' => 'Title of Artwork 2:',
        'medium_column2' => 'Medium (2):',
        'size_column2' => 'Size (2)',
        'year_column2' => 'Year created (2):',        
        'title_column3' => 'Title of Artwork 3:',
        'medium_column3' => 'Medium (3):',
        'size_column3' => 'Size (3)',
        'year_column3' => 'Year created (3):',
        
        'select_column' => 'SelectedArtWork',
        'artist_link_column' => 'Social media / Portfolio link (if any):',
		'start_row' => 2
    ], $atts);

    if (!$atts['sheet_url']) return '<p>Vui lòng cung cấp sheet_url.</p>';

// 1) Lấy thẳng CSV (convert_sheet_to_csv đã bao gồm wp_remote_get)
$csv = convert_sheet_to_csv_exhibitionOld( $atts['sheet_url'] );
if ( empty( $csv ) ) {
    return '<p style="color:red;">Lỗi khi đọc dữ liệu từ Google Sheets. Vui lòng kiểm tra lại sheet_url và quyền truy cập.</p>';
}

   
   // $rows = array_map('str_getcsv', explode("\n", $csv)); //Se gay ra loi neu trong o co dau xuong dong, doan phia duoi se khac phuc loi nay
    $rows = [];
$stream = fopen('php://memory', 'r+');
fwrite($stream, $csv);
rewind($stream);
while (($row = fgetcsv($stream)) !== false) {
    $rows[] = $row;
}
fclose($stream);
    
    $headers = array_shift($rows);
// Cắt bỏ các dòng trước start_row
$rows = array_slice($rows, max(0, intval($atts['start_row']) - 2));

    $image_index = array_search($atts['image_column'], $headers);
    $select_index = array_search($atts['select_column'], $headers);
$author_index = array_search($atts['author_column'], $headers);
$country_index = array_search($atts['country_column'], $headers);

/*$title_index = array_search($atts['title_column1'], $headers);
$medium_index = array_search($atts['medium_column1'], $headers);
$size_index = array_search($atts['size_column1'], $headers);
$year_index = array_search($atts['year_column1'], $headers);*/

$artist_index = array_search($atts['artist_link_column'], $headers);
    if ($image_index === false) return '<p>Không tìm thấy cột ảnh.</p>';

    $html = '<div class="gsheet-gallery" style="column-count: ' . intval($atts['columns']) . '; column-gap: 16px;">';
    foreach ($rows as $row) {
    // Nếu cột A trống thì dừng lại
    if (!isset($row[0]) || trim($row[0]) === '') break;
        if (!isset($row[$image_index])) continue;
        $img_url = isset($row[$image_index]) ? trim($row[$image_index]) : ''; 
        //Nếu đường link rỗng hoặc lựa chọn khong phải 1 /2 /3 (bị loại)  thì bỏ qua dòng !!!!
        if (($img_url === '')|| !in_array($row[$select_index], [1, 2, 3])) continue;
        
        $optimized_url = str_replace('/upload/', '/upload/w_300,q_auto,f_auto/', $img_url); // Tao dong lenh lay link anh toi uu w = 300, auto chat luong va dinh dang !!!!
        $optimizedBig_url = str_replace('/upload/', '/upload/w_800,h_800,c_limit,q_auto,f_auto/', $img_url); // Tao dong lenh lay link anh lightbox toi uu w = 800, auto chat luong va dinh dang !!!!               
                           
        //Dua vao lua chon cua giam khao de doc thong tin tac pham 1, 2 hay 3
        switch ($row[$select_index]) { 
    case "1":
    	$title_index = array_search($atts['title_column1'], $headers);
	$medium_index = array_search($atts['medium_column1'], $headers);
	$size_index = array_search($atts['size_column1'], $headers);
	$year_index = array_search($atts['year_column1'], $headers);
 	//echo "Giá trị 1";
        break;

    case "2":
   	$title_index = array_search($atts['title_column2'], $headers);
	$medium_index = array_search($atts['medium_column2'], $headers);
	$size_index = array_search($atts['size_column2'], $headers);
	$year_index = array_search($atts['year_column2'], $headers);
	//echo "Giá trị 2";
        break;

    case "3":
    	$title_index = array_search($atts['title_column3'], $headers);
	$medium_index = array_search($atts['medium_column3'], $headers);
	$size_index = array_search($atts['size_column3'], $headers);
	$year_index = array_search($atts['year_column3'], $headers);
	//echo "Giá trị 3";
        break;

    default:
        // Nếu là các giá trị khác = 0 hoac cac so khac  tuc la khong duoc duyet 
       // echo "Giá trị khác";
        break;
}

        
$html .= '<div style="break-inside: avoid; margin-bottom: 20px;">';
$html .= '<img src="' . esc_url($optimized_url) . '" ' 
    . 'data-author="' . esc_attr(isset($row[$author_index]) ? $row[$author_index] : '') . '" '
    . 'data-country="' . esc_attr(isset($row[$country_index]) ? $row[$country_index] : '') . '" '
    . 'data-title="' . esc_attr(isset($row[$title_index]) ? $row[$title_index] : '') . '" '
    . 'data-medium="' . esc_attr(isset($row[$medium_index]) ? $row[$medium_index] : '') . '" '
    . 'data-size="' . esc_attr(isset($row[$size_index]) ? $row[$size_index] : '') . '" '
    . 'data-year="' . esc_attr(isset($row[$year_index]) ? $row[$year_index] : '') . '" '
    . 'data-artist="' . esc_attr(isset($row[$artist_index]) ? $row[$artist_index] : '') . '" '
    //. 'data-imageBig="' . esc_attr(isset($row[$imageBig_index]) ? $row[$imageBig_index] : '') . '" ' //Khong duoc vi html chu dong chuyen sang chu thuong, nen dung image-big thay vi imageBig*****
    . 'data-image-big="' . esc_url($optimizedBig_url) . '" ' //Them truong img.dataset.imgageBig luu tru link anh goc chuan bi cho lightbox

    . 'onclick="openLightbox_exhibitionOld(this)" '
    . 'style="width:100%; height:auto; border-radius:8px; cursor:pointer;" />';
    
// Caption dưới ảnh nếu có dữ liệu author hoặc country
$html .= '<div class="caption" style="text-align:left; font-size:14px; color:#333; margin-top:0px;">';
if (!empty($row[$author_index])) {
    $html .= '<div class="caption-author"><strong>' . esc_html($row[$author_index]) . '</div>';
}
if (!empty($row[$country_index])) {
    $html .= '<div class="caption-country">' . esc_html($row[$country_index]) . '</div>';
}
$html .= '</div>'; // đóng caption

$html .= '</div>'; // đóng block ảnh
    
				  
				  
    }
    

    
    $html .= '
    <style>
    #lightbox-overlay {
        position: fixed !important;
        top: 0; left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
    }
    #lightbox-overlay img {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        box-shadow: 0 0 30px black;
    }
      #lightbox {
    opacity: 0;
    transition: opacity 0.75s ease-in-out;
    pointer-events: none;
  }
  #lightbox.visible {
    opacity: 1;
    pointer-events: auto;
  }
    </style>
    
';
    
$html .= <<<HTML
<div id="lightbox" style="width:100%; height:100%;">
  <div id="lightbox-overlay" onclick="closeLightbox_exhibitionOld()" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.95); z-index:99999; justify-content:center; align-items:center;">
    <div style="display:flex; flex-direction:row; gap:16px; padding:40px; border-radius:0px; max-width:90vw; max-height:90vh; align-items: flex-end;">
      <div style="color:white; width:30%; font-size:16px;">
        <div id="lightbox-author" style="margin-bottom:8px;"></div>
        <div id="lightbox-country"></div>
        <div id="lightbox-title"></div>
        <div id="lightbox-medium"></div>
        <div id="lightbox-size"></div>
        <div id="lightbox-year"></div>
        <div id="lightbox-artist" style="margin-top:12px;"></div>
      </div>
      <div style="flex:1;">
        <img id="lightbox-image" src="" style="max-width:100%; max-height:80vh; border-radius:0px;" />
      </div>
    </div>
  </div>
</div>
<script>

function openLightbox_exhibitionOld(img) {
  const image = document.getElementById("lightbox-image");
  const overlay = document.getElementById("lightbox-overlay");
  const lightbox = document.getElementById("lightbox");

  // Mở overlay và fade-in ngay lập tức
  overlay.style.display = "flex";
  lightbox.classList.add("visible");

  // Ẩn ảnh và thông tin trong lúc ảnh đang load
  image.style.visibility = "hidden";
  const fields = ["author", "country", "title", "medium", "size", "year", "artist"];
  fields.forEach(id => {
    const el = document.getElementById("lightbox-" + id);
    if (el) el.style.visibility = "hidden";
  });

  // Bắt đầu preload ảnh (nhưng fade-in đang diễn ra)
  image.onload = function () {
    // Khi ảnh đã load xong, hiện ảnh và thông tin
    image.style.visibility = "visible";

    document.getElementById("lightbox-author").innerHTML = "<strong>Author: </strong>" + (img.dataset.author || "N/A");
    document.getElementById("lightbox-author").style.fontSize = "x-large";
    document.getElementById("lightbox-country").innerText = "Nationality: " + (img.dataset.country || "N/A");
    document.getElementById("lightbox-title").innerText = "Title: " + (img.dataset.title || "N/A");
    document.getElementById("lightbox-medium").innerText = "Medium: " + (img.dataset.medium || "N/A");
    document.getElementById("lightbox-size").innerText = "Size: " + (img.dataset.size || "N/A");
    document.getElementById("lightbox-year").innerText = "Year: " + (img.dataset.year || "N/A");

    const artistBox = document.getElementById("lightbox-artist");
    if (artistBox) {
      const artistLink = img.dataset.artist;
      if (artistLink && artistLink.trim() !== "") {
        artistBox.innerHTML = '<a href="' + artistLink + '" target="_blank" style="color:#00d0ff;">Artist profile (External Link)</a>';
      } else {
        artistBox.innerHTML = "";
      }
    }

    // Hiện tất cả các phần tử sau khi ảnh đã load
    fields.forEach(id => {
      const el = document.getElementById("lightbox-" + id);
      if (el) el.style.visibility = "visible";
    });
  };

  // Gán ảnh và bắt đầu tải (song song với fade-in)
  image.src = img.dataset.imageBig;
}

function closeLightbox_exhibitionOld() {
  const lightbox = document.getElementById("lightbox");
  lightbox.classList.remove("visible");

  // Đợi hiệu ứng kết thúc rồi mới ẩn overlay
  setTimeout(() => {
    document.getElementById("lightbox-overlay").style.display = "none";
  }, 0);
}

</script>
HTML;

return $html;
}

function convert_sheet_to_csv_exhibitionOld( $sheet_url ) {
    // 1) Tách sheet ID và gid (hỗ trợ cả ?gid= và #gid=)
    if ( preg_match( '/\/d\/([a-zA-Z0-9\-_]+)\/.*[?#]gid=([0-9]+)/', $sheet_url, $m ) ) {
        $sheet_id = $m[1];
        $gid      = $m[2];
        $export_url = "https://docs.google.com/spreadsheets/d/{$sheet_id}/export?format=csv&gid={$gid}";

        // 2) Fetch CSV qua HTTP API của WP
        $resp = wp_remote_get( $export_url );
        if ( is_wp_error( $resp ) || wp_remote_retrieve_response_code( $resp ) !== 200 ) {
            return '';
        }
        return wp_remote_retrieve_body( $resp );
    }
    // Nếu không parse được ID/gid
    return '';
}




$html .= '</div>';









