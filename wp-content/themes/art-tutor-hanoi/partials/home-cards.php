<?php
/**
 * Homepage cards — [ath_home_cards].
 *
 * Expects: $cards, $button_label, $aria_label
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $cards ) ) {
	return;
}
?>
  <!-- Cards -->
  <section class="cards-section" aria-label="<?php echo esc_attr( $aria_label ); ?>">
    <div class="cards">
      <?php foreach ( $cards as $card ) : ?>
        <?php
        $title = (string) ( $card['title'] ?? '' );
		$desc  = (string) ( $card['desc'] ?? $card['description'] ?? '' );
		$btn   = (string) ( $card['button'] ?? $button_label );
		$url   = ath_home_cards_resolve_link( $card );
        ?>
      <article class="card">
        <h2 class="card__title"><?php echo esc_html( $title ); ?></h2>
        <p class="card__desc"><?php echo esc_html( $desc ); ?></p>
        <?php echo ath_btn_html( $btn, $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </article>
      <?php endforeach; ?>
    </div>
  </section>
