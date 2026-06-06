<?php
/**
 * Plugin Name: Artist Country Summary
 * Description: Hiển thị câu tóm tắt số lượng và danh sách quốc gia từ Google Sheet.
 * Version: 1.0
 * Author: Hung Nguyen
 */

function convert_to_csv_url($sheet_url) {
    preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $sheet_url, $id_matches);
    preg_match('/gid=([0-9]+)/', $sheet_url, $gid_matches);

    $sheet_id = $id_matches[1] ?? '';
    $gid = $gid_matches[1] ?? '0';

    if (!$sheet_id) return '';

    return "https://docs.google.com/spreadsheets/d/$sheet_id/gviz/tq?tqx=out:csv&gid=$gid";
}

function get_sheet_cell_value($sheet_url, $row, $column_name) {
    $csv_url = convert_to_csv_url($sheet_url);
    if (!$csv_url) return 'LỖI: URL không hợp lệ';

    $response = wp_remote_get($csv_url);
    if (is_wp_error($response)) return 'LỖI: Không tải được dữ liệu từ Google Sheets';

    $csv = wp_remote_retrieve_body($response);
    $tmp = tmpfile();
    fwrite($tmp, $csv);
    fseek($tmp, 0);

    $data = [];
    while (($line = fgetcsv($tmp)) !== false) {
        $data[] = $line;
    }
    fclose($tmp);

    if (count($data) < $row) return '';

    $headers = array_map('trim', $data[0]);
    $target_row = $data[$row - 1];
    $col_index = array_search($column_name, $headers);

    if ($col_index === false) return '';

    return $target_row[$col_index] ?? '';
}

function shortcode_artist_summary($atts) {
    $a = shortcode_atts(array(
        'sheet_url' => '',
        'row_x' => 2,
        'row_y' => 4,
        'column' => 'Note',
    ), $atts);

    $x = get_sheet_cell_value($a['sheet_url'], $a['row_x'], $a['column']);
    $y = get_sheet_cell_value($a['sheet_url'], $a['row_y'], $a['column']);

    if ($x === '' || $y === '') return 'Không thể lấy dữ liệu từ sheet.';

return "<p style='font-size: 0.6em; font-style: italic;'>The participating artists come from <strong>$x</strong> countries, including: $y.</p>";

}

add_shortcode('artist_summary', 'shortcode_artist_summary');
