<?php
/**
 * Pricing page — accordion program list.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$pricing = require ATH_THEME_DIR . '/data/pricing-programs.php';
?>
<div class="pricing-list" id="pricing-list">
	<?php foreach ( $pricing['sections'] as $section ) : ?>
		<?php
		$panel_id  = 'pricing-panel-' . $section['id'];
		$header_id = 'pricing-header-' . $section['id'];
		$items     = ! empty( $section['items'] ) ? $section['items'] : array();
		$has_panel = ! empty( $section['description'] ) || ! empty( $items ) || ! empty( $section['price'] );
		?>
		<article class="pathway pathway--pricing" id="<?php echo esc_attr( $section['id'] ); ?>" data-pathway="<?php echo esc_attr( $section['id'] ); ?>">
			<button
				type="button"
				class="pathway__header pricing-row__header"
				id="<?php echo esc_attr( $header_id ); ?>"
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $panel_id ); ?>"
				<?php echo $has_panel ? '' : 'disabled'; ?>
			>
				<span class="pricing-row__num"><?php echo esc_html( $section['num'] ); ?></span>
				<span class="pricing-row__title"><?php echo esc_html( $section['title'] ); ?></span>
				<?php if ( $has_panel ) : ?>
					<span class="pricing-row__chevron pathway__chevron" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="18" height="18"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
				<?php endif; ?>
			</button>

			<?php if ( $has_panel ) : ?>
				<div
					class="pathway__panel pricing-row__panel"
					id="<?php echo esc_attr( $panel_id ); ?>"
					role="region"
					aria-labelledby="<?php echo esc_attr( $header_id ); ?>"
					hidden
				>
					<?php if ( ! empty( $section['description'] ) ) : ?>
						<p class="pricing-panel__desc"><?php echo esc_html( $section['description'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $items ) ) : ?>
						<ul class="pricing-items">
							<?php foreach ( $items as $item ) : ?>
								<li class="pricing-item">
									<span class="pricing-item__title"><?php echo esc_html( $item['title'] ); ?></span>
									<span class="pricing-item__price"><?php echo esc_html( $item['price'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php elseif ( ! empty( $section['price'] ) ) : ?>
						<p class="pricing-single">
							<span class="pricing-single__label">Session fee</span>
							<span class="pricing-single__price"><?php echo esc_html( $section['price'] ); ?></span>
						</p>
					<?php endif; ?>

					<div class="pricing-panel__footer">
						<a href="<?php echo esc_url( ath_book_url( $section['book_tab'] ) ); ?>" class="pricing-panel__btn">Book Now</a>
					</div>
				</div>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
</div>
