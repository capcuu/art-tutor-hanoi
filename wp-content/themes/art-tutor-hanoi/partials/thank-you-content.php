<?php
/**
 * Thank you page content — post-booking confirmation.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$from   = isset( $_GET['from'] ) ? sanitize_key( wp_unslash( $_GET['from'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$legacy = ath_book_tab_legacy_map();

if ( isset( $legacy[ $from ] ) ) {
	$from = $legacy[ $from ];
}

$entry_id = isset( $_GET['entry'] ) ? (int) $_GET['entry'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$qr_data  = ath_thank_you_qr_data( $entry_id, $from );

$messages = array(
	'adult' => array(
		'title'   => 'Booking received',
		'lead'    => 'Thank you for your booking at Art Tutor Hanoi. We have received your request and will confirm your session by email or WhatsApp shortly.',
		'details' => 'Please check your inbox (and spam folder) for a confirmation with studio directions and what to bring.',
	),
	'kids'  => array(
		'title'   => 'Kids class enquiry received',
		'lead'    => 'Thank you for your interest in our kids art programs. We will contact you to confirm age group, schedule, and class placement.',
		'details' => 'We look forward to welcoming your child to the studio.',
	),
);

$content = isset( $messages[ $from ] ) ? $messages[ $from ] : array(
	'title'   => 'Thank you',
	'lead'    => 'We have received your submission. Our team will be in touch shortly to confirm the details.',
	'details' => 'If you need immediate assistance, call (+84) 98 828 8302 or email contact@arttutorhanoi.com.',
);

if ( $qr_data ) {
	$content['title']   = 'Booking received — please complete payment';
	$content['lead']    = 'Your booking details are saved. Transfer the exact amount below via VietQR to confirm your place.';
	$content['details'] = 'We will confirm your booking by email or WhatsApp once payment is received. If you have already paid, you can ignore this step.';
}
?>
<main class="thank-you-page">
  <section class="courses-hero thank-you-page__hero" aria-labelledby="thank-you-heading">
    <div class="courses-hero__inner">
      <p class="thank-you-page__badge" aria-hidden="true">
        <span class="thank-you-page__icon<?php echo $qr_data ? ' thank-you-page__icon--pending' : ''; ?>"><?php echo $qr_data ? '!' : '&#10003;'; ?></span>
      </p>
      <h1 id="thank-you-heading" class="courses-hero__title"><?php echo esc_html( $content['title'] ); ?></h1>
      <p class="courses-hero__subtitle thank-you-page__lead"><?php echo esc_html( $content['lead'] ); ?></p>
    </div>
  </section>

  <section class="thank-you-page__body" aria-label="What happens next">
    <div class="thank-you-page__inner">
      <?php if ( $qr_data ) : ?>
        <div class="thank-you-page__payment ath-vietqr" aria-labelledby="thank-you-payment-heading">
          <h2 id="thank-you-payment-heading" class="thank-you-page__payment-title">Pay by bank transfer</h2>
          <p class="thank-you-page__payment-lead">Scan the QR code with your banking app and pay the exact amount shown.</p>

          <dl class="thank-you-page__bank-details">
            <div class="thank-you-page__bank-row">
              <dt>Bank</dt>
              <dd><?php echo esc_html( $qr_data['bank_name'] ); ?></dd>
            </div>
            <div class="thank-you-page__bank-row">
              <dt>Account number</dt>
              <dd><?php echo esc_html( $qr_data['account_number'] ); ?></dd>
            </div>
            <div class="thank-you-page__bank-row">
              <dt>Account name</dt>
              <dd><?php echo esc_html( $qr_data['account_name'] ); ?></dd>
            </div>
            <div class="thank-you-page__bank-row">
              <dt>Amount</dt>
              <dd><strong><?php echo esc_html( $qr_data['amount_label'] ); ?></strong></dd>
            </div>
            <?php if ( ! empty( $qr_data['add_info'] ) ) : ?>
            <div class="thank-you-page__bank-row">
              <dt>Transfer note</dt>
              <dd><?php echo esc_html( $qr_data['add_info'] ); ?></dd>
            </div>
            <?php endif; ?>
          </dl>

          <?php if ( ! empty( $qr_data['qr_image_url'] ) ) : ?>
            <p class="thank-you-page__qr-wrap">
              <img
                class="thank-you-page__qr-image"
                src="<?php echo esc_url( $qr_data['qr_image_url'] ); ?>"
                alt="<?php esc_attr_e( 'VietQR payment code', 'art-tutor-hanoi' ); ?>"
                width="280"
                height="280"
                loading="eager"
                decoding="async"
              />
            </p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <p class="thank-you-page__details"><?php echo esc_html( $content['details'] ); ?></p>

      <div class="thank-you-page__steps">
        <h2 class="thank-you-page__steps-title">What happens next</h2>
        <ol class="thank-you-page__steps-list">
          <?php if ( $qr_data ) : ?>
            <li>Pay the exact amount using the QR code above</li>
            <li>We verify your transfer and confirm by email or WhatsApp</li>
            <li>Arrive at the studio ready to create</li>
          <?php else : ?>
            <li>We review your booking details</li>
            <li>You receive confirmation by email or WhatsApp</li>
            <li>Arrive at the studio ready to create</li>
          <?php endif; ?>
        </ol>
      </div>

      <div class="thank-you-page__actions">
        <a href="<?php echo esc_url( ath_page_url( 'calendar' ) ); ?>" class="courses-cta__btn">View weekly calendar</a>
        <a href="<?php echo esc_url( ath_page_url( 'home' ) ); ?>" class="thank-you-page__link">Back to homepage</a>
      </div>
    </div>
  </section>
</main>
