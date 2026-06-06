<?php
/**
 * Thank you page content — post-booking confirmation.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$from = isset( $_GET['from'] ) ? sanitize_key( wp_unslash( $_GET['from'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$messages = array(
	'trial'        => array(
		'title'   => 'Your trial class is booked',
		'lead'    => 'Thank you for booking a sample class at Art Tutor Hanoi. We have received your request and will confirm your session by email or WhatsApp shortly.',
		'details' => 'Please check your inbox (and spam folder) for a confirmation with studio directions and what to bring.',
	),
	'adults'       => array(
		'title'   => 'Registration received',
		'lead'    => 'Thank you for registering for adult art classes. Our team will review your details and follow up with schedule options and next steps.',
		'details' => 'If you have questions before we reply, call (+84) 98 828 8302 or email contact@arttutorhanoi.com.',
	),
	'kids'         => array(
		'title'   => 'Kids class enquiry received',
		'lead'    => 'Thank you for your interest in our kids art programs. We will contact you to confirm age group, schedule, and class placement.',
		'details' => 'We look forward to welcoming your child to the studio.',
	),
	'residency'    => array(
		'title'   => 'Residency request received',
		'lead'    => 'Thank you for your art residency enquiry. We will review your dates and studio needs, then reply with availability and pricing.',
		'details' => 'Residency spots are limited — we aim to respond within 1–2 business days.',
	),
	'life-drawing' => array(
		'title'   => 'Workshop booking received',
		'lead'    => 'Thank you for booking a life drawing session. We will confirm your place and send session details before the workshop date.',
		'details' => 'Sessions run Saturday afternoons at our Tay Ho studio.',
	),
);

$content = isset( $messages[ $from ] ) ? $messages[ $from ] : array(
	'title'   => 'Thank you',
	'lead'    => 'We have received your submission. Our team will be in touch shortly to confirm the details.',
	'details' => 'If you need immediate assistance, call (+84) 98 828 8302 or email contact@arttutorhanoi.com.',
);
?>
<main class="thank-you-page">
  <section class="courses-hero thank-you-page__hero" aria-labelledby="thank-you-heading">
    <div class="courses-hero__inner">
      <p class="thank-you-page__badge" aria-hidden="true">
        <span class="thank-you-page__icon">&#10003;</span>
      </p>
      <h1 id="thank-you-heading" class="courses-hero__title"><?php echo esc_html( $content['title'] ); ?></h1>
      <p class="courses-hero__subtitle thank-you-page__lead"><?php echo esc_html( $content['lead'] ); ?></p>
    </div>
  </section>

  <section class="thank-you-page__body" aria-label="What happens next">
    <div class="thank-you-page__inner">
      <p class="thank-you-page__details"><?php echo esc_html( $content['details'] ); ?></p>

      <div class="thank-you-page__steps">
        <h2 class="thank-you-page__steps-title">What happens next</h2>
        <ol class="thank-you-page__steps-list">
          <li>We review your booking details</li>
          <li>You receive confirmation by email or WhatsApp</li>
          <li>Arrive at the studio ready to create</li>
        </ol>
      </div>

      <div class="thank-you-page__actions">
        <a href="<?php echo esc_url( ath_page_url( 'calendar' ) ); ?>" class="courses-cta__btn">View weekly calendar</a>
        <a href="<?php echo esc_url( ath_page_url( 'home' ) ); ?>" class="thank-you-page__link">Back to homepage</a>
      </div>
    </div>
  </section>
</main>
