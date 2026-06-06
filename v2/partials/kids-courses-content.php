<?php
$pathways = require __DIR__ . '/../data/kids-pathways.php';
?>
<main class="courses-page courses-page--kids">
  <section class="courses-hero" aria-labelledby="kids-courses-heading">
    <div class="courses-hero__inner">
      <h1 id="kids-courses-heading" class="courses-hero__title">Art Classes for Kids</h1>
      <p class="courses-hero__subtitle">Choose what they&rsquo;d like to learn.</p>
    </div>
  </section>

  <section class="courses-intro">
    <div class="courses-intro__inner">
      <p class="courses-intro__tag">Art class for Kids that teaches in English</p>
      <p><strong>Worried that your child spends too much time on screens or feels stressed with too many activities?</strong> Give them a calm and creative space to relax, draw, and express themselves through art.</p>
      <p>A creative program for children to explore drawing, painting, storytelling, and contemporary art through hands-on studio practice in Tay Ho, Hanoi.</p>
    </div>
  </section>

  <section class="pathways-section" aria-label="Kids course pathways">
    <div class="pathways-section__inner">
      <?php
      $pathways_panel = 'detail';
      $pathways_hub   = 'kids';
      require __DIR__ . '/pathways-list.php';
      ?>
    </div>
  </section>

  <section class="courses-schedule" aria-labelledby="kids-schedule-heading">
    <div class="courses-schedule__inner">
      <h2 id="kids-schedule-heading" class="courses-schedule__title">Class Schedule &amp; Price</h2>

      <div class="courses-schedule__table-wrap">
        <table class="courses-schedule__table courses-schedule__table--combined">
          <caption class="courses-schedule__caption">12-session course &middot; 1.5 hours per session &middot; Ages 5&ndash;11</caption>
          <thead>
            <tr class="courses-schedule__section-row">
              <th scope="colgroup" colspan="6">Available schedule</th>
            </tr>
            <tr class="courses-schedule__days-row">
              <th scope="col">Monday</th>
              <th scope="col">Tuesday</th>
              <th scope="col">Wednesday</th>
              <th scope="col">Friday</th>
              <th scope="col">Saturday</th>
              <th scope="col">Sunday</th>
            </tr>
          </thead>
          <tbody>
            <tr class="courses-schedule__times-row">
              <td>2:00 PM &ndash; 3:30 PM</td>
              <td>10:00 AM &ndash; 11:30 AM<br>2:00 PM &ndash; 3:30 PM<br>5:00 PM &ndash; 6:30 PM</td>
              <td>2:00 PM &ndash; 3:30 PM</td>
              <td>10:00 AM &ndash; 11:30 AM<br>2:00 PM &ndash; 3:30 PM<br>5:00 PM &ndash; 6:30 PM</td>
              <td>5:00 PM &ndash; 6:30 PM</td>
              <td>10:00 AM &ndash; 11:30 AM<br>2:00 PM &ndash; 3:30 PM<br>5:00 PM &ndash; 6:30 PM</td>
            </tr>
            <tr class="courses-schedule__section-row">
              <th scope="colgroup" colspan="6">Tuition &mdash; 4,800,000 VND / 12 sessions</th>
            </tr>
            <tr class="courses-schedule__price-head">
              <th scope="col" colspan="2">Summer 2026 Offer</th>
              <th scope="col">USD</th>
              <th scope="col" colspan="3">VND</th>
            </tr>
            <tr>
              <td colspan="2">First student</td>
              <td>138</td>
              <td colspan="3">3,600,000</td>
            </tr>
            <tr>
              <td colspan="2">Additional student</td>
              <td>+ 92</td>
              <td colspan="3">+ 2,400,000</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="courses-benefits" aria-labelledby="kids-benefits-heading">
    <div class="courses-benefits__inner">
      <h2 id="kids-benefits-heading" class="courses-benefits__title">What&rsquo;s included</h2>
      <ul class="courses-benefits__list">
        <li>Small group classes with English-speaking teachers</li>
        <li>All materials included</li>
        <li>Guidance based on each child&rsquo;s level</li>
        <li>Certificate after course completion</li>
        <li>Artwork portfolio to bring home</li>
        <li>Optional guided visit to an art museum or exhibition</li>
      </ul>
    </div>
  </section>

  <section class="courses-teachers" aria-labelledby="kids-teachers-heading">
    <div class="courses-teachers__inner">
      <h2 id="kids-teachers-heading" class="courses-teachers__title">Who will teach</h2>
      <p>Classes are taught by graduates from the <a href="https://mythuatvietnam.edu.vn/" target="_blank" rel="noopener noreferrer">Vietnam University of Fine Arts</a>, with expertise in both academic drawing and contemporary art practices.</p>
      <p><a href="https://arttutorhanoi.com/meet-the-artists/">Meet our artists</a></p>
    </div>
  </section>

  <section class="kids-gallery" aria-label="Photos from kids classes">
    <div class="kids-gallery__inner">
      <figure class="kids-gallery__item">
        <img src="https://res.cloudinary.com/dftadlujq/images/w_600,h_400,c_fill,f_auto,q_auto/v1759744433/Arttutor_Pics/IMG_0434-1/IMG_0434-1.jpg" alt="Child sketching at Art Tutor Hanoi" width="600" height="400" loading="lazy" decoding="async">
      </figure>
      <figure class="kids-gallery__item">
        <img src="https://res.cloudinary.com/dftadlujq/images/w_600,h_400,c_fill,f_auto,q_auto/v1759373017/Arttutor_Pics/298/298.jpg" alt="Kids art class in the studio" width="600" height="400" loading="lazy" decoding="async">
      </figure>
      <figure class="kids-gallery__item">
        <img src="https://res.cloudinary.com/dftadlujq/images/w_600,h_400,c_fill,f_auto,q_auto/v1759373031/Arttutor_Pics/IMG_0426-EDIT/IMG_0426-EDIT.jpg" alt="Child creating artwork at Art Tutor Hanoi" width="600" height="400" loading="lazy" decoding="async">
      </figure>
    </div>
  </section>

  <section class="courses-cta">
    <div class="courses-cta__inner">
      <p class="courses-cta__text">Try a trial class — teachers will observe your child&rsquo;s level and recommend a suitable group.</p>
      <p class="courses-cta__note">Trial class: <s>400,000 VND</s> <strong>300,000 VND</strong> (Summer offer, 25% off)</p>
      <a href="experience.php?slug=trial-art-class" class="courses-cta__btn">Book a Trial Class</a>
      <p class="courses-cta__links">
        <a href="pricing.php">View pricing</a>
        <span aria-hidden="true">&middot;</span>
        <a href="calendar.php">Weekly calendar</a>
      </p>
    </div>
  </section>
</main>
