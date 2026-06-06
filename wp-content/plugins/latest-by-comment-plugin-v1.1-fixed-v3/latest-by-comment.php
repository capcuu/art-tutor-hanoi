<?php
/**
 * Plugin Name: Latest Posts by Latest Comment or Post Date
 * Description: Hiển thị bài viết theo comment mới nhất hoặc ngày đăng nếu chưa có comment
 * Version: 2.2
 */

function latest_posts_by_latest_comment_or_post($atts) {
    global $wpdb;

    $atts = shortcode_atts(array(
        'number' => 5,
        'columns' => 3,
        'category' => '',
        'show_date' => 'true',
        'show_thumb' => 'true',
        'thumb_size' => 'medium'
    ), $atts, 'latest_by_comment');

    $limit = intval($atts['number']);
    $limit_clause = ($limit > 0) ? "LIMIT $limit" : "";
    $columns = max(1, min(6, intval($atts['columns'])));
    $category_clause = '';

    // Filter category theo slug (ổn định hơn name)
    if (!empty($atts['category'])) {
        $term = get_term_by('slug', sanitize_title($atts['category']), 'category');
        if ($term) {
            $category_clause = " 
                AND EXISTS (
                    SELECT 1 FROM $wpdb->term_relationships tr
                    INNER JOIN $wpdb->term_taxonomy tt 
                        ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE tr.object_id = p.ID
                    AND tt.term_id = {$term->term_id}
                    AND tt.taxonomy = 'category'
                )
            ";
        }
    }

    // 🔥 Query mới (KHÔNG dùng GREATEST)
    $results = $wpdb->get_results("
        SELECT 
            p.ID,
            p.post_date,
            c.last_comment
        FROM $wpdb->posts p
        LEFT JOIN (
            SELECT comment_post_ID, MAX(comment_date) AS last_comment
            FROM $wpdb->comments
            WHERE comment_approved = 1
            GROUP BY comment_post_ID
        ) c ON p.ID = c.comment_post_ID
        WHERE p.post_status = 'publish'
        AND p.post_type = 'post'
        $category_clause
        ORDER BY 
            COALESCE(c.last_comment, p.post_date) DESC
        $limit_clause
    ");

    if (empty($results)) return '<p>No posts found.</p>';

    $output = "<div class='lpc-grid lpc-cols-{$columns}'>";

    foreach ($results as $row) {
        $post_id = $row->ID;
        $title = get_the_title($post_id);
        $link = get_permalink($post_id);

        // ✅ chọn đúng date
        $date_source = $row->last_comment ? $row->last_comment : $row->post_date;
        $activity_date = date_i18n(get_option('date_format'), strtotime($date_source));

        $thumb = $atts['show_thumb'] === 'true'
            ? get_the_post_thumbnail($post_id, $atts['thumb_size'])
            : '';

        $output .= "<div class='lpc-item'>";

        if ($thumb) {
            $output .= "<a href='{$link}' class='lpc-thumb'>{$thumb}</a>";
        }

        $output .= "<h4 class='lpc-title'><a href='{$link}'>{$title}</a></h4>";

        if ($atts['show_date'] === 'true') {
            $label = $row->last_comment ? 'Updated at' : 'Posted on';
            $output .= "<div class='lpc-date'>{$label} {$activity_date}</div>";
        }

        $output .= "</div>";
    }

    $output .= "</div>";

    return $output;
}

// hỗ trợ cả 2 shortcode
add_shortcode('latest_by_comment', 'latest_posts_by_latest_comment_or_post');
add_shortcode('latest_by_comment_number', 'latest_posts_by_latest_comment_or_post');

// load CSS
function lpc_enqueue_styles() {
    wp_enqueue_style('lpc-style', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'lpc_enqueue_styles');