<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/sheet.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/gallery.php';

header('Cache-Control: no-cache, must-revalidate');

$force_sheet_refresh = ap_should_refresh_sheet_cache();

if ($force_sheet_refresh) {
    ap_invalidate_sheet_cache();
    ap_invalidate_gallery_cache();
}

$artist_id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';

if ($artist_id === '') {
    http_response_code(400);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Artist portfolio</title></head><body>';
    echo '<p>Missing portfolio id. Use <code>?id=AD0601</code> in the URL.</p>';
    echo '</body></html>';
    exit;
}

$artist = ap_find_artist_by_id($artist_id, $force_sheet_refresh);

if ($artist === null) {
    http_response_code(404);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Artist not found</title></head><body>';
    echo '<p>No portfolio found for id <code>' . ap_esc($artist_id) . '</code>.</p>';
    echo '</body></html>';
    exit;
}

$name = $artist['name'] ?? 'Artist';
$intro = $artist['fine_art_portfolio'] ?? '';
$date = $artist['date'] ?? '';
$about = $artist['about_me'] ?? '';
$teacher = $artist['teacher'] ?? '';
$teacher_comment = $artist['teacher_commnet'] ?? '';
$looking_ahead = ap_parse_paragraphs($artist['looking_ahead'] ?? '');
$contact = ap_parse_contact($artist['contact'] ?? '');
$timeline = ap_parse_timeline($artist['timeline'] ?? '');
$hero_image = $artist['hhighlight'] ?? '';
$portrait_one = $artist['portrait_1'] ?? '';
$closing_gallery_items = ap_closing_gallery_items($artist, $force_sheet_refresh);
$artwork_folders = ap_artwork_section_folders($artist);
$artwork_galleries = ap_collect_galleries($artist, $artwork_folders, $force_sheet_refresh);

$teacher_record = ap_find_teacher_by_name($teacher, $force_sheet_refresh);
$teacher_name = trim((string) ($teacher_record['teacher_name'] ?? ''));

if ($teacher_name === '') {
    $teacher_name = $teacher !== '' ? $teacher : 'Art Tutor Hanoi';
}
$teacher_photo = trim((string) ($teacher_record['portrait_link'] ?? ''));
$teacher_signature = trim((string) ($teacher_record['signature_link'] ?? ''));
$show_teacher = $teacher_comment !== '';

function ap_render_gallery_group(array $gallery, string $artist_name): void
{
    $label = ap_esc($gallery['label'] ?? '');
    $gallery_class = ap_esc($gallery['gallery_class'] ?? 'gallery');
    $item_alt = $label !== '' ? $label . ' — ' . $artist_name : $artist_name;
    ?>
    <div class="artworks__group">
      <?php if ($label !== '') : ?>
        <p class="artworks__group-label"><?php echo $label; ?></p>
      <?php endif; ?>

      <div class="gallery <?php echo $gallery_class; ?>">
        <?php foreach (($gallery['items'] ?? []) as $item) : ?>
          <?php ap_render_gallery_item($item, $item_alt); ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
}

?>
<!DOCTYPE html>
<html lang="en" class="js-motion">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
  <title><?php echo ap_esc($name); ?> — Fine Art Portfolio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=66">
</head>
<body>
  <div class="page">

    <header class="hero">
      <div class="hero__top">
        <?php if ($hero_image !== '') : ?>
          <div class="hero__highlight parallax-wrap">
            <img
              data-parallax="0.1"
              src="<?php echo ap_esc($hero_image); ?>"
              alt="<?php echo ap_esc($name); ?> artwork highlight"
            >
          </div>
        <?php endif; ?>
        <h1 class="hero__name"><?php echo ap_esc($name); ?></h1>
      </div>

      <div class="hero__bottom">
        <div class="hero__intro">
          <p class="hero__intro-title">Fine Art Portfolio</p>
          <?php if ($intro !== '') : ?>
            <p class="hero__intro-text"><?php echo ap_esc($intro); ?></p>
          <?php endif; ?>
        </div>

        <?php if ($date !== '') : ?>
          <div class="hero__meta">
            <span class="hero__meta-line" aria-hidden="true"></span>
            <span class="hero__meta-text">
              <a class="hero__meta-studio" href="https://arttutorhanoi.com/" target="_blank" rel="noopener noreferrer">Art Tutor Hanoi</a>
              <span class="hero__meta-date" x-apple-data-detectors="false">· <?php echo ap_esc($date); ?></span>
            </span>
          </div>
        <?php endif; ?>
      </div>
    </header>

    <?php if ($about !== '' || $portrait_one !== '') : ?>
      <section class="about">
        <?php if ($about !== '') : ?>
          <div class="about__content">
            <h2 class="about__heading">ABOUT ME</h2>
            <div class="about__text">
              <p><?php echo ap_text($about); ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($portrait_one !== '') : ?>
          <div class="about__portrait parallax-wrap">
            <img
              data-parallax="0.08"
              src="<?php echo ap_esc($portrait_one); ?>"
              alt="<?php echo ap_esc($name); ?> portrait"
              width="400"
              height="540"
            >
          </div>
        <?php endif; ?>
      </section>
    <?php endif; ?>

    <?php if ($timeline !== []) : ?>
      <section class="timeline">
        <h2 class="timeline__heading">TIMELINE</h2>
        <p class="timeline__intro">Two-year learning path — each phase built on the last.</p>

        <ol class="timeline__list">
          <?php foreach ($timeline as $item) : ?>
            <li class="timeline__item">
              <?php if (($item['range'] ?? '') !== '') : ?>
                <span class="timeline__range" x-apple-data-detectors="false"><?php echo ap_esc($item['range']); ?></span>
              <?php endif; ?>
              <div class="timeline__content">
                <?php if (($item['title'] ?? '') !== '') : ?>
                  <h3 class="timeline__title"><?php echo ap_esc($item['title']); ?></h3>
                <?php endif; ?>
                <?php if (($item['text'] ?? '') !== '') : ?>
                  <p class="timeline__text"><?php echo ap_esc($item['text']); ?></p>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      </section>
    <?php endif; ?>

    <?php if ($show_teacher) : ?>
      <section class="comment">
        <h2 class="comment__heading">TEACHER'S COMMENT</h2>
        <div class="comment__body">
          <p class="comment__text"><?php echo ap_text($teacher_comment); ?></p>
          <div class="comment__author">
            <?php if ($teacher_photo !== '') : ?>
              <img
                class="comment__photo"
                src="<?php echo ap_esc($teacher_photo); ?>"
                alt="<?php echo ap_esc($teacher_name); ?>"
                width="120"
                height="120"
              >
            <?php endif; ?>
            <?php if ($teacher_signature !== '') : ?>
              <img
                class="comment__signature"
                src="<?php echo ap_esc($teacher_signature); ?>"
                alt="Signature <?php echo ap_esc($teacher_name); ?>"
                width="160"
                height="60"
              >
            <?php endif; ?>
            <p class="comment__name"><?php echo ap_esc($teacher_name); ?></p>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($artwork_galleries !== []) : ?>
      <section class="artworks" id="artworks">
        <h2 class="artworks__heading">ARTWORKS</h2>
        <?php foreach ($artwork_galleries as $gallery) : ?>
          <?php ap_render_gallery_group($gallery, $name); ?>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <?php if ($looking_ahead !== [] || $contact !== [] || $closing_gallery_items !== []) : ?>
      <section class="closing" id="continuing">
        <h2 class="closing__heading">LOOKING AHEAD</h2>

        <div class="closing__layout">
          <div class="closing__content">
            <?php if ($looking_ahead !== []) : ?>
              <div class="closing__text">
                <?php foreach ($looking_ahead as $paragraph) : ?>
                  <p><?php echo ap_text($paragraph); ?></p>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if ($contact['email'] !== '' || $contact['phone'] !== '' || $contact['lines'] !== []) : ?>
              <div class="closing__contact">
                <p class="closing__contact-label">Contact</p>
                <?php if ($contact['email'] !== '') : ?>
                  <p><a href="mailto:<?php echo ap_esc($contact['email']); ?>"><?php echo ap_esc($contact['email']); ?></a></p>
                <?php endif; ?>
                <?php if ($contact['phone'] !== '') : ?>
                  <p><a href="tel:<?php echo ap_esc(ap_phone_href($contact['phone'])); ?>"><?php echo ap_esc($contact['phone']); ?></a></p>
                <?php endif; ?>
                <?php foreach ($contact['lines'] as $line) : ?>
                  <p><?php echo ap_esc($line); ?></p>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if ($date !== '') : ?>
              <div class="closing__meta">
                <span class="closing__meta-line" aria-hidden="true"></span>
                <span class="closing__meta-text">
                  <a class="closing__meta-studio" href="https://arttutorhanoi.com/" target="_blank" rel="noopener noreferrer">Art Tutor Hanoi</a>
                  <span class="closing__meta-date" x-apple-data-detectors="false">· <?php echo ap_esc($date); ?></span>
                </span>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($closing_gallery_items !== []) : ?>
            <?php $closing_gallery_count = count($closing_gallery_items); ?>
            <div class="closing__gallery" data-closing-gallery>
              <button
                type="button"
                class="closing__gallery-nav closing__gallery-nav--prev"
                aria-label="Previous photo"
                <?php echo $closing_gallery_count < 2 ? 'hidden' : ''; ?>
              >&lsaquo;</button>
              <div class="closing__gallery-view">
                <div class="gallery gallery--closing">
                  <?php foreach ($closing_gallery_items as $index => $item) : ?>
                    <?php ap_render_gallery_item($item, $name . ' gallery', $index === 0); ?>
                  <?php endforeach; ?>
                </div>
              </div>
              <button
                type="button"
                class="closing__gallery-nav closing__gallery-nav--next"
                aria-label="Next photo"
                <?php echo $closing_gallery_count < 2 ? 'hidden' : ''; ?>
              >&rsaquo;</button>
            </div>
          <?php endif; ?>
        </div>
      </section>
    <?php endif; ?>

  </div>
  <?php if (isset($_GET['debug']) && $_GET['debug'] === '1') : ?>
    <?php $debug_report = ap_gallery_debug_report($artist); ?>
    <pre class="ap-debug" style="margin:2rem;padding:1rem;background:#111;color:#eee;overflow:auto;font:12px/1.5 monospace;"><?php echo ap_esc(json_encode($debug_report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
  <?php endif; ?>
  <script src="main.js?v=66"></script>
</body>
</html>
