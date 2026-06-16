<?php
/**
 * @var string $week_range
 * @var string $art_calendar_html
 * @var string $table_html
 */
?>
<main class="courses-page">
  <section class="courses-hero" aria-labelledby="calendar-heading">
    <div class="courses-hero__inner">
      <h1 id="calendar-heading" class="courses-hero__title">Weekly calendar</h1>
      <p class="courses-hero__subtitle"><?php echo htmlspecialchars( $week_range, ENT_QUOTES, 'UTF-8' ); ?></p>
    </div>
  </section>

  <?php if ( $art_calendar_html !== '' || $table_html !== '' ) : ?>
  <section class="calendar-schedule" aria-label="Class schedule">
    <div class="calendar-schedule__inner">
      <div class="calendar-schedule__grid">

        <?php if ( $art_calendar_html !== '' ) : ?>
        <article class="calendar-panel">
          <h2 class="calendar-panel__title">Student timetable</h2>
          <div class="calendar-panel__body calendar-art-calendar">
            <?php echo $art_calendar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </div>
        </article>
        <?php endif; ?>

        <?php if ( $table_html !== '' ) : ?>
        <article class="calendar-panel">
          <h2 class="calendar-panel__title">Class schedule</h2>
          <div class="calendar-panel__body">
            <?php echo $table_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </div>
        </article>
        <?php endif; ?>

      </div>
    </div>
  </section>
  <?php endif; ?>
</main>
