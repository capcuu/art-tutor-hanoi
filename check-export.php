<?php
require __DIR__ . '/wp-load.php';

$path = __DIR__ . '/wp-content/themes/art-tutor-hanoi/backup/adult-booking-form-export.json';
$forms = json_decode( file_get_contents( $path ), true );
$f = $forms[0];

echo "keys: " . implode( ', ', array_keys( $f ) ) . "\n";
echo "title: " . $f['title'] . "\n";
echo "has id: " . ( isset( $f['id'] ) ? 'yes ' . $f['id'] : 'no' ) . "\n";
echo "meta count: " . count( $f['metas'] ?? [] ) . "\n";
echo "form_meta key: " . ( isset( $f['form_meta'] ) ? 'yes' : 'no' ) . "\n";
echo "fields count: " . count( $f['form_fields']['fields'] ?? [] ) . "\n";
$keys = array_column( $f['metas'] ?? [], 'meta_key' );
echo "meta keys:\n  " . implode( "\n  ", $keys ) . "\n";
$revs = array_filter( $keys, fn( $k ) => $k === 'revision' );
echo "revision entries: " . count( $revs ) . "\n";
