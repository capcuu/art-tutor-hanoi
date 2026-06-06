<?php
/**
 * Plugin Name: Artist Countries Shortcode
 * Description: Hiển thị số và danh sách quốc gia từ Google Sheets qua shortcode.
 * Version: 1.0
 * Author: Hung Nguyen
 */

function fetch_google_sheet_cell($sheet_url, $row, $column) {
    $csv_url = preg_replace('/\/edit.*$/', '/gviz/tq?tqx=out:csv', $sheet_url);
    $response = wp_remote_get($csv_url);

    if (is_wp_error($response)) return '';

    $csv = wp_remote_retrieve_body($response);
    $lines = str_getcsv($csv, "\n");

    if (!isset($lines[$row - 1])) return '';

    $data = str_getcsv($lines[$row - 1]);

    // Dò cột theo tên hoặc chỉ số
    if (is_numeric($column)) {
        return $data[$column - 1] ?? '';
    } else {
        $headers = str_getcsv($lines[0]);
        $col_index = array_search($column, $headers);
        return $col_index !== false ? ($data[$col_index] ?? '') : '';
    }
}

function artist_countries_shortcode($atts) {
    $a = shortcode_atts(array(
        'sheet_url' => '',
        'row_number_x' => 2,
        'row_number_y' => 3,
        'column_name' => 'Note',
    ), $atts);

    if (empty($a['sheet_url'])) return 'Vui lòng cung cấp sheet_url.';

    $x = fetch_google_sheet_cell($a['sheet_url'], $a['row_number_x'], $a['column_name']);
    $y = fetch_google_sheet_cell($a['sheet_url'], $a['row_number_y'], $a['column_name']);

    return "Các nghệ sĩ tham gia đến từ $x quốc gia, bao gồm: $y.";
}

add_shortcode('artist_countries', 'artist_countries_shortcode');
