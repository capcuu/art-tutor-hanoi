<?php
/**
 * Calendar page.
 */
require __DIR__ . '/config/wordpress.php';

$page_title = 'Weekly Calendar — Art Tutor Hanoi';

$now    = new DateTime();
$monday = ( clone $now )->modify( '-' . ( (int) $now->format( 'N' ) - 1 ) . ' days' );
$sunday = ( clone $monday )->modify( '+6 days' );
$fmt    = static function ( DateTime $d ) {
	return $d->format( 'M j, Y' );
};
$week_range = 'From ' . $fmt( $monday ) . ' to ' . $fmt( $sunday );

$art_calendar_html = v2_art_calendar_html();

$table_html = v2_tablepress_table( 2 );
if ( $table_html === '' ) {
	$table_html = v2_tablepress_table_from_live( 2 );
}
$table_html = v2_prepare_tablepress_html( $table_html );

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.html';
require __DIR__ . '/partials/calendar-content.php';
require __DIR__ . '/partials/footer.html';
?>
  <script src="assets/js/main.js"></script>
</body>
</html>
