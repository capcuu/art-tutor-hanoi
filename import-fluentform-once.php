<?php
/**
 * One-time Fluent Forms import for server (when Tools → Import has no response).
 *
 * Usage:
 * 1. Upload this file + adult-booking-form-export.json to WordPress root on server.
 * 2. Log in as admin, visit: https://yoursite.com/import-fluentform-once.php
 * 3. Delete both files immediately after success.
 *
 * Optional: ?file=adult-booking-form-export-slim.json for slim export (no revision meta).
 */

require __DIR__ . '/wp-load.php';

if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Admin login required.' );
}

global $wpdb;

$forms_table = $wpdb->prefix . 'fluentform_forms';
$meta_table  = $wpdb->prefix . 'fluentform_form_meta';
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $forms_table ) ) !== $forms_table ) {
	wp_die( 'Fluent Forms tables not found. Is the plugin active?' );
}

$json_name = isset( $_GET['file'] ) ? basename( sanitize_file_name( wp_unslash( $_GET['file'] ) ) ) : 'adult-booking-form-export.json';
$json_path = __DIR__ . '/' . $json_name;
if ( ! is_readable( $json_path ) ) {
	wp_die(
		'Missing ' . esc_html( $json_name ) . ' in WordPress root.<br>'
		. 'Upload the JSON next to this script, then reload.'
	);
}

$contents = file_get_contents( $json_path );
$forms    = json_decode( $contents, true );

if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $forms ) || empty( $forms ) ) {
	wp_die( 'Invalid JSON: ' . esc_html( json_last_error_msg() ) );
}

$inserted = array();
$errors   = array();

foreach ( $forms as $index => $form_item ) {
	$form_fields = array();
	if ( ! empty( $form_item['form'] ) ) {
		$form_fields = $form_item['form'];
	} elseif ( ! empty( $form_item['form_fields'] ) ) {
		$form_fields = $form_item['form_fields'];
	} else {
		$errors[] = 'Form #' . ( $index + 1 ) . ': missing form_fields.';
		continue;
	}

	$row = array(
		'title'       => sanitize_text_field( $form_item['title'] ?? 'Imported Form' ),
		'form_fields' => wp_json_encode( $form_fields ),
		'status'      => sanitize_text_field( $form_item['status'] ?? 'published' ),
		'has_payment' => (int) ( $form_item['has_payment'] ?? 0 ),
		'type'        => sanitize_text_field( $form_item['type'] ?? 'form' ),
		'created_by'  => get_current_user_id(),
	);

	if ( ! empty( $form_item['conditions'] ) ) {
		$row['conditions'] = is_string( $form_item['conditions'] )
			? $form_item['conditions']
			: wp_json_encode( $form_item['conditions'] );
	}
	if ( isset( $form_item['appearance_settings'] ) ) {
		$row['appearance_settings'] = is_string( $form_item['appearance_settings'] )
			? $form_item['appearance_settings']
			: wp_json_encode( $form_item['appearance_settings'] );
	}

	$ok = $wpdb->insert( $forms_table, $row );
	if ( false === $ok ) {
		$errors[] = 'Form #' . ( $index + 1 ) . ' insert failed: ' . $wpdb->last_error;
		continue;
	}

	$form_id = (int) $wpdb->insert_id;
	$old_id  = (int) ( $form_item['id'] ?? 0 );

	$metas = $form_item['metas'] ?? array();
	if ( empty( $metas ) && ! empty( $form_item['form_meta'] ) ) {
		$metas = $form_item['form_meta'];
	}

	foreach ( $metas as $meta ) {
		$meta_key   = sanitize_text_field( $meta['meta_key'] ?? '' );
		$meta_value = $meta['value'] ?? '';
		if ( '' === $meta_key ) {
			continue;
		}
		if ( $old_id && in_array( $meta_key, array( 'ffc_form_settings_generated_css', 'ffc_form_settings_meta' ), true ) ) {
			$meta_value = str_replace( 'ff_conv_app_' . $old_id, 'ff_conv_app_' . $form_id, $meta_value );
		}
		$meta_ok = $wpdb->insert(
			$meta_table,
			array(
				'form_id'  => $form_id,
				'meta_key' => $meta_key,
				'value'    => $meta_value,
			)
		);
		if ( false === $meta_ok ) {
			$errors[] = 'Meta "' . $meta_key . '" for form ' . $form_id . ' failed: ' . $wpdb->last_error;
		}
	}

	do_action( 'fluentform/form_imported', $form_id );
	$inserted[ $form_id ] = admin_url( 'admin.php?page=fluent_forms&route=editor&form_id=' . $form_id );
}

header( 'Content-Type: text/html; charset=utf-8' );
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Fluent Forms import</title></head><body style="font-family:sans-serif;max-width:640px;margin:2rem auto;">';

if ( ! empty( $inserted ) ) {
	echo '<h1>Import successful</h1><ul>';
	foreach ( $inserted as $id => $url ) {
		echo '<li>Form ID <strong>' . esc_html( (string) $id ) . '</strong> — <a href="' . esc_url( $url ) . '">Edit form</a></li>';
	}
	echo '</ul>';
	echo '<p>Update <code>ATH_ADULT_BOOKING_FORM_ID</code> in theme <code>config/book-tabs.php</code> to the new ID.</p>';
	echo '<p>Fix redirect URL in form settings (replace <code>localhost</code> with your dev domain).</p>';
	echo '<p><strong>Delete this script and the JSON file from the server now.</strong></p>';
}

if ( ! empty( $errors ) ) {
	echo '<h2 style="color:#b32d2e;">Errors</h2><ul>';
	foreach ( $errors as $err ) {
		echo '<li>' . esc_html( $err ) . '</li>';
	}
	echo '</ul>';
}

if ( empty( $inserted ) && empty( $errors ) ) {
	echo '<p>No forms imported.</p>';
}

echo '</body></html>';
