<?php
/**
 * Site footer — v2 layout.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;
?>
  <footer class="site-footer">
    <div class="site-footer__main">
      <div class="site-footer__brand">
        <a href="<?php echo esc_url( ath_page_url( 'home' ) ); ?>" aria-label="Art Tutor Hanoi home">
          <img src="<?php echo esc_url( ath_asset_url( 'logo.png' ) ); ?>" alt="Art Tutor Hanoi" class="logo" width="252" height="168" decoding="async">
        </a>
        <p class="site-footer__desc">English-speaking painting and drawing classes in a quiet studio near West Lake, Hanoi — for beginners, expats, and travellers.</p>
      </div>

      <div>
        <ul class="site-footer__links">
          <li><a href="<?php echo esc_url( ath_page_url( 'faq' ) ); ?>">FAQ</a></li>
          <li><a href="<?php echo esc_url( ath_students_artworks_url() ); ?>">Students&rsquo; Artworks</a></li>
          <li><a href="<?php echo esc_url( ath_page_url( 'art-supplies' ) ); ?>">Art Supplies</a></li>
          <li><a href="<?php echo esc_url( ath_page_url( 'exhibition' ) ); ?>">Exhibition 2026</a></li>
          <li><a href="<?php echo esc_url( ath_page_url( 'links' ) ); ?>">More links</a></li>
        </ul>
      </div>

      <div class="site-footer__map-col">
        <a href="https://maps.app.goo.gl/iNPAGGuyeTB5r3Qi8" class="site-footer__map" target="_blank" rel="noopener noreferrer" aria-label="View Art Tutor Hanoi on Google Maps">
          <iframe
            src="https://www.google.com/maps?q=Art+tutor+Hanoi,21.077096,105.8237665&hl=en&z=16&output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Art Tutor Hanoi on Google Maps"
            tabindex="-1">
          </iframe>
        </a>
      </div>

      <div class="site-footer__aside">
        <ul class="site-footer__contact">
          <li>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
            <span>82 Ngách 264/15 Ngõ 374 Đường Âu Cơ, Tây Hồ, Hanoi</span>
          </li>
          <li>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.5 2.6a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.5-1.5a2 2 0 0 1 2.1-.5c.9.2 1.7.4 2.6.5A2 2 0 0 1 22 16.9z"/></svg>
            <a href="tel:+84988288302">(+84) 98 828 8302</a>
          </li>
          <li>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
            <a href="mailto:contact@arttutorhanoi.com">contact@arttutorhanoi.com</a>
          </li>
        </ul>

        <div class="site-footer__social" aria-label="Social media">
          <a href="https://www.facebook.com/arttutorhanoi" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.5h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.23 2.68.23v2.95h-1.51c-1.49 0-1.95.93-1.95 1.88v2.26h3.32l-.53 3.5h-2.79V24C19.61 23.1 24 18.1 24 12.07z"/></svg>
          </a>
          <a href="https://www.instagram.com/arttutorhanoi/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.2 2.3.4.6.2 1 .5 1.5 1 .5.5.8.9 1 1.5.2.4.4 1.1.4 2.3.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.9-.4 2.3-.2.6-.5 1-1 1.5-.5.5-.9.8-1.5 1-.4.2-1.1.4-2.3.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.2-2.3-.4-.6-.2-1-.5-1.5-1-.5-.5-.8-.9-1-1.5-.2-.4-.4-1.1-.4-2.3-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.2-1.9.4-2.3.2-.6.5-1 1-1.5.5-.5.9-.8 1.5-1 .4-.2 1.1-.4 2.3-.4C8.4 2.2 8.8 2.2 12 2.2m0-2.2C8.7 0 8.3 0 7 0 5.7.1 4.8.3 4 .6c-.9.3-1.6.7-2.3 1.4C1 2.7.6 3.4.3 4.3.1 5.1 0 6 0 7.3s0 1.4.1 2.7c.1 1.3.3 2.2.6 3 .3.9.7 1.6 1.4 2.3.7.7 1.4 1.1 2.3 1.4.8.3 1.7.5 3 .6 1.3.1 1.7.1 5 .1s3.7 0 5-.1c1.3-.1 2.2-.3 3-.6.9-.3 1.6-.7 2.3-1.4.7-.7 1.1-1.4 1.4-2.3.3-.8.5-1.7.6-3 .1-1.3.1-1.7.1-5s0-3.7-.1-5c-.1-1.3-.3-2.2-.6-3-.3-.9-.7-1.6-1.4-2.3C21.3 1 20.6.6 19.7.3 18.9.1 18 0 16.7 0 15.3 0 15 0 12 0z"/><path d="M12 5.8A6.2 6.2 0 1 0 12 18.2 6.2 6.2 0 1 0 12 5.8m0 10.2A4 4 0 1 1 12 7.8a4 4 0 0 1 0 8.2zM19.8 4.6a1.4 1.4 0 1 1-2.9 0 1.4 1.4 0 0 1 2.9 0z"/></svg>
          </a>
          <a href="https://www.pinterest.com/arttutorhanoi/" target="_blank" rel="noopener noreferrer" aria-label="Pinterest">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.4 0 0 5.4 0 12c0 5.1 3.2 9.4 7.6 11.2-.1-.9-.2-2.3 0-3.3.2-.9 1.4-5.8 1.4-5.8s-.4-.8-.4-2c0-1.9 1.1-3.3 2.5-3.3 1.2 0 1.7.9 1.7 2 0 1.2-.8 3-1.2 4.6-.3 1.4.7 2.5 2.1 2.5 2.5 0 4.4-2.6 4.4-6.4 0-3.3-2.4-5.6-5.8-5.6-4 0-6.3 3-6.3 6.1 0 1.2.5 2.5 1.1 3.2.1.1.1.2.1.3-.1.4-.3 1.5-.4 1.7-.1.2-.3.2-.6.1-1.7-.8-2.7-3.2-2.7-5.1 0-4.2 3-8 8.7-8 4.6 0 8.1 3.3 8.1 7.7 0 4.5-2.8 8.2-6.8 8.2-1.3 0-2.6-.7-3-1.5l-.8 3.1c-.3 1.1-1.1 2.5-1.6 3.4 1.2.4 2.5.6 3.8.6 6.6 0 12-5.4 12-12S18.6 0 12 0z"/></svg>
          </a>
          <a href="https://www.tripadvisor.com/Search?q=Art+Tutor+Hanoi" target="_blank" rel="noopener noreferrer" aria-label="Tripadvisor">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/><circle cx="6.5" cy="10.5" r="2.2"/><circle cx="17.5" cy="10.5" r="2.2"/><path d="M6.5 12.7a3.5 3.5 0 0 0 3.4 2.6M17.5 12.7a3.5 3.5 0 0 1-3.4 2.6"/></svg>
          </a>
          <a href="https://www.getyourguide.com/s/?q=Art+Tutor+Hanoi" target="_blank" rel="noopener noreferrer" aria-label="GetYourGuide">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" fill="none" stroke="currentColor" stroke-width="2"/><path d="M8 8h8v8H8z"/></svg>
          </a>
          <a href="https://www.airbnb.com/s/Hanoi/experiences?query=Art+Tutor+Hanoi" target="_blank" rel="noopener noreferrer" aria-label="Airbnb">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.5c-1.2 2.4-2 4.2-2.6 5.6-.8 1.8-1.2 3.1-1.2 4 0 2.2 1.8 4 4 4s4-1.8 4-4c0-.9-.4-2.2-1.2-4-.6-1.4-1.4-3.2-2.6-5.6-.3-.6-.9-.6-1.2 0z"/></svg>
          </a>
        </div>

        <div class="site-footer__chat">
          <a href="https://zalo.me/84988288302" class="site-footer__chat-btn site-footer__chat-btn--zalo" target="_blank" rel="noopener noreferrer">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" font-size="10" font-weight="700" fill="currentColor">Z</text></svg>
            Zalo
          </a>
          <a href="https://wa.me/84988288302" class="site-footer__chat-btn site-footer__chat-btn--whatsapp" target="_blank" rel="noopener noreferrer">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.5 2 2 6.1 2 11.2c0 1.8.5 3.5 1.4 5L2 22l6-1.3c1.4.8 3 1.2 4.7 1.2 5.5 0 10-4.1 10-9.2S17.5 2 12 2zm5.8 13.5c-.2.6-1.2 1.1-1.7 1.1-.4 0-1 .2-3.4-.7-2.9-1.1-4.8-3.8-4.9-4-.1-.2-1.2-1.5-1.2-2.9s.8-2.1 1.1-2.4c.3-.3.7-.4 1-.4h.7c.2 0 .5-.1.7.5.3.7 1 2.5 1.1 2.7.1.2.1.4 0 .6-.2.4-.4.6-.6.9-.2.2-.4.4-.2.7.2.4 1.7 2.7 3.7 3.6 2.6 1.1 2.6.7 3.1.7.5 0 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2-.1-.1-.4-.2-.8-.4z"/></svg>
            WhatsApp
          </a>
        </div>
      </div>
    </div>

    <div class="site-footer__bar">
      <span>&copy; 2016&ndash;<?php echo esc_html( gmdate( 'Y' ) ); ?> Art Tutor Hanoi</span>
      <span>English-speaking art studio in Hanoi</span>
    </div>
  </footer>
  <script>
  (function () {
    var path = window.location.pathname || '';
    var search = window.location.search || '';
    if (path.indexOf('thank-you') === -1 || search.indexOf('entry=') === -1) {
      return;
    }
    var params = new URLSearchParams(search);
    var entry = params.get('entry');
    if (!entry) {
      return;
    }
    var api = <?php echo wp_json_encode( rest_url( 'ath/v1/thank-you-qr' ) ); ?>;
    var url = api + '?entry=' + encodeURIComponent(entry) + '&from=' + encodeURIComponent(params.get('from') || '') + '&_=' + Date.now();
    fetch(url, { credentials: 'same-origin', cache: 'no-store' })
      .then(function (res) { return res.ok ? res.json() : null; })
      .then(function (data) {
        if (!data || !data.html) {
          return;
        }
        var inner = document.querySelector('.thank-you-page__inner');
        if (!inner || inner.querySelector('.thank-you-page__payment')) {
          return;
        }
        inner.insertAdjacentHTML('afterbegin', data.html);
        var heading = document.getElementById('thank-you-heading');
        var lead = document.querySelector('.thank-you-page__lead');
        if (heading) {
          heading.textContent = 'Booking received — please complete payment';
        }
        if (lead) {
          lead.textContent = 'Your booking details are saved. Transfer the exact amount below via VietQR to confirm your place.';
        }
      })
      .catch(function () {});
  })();
  </script>
  <?php wp_footer(); ?>
</body>
</html>
