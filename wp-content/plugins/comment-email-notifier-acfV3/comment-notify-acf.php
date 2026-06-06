<?php
/*
Plugin Name: Comment Notify with ACF
Description: Sends an email notification when a new comment is posted, using recipient info from ACF fields.
Version: 1.0
Author: YourName
*/

add_action('comment_post', function($comment_ID, $comment_approved) {
    if (1 === $comment_approved) {
        $comment = get_comment($comment_ID);
        $post_id = $comment->comment_post_ID;

        // Lấy dữ liệu từ ACF
        $student_name = get_post_meta($post_id, 'student_name', true);
        $notify_email = get_post_meta($post_id, 'comment_notify_email', true);

        if (is_email($notify_email)) {
            if (empty($student_name)) {
                $student_name = 'student';
            }

            $subject = 'New comment for ' . $student_name;
            $message = "Hi $student_name,\n\n";
	    $message .= "Your teacher from Art tutor has posted feedback on your drawing.\n\n";
	    $message .= "Please click the link below to view it:\n";
	    $message .= get_permalink($comment->comment_post_ID) . "\n\n";
	    $message .= "Art tutor team\n";
	    $message .= "0988288302 (zalo/whatsapp)";

            wp_mail($notify_email, $subject, $message);
        }
    }
}, 10, 2);
