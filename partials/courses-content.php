<?php
$pathways = require __DIR__ . '/../data/pathways.php';
?>
<main class="courses-page">
  <section class="courses-hero" aria-labelledby="courses-heading">
    <div class="courses-hero__inner">
      <h1 id="courses-heading" class="courses-hero__title">Art Classes in Hanoi</h1>
      <p class="courses-hero__subtitle">Choose what you&rsquo;d like to learn.</p>
    </div>
  </section>

  <section class="pathways-section" aria-label="Course pathways">
    <div class="pathways-section__inner">
      <?php
      $pathways_panel = 'courses';
      require __DIR__ . '/pathways-list.php';
      ?>
    </div>
  </section>

  <section class="courses-benefits" aria-labelledby="courses-benefits-heading">
    <div class="courses-benefits__inner">
      <h2 id="courses-benefits-heading" class="courses-benefits__title">Why study at Art Tutor Hanoi?</h2>
      <ul class="courses-benefits__list">
        <li>Learn drawing, painting, and composition from instructors trained at Vietnam University of Fine Arts</li>
        <li>Classes in English or bilingual instruction &mdash; ideal for expats and visitors</li>
        <li>Flexible scheduling for short stays or long-term residents</li>
        <li>Central Hanoi studio, welcoming to all skill levels</li>
      </ul>
    </div>
  </section>

  <section class="courses-cta">
    <div class="courses-cta__inner">
      <p class="courses-cta__text">Still undecided? Try a sample class and find your pathway.</p>
      <a href="experience.php?slug=trial-art-class" class="courses-cta__btn">Book a Sample Class</a>
      <p class="courses-cta__links">
        <a href="pricing.php">View pricing</a>
        <span aria-hidden="true">&middot;</span>
        <a href="calendar.php">Weekly calendar</a>
      </p>
    </div>
  </section>
</main>
