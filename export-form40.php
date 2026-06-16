<?php
/**
 * Export Fluent Form #40 in Fluent Forms import-compatible JSON.
 * Run: docker exec wordpress-wordpress-1 php /var/www/html/export-form40.php
 */

require __DIR__ . '/wp-load.php';

global $wpdb;

$result = $wpdb->get_results( 'SELECT * FROM wp_fluentform_forms WHERE id = 40' );
if ( ! $result ) {
	fwrite( STDERR, "Form 40 not found\n" );
	exit( 1 );
}

$forms = array();
foreach ( $result as $item ) {
	$form          = (array) $item;
	$exclude_meta = array( '_total_views' );
	if ( ! empty( $argv[1] ) && 'slim' === $argv[1] ) {
		$exclude_meta[] = 'revision';
	}
	$placeholders = implode( ', ', array_fill( 0, count( $exclude_meta ), '%s' ) );
	$sql          = "SELECT meta_key, value FROM {$wpdb->prefix}fluentform_form_meta WHERE form_id = %d AND meta_key NOT IN ($placeholders)";
	$params       = array_merge( array( $item->id ), $exclude_meta );
	$metas        = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
	$form['form_fields'] = json_decode( $form['form_fields'] );
	$form['metas']       = $metas;
	unset( $form['id'] );
	$forms[] = $form;
}

$slim = ! empty( $argv[1] ) && 'slim' === $argv[1];
$out  = __DIR__ . '/wp-content/themes/art-tutor-hanoi/backup/' . ( $slim ? 'adult-booking-form-export-slim.json' : 'adult-booking-form-export.json' );
file_put_contents( $out, wp_json_encode( $forms, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

echo 'Exported to ' . $out . PHP_EOL;
echo 'Title: ' . $forms[0]['title'] . PHP_EOL;
echo 'Size: ' . number_format( filesize( $out ) ) . ' bytes' . PHP_EOL;
if ( $slim ) {
	echo 'Slim export (no revision meta) — use this if Tools → Import fails on Bluehost.' . PHP_EOL;
}
